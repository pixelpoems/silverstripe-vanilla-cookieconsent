<% if $DisplayCookieConsent %>
    <button id="show-consent-dialog-btn">
        <%t VanillaCookieConsent\ConsentModal.ShowConsent 'Cookie Settings' %>
    </button>

    <dialog id="dialog__cookieconsent" data-config="$JSConfig">

        <div class="cookieconsent__content">
            <h2>
                <%t VanillaCookieConsent\ConsentModal.Title 'We use cookies' %>
            </h2>

            <p>
                <%t VanillaCookieConsent\ConsentModal.Description 'We use cookies to give you the best possible experience.' %>
            </p>

            <div class="cookieconsent__btns">
                <button data-cc-accept='"all"' data-cc="accept-all">
                    <%t VanillaCookieConsent\Buttons.AcceptAll 'Accept All' %>
                </button>
                <button data-cc-accept='"necessary"' data-cc="accept-necessary">
                    <%t VanillaCookieConsent\Buttons.AcceptNecessary 'Accept Necessary' %>
                </button>
                <button data-cc="show-preferencesModal">
                    <%t VanillaCookieConsent\Buttons.ShowPreferences 'Show Preferences' %>
                </button>
            </div>
        </div>

        <div class="cookieconsent__footer">
            <% if $SiteConfig.ImprintPage %>
                <a href="$SiteConfig.ImprintPage.Link">
                    <%t VanillaCookieConsent\Links.PrivacyPolicy 'PrivacyPolicy' %>
                </a>
            <% end_if %>

            <% if $SiteConfig.DataProtectionPage %>
                <a href="$SiteConfig.DataProtectionPage.Link">
                    <%t VanillaCookieConsent\Links.Imprint 'Imprint' %>
                </a>
            <% end_if %>
        </div>
    </dialog>
<% end_if %>
