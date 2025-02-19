import * as cc from 'vanilla-cookieconsent';

document.addEventListener('DOMContentLoaded', () => {
    const consentDialog = document.querySelector('#dialog__cookieconsent');
    if(!consentDialog) return;

    const consentConfigData = consentDialog.dataset.config;
    const consentConfig = JSON.parse(consentConfigData);
    if(!consentConfig) return;

    initShowButton();
    initAcceptButtons();
    initPrefsButton();

    setTimeout(() => {
        cc.run({
            autoShow: false,
            categories: {
                necessary: {
                    enabled: true,  // this category is enabled by default
                    readOnly: true  // this category cannot be disabled
                },
                ...consentConfig.categories
            },
            language: consentConfig.language
        }).then(() => {
            // Show dialog if consent is not valid
            if (!cc.validConsent()) consentDialog.showModal();
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
        const showConsentDialogBtn = document.querySelector('#show-consent-dialog-btn');
        if(!showConsentDialogBtn) return;

        showConsentDialogBtn.addEventListener('click', () => {
            consentDialog.showModal();
        })
    }

    function initPrefsButton() {
        const prefsBtn = document.querySelector('[data-cc="show-preferencesModal"]');

        prefsBtn.addEventListener('click', () => {
            consentDialog.close();

            const closePrefsBtn = document.querySelector('.pm__close-btn');
            closePrefsBtn.addEventListener('click', () => {
                consentDialog.showModal();
            })
        })
    }
});
