<section>
    <% if $ShowTitle %>
        <h2>$Title</h2>
    <% end_if %>

    <% if $isEmbedded %>
        <div data-service="$SourceType" data-id="$EmbeddedVideoID" <% if $iFrameTitle %>data-title="$iFrameTitle"<% end_if %> data-autoscale></div>
    <% end_if %>

    <% if $isUpload %>
        $Video
    <% end_if %>
</section>
