<div id="live-content">
    <div
        hx-ext="sse"
        sse-connect="{{ route('yard.live-content.stream', ['id' => $postData->id]) }}"
        sse-swap="update"
        sse-close="close">
    </div>
    {!! $postData->content() !!}
</div>
