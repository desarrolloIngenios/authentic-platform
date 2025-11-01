<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Authentic - @yield('title', 'Error')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="keywords" content="">
    
    <link rel="icon" href="{{ asset("logo-app.ico") }}">
	<link rel="icon" href="https://www.authenticfarma.com/wp-content/uploads/2025/05/cropped-isotipo_color-scaled-1-32x32.png" sizes="32x32" />
	<link rel="icon" href="https://www.authenticfarma.com/wp-content/uploads/2025/05/cropped-isotipo_color-scaled-1-192x192.png" sizes="192x192" />
	<link rel="apple-touch-icon" href="https://www.authenticfarma.com/wp-content/uploads/2025/05/cropped-isotipo_color-scaled-1-180x180.png" />
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/line-awesome/1.3.0/line-awesome/css/line-awesome.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;600;700&display=swap" rel="stylesheet">    <style>
        :root {
            --primary-green: #00a86b;
            --primary-blue: #0057b8;
            --dark-text: #2D3748;
            --light-text: #718096;
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.1);
            --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.1);
            --shadow-lg: 0 10px 15px -3px rgba(0,0,0,0.1);
            --gradient: linear-gradient(-45deg, rgba(0,168,107,0.05) 0%, rgba(0,87,184,0.05) 100%);
        }

        ::selection {
            background: var(--primary-green);
            color: white;
        }

        body {
            background: var(--gradient);
            font-family: 'Quicksand', sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            color: var(--dark-text);
        }

        .header {
            background: white;
            padding: 1rem 0;
            box-shadow: var(--shadow-sm);
            position: relative;
        }

        .header::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(to right, var(--primary-green), var(--primary-blue));
            opacity: 0.8;
        }

        .logo-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        .logo-container img {
            height: 45px;
            transition: transform 0.2s ease;
        }

        .logo-container img:hover {
            transform: scale(1.05);
        }

        .main-content {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }        .error-card {
            background: white;
            border-radius: 24px;
            box-shadow: var(--shadow-lg);
            border: 1px solid rgba(0,0,0,0.05);
            overflow: hidden;
            width: 100%;
            max-width: 500px;
            transition: all 0.3s ease;
            position: relative;
        }

        .error-card::before {
            content: '';
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            background: linear-gradient(45deg, var(--primary-green), var(--primary-blue));
            z-index: -1;
            border-radius: 26px;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .error-card:hover {
            transform: translateY(-5px) scale(1.02);
        }

        .error-card:hover::before {
            opacity: 1;
        }

        .error-header {
            background: linear-gradient(135deg, var(--primary-green), var(--primary-blue));
            padding: 2.5rem 2rem;
            color: white;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .error-header::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(255,255,255,0.1), transparent);
            pointer-events: none;
        }

        .error-code {
            font-size: 5rem;
            font-weight: 700;
            line-height: 1;
            margin-bottom: 1rem;
            background: rgba(255,255,255,0.15);
            border-radius: 16px;
            padding: 1rem 2rem;
            display: inline-block;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
            position: relative;
            overflow: hidden;
        }

        .error-code::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, rgba(255,255,255,0), rgba(255,255,255,0.1), rgba(255,255,255,0));
            transform: rotate(45deg);
            animation: shimmer 3s infinite;
        }

        @keyframes shimmer {
            0% { transform: translateX(-100%) rotate(45deg); }
            100% { transform: translateX(100%) rotate(45deg); }
        }

        .error-title {
            font-weight: 600;
            margin: 0;
            opacity: 0.95;
            font-size: 1.5rem;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
        }

        .error-body {
            padding: 2.5rem 2rem;
            text-align: center;
            background: linear-gradient(180deg, white, var(--light-bg));
        }

        .error-message {
            color: var(--dark-text);
            font-size: 1.1rem;
            margin-bottom: 2.5rem;
            line-height: 1.6;
            opacity: 0.9;
        }        .btn-custom {
            padding: 1rem 2.5rem;
            font-weight: 600;
            text-decoration: none;
            border-radius: 12px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-custom i {
            font-size: 1.2em;
            transition: transform 0.3s ease;
        }

        .btn-custom:hover i {
            transform: translateX(3px);
        }

        .btn-primary-gradient {
            background: linear-gradient(135deg, var(--primary-green), var(--primary-blue));
            color: white;
            border: none;
            box-shadow: var(--shadow-md);
        }

        .btn-primary-gradient::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, var(--primary-green), var(--primary-blue));
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .btn-primary-gradient:hover {
            color: white;
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg), 0 8px 20px rgba(0,87,184,0.2);
        }

        .btn-primary-gradient:active {
            transform: translateY(1px);
        }

        .btn-outline {
            border: 2px solid var(--divider);
            color: var(--light-text);
            background: white;
        }

        .btn-outline:hover {
            border-color: var(--primary-blue);
            color: var(--primary-blue);
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .footer {
            text-align: center;
            padding: 2.5rem;
            color: var(--light-text);
            font-size: 0.9rem;
            background: white;
            box-shadow: var(--shadow-sm);
            position: relative;
        }

        .footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 20%;
            right: 20%;
            height: 1px;
            background: linear-gradient(to right, 
                transparent, 
                var(--divider),
                transparent
            );
        }        @media (max-width: 768px) {
            .error-card {
                margin: 1rem;
                border-radius: 20px;
            }

            .error-card::before {
                border-radius: 22px;
            }

            .error-code {
                font-size: 4rem;
                padding: 0.8rem 1.5rem;
            }
            
            .error-header {
                padding: 2rem 1.5rem;
            }

            .error-title {
                font-size: 1.25rem;
            }
            
            .error-body {
                padding: 2rem 1.5rem;
            }

            .error-message {
                font-size: 1rem;
                margin-bottom: 2rem;
            }

            .btn-custom {
                width: 100%;
                margin-bottom: 1rem;
            }
        }

        @media (max-width: 480px) {
            .error-code {
                font-size: 3rem;
                padding: 0.6rem 1.2rem;
            }

            .error-header {
                padding: 1.5rem 1rem;
            }

            .error-body {
                padding: 1.5rem 1rem;
            }

            .btn-custom {
                padding: 0.8rem 1.5rem;
                font-size: 0.95rem;
            }

            .footer {
                padding: 1.5rem 1rem;
                font-size: 0.85rem;
            }
        }
    </style>
</head>
<body>
    <main class="main-content">
        @yield('content')
    </main>

    <footer class="footer">
        <p>© {{ date('Y') }} Authentic Farma. Todos los derechos reservados.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
