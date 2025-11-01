<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenida a la Plataforma</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            background-color: #f5f5f5;
            margin: 0;
            padding: 20px;
        }
        
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }
        
        .header {
            background: linear-gradient(135deg, #0057b8 0%, #00a86b 100%);
            padding: 30px;
            text-align: center;
            color: white;
        }
        
        .logo-container {
            margin-bottom: 15px;
        }
        
        .logo {
            max-width: 150px;
            height: auto;
            background: rgba(255, 255, 255, 0.2);
            padding: 10px;
            border-radius: 8px;
            color: white;
            font-size: 24px;
            font-weight: bold;
            display: inline-block;
        }
        
        .header-title {
            font-size: 28px;
            font-weight: 600;
            margin-top: 10px;
        }
        
        .content {
            padding: 40px 30px;
        }
        
        .welcome-icon {
            text-align: center;
            font-size: 64px;
            margin-bottom: 20px;
            color: #0057b8;
        }
        
        .title {
            font-size: 24px;
            color: #333;
            margin-bottom: 15px;
            font-weight: 600;
            text-align: center;
        }
        
        .message {
            color: #666;
            font-size: 16px;
            line-height: 1.7;
            margin-bottom: 25px;
        }
        
        .features-section {
            margin: 30px 0;
        }
        
        .features-title {
            font-size: 20px;
            color: #333;
            margin-bottom: 20px;
            font-weight: 600;
            text-align: center;
        }
        
        .features {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            margin: 25px 0;
        }
        
        .feature {
            flex: 1;
            text-align: center;
            padding: 20px 15px;
            background: linear-gradient(135deg, #e6f1ff 0%, #e6f4f1 100%);
            border-radius: 12px;
            border: 1px solid #b3d9ff;
        }
        
        .feature-icon {
            font-size: 32px;
            margin-bottom: 10px;
        }
        
        .feature-title {
            font-size: 14px;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }
        
        .feature-desc {
            font-size: 12px;
            color: #666;
            line-height: 1.4;
        }
        
        .cta-section {
            text-align: center;
            margin: 35px 0;
            padding: 25px;
            background: linear-gradient(135deg, #e6f1ff 0%, #e6f4f1 100%);
            border-radius: 12px;
            border: 1px solid #b3d9ff;
        }
        
        .cta-title {
            font-size: 18px;
            color: #333;
            margin-bottom: 15px;
            font-weight: 600;
        }
        
        .cta-button {
             display: inline-block;
            background: linear-gradient(135deg, #0057b8 0%, #00a86b 100%);
            color: white !important;
            padding: 15px 40px;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 600;
            font-size: 16px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 87, 184, 0.3);
            margin-bottom: 20px;
        }
        
        .cta-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 87, 184, 0.4);
        }
        
        .tips-section {
            background: #fff8dc;
            border: 1px solid #f0e68c;
            border-radius: 12px;
            padding: 20px;
            margin: 25px 0;
        }
        
        .tips-title {
            font-size: 16px;
            font-weight: 600;
            color: #b8860b;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }
        
        .tip {
            margin-bottom: 8px;
            color: #8b7355;
            font-size: 14px;
        }
        
        .support-section {
            text-align: center;
            margin: 30px 0;
            padding: 20px;
            background: #f9f9f9;
            border-radius: 8px;
        }
        
        .support-title {
            font-size: 16px;
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
        }
        
        .support-text {
            font-size: 14px;
            color: #666;
        }
        
        .footer {
            background-color: #f8f9fa;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e9ecef;
        }
        
        .footer-content {
            color: #666;
            font-size: 14px;
            line-height: 1.6;
        }
        
        .company-name {
            font-weight: 600;
            color: #333;
        }
        
        .contact-info {
            margin: 15px 0;
        }
        
        .social-links {
            margin: 20px 0;
        }
        
        .social-links a {
            display: inline-block;
            margin: 0 8px;
            padding: 8px;
            background: #0057b8;
            color: white;
            border-radius: 50%;
            text-decoration: none;
            width: 36px;
            height: 36px;
            line-height: 20px;
            transition: background 0.3s ease;
        }
        
        .social-links a:hover {
            background: #00a86b;
        }
        
        .divider {
            height: 1px;
            background: #ddd;
            margin: 20px 0;
        }
        
        .disclaimer {
            font-size: 12px;
            color: #999;
            margin-top: 20px;
        }
        
        @media only screen and (max-width: 600px) {
            body {
                padding: 10px;
            }
            
            .header, .content, .footer {
                padding: 20px;
            }
            
            .features {
                flex-direction: column;
                gap: 15px;
            }
            
            .header-title {
                font-size: 24px;
            }
            
            .title {
                font-size: 20px;
            }
            
            .message {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <div class="logo-container">
                <!-- Reemplaza con tu logo -->
                <div class=""><img width="150" src="https://empresa.authenticfarma.com/images/logo-authenticfarma-white.png" class="img-fluid" alt=""></div>
            </div>
            <div class="header-title">¡Bienvenido!</div>
        </div>
        
        <!-- Content -->
        <div class="content">
            <div class="welcome-icon">🎉</div>
            
            <div class="title">Tu cuenta está lista</div>
            
            <div class="message">
                ¡Hola {{ $user->nombre }}!<br><br>
                
                ¡Felicidades! Tu cuenta ha sido activada exitosamente y ya formas parte de nuestra comunidad. Estamos emocionados de tenerte con nosotros y queremos asegurarnos de que aproveches al máximo todas las herramientas y funcionalidades que tenemos para ofrecerte.
            </div>
            
            
            <div class="cta-section">
                <div class="cta-title">¿Listo para comenzar?</div>
                <a href="{{route('login.index')}}" class="cta-button text-white">Acceder a Mi Cuenta</a>
            </div>
            
            <div class="tips-section">
                <div class="tips-title">💡 Consejos para empezar:</div>
                <div class="tip">• Completa tu perfil para una experiencia personalizada</div>
                <div class="tip">• Conecta con nuestro equipo si tienes preguntas</div>
            </div>
            
            <div class="message">
                Recuerda que nuestro equipo de soporte está siempre disponible para ayudarte. No dudes en contactarnos si tienes alguna pregunta o necesitas asistencia.
            </div>
            
            <div class="message" style="text-align: center; margin-top: 30px;">
                <strong>¡Gracias por confiar en nosotros!</strong><br>
                El Equipo de AuthenticFarma
            </div>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <div class="footer-content">
                <div class="company-name">AuthenticFarma</div>
                 <div class="contact-info">
                    Dirección: Cra. 11 #140 – 52 Oficina 315, Bogotá, Colombia<br>
                    Email: consultor@authentic.com.co | Tel: +57 3334002303
                </div>
                
                <div class="social-links">
                    <a href="mailto:consultor@authentic.com.co">📧</a>
                    <a href="https://www.linkedin.com/company/authenticfarma/posts/?feedView=all">📘</a>
                    <a href="https://www.authenticfarma.com/">🌐</a>
                    <a href="tel:+573334002303">📞</a>
                </div>
                
                <div class="divider"></div>
                
                <div class="disclaimer">
                    Has recibido este correo porque te registraste en nuestra plataforma.<br>
                    <a href="#" style="color: #0057b8;">Configurar preferencias</a> | <a href="#" style="color: #0057b8;">Centro de ayuda</a>
                </div>
            </div>
        </div>
    </div>
</body>