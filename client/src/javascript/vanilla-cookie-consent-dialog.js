import dialogPolyfill from "dialog-polyfill";
import * as CookieConsent from "vanilla-cookieconsent";

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

    const cc = CookieConsent;
    setTimeout(() => {
        cc.run({
            autoShow: !consentDialog,
            categories: {
                necessary: {
                    enabled: true,  // this category is enabled by default
                    readOnly: true  // this category cannot be disabled
                },
                ...consentConfig.categories
            },
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
                // console.log(acceptValue);
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