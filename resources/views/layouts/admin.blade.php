<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    @include('layouts.parts.header-includes')
    @include('layouts.parts.footer-includes')
</head>
<body>
    @include('layouts.parts.loader')
    <div id="main-wrapper" data-theme="light" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed" data-boxed-layout="full">
        @include('layouts.parts.header-topbar')
        @include('layouts.parts.sidebar')
        <div class="page-wrapper">
            @include('layouts.parts.topbar')
            <div class="container-fluid">
                @include('layouts.parts.toast')
                @yield('content')
            </div>
            @include('layouts.parts.footer')
        </div>
    </div>
    @include('layouts.parts.footer-scripts')
    @include('layouts.parts.modal')
</body>
</html>

