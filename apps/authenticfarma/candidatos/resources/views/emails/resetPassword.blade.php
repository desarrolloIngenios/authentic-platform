<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer Contraseña</title>
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
            font-size: 24px;
            font-weight: 600;
            margin-top: 10px;
        }
        
        .content {
            padding: 40px 30px;
            text-align: center;
        }
        
        .icon {
            font-size: 64px;
            margin-bottom: 20px;
            color: #0057b8;
        }
        
        .title {
            font-size: 24px;
            color: #333;
            margin-bottom: 15px;
            font-weight: 600;
        }
        
        .message {
            color: #666;
            font-size: 16px;
            line-height: 1.7;
            margin-bottom: 30px;
            text-align: left;
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
        
        .security-note {
            background: #fff3cd;
            border: 1px solid #ffecb5;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
            color: #856404;
            font-size: 14px;
        }
        
        .expiry-info {
            font-size: 14px;
            color: #999;
            margin-top: 15px;
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
            
            .header-title {
                font-size: 20px;
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
            <div class="header-title">Solicitud de Restablecimiento</div>
        </div>
        
        <!-- Content -->
        <div class="content">
            <div class="icon">🔐</div>
            
            <div class="title">Restablecer tu contraseña</div>
              <div class="message">
                Hola {{ $user->nombre }},<br><br>
                
                Recibimos una solicitud para restablecer la contraseña de tu cuenta. Si no realizaste esta solicitud, puedes ignorar este correo de forma segura.
                <br><br>
                
                Para restablecer tu contraseña, haz clic en el botón de abajo:
            </div>
            
            <a href="{{ $resetUrl }}" class="cta-button">Restablecer Contraseña</a>
            
            <div class="security-note">
                <strong>⚠️ Nota de seguridad:</strong> Este enlace es único y solo funciona una vez. Por tu seguridad, nunca compartas este enlace con otras personas.
            </div>
            
            <div class="expiry-info">
                Este enlace expirará en 24 horas por motivos de seguridad.
            </div>
              <div class="message">
                Si tienes problemas para hacer clic en el botón, copia y pega el siguiente enlace en tu navegador:<br>
                <small style="color: #666; word-break: break-all;" class="text-white">{{ $resetUrl }}</small>
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
                
                <div class="divider"></div>
                
                <div class="disclaimer">
                    Este es un correo automático, por favor no respondas a este mensaje.<br>
                    Si no solicitaste este restablecimiento, contacta a nuestro equipo de soporte.
                </div>
            </div>
        </div>
    </div>
</body>
</html>