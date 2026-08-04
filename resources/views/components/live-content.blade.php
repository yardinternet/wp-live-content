<div id="live-content">
    <div
            hx-get="/yard/live-content/poll?id={{ $postId }}&since={{ time() }}"
            hx-trigger="every 10s"
            hx-swap="outerHTML"></div>
    {!! apply_filters('the_content', get_the_content(null, false, $postId)) !!}
</div>
