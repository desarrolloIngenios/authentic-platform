<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Authentic - @yield('title', 'Login')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="author" content="CreativeLayers">

   <link rel="icon" href="{{ asset("logo-app.ico") }}">
	<link rel="icon" href="https://www.authenticfarma.com/wp-content/uploads/2025/05/cropped-isotipo_color-scaled-1-32x32.png" sizes="32x32" />
	<link rel="icon" href="https://www.authenticfarma.com/wp-content/uploads/2025/05/cropped-isotipo_color-scaled-1-192x192.png" sizes="192x192" />
	<link rel="apple-touch-icon" href="https://www.authenticfarma.com/wp-content/uploads/2025/05/cropped-isotipo_color-scaled-1-180x180.png" />
    <!-- Styles -->
    <link rel="stylesheet" type="text/css" href="{{ asset('css/bootstrap-grid.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/icons.css') }}">
    <link rel="stylesheet" href="{{ asset('css/animate.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/style.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('css/responsive.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('css/chosen.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('css/colors/colors.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('css/bootstrap.css') }}" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="{{ asset('css/toastr.css') }}">
    <style>
        body {
            background: url('./images/fondo.png') no-repeat center center fixed;
            background-size: cover;
            position: relative;
            font-family: 'Quicksand', serif !important;
        }

        /* body::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.85);
            z-index: 0;
        } */

        .top-logo {
            position: fixed;
            top: 0px;
            left: 0px;
            z-index: 10;
        }

        .top-logo img {
             height: 4rem;
        }

        .login-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px;
            position: relative;
            z-index: 1;
        }

        .login-card {
            background: #ffffff;
            padding: 2.5rem;
            border-radius: 1rem;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
            width: 100%;
            max-width: 420px;
        }

        .login-card h4 {
            font-weight: 600;
            margin-bottom: 1.5rem;
            color: #0057b8;
        }

        .form-group label {
            font-weight: 500;
            color: #2c3e50;
        }

        .form-control {
            border-radius: 0.5rem;
            border: 1px solid #ced4da;
        }

        .btn-primary {
            border-radius: 0.5rem;
            font-weight: 600;
            background-color: #00a86b;
            border-color: #00a86b;
        }

        .btn-primary:hover {
            background-color: #0057b8;
            border-color: #0057b8;
        }

        .btn-google {
            border-radius: 0.5rem;
            font-weight: 600;
            background-color: #ffffff;
            border: 1px solid #ced4da;
            color: #444;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease-in-out;
        }

        .btn-google img {
            height: 20px;
            margin-right: 10px;
        }

        .btn-google:hover {
            background-color: #f5f5f5;
        }

        .text-muted a {
            color: #0057b8;
        }

        .text-muted a:hover {
            text-decoration: underline;
        }

        footer {
            background-color: #f8f9fa;
            padding: 15px 0;
            text-align: center;
            font-size: 0.9rem;
            color: #444;
            border-top: 1px solid #dee2e6;
            position: relative;
            z-index: 1;
        }
    </style>

</head>

<body>

    <div class="page-loading">
        <img src="{{ asset("images/logo_loader.gif") }}" alt="" height="50" />
    </div>
    

        @yield('content')

        <footer class="bg-light border-top py-3 mt-5">
            <div class="container">
                <div class="row text-center text-md-left align-items-center">
                <div class="col-12 col-md-6 mb-2 mb-md-0">
                    <small>© 2025 Authentic Farma</small><br />
                </div>
                <div class="col-12 col-md-6">
                    <a target="_blank" href="{{ route('policies-data.index') }}" class="text-muted mx-2">Política de Privacidad de Datos</a> |
                    <a target="_blank" href="{{ route('management-data.index') }}" class="text-muted mx-2">Política de Manejo de Datos</a>
                </div>
                </div>
            </div>
        </footer>
    </div>


    <script src="{{ asset('js/jquery.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/modernizr.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/script.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/bootstrap.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/wow.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/slick.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/parallax.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/select-chosen.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/jquery.scrollbar.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/toastr.js') }}"></script>

    {{-- aqui --}}
    @yield('js')
    <script>
        @if(session('warning'))
            toastr.warning(@json(session('warning')));
        @endif
    </script>
</body>

</html>
