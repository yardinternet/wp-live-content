<div id="live-content">
	<div hx-ext="sse" sse-connect="/sse?id={{ $postData->id }}" sse-swap="update" sse-close="close">
	</div>
	{!! $postData->content() !!}
</div>
