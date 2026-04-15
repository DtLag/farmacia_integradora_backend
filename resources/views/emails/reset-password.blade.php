<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        /* Mismos estilos que el anterior para mantener consistencia */
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f7fa; margin: 0; padding: 0; }
        .email-wrapper { width: 100%; background-color: #f4f7fa; padding: 40px 0; }
        .email-content { max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .header { background-color: #2563eb; padding: 30px 20px; text-align: center; color: #ffffff; }
        .header h1 { margin: 0; font-size: 24px; font-weight: 600; letter-spacing: 1px; }
        .body-section { padding: 40px 30px; color: #334155; line-height: 1.6; text-align: center; }
        .body-section h2 { margin-top: 0; font-size: 20px; color: #1e293b; }
        .code-container { margin: 30px 0; }
        .code { display: inline-block; font-size: 36px; font-weight: bold; letter-spacing: 8px; color: #2563eb; background-color: #eff6ff; padding: 15px 30px; border-radius: 8px; border: 1px dashed #bfdbfe; }
        .warning { font-size: 14px; color: #64748b; margin-top: 20px; }
        .footer { background-color: #f8fafc; padding: 20px; text-align: center; font-size: 13px; color: #94a3b8; border-top: 1px solid #e2e8f0; }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-content">
            <div class="header">
                <h1>Farmacia Integradora</h1>
            </div>
            <div class="body-section">
                <h2>Recuperación de Contraseña</h2>
                <p>Hemos recibido una solicitud para restablecer la contraseña de tu cuenta. Ingresa el siguiente código en la aplicación para continuar:</p>
                
                <div class="code-container">
                    <span class="code">{{ $code }}</span>
                </div>
                
                <p class="warning">Este código <strong>expira en 10 minutos</strong>. Si tú no realizaste esta solicitud, tu cuenta está segura y puedes ignorar este correo.</p>
            </div>
            <div class="footer">
                &copy; {{ date('Y') }} Farmacia Integradora. Todos los derechos reservados.
            </div>
        </div>
    </div>
</body>
</html>