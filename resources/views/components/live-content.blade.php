<div id="live-content">
    <div
            hx-ext="sse"
            sse-connect="/yard/live-content/stream?id={{ $postId }}"
            sse-swap="update"
            sse-close="close">
    </div>
    {!! apply_filters('the_content', get_the_content(null, false, $postId)) !!}
</div>
