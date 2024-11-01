@if(config('broadcasting.default') === "pusher")
    @vite('resources/js/pusher.js')
@endif