<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Authentic - @yield('title', 'Farma')</title>
    
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="">
	<meta name="keywords" content="">
	<meta name="author" content="CreativeLayers">

	<link rel="icon" href="{{ asset("logo-app.ico") }}">
	<link rel="icon" href="https://www.authenticfarma.com/wp-content/uploads/2025/05/cropped-isotipo_color-scaled-1-32x32.png" sizes="32x32" />
	<link rel="icon" href="https://www.authenticfarma.com/wp-content/uploads/2025/05/cropped-isotipo_color-scaled-1-192x192.png" sizes="192x192" />
	<link rel="apple-touch-icon" href="https://www.authenticfarma.com/wp-content/uploads/2025/05/cropped-isotipo_color-scaled-1-180x180.png" />

	<!-- Styles -->
	<link rel="stylesheet" href="{{ asset('css/bootstrap-grid.css') }}">
	<link rel="stylesheet" href="{{ asset('css/icons.css') }}">
	<link rel="stylesheet" href="{{ asset('css/animate.min.css') }}">
	<link rel="stylesheet" href="{{ asset('css/style.css') }}">
	<link rel="stylesheet" href="{{ asset('css/responsive.css') }}">
	<link rel="stylesheet" href="{{ asset('css/chosen.css') }}">
	<link rel="stylesheet" href="{{ asset('css/colors/colors.css') }}">
	<link rel="stylesheet" href="{{ asset('css/bootstrap.css') }}">
	<link rel="stylesheet" href="{{ asset('css/modal.css') }}">
	<link rel="stylesheet" href="{{ asset('css/forms.css') }}">
	<link rel="stylesheet" href="{{ asset('css/postulations.css') }}">
	<link rel="stylesheet" href="{{ asset('css/drawer.css') }}">
	<link rel="stylesheet" href="{{ asset('css/vacants-dashboard.css') }}">
	<link rel="stylesheet" href="{{ asset('css/table-card.css') }}">
	
	<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
	<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    {{-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css" /> --}}
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/line-awesome/1.3.0/line-awesome/css/line-awesome.min.css">
	@yield('styles')
	<style>
		body {
			background-color: #f4f6f8;
			margin: 0;
			padding: 0;
			height: 100%;
			overflow-x: hidden;
			font-family: 'Quicksand', serif !important;
		}
		/*BARRA DE CARGA*/
		.progress-bar .bg-profile {
			background: linear-gradient(to right, #00a86b, #0057b8);
		}
		/* NAVBAR */
		.navbar-custom {
			background: linear-gradient(to right, #00a86b, #0057b8);
			box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
		}

		.navbar-logo {
			height: 45px;
		}

		.profile-img {
			width: 40px;
			height: 40px;
			border-radius: 50%;
			object-fit: cover;
		}

		/* SIDEBAR */
		.sidebar-card {
			width: 280px;
			position: fixed;
			top: 80px;
			left: 20px;
			z-index: 1030;
			transition: transform 0.6s ease;
		}

		.card-sidebar {
			border: none;
			box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
			height: 85vh;
			overflow-y: auto;
			border-radius: 10px;
			background-color: #fff;
		}

	/* Enlaces del sidebar */
		.nav-sidebar .nav-link {
			color: #333;
			font-weight: 500;
			padding: 10;
			border-radius: 10px;
			transition: background-color 0.3s ease, color 0.3s ease;
			display: flex;
			align-items: center;
		}

		/* Solo mover el texto (span) */
		.nav-sidebar .nav-link span {
			transition: transform 0.5s ease;
		}

		/* Hover y active */
		.nav-sidebar .nav-link:hover,
		.nav-sidebar .nav-link.active {
			background-color: #00a86b;
			color: #fff;
			box-shadow: 0 0 10px rgba(0, 168, 107, 0.5);
  			font-weight: bold;
		}



		.nav-sidebar .nav-link:hover span,
		.nav-sidebar .nav-link.active span {
			transform: translateX(25px);  /* Mueve solo el texto hacia la izquierda */
		}


		/* ÁREA DE CONTENIDO */
		.content-area {
			margin-left: 280px;
			padding: 30px;
			padding-top: 40px;
			margin-top: 40px;
			min-height: 95vh;
			display: flex;
			flex-direction: column;
			transition: margin-left 0.3s ease;
		}

		.content-wrapper {
			background-color: #ffffff;
			padding: 20px 30px;
			border-radius: 12px;
			box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
			flex: 1;
			display: flex;
			flex-direction: column;
		}

		.content-wrapper2 {
			background-color: #F4F7F9;
			padding: 20px 30px;
			
			flex: 1;
			display: flex;
			flex-direction: column;
		}

		.content-wrapper > .flex-grow-1 {
			flex-grow: 1;
		}

		/* Tarjetas */
		.card {
			border-radius: 12px;
			box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
		}

		/* IMAGEN DE PERFIL (opcional extra) */
		.profile-image {
			width: 100px;
			height: 100px;
			border-radius: 50%;
			object-fit: cover;
		}

		/* BOTÓN TOGGLE SOLO EN MÓVIL */
		.btn-toggle-sidebar {
			font-size: 24px;
			background: none;
			border: none;
			cursor: pointer;
		}

		/* OVERLAY (fondo oscuro al abrir sidebar en móvil) */
		.overlay-sidebar {
			position: fixed;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			background: rgba(0, 0, 0, 0.4);
			z-index: 1020;
			display: none;
		}

		/* MOSTRAR/MOVER sidebar en modo móvil */
		@media (max-width: 767.98px) {
			.sidebar-card {
				left: 0;
				top: 0;
				height: 100vh;
				width: 250px;
				transform: translateX(-100%);
				background: white;
				z-index: 1040;
			}

			.content-area {
				margin-left: 0;
			}

			body.sidebar-visible .sidebar-card {
				transform: translateX(0);
			}

			body.sidebar-visible .overlay-sidebar {
				display: block;
			}
		}


        .upload-btn {
            position: absolute;
            bottom: 0;
            right: 0;
            background-color: #3498db;
            border: none;
            border-radius: 50%;
            padding: 5px;
            color: white;
            font-size: 14px;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
		ul li{
			color: #1e1e1e;
			margin-bottom: 2px;
			position: relative;
		}

        .upload-btn i {
            font-size: 15px;
        }

        .upload-btn:hover {
            background-color: #2980b9;
        }
		#loader-overlay {
			position: fixed;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			background-color: rgba(0, 0, 0, 0.6); /* Fondo oscuro */
			display: none;
			justify-content: center;
			align-items: center;
			z-index: 9999;
		}

		.loader-content {
			text-align: center;
		}

		.loader-gif {
			width: 80px;  /* Cambia el tamaño del GIF */
			height: 80px;
		}

		.loader-message {
			color: #ffffff;
			font-size: 18px;
			margin-top: 15px;
		}
		//logo men
		.sidebar-logo {
			text-align: center;
			border-bottom: 1px solid #E0E0E0;
		}

		.sidebar-logo img {
			max-width: 30%;
			height: auto;
		}
		.icon-blue {
            color: #0057b8;
        }
		.font-green{
			color: #00a86b;
		}
		//estilo custom modal
		
	</style>
</head>
<body>
	<div class="page-loading">
		<img src="{{ asset("images/logo_loader.gif") }}" alt="" height="50" />
	</div>

	<div class="theme-layout" id="scrollup">

	<!-- BARRA DE NAVEGACIÓN -->
		<nav class="navbar navbar-expand-lg navbar-dark navbar-custom fixed-top">
			<div class="container-fluid d-flex align-items-center justify-content-between px-4">
				<!-- Botón hamburguesa solo visible en móvil -->
				<button class="btn btn-toggle-sidebar d-md-none text-white border-0">
					<i class="la la-bars"></i>
				</button>

				<!-- Logo -->
				<a class="navbar-brand d-flex align-items-center" href="#">
					<img src="{{ asset('images/logo-authenticfarma-white.png') }}" alt="Logo" class="navbar-logo">
				</a>

				<!-- Perfil usuario -->
				<div class="dropdown">
					<a href="#" class="d-flex align-items-center text-white dropdown-toggle" id="userMenu" data-toggle="dropdown">
						<span class="mr-2">{{ $user->nombre }} {{ $user->apellido }}</span>
						{{--<img src="{{ asset('images/profile.jpg') }}" alt="Avatar" class="profile-img">--}}
					</a>
					<div class="dropdown-menu dropdown-menu-right" aria-labelledby="userMenu">
						{{--<a class="dropdown-item" href="{{route('account.index')}}"><i class="la la-user mr-2"></i> Mi perfil</a>
						<a class="dropdown-item" href="{{route('vacant.index')}}"><i class="las la-briefcase mr-2"></i> Vacantes</a>
						<a class="dropdown-item" href="{{route('profile.edit', $user->id)}}"><i class="la la-cog mr-2"></i>Mi configuración</a>--}}
						<div class="dropdown-divider"></div>
						 <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
							@csrf
						</form>
						<a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="la la-sign-out-alt mr-2"></i> Cerrar sesión</a>
					</div>
				</div>
			</div>
		</nav>

		<div class="overlay-sidebar d-md-none"></div>

		<aside class="sidebar-card">
			<div class="card card-sidebar">
				 <div class="sidebar-logo text-center">
					<img src="{{asset('images/logo-app.png')}}" class="img-fluid mt-3" alt="AuthenticFarma Logo">
				</div>
				<div class="card-body">
					{{-- <ul class="nav flex-column nav-sidebar"> --}}
					<ul class="nav flex-column nav-sidebar d-flex flex-column h-100">


						
						<li class="nav-item">
							<a class="nav-link " href="{{route('admin.dashboard')}}">
								<span><i class="la la-tachometer mr-1"></i>Dashboard</span>
							</a>
						</li>
						
						<li class="nav-item">
							<a class="nav-link " href="{{route('candidates.index')}}">
								<span><i class="la la-users mr-1"></i>Candidatos</span>
							</a>
						</li>
					
						{{--<li class="nav-item">
							<a class="nav-link " href="">
								<span><i class="la la-building mr-1"></i>Empresas</span>
							</a>
						</li>
						<li class="nav-item">
							<a class="nav-link " href="">
								<span><i class="la la-user-tie mr-1"></i>Clientes</span>
							</a>
						</li>--}}
						<li class="nav-item">
							<a class="nav-link " href="{{route('admin.vacant.index')}}">
								<span><i class="la la-file-text mr-1"></i>Ofertas</span>
							</a>
						</li>
						<li class="nav-item">
							<a class="nav-link " href="{{route('plan.index')}}">
								<span><i class="la la-briefcase mr-1"></i>Planes</span>
							</a>
						</li>
						{{--<li class="nav-item">
							<a class="nav-link {{ request()->routeIs('postulation.index') ? 'active' : '' }}" href="{{ route('postulation.index') }}">
								<span><i class="la la-paper-plane mr-1"></i>Aplicaciones</span>
							</a>
						</li>--}}
						
						<li class="nav-item">
							<a class="nav-link" href="#" data-modal-target="modalSoporte">
								<span><i class="las la-headset mr-1"></i>Soporte</span>
							</a>
						</li>

						<li class="nav-item mt-auto">
							<small class="text-muted d-block text-center">
								© {{ date('Y') }} AuthenticFarma
							</small>
						</li>

					</ul>
				</div>
			</div>
		</aside>

	

		@include('support.modals.support')

		
		<main class="content-area">

			<div class="container-fluid d-flex flex-column {{ request()->routeIs('postulation.*') ? 'content-wrapper2' : 'content-wrapper' }}">

				<div class="flex-grow-1">
					@yield('content')
				</div>

				<footer class="card bg-light border-0 mt-4">
					<div class="card-body">
						<div class="container">
							<div class="row align-items-center text-center text-md-start">
								<div class="col-12 col-md-6 mb-2 mb-md-0">
									<small class="text-muted">
										&copy; 2025 AuthenticFarma. Todos los derechos reservados.
									</small>
								</div>
								<div class="col-12 col-md-6 text-md-end">
									<a href="{{ route('policies-data.index') }}" target="_blank" class="text-muted text-decoration-none small me-3">
										<i class="bi bi-shield-lock"></i> Política de Privacidad
									</a>
									<a href="{{ route('management-data.index') }}" target="_blank" class="text-muted text-decoration-none small">
										<i class="bi bi-gear"></i> Manejo de Datos
									</a>
								</div>
							</div>
						</div>
					</div>
				</footer>
			</div>
		</main>
	</div>

    @yield('javascript')
	<script>
		document.addEventListener('DOMContentLoaded', function () {
			const toggleBtn = document.querySelector('.btn-toggle-sidebar');
			const overlay = document.querySelector('.overlay-sidebar');

			toggleBtn?.addEventListener('click', function () {
				document.body.classList.toggle('sidebar-visible');
			});

			overlay?.addEventListener('click', function () {
				document.body.classList.remove('sidebar-visible');
			});

			document.querySelectorAll('.nav-sidebar .nav-link').forEach(link => {
				link.addEventListener('click', () => {
					if (window.innerWidth < 768) {
						document.body.classList.remove('sidebar-visible');
					}
				});
			});
		});
	</script>
	<script src="{{ asset('js/modal.js') }}"></script>
	<script src="{{ asset('js/jquery.min.js') }}"></script>
	<script src="{{ asset('js/modernizr.js') }}"></script>
	<script src="{{ asset('js/script.js') }}"></script>
	<script src="{{ asset('js/bootstrap.min.js') }}"></script>
	<script src="{{ asset('js/wow.min.js') }}"></script>
	<script src="{{ asset('js/slick.min.js') }}"></script>
	<script src="{{ asset('js/parallax.js') }}"></script>
	<script src="{{ asset('js/select-chosen.js') }}"></script>
	<script src="{{ asset('js/circle-progress.min.js') }}"></script>
	<script src="{{ asset('js/vacantes-admin.js') }}"></script>

    @stack('scripts')
</body>
</html>
