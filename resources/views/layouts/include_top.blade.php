    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#6C1CFF">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title> @yield('title') </title>
    
    @if(get_frontend_settings('favicon_logo'))
    <link rel="shortcut icon" href="{{ asset('uploads/logo/' . get_frontend_settings('favicon_logo')) }}" type="image/x-icon">
    <link rel="apple-touch-icon" href="{{ asset('uploads/logo/' . get_frontend_settings('favicon_logo')) }}">
    @else
        <link rel="shortcut icon" href="{{ asset('uploads/logo/favicon.svg') }}" type="image/x-icon">
        <link rel="apple-touch-icon" href="{{ asset('uploads/logo/favicon.svg') }}">
    @endif
    
    <!-- PWA Manifest -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    
    <!-- Bootstrap -->
    <link rel="stylesheet" href="{{ asset('assets/frontend/vendors/bootstrap/bootstrap.min.css') }}">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Nice Select -->
    <link rel="stylesheet" href="{{ asset('assets/frontend/vendors/nice-select/nice-select.css') }}">
    <!-- Select 2 -->
    <link rel="stylesheet" href="{{ asset('assets/frontend/vendors/select2/select2.min.css') }}">
    
    <!-- listing slider -->
    <link rel="stylesheet" href="{{ asset('assets/frontend/css/swiper-bundle.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/backend/icon-picker/icons/fontawesome-all.min.css') }}" />
    <!-- toastr css -->
    <link rel="stylesheet" href="{{asset('plugin/toastr/toastr.min.css')}}">

     

    <!-- Custom Css -->
    <link rel="stylesheet" href="{{ asset('assets/frontend/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/frontend/css/responsive.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/frontend/css/custom.css') }}">
    <script src="{{ asset('assets/frontend/js/jquery-3.7.1.min.js') }}"></script>
    
    <!-- PWA Service Worker Registration -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/sw.js')
                    .then(function(registration) {
                        console.log('ServiceWorker registered successfully:', registration.scope);
                    })
                    .catch(function(err) {
                        console.log('ServiceWorker registration failed:', err);
                    });
            });
        }
    </script>
