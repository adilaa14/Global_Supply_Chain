<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Global Supply Chain Risk Intelligence Platform</title>
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Material Symbols -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,300,0,0" />
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <style>
        .login-wrapper {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        .login-card {
            background: var(--glass-bg);
            backdrop-filter: blur(25px);
            -webkit-backdrop-filter: blur(25px);
            border-radius: 40px;
            padding: 50px;
            width: 100%;
            max-width: 450px;
            box-shadow: var(--bubble-shadow-hover);
            border: 1px solid var(--glass-border);
            z-index: 10;
        }

        .login-logo {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-logo .material-symbols-outlined {
            font-size: 52px;
            color: var(--primary);
            margin-bottom: 15px;
        }

        .login-logo h4 {
            font-weight: 700;
            color: var(--secondary);
            margin-bottom: 5px;
            letter-spacing: -0.5px;
        }

        .login-logo p {
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .form-control-glass {
            background: rgba(255, 255, 255, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 14px 20px;
            font-size: 0.95rem;
            color: var(--secondary);
            transition: all 0.3s ease;
        }

        .form-control-glass:focus {
            background: rgba(255, 255, 255, 0.7);
            border-color: var(--primary);
            box-shadow: 0 0 15px rgba(240, 49, 100, 0.2);
            outline: none;
        }

        .btn-login {
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 25px;
            padding: 14px;
            font-weight: 600;
            font-size: 1rem;
            width: 100%;
            transition: all 0.3s ease;
            box-shadow: 0 8px 20px rgba(240, 49, 100, 0.3);
            margin-top: 10px;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(240, 49, 100, 0.4);
        }

        .floating-shape {
            position: absolute;
            background: linear-gradient(135deg, rgba(240, 49, 100, 0.2), rgba(93, 174, 255, 0.2));
            border-radius: 50%;
            filter: blur(40px);
            z-index: 1;
            animation: float 8s infinite alternate ease-in-out;
        }

        .shape-1 { width: 300px; height: 300px; top: 10%; left: 15%; }
        .shape-2 { width: 400px; height: 400px; bottom: 5%; right: 10%; animation-delay: -3s; }

        @keyframes float {
            0% { transform: translateY(0) scale(1); }
            100% { transform: translateY(-40px) scale(1.05); }
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="floating-shape shape-1"></div>
        <div class="floating-shape shape-2"></div>
        
        @yield('content')
        
    </div>
</body>
</html>
