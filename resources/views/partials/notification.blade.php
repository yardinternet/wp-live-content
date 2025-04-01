<button
    hx-post="{{ route('yard.live-content.content', ['id' => $postId]) }}"
    hx-swap="outerHTML"
    hx-target="#live-content"
    class="w-full bg-primary text-white p-4 text-center">
    Er is 1 nieuwe update, klik om te herladen
</button>