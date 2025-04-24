<% if $SiteConfig.shouldIncludeDialog %>
    <dialog id="dialog__cookieconsent" data-cc-config="$CCJSConfig">
        <div class="cookieconsent__content">
            <h2>
                <% if $SiteConfig.ModalTitle %>
                    $SiteConfig.ModalTitle
                <% else %>
                    <%t VanillaCookieConsent\ConsentModal.Title 'We use cookies' %>
                <% end_if %>

            </h2>

            <p>
                <% if $SiteConfig.ModalDescription %>
                    $SiteConfig.ModalDescription
                <% else %>
                    <%t VanillaCookieConsent\ConsentModal.Description 'We use cookies to give you the best possible experience.' %>
                <% end_if %>
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
    </dialog>
<% end_if %>
