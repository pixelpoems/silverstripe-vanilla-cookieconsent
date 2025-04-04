import dialogPolyfill from "dialog-polyfill";
import * as CookieConsent from "vanilla-cookieconsent";
require('@orestbida/iframemanager/src/iframemanager');

export function handleCookieConsentDialog() {
    const consentDialog = document.querySelector('#dialog__cookieconsent');
    let removedCCDefaultDialog = false;

    let consentConfigData;
    if (consentDialog) {
        consentConfigData = consentDialog.dataset.ccConfig;
        dialogPolyfill.registerDialog(consentDialog);
    } else consentConfigData = document.body.dataset.ccConfig;

    let consentConfig;
    if (consentConfigData) {
        consentConfig = JSON.parse(consentConfigData);
    } else {
        console.error('No valid config for cookie consent given.');
        return;
    }

    if (consentDialog) {
        initShowButton();
        initAcceptButtons();
        initPrefsButton();
    }

    let categories = {
        necessary: {
            enabled: true,  // this category is enabled by default
            readOnly: true  // this category cannot be disabled
        },
        ...consentConfig.categories
    };

    for (const category in categories) {
        if (categories[category].services === undefined) {
            categories[category].services = {};
        }
    }

    if(consentConfig.enableIFrameManager &&  window.iframemanager()) {
        console.log('IFrameManager enabled.');

        // Initialize iframemanager
        setTimeout(() => {
            window.iframemanager().run({
                onChange: ({ changedServices, eventSource }) => {
                    if (eventSource.type === 'click') {
                        // console.log('Changed services:', changedServices);
                        for (const category in categories) {
                            const servicesToAccept = [
                                ...CookieConsent.getUserPreferences().acceptedServices[category],
                                ...changedServices,
                            ];

                            CookieConsent.acceptService(servicesToAccept, category);
                        }
                    }
                },
                currLang: consentConfig.language.default,
                // data-title is added to the iframe container threw data-title attribute
                services: {
                    youtube: {
                        embedUrl: 'https://www.youtube-nocookie.com/embed/{data-id}',
                        thumbnailUrl: 'https://i3.ytimg.com/vi/{data-id}/hqdefault.jpg',
                        iframe: {
                            allow: 'accelerometer; encrypted-media; gyroscope; picture-in-picture; fullscreen;',
                        },
                        languages: {
                            de: {
                                notice: 'This content is hosted by a third party. By showing the external content you accept the <a rel="noreferrer noopener" href="https://www.youtube.com/t/terms" target="_blank">terms and conditions</a> of youtube.com.',
                                loadBtn: 'Load once',
                                loadAllBtn: "Don't ask again"
                            },
                        },
                    },
                    vimeo: {
                        embedUrl: 'https://player.vimeo.com/video/{data-id}',
                        iframe: {
                            allow: 'fullscreen; picture-in-picture;',
                        },

                        thumbnailUrl: async (dataId, setThumbnail) => {
                            const url = `https://vimeo.com/api/v2/video/${dataId}.json`;
                            const response = await (await fetch(url)).json();
                            const thumbnailUrl = response[0]?.thumbnail_large;
                            thumbnailUrl && setThumbnail(thumbnailUrl);
                        },

                        languages: {
                            de: {
                                notice: 'This content is hosted by a third party. By showing the external content you accept the <a rel="noreferrer noopener" href="https://vimeo.com/terms" target="_blank">terms and conditions</a> of vimeo.com.',
                                loadBtn: 'Load once',
                                loadAllBtn: "Don't ask again",
                            },
                        },
                    },
                    yumpu: {
                        embedUrl: 'https://www.yumpu.com/de/embed/view/{data-id}',
                        languages: {
                            de: {
                                notice: 'This content is hosted by a third party. By showing the external content you accept the <a rel="noreferrer noopener" href="https://www.yumpu.com/en/info/privacy_policy" target="_blank">privacy policy</a> of yumpu.com.',
                                loadBtn: 'Load once',
                                loadAllBtn: "Don't ask again",
                            },
                        },
                    }
                }
            });
        }, 500);

        // Setup for cc to accept/reject iframemanager services
        for (const category in categories) {
            if (category === 'video') {
                let services = categories[category].services;
                categories[category].services = {};
                services.forEach(service => {
                    const label = service.charAt(0).toUpperCase() + service.slice(1);
                    categories[category].services[service] = {
                        label: label,
                        onAccept: () => {
                            // console.log('Accepting service:', service);
                            window.iframemanager().acceptService(service)
                        },
                        onReject: () => {
                            // console.log('Rejecting service:', service);
                            window.iframemanager().rejectService(service)
                        },
                    }
                });
                break;
            }
        }
    }

    const cc = CookieConsent;
    setTimeout(() => {
        cc.run({
            autoShow: !consentDialog,
            categories: categories,
            language: consentConfig.language,
            hideFromBots: true,
            lazyHtmlGeneration: true,
        }).then(() => {
            // Show dialog if consent is not valid
            if (!cc.validConsent() && consentDialog) {
                document.onkeyup = function (e) {
                    if (e.key === 'Escape') {
                        // We need to show the dialog again if it was closed
                        consentDialog.showModal();
                    }
                };

                consentDialog.showModal();
                consentDialog.focus();

                // Remove default cc created
                if (!removedCCDefaultDialog) {
                    const ccDialog = document.querySelector('#cc-main .cm-wrapper');
                    if (ccDialog) {
                        ccDialog.remove();
                        // console.log('Default cookie consent dialog removed.');
                        removedCCDefaultDialog = true;
                    }
                }
            }
        });

    }, 500);

    function initAcceptButtons() {
        const acceptBtns = consentDialog.querySelectorAll('[data-cc-accept]');

        /**
         * Connect "data-cc-accept" buttons to API
         */
        for (const button of acceptBtns) {
            button.addEventListener('click', () => {
                /**
                 * See https://cookieconsent.orestbida.com/reference/api-reference.html#acceptcategory
                 * for more acceptCategory() examples
                 */
                const acceptValue = JSON.parse(button.dataset.ccAccept);
                cc.acceptCategory(acceptValue);
                consentDialog.close();
            });
        }
    }


    function initShowButton() {
        const showConsentDialogBtn = document.querySelector('#cookieconsent__settings-btn');
        if (!showConsentDialogBtn) return;

        showConsentDialogBtn.addEventListener('click', () => {
            consentDialog.showModal();
        })
    }

    function initPrefsButton() {
        const prefsBtn = document.querySelector('[data-cc="show-preferencesModal"]');

        prefsBtn.addEventListener('click', () => {
            consentDialog.close();

            const overlay = document.querySelector('.pm-overlay');
            if (overlay) {
                document.onkeyup = function (e) {
                    if (e.key === 'Escape') {
                        consentDialog.showModal();
                    }
                }

                overlay.addEventListener('click', () => {
                    consentDialog.showModal();
                    document.onkeyup = null;
                });
            }

            const closePrefsBtn = document.querySelector('.pm__close-btn');
            closePrefsBtn.addEventListener('click', () => {
                consentDialog.showModal();
                document.onkeyup = null;
            })
        })
    }
}

window.handleCookieConsentDialog = handleCookieConsentDialog;