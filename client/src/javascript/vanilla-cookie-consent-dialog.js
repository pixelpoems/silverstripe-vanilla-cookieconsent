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
        // console.log(consentConfig);
    } else {
        console.error('No valid config for cookie consent given.');
        return;
    }

    if (consentDialog && consentConfig.enableConsentModal) {
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
                                // ToDo: send insight for accepted services
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
                        window.iframemanager().acceptService(service);
                    },
                    onReject: () => {
                        // console.log('Rejecting service:', service);
                        window.iframemanager().rejectService(service);
                    },
                }
            }
        }
    }

    if(consentConfig.enableConsentModal) {
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

        window.addEventListener('cc:onFirstConsent', ({detail}) => {
            let acceptedCategoriesString = detail.cookie.categories.join(',');


            // Check the categories accepted
            // If only nessary category is accepted
            if (detail.cookie.categories.necessary && Object.keys(detail.cookie.categories).length === 1 || acceptedCategoriesString === 'necessary') {
                // Only necessary category is accepted
                sendInsightCreate('Rejected', 'necessary');
                return;
            }

            // If all categories are accepted
            if (Object.keys(detail.cookie.categories).length === Object.keys(categories).length) {
                // All categories are accepted
                sendInsightCreate('Accepted', 'all');
                return;
            }

            // If not all categories are accepted and not only necessary
            // Check if specific categories are accepted
            // We filter out the 'necessary' category as it is always accepted
            // and we want to know if any other categories are accepted
            // If no specific categories are accepted, we consider it as rejected
            const acceptedCategories = Object.keys(detail.cookie.categories).filter(cat => cat !== 'necessary' && detail.cookie.categories[cat]);
            if (acceptedCategories.length > 0) {
                // Specific categories are accepted
                sendInsightCreate('Partly', acceptedCategoriesString);
                return;
            }
        });
    }

    // This initializes the accept buttons in the consent dialog
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
                //
                // switch (acceptValue) {
                //     case 'necessary':
                //         // Accept only necessary category
                //         sendInsightCreate('Rejected', acceptValue);
                //         break;
                //     case 'all':
                //         // Accept all categories
                //         sendInsightCreate('Accepted', acceptValue);
                //         break;
                //     default:
                //         // Accept only specific categories
                //         sendInsightCreate('Partly', acceptValue);
                //         break;
                // }

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

    // This initializes the open preferences button in the dialog including handle the close button
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

    function sendInsightCreate(consentType, acceptedCategories) {
        if(!consentConfig.enableInsights) return;

        const insightData = {
            consentType,
            acceptedCategories
        }

        if(consentConfig.language?.locale) {
            insightData.locale = consentConfig.language.locale;
        }

        if(consentConfig?.subsite?.id) {
            insightData.subsiteId = consentConfig.subsite.id;
        }

        const insightUrl = '/insights/save/';

        console.log('Sending insight data:', insightData);

        fetch(insightUrl, {
            method: 'POST',
            mode: 'cors',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(insightData),
        })
        .then(response => {
            // Check if response is 201
            if (response.status === 201) {
                console.log('Insight created successfully');
            } else {
                console.error('Failed to create insight:', response.statusText);
            }
        }).catch(error => {
            console.error('Error creating insight:', error);
        })

    }
}

window.handleCookieConsentDialog = handleCookieConsentDialog;



