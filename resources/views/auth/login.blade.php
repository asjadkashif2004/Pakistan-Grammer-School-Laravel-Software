<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Pakistan Grammar School') }} | Login</title>
    <link rel="icon" type="image/png" href="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT4INo84NyYsKwGYOhW1pZL-6hH4r76HLaBcA&s">
    <link rel="apple-touch-icon" href="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT4INo84NyYsKwGYOhW1pZL-6hH4r76HLaBcA&s">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --sidebar-bg: #0b5a2a;
            --sidebar-dark: #064620;
            --primary: #0f7a35;
            --primary-light: #17a34a;
            --ink: #1d2d1f;
            --muted: #6f8570;
            --line: #d4ead4;
            --accent-soft: rgba(15, 122, 53, 0.18);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            font-family: 'Figtree', system-ui, sans-serif;
            background:
                radial-gradient(ellipse 120% 80% at 50% -20%, rgba(15, 122, 53, 0.12), transparent 50%),
                linear-gradient(165deg, var(--sidebar-dark) 0%, var(--sidebar-bg) 45%, #0d4d24 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
        }

        .login-card {
            width: 100%;
            max-width: 440px;
            background: #ffffff;
            border-radius: 28px;
            overflow: hidden;
            border: 1px solid var(--line);
            box-shadow: 
                0 30px 60px -20px rgba(6, 70, 32, 0.48),
                0 0 0 1px rgba(255, 255, 255, 0.08) inset;
        }

        .card-header {
            position: relative;
            background: linear-gradient(120deg, var(--sidebar-dark), var(--sidebar-bg));
            padding: 28px 20px 68px;
            text-align: center;
        }

        .card-header::after {
            content: '';
            position: absolute;
            left: -15%;
            right: -15%;
            bottom: -42px;
            height: 85px;
            background: #ffffff;
            border-radius: 50% 50% 0 0 / 100% 100% 0 0;
        }

        .logo-wrap img {
            height: 72px;
            width: auto;
            margin: 0 auto;
            filter: drop-shadow(0 5px 15px rgba(0, 0, 0, 0.3));
        }

        .logo-wrap p {
            margin-top: 14px;
            color: #c5f5d3;
            font-size: 13.5px;
            letter-spacing: 2px;
            text-transform: uppercase;
            font-weight: 700;
        }

        .form-area {
            padding: 40px 32px 36px;
        }

        /* Welcome Back - Made more prominent & mobile-friendly */
        .form-title {
            margin-bottom: 10px;
            text-align: center;
            font-size: 29px;
            font-weight: 700;
            color: var(--ink);
            line-height: 1.05;
            letter-spacing: -0.6px;
        }

        .form-subtitle {
            text-align: center;
            margin-bottom: 32px;
            font-size: 13.8px;
            letter-spacing: 1.4px;
            text-transform: uppercase;
            color: var(--muted);
            font-weight: 600;
        }

        .field-group {
            margin-bottom: 20px;
        }

        .field-label {
            display: block;
            margin-bottom: 9px;
            font-size: 12.8px;
            letter-spacing: 1.4px;
            text-transform: uppercase;
            color: #12703a;
            font-weight: 700;
        }

        /* Sharp black text in inputs */
        .field-input {
            width: 100%;
            border: 1.5px solid var(--line);
            border-radius: 12px;
            padding: 15px 16px;
            font-size: 16.5px;
            font-weight: 500;
            color: #1a1a1a;           /* Sharp black */
            background: #fafdfb;
            transition: all 0.25s ease;
        }

        .field-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px var(--accent-soft);
            background: #ffffff;
        }

        .field-input::placeholder {
            color: #9ca9a0;
        }

        .helper-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: 10px 0 26px;
        }

        .remember {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            font-size: 15px;
            color: #47624a;
            font-weight: 600;
        }

        .remember input {
            width: 18px;
            height: 18px;
            accent-color: var(--primary);
        }

        .submit-btn {
            width: 100%;
            border: none;
            border-radius: 14px;
            padding: 15px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            color: #ffffff;
            background: linear-gradient(90deg, var(--sidebar-dark), var(--primary), var(--primary-light));
            box-shadow: 0 14px 28px -12px rgba(6, 70, 32, 0.55);
            transition: all 0.25s ease;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 36px -14px rgba(6, 70, 32, 0.65);
        }

        .footer-note {
            margin-top: 28px;
            text-align: center;
            font-size: 12.8px;
            color: var(--muted);
        }

        .footer-note a {
            color: var(--primary);
            font-weight: 600;
            text-decoration: none;
        }

        /* Mobile Optimization */
        @media (max-width: 480px) {
            body {
                padding: 12px;
            }
            
            .login-card {
                border-radius: 24px;
            }
            
            .card-header {
                padding: 24px 20px 62px;
            }
            
            .form-area {
                padding: 32px 26px 32px;
            }
            
            .form-title {
                font-size: 26.5px;
            }
            
            .form-subtitle {
                font-size: 13px;
                margin-bottom: 28px;
            }
            
            .field-input {
                padding: 14px 15px;
                font-size: 16px;
            }
            
            .submit-btn {
                padding: 14px;
                font-size: 15.5px;
            }
        }
    </style>
</head>
<body>
    <div class="login-card">
        <header class="card-header">
            <div class="logo-wrap">
                <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT4INo84NyYsKwGYOhW1pZL-6hH4r76HLaBcA&s" alt="Pakistan Grammar School logo">
                <p>PAKISTAN GRAMMAR SCHOOL</p>
            </div>
        </header>

        <section class="form-area">
            <h2 class="form-title">Welcome Back</h2>
            <p class="form-subtitle">SIGN IN TO YOUR ACCOUNT</p>

            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="field-group">
                    <label for="email" class="field-label">EMAIL</label>
                    <x-text-input
                        id="email"
                        class="field-input"
                        type="email"
                        name="email"
                        :value="old('email')"
                        required
                        autofocus
                        autocomplete="username"
                        placeholder="you@example.com" />
                    <x-input-error :messages="$errors->get('email')" class="mt-1.5 text-red-500 text-sm" />
                </div>

                <div class="field-group">
                    <label for="password" class="field-label">PASSWORD</label>
                    <x-text-input
                        id="password"
                        class="field-input"
                        type="password"
                        name="password"
                        required
                        autocomplete="current-password"
                        placeholder="Enter your password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-1.5 text-red-500 text-sm" />
                </div>

                <div class="helper-row">
                    <label for="remember_me" class="remember">
                        <input id="remember_me" type="checkbox" name="remember">
                        <span>Remember me</span>
                    </label>
                </div>

                <button type="submit" class="submit-btn">Sign In</button>
            </form>

            <p class="footer-note">
                Software developed by <a href="https://addsmint.com" target="_blank">AddsMint.com</a>
            </p>
        </section>
    </div>
</body>
</html>
