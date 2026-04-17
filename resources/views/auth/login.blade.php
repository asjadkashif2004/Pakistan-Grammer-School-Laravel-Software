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
        /* Aligned with dashboard / layouts.school ERP theme */
        :root {
            --sidebar-bg: #0b5a2a;
            --sidebar-dark: #064620;
            --primary: #0f7a35;
            --primary-light: #17a34a;
            --shell-bg: #f4faf3;
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
            padding: 24px;
        }

        .login-card {
            width: 100%;
            max-width: 430px;
            background: #ffffff;
            border-radius: 24px;
            overflow: hidden;
            border: 1px solid var(--line);
            box-shadow:
                0 28px 50px -20px rgba(6, 70, 32, 0.45),
                0 0 0 1px rgba(255, 255, 255, 0.06) inset;
        }

        .card-header {
            position: relative;
            background: linear-gradient(120deg, var(--sidebar-dark), var(--sidebar-bg));
            padding: 26px 20px 52px;
            text-align: center;
        }

        .card-header::after {
            content: '';
            position: absolute;
            left: -12%;
            right: -12%;
            bottom: -36px;
            height: 76px;
            background: #ffffff;
            border-radius: 50% 50% 0 0 / 100% 100% 0 0;
            pointer-events: none;
        }

        .logo-wrap {
            position: relative;
            z-index: 1;
        }

        .logo-wrap img {
            height: 64px;
            width: auto;
            margin: 0 auto;
            filter: drop-shadow(0 4px 12px rgba(0, 0, 0, 0.25));
        }

        .logo-wrap p {
            margin-top: 10px;
            color: #c5f5d3;
            font-size: 12px;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            font-weight: 600;
        }

        .form-area {
            padding: 26px 34px 30px;
        }

        .form-title {
            margin-bottom: 18px;
            text-align: center;
            font-size: 26px;
            font-weight: 700;
            color: var(--ink);
        }

        .form-subtitle {
            text-align: center;
            margin-bottom: 24px;
            font-size: 12.5px;
            letter-spacing: 1.1px;
            text-transform: uppercase;
            color: var(--muted);
            font-weight: 700;
        }

        .field-group {
            margin-bottom: 16px;
        }

        .field-label {
            display: block;
            margin-bottom: 8px;
            font-size: 12px;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            color: #12703a;
            font-weight: 700;
        }

        .field-input {
            width: 100%;
            border: 1px solid var(--line);
            border-radius: 11px;
            padding: 12px 14px;
            font-size: 15px;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
            background: #fafdfb;
        }

        .field-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px var(--accent-soft);
        }

        .helper-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin: 4px 0 18px;
            flex-wrap: wrap;
        }

        .remember {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: #47624a;
            font-weight: 600;
        }

        .remember input {
            width: 16px;
            height: 16px;
            accent-color: var(--primary);
        }

        .helper-link {
            color: var(--primary);
            font-size: 12.5px;
            text-decoration: none;
            font-weight: 600;
        }

        .helper-link:hover {
            text-decoration: underline;
        }

        .submit-btn {
            width: 100%;
            border: none;
            border-radius: 12px;
            padding: 12px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            color: #ffffff;
            background: linear-gradient(90deg, var(--sidebar-dark), var(--primary), var(--primary-light));
            box-shadow: 0 12px 24px -12px rgba(6, 70, 32, 0.55);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .submit-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 16px 32px -14px rgba(6, 70, 32, 0.6);
        }

        .register-link {
            margin-top: 12px;
            text-align: center;
            font-size: 12px;
            color: var(--muted);
        }

        .register-link a {
            color: var(--primary);
            font-weight: 600;
            text-decoration: none;
        }

        .register-link a:hover {
            text-decoration: underline;
        }

        .footer-note {
            margin-top: 18px;
            text-align: center;
            font-size: 12px;
            color: var(--muted);
        }

        .footer-note a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
        }

        .footer-note a:hover {
            text-decoration: underline;
        }

        @media (max-width: 520px) {
            body {
                padding: 14px;
            }

            .form-area {
                padding: 24px 22px 28px;
            }
        }
    </style>
</head>
<body>
    <div class="login-card">
        <header class="card-header">
            <div class="logo-wrap">
                <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT4INo84NyYsKwGYOhW1pZL-6hH4r76HLaBcA&s" alt="Pakistan Grammar School logo">
                <p>Pakistan Grammar School</p>
            </div>
        </header>

        <section class="form-area">
            <h2 class="form-title">Welcome Back</h2>
            <p class="form-subtitle">Sign in to your account</p>

            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="field-group">
                    <label for="email" class="field-label">Email</label>
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
                    <label for="password" class="field-label">Password</label>
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