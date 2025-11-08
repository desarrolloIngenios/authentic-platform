<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Authentic - @yield('title', 'Farma')</title>

    
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="">	<meta name="keywords" content="">
	<meta name="author" content="CreativeLayers">
<link rel="icon" href="{{ asset("logo-app.ico") }}">
	<link rel="icon" href="https://www.authenticfarma.com/wp-content/uploads/2025/05/cropped-isotipo_color-scaled-1-32x32.png" sizes="32x32" />
	<link rel="icon" href="https://www.authenticfarma.com/wp-content/uploads/2025/05/cropped-isotipo_color-scaled-1-192x192.png" sizes="192x192" />
	<link rel="apple-touch-icon" href="https://www.authenticfarma.com/wp-content/uploads/2025/05/cropped-isotipo_color-scaled-1-180x180.png" />
	<!-- Bootstrap 4.5 CSS -->
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
	<!-- Local Styles -->
	<link rel="stylesheet" type="text/css" href="css/bootstrap.css" />
	<link rel="stylesheet" type="text/css" href="css/bootstrap-grid.css" />
	<link rel="stylesheet" href="css/icons.css">
	<link rel="stylesheet" href="css/animate.min.css">
	<link rel="stylesheet" type="text/css" href="css/style.css" />
	<link rel="stylesheet" type="text/css" href="css/responsive.css" />
	<link rel="stylesheet" type="text/css" href="css/chosen.css" />
	<link rel="stylesheet" type="text/css" href="css/colors/colors.css" />
	<link rel="stylesheet" type="text/css" href="css/modal.css" />
	<link rel="stylesheet" type="text/css" href="css/forms.css" />
	<link rel="stylesheet" type="text/css" href="css/vacants.css" />
	<link rel="stylesheet" type="text/css" href="css/vacants2.css" />
	<link rel="stylesheet" type="text/css" href="css/drawer.css" />
	{{-- <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    {{-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css" 
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/line-awesome/1.3.0/line-awesome/css/line-awesome.min.css"> --}}
	@yield('styles')
	<style>
		/* body {
			background-color: #f4f6f8;
			
			margin: 0;
			padding: 0;
			height: 100%;
			overflow-x: hidden;
			font-family: 'Quicksand', serif !important;
		} */
		 body {
            background: url('./images/fondo.png') no-repeat center center fixed;
            background-size: cover;
            position: relative;
            font-family: 'Quicksand', serif !important;
        }

        body::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.65);
            z-index: 0;
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
			margin-left: 0px;
			padding: 30px;
			padding-top: 40px;
			margin-top: 40px;
			min-height: 95vh;
			display: flex;
			flex-direction: column;
			transition: margin-left 0.3s ease;
		}

		.content-wrapper {
			/* background-color: #ffffff; */
			padding: 20px 30px;
			border-radius: 12px;
			/* box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); */
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
		<main class="content-area">
			<div class="d-flex flex-column">

				<div class="flex-grow-1">
					@yield('content')
				</div>

				
			</div>
		</main>
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
							</a> /
							<a href="{{ route('management-data.index') }}" target="_blank" class="text-muted text-decoration-none small">
								<i class="bi bi-gear"></i> Manejo de Datos
							</a>
						</div>
					</div>
				</div>
			</div>
		</footer>
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
	</script>    <!-- jQuery first, then Bootstrap, then other scripts -->
    <script src="js/jquery.min.js" type="text/javascript"></script>
    <script src="js/bootstrap.min.js" type="text/javascript"></script>
    <script src="js/modernizr.js" type="text/javascript"></script>
    <script src="js/modal.js" type="text/javascript"></script>
	<script src="js/vacantes.js" type="text/javascript"></script>
    <script src="js/script.js" type="text/javascript"></script>
    <script src="js/wow.min.js" type="text/javascript"></script>
    <script src="js/slick.min.js" type="text/javascript"></script>
    <script src="js/parallax.js" type="text/javascript"></script>
    <script src="js/select-chosen.js" type="text/javascript"></script>
    <script src="js/jquery.scrollbar.min.js" type="text/javascript"></script>
    <script src="js/circle-progress.min.js" type="text/javascript"></script>

</body>
</html>
