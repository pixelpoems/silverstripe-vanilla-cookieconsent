import dialogPolyfill from "dialog-polyfill";
import * as CookieConsent from "vanilla-cookieconsent";
require('@orestbida/iframemanager/src/iframemanager');

let cc = null;

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

    // console.log(consentConfig.enableConsentModal)
    if (consentDialog && consentConfig.enableConsentModal) {
        // console.log('Consent dialog enabled');
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
        // Add iframemanager services to config
        let iframemanagerConfigServices = {};
        let iframeLangStrings = consentConfig.language.translations[consentConfig.language.default].iframeManager;

        // If no services but manager is enabled - we add all
        let services = consentConfig.services ?? ['youtube', 'vimeo', 'yump', 'googlemaps'];
        for (const service of services) {
            switch (service) {
                case 'googlemaps':
                    iframemanagerConfigServices.googlemaps = {
                        embedUrl: 'https://www.google.com/maps/embed?pb={data-id}',
                        //thumbnailUrl: 'https://maps.googleapis.com/maps/api/staticmap?size=600x300&markers={data-id}&key={api-key}',
                        iframe: {
                            allow: 'fullscreen; picture-in-picture;',
                        },
                        languages: {} // Needed to add language support
                    }

                    iframemanagerConfigServices.googlemaps.languages[consentConfig.language.default] = {
                        notice: iframeLangStrings.notices.googlemaps,
                        loadBtn: iframeLangStrings.loadBtn,
                        loadAllBtn: iframeLangStrings.loadAllBtn
                    }
                    break;
                case 'youtube':
                    iframemanagerConfigServices.youtube = {
                        embedUrl: 'https://www.youtube-nocookie.com/embed/{data-id}',
                        thumbnailUrl: 'https://i3.ytimg.com/vi/{data-id}/hqdefault.jpg',
                        iframe: {
                            allow: 'accelerometer; encrypted-media; gyroscope; picture-in-picture; fullscreen;',
                        },
                        languages: {} // Needed to add language support
                    }

                    iframemanagerConfigServices.youtube.languages[consentConfig.language.default] = {
                        notice: iframeLangStrings.notices.youtube,
                        loadBtn: iframeLangStrings.loadBtn,
                        loadAllBtn: iframeLangStrings.loadAllBtn
                    }
                    break;
                case 'vimeo':
                    iframemanagerConfigServices.vimeo = {
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
                        languages: {} // Needed to add language support
                    }

                    iframemanagerConfigServices.vimeo.languages[consentConfig.language.default] = {
                        notice: iframeLangStrings.notices.vimeo,
                        loadBtn: iframeLangStrings.loadBtn,
                        loadAllBtn: iframeLangStrings.loadAllBtn
                    }

                    break;
                case 'yumpu':
                    iframemanagerConfigServices.yumpu = {
                        embedUrl: 'https://www.yumpu.com/de/embed/view/{data-id}',
                        languages: {}, // Needed to add language support
                    }

                    iframemanagerConfigServices.yumpu.languages[consentConfig.language.default] = {
                        notice: iframeLangStrings.notices.yumpu,
                        loadBtn: iframeLangStrings.loadBtn,
                        loadAllBtn: iframeLangStrings.loadAllBtn
                    }
                    break;
            }
        }

        // Initialize iframemanager
        setTimeout(() => {
            window.iframemanager().run({
                onChange: ({ changedServices, eventSource }) => {
                    if (eventSource.type === 'click') {
                        // console.log('Changed services:', changedServices);
                        for (const category in categories) {
                            if(category) {

                                let servicesToAccept = [];
                                if(consentConfig.enableConsentModal) {
                                    servicesToAccept = [
                                        ...CookieConsent.getUserPreferences().acceptedServices[category],
                                        ...changedServices,
                                    ];
                                } else servicesToAccept = [...changedServices];

                                CookieConsent.acceptService(servicesToAccept, category);
                            }
                        }
                    }
                },
                currLang: consentConfig.language.default,
                // data-title is added to the iframe container threw data-title attribute
                services: iframemanagerConfigServices ?? [],
            });
        }, 500);

        // Setup for cc to accept/reject iframemanager services
        for (const category in categories) {
            let services = categories[category].services;
            categories[category].services = {};

            if(services.length === undefined) continue;
            for (const service of services) {
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
            }
        }
    }

    if(consentConfig.enableConsentModal) {
        // console.log('Consent dialog enabled');
        cc = CookieConsent;
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
    }

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