<!DOCTYPE html>
<html lang="en" data-kit-theme="default">

<head>
    @include("shared.header")
    @include("shared.websocket")
    @routes('admin', csp_nonce())
    @include("shared.context")

    <title>{{config('app.name')}}</title>
</head>

<body>

@include("shared.content")

@viteReactRefresh
@vite('resources/js/admin.jsx')
</body>

</html>
