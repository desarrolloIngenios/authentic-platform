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
	<link rel="icon" href="https://www.authenticfarma.com/wp-content/uploads/2025/05/cropped-isotipo_color-scaled-1-192x192.png" sizes="192x192" />
	<link rel="apple-touch-icon" href="https://www.authenticfarma.com/wp-content/uploads/2025/05/cropped-isotipo_color-scaled-1-180x180.png" />
    <link rel="shortcut icon" type="image/png" href="https://www.authenticfarma.com/wp-content/uploads/2020/11/isotipo_color-e1742350424597-150x150.png" sizes="32x32" />

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
	
	<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
	<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    {{-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css" /> --}}
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/line-awesome/1.3.0/line-awesome/css/line-awesome.min.css">
	@yield('styles')
	<!-- Google Fonts: Quicksand -->
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;600;700&display=swap" rel="stylesheet">
	<style>
		body {
			background-color: #f4f6f8;
			margin: 0;
			padding: 0;
			height: 100%;
			overflow-x: hidden;
			font-family: 'Quicksand', sans-serif !important;
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
		
		/* FOOTER PROFESIONAL */
		.footer-professional {
			background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
			color: #ffffff;
			border-radius: 15px 15px 0 0;
			box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.08);
			overflow: hidden;
			position: relative;
		}

		.footer-professional::before {
			content: '';
			position: absolute;
			top: 0;
			left: 0;
			right: 0;
			height: 3px;
			background: linear-gradient(to right, #00a86b, #0057b8);
		}

		.footer-professional .card-body {
			padding: 25px 30px;
			background: transparent;
		}

		.footer-professional .container {
			position: relative;
			z-index: 2;
		}

		.footer-professional .row {
			align-items: center;
		}

		.footer-professional small {
			color: #6c757d;
			font-size: 14px;
			font-weight: 500;
		}

		.footer-professional a {
			color: #0057b8;
			text-decoration: none;
			font-weight: 500;
			transition: all 0.3s ease;
			padding: 5px 10px;
			border-radius: 5px;
			display: inline-flex;
			align-items: center;
			gap: 5px;
		}

		.footer-professional a:hover {
			color: #ffffff;
			background-color: #0057b8;
			transform: translateY(-2px);
		}

		.footer-professional a i {
			font-size: 16px;
			margin-right: 3px;
		}

		.footer-copyright-text {
			margin-bottom: 5px;
		}

		.footer-copyright-text strong {
			color: #00a86b;
			font-weight: 600;
		}

		.footer-links-container {
			display: flex;
			flex-wrap: wrap;
			gap: 15px;
			justify-content: flex-end;
		}

		@media (max-width: 768px) {
			.footer-professional .container {
				text-align: center !important;
			}
			
			.footer-links-container {
				justify-content: center;
				margin-top: 15px;
			}
			
			.footer-professional .text-md-end {
				text-align: center !important;
			}
		}
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
						@php
							$hoja_vida = \App\Models\HvHojaVida::where('id_usuario', Auth::id())->first();
							$foto_url = null;
							if ($hoja_vida && $hoja_vida->foto) {
								try {
									$storage = new \Google\Cloud\Storage\StorageClient([
										'keyFilePath' => base_path(env('GOOGLE_CLOUD_KEY_FILE'))
									]);
									$bucket = $storage->bucket(env('GOOGLE_CLOUD_STORAGE_BUCKET'));
									$object = $bucket->object('profile_images/' . $hoja_vida->foto);
									if ($object->exists()) {
										$foto_url = $object->signedUrl(
											new \DateTime('+1 hour'),
											[
												'version' => 'v4',
												'method' => 'GET',
												'responseDisposition' => 'inline',
												'responseType' => 'image/jpeg'
											]
										);
									}
								} catch (\Exception $e) {
									Log::error('Error al obtener URL de la foto: ' . $e->getMessage());
								}
							}
						@endphp
						<img src="{{ $foto_url ?? asset('images/profile.jpg') }}" alt="Avatar" class="profile-img">
					</a>
					<div class="dropdown-menu dropdown-menu-right" aria-labelledby="userMenu">
						<a class="dropdown-item" href="{{route('account.index')}}"><i class="la la-user mr-2"></i> Mi perfil</a>
						@if(session('candidato'))
							<a class="dropdown-item" href="{{route('vacant.index')}}"><i class="las la-briefcase mr-2"></i> Vacantes</a>
						@endif
						<a class="dropdown-item" href="{{route('profile.edit', $user->id)}}"><i class="la la-cog mr-2"></i>Mi configuración</a>
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


						@if(session('candidato'))
							<li class="nav-item">
								<a class="nav-link {{ request()->routeIs('dashboard.index') ? 'active' : '' }}" href="{{ route('dashboard.index') }}">
									<span><i class="la la-tachometer mr-2"></i>Mi tablero</span>
								</a>
							</li>
						@endif
						<li class="nav-item">
							<a class="nav-link {{ request()->routeIs('account.index') ? 'active' : '' }}" href="{{ route('account.index') }}">
								<span><i class="la la-user mr-1"></i>Mi cuenta</span>
							</a>
						</li>
						@if(session('candidato'))
							<li class="nav-item">
								<a class="nav-link {{ request()->routeIs('profile.index') ? 'active' : '' }}" href="{{ route('profile.index') }}">
									<span><i class="la la-id-card mr-1"></i>Perfil</span>
								</a>
							</li>
						
							<li class="nav-item">
								<a class="nav-link {{ request()->routeIs('educacion.index') ? 'active' : '' }}" href="{{ route('educacion.index') }}">
									<span><i class="la la-graduation-cap mr-1"></i>Educación</span>
								</a>
							</li>
							<li class="nav-item">
								<a class="nav-link {{ request()->routeIs('job.index') ? 'active' : '' }}" href="{{ route('job.index') }}">
									<span><i class="la la-briefcase mr-1"></i>Experiencia</span>
								</a>
							</li>
							<li class="nav-item">
								<a class="nav-link {{ request()->routeIs('vacant.index') ? 'active' : '' }}" href="{{ route('vacant.index') }}">
									<span><i class="la la-file-text mr-1"></i>Ofertas</span>
								</a>
							</li>
							<li class="nav-item">
								<a class="nav-link {{ request()->routeIs('postulation.index') ? 'active' : '' }}" href="{{ route('postulation.index') }}">
									<span><i class="la la-paper-plane mr-1"></i>Aplicaciones</span>
								</a>
							</li>
						@endif
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

				<footer class="footer-professional border-0 mt-4">
					<div class="card-body">
						<div class="container">
							<div class="row align-items-center text-center text-md-start">
								<div class="col-12 col-md-6 mb-2 mb-md-0">
									<div class="footer-copyright-text">
										<small>
											&copy; 2025 <strong>AuthenticFarma</strong>. Todos los derechos reservados.
										</small>
									</div>
								</div>
								<div class="col-12 col-md-6 text-md-end">
									<div class="footer-links-container">
										<a href="{{ route('policies-data.index') }}" target="_blank" class="small">
											<i class="la la-shield-alt"></i> Política de Privacidad
										</a>
										<a href="{{ route('management-data.index') }}" target="_blank" class="small">
											<i class="la la-cog"></i> Manejo de Datos
										</a>
									</div>
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
	<script src="{{ asset('js/modal.js') }}"></script>
	<script src="{{ asset('js/vacantes.js') }}"></script>
	<script src="{{ asset('js/script.js') }}"></script>
	<script src="{{ asset('js/bootstrap.min.js') }}"></script>
	<script src="{{ asset('js/wow.min.js') }}"></script>
	<script src="{{ asset('js/slick.min.js') }}"></script>
	<script src="{{ asset('js/parallax.js') }}"></script>
	<script src="{{ asset('js/select-chosen.js') }}"></script>
	<script src="{{ asset('js/circle-progress.min.js') }}"></script>


    @stack('scripts')
</body>
</html>
