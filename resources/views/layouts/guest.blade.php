<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Sky Digital Advertising') }}</title>
    <link rel="icon" type="image/png" href="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT4INo84NyYsKwGYOhW1pZL-6hH4r76HLaBcA&s">
    <link rel="apple-touch-icon" href="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT4INo84NyYsKwGYOhW1pZL-6hH4r76HLaBcA&s">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --sky-blue: #2563eb;
            --sky-dark-blue: #1e40af;
        }

        body {
            font-family: 'Figtree', system-ui, sans-serif;
        }

        .login-container {
            min-height: 100vh;
            background: linear-gradient(to bottom, #1e3a8a, #1e40af);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-card {
            width: 100%;
            max-width: 420px;
            background: white;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgb(0 0 0 / 0.4);
        }

        .header {
            background: linear-gradient(to right, var(--sky-blue), var(--sky-dark-blue));
            padding: 48px 32px 40px;
            text-align: center;
        }

        .logo {
            display: inline-block;
            background: white;
            padding: 12px 28px;
            border-radius: 12px;
            box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1);
        }

        .logo-text {
            font-size: 42px;
            font-weight: 900;
            letter-spacing: -2px;
            line-height: 1;
        }

        .logo-sky { color: var(--sky-blue); }
        .logo-digital { color: var(--sky-dark-blue); }

        .logo-subtitle {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 4px;
            color: var(--sky-dark-blue);
            margin-top: -4px;
        }

        .form-area {
            padding: 40px 48px 48px;
        }

        .form-title {
            text-align: center;
            color: #64748b;
            font-size: 14px;
            font-weight: 600;
            letter-spacing: 2px;
            margin-bottom: 32px;
            text-transform: uppercase;
        }

        .input-label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: #475569;
            margin-bottom: 8px;
            letter-spacing: 1px;
        }

        .input-field {
            width: 100%;
            padding: 14px 18px;
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            font-size: 15px;
            transition: all 0.2s;
        }

        .input-field:focus {
            outline: none;
            border-color: var(--sky-blue);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
        }

        .remember-label {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            color: #475569;
            cursor: pointer;
        }

        .sign-in-btn {
            width: 100%;
            background: var(--sky-dark-blue);
            color: white;
            border: none;
            padding: 16px;
            border-radius: 14px;
            font-size: 16px;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 12px;
            transition: background 0.3s;
            box-shadow: 0 10px 15px -3px rgb(30 64 175 / 0.3);
        }

        .sign-in-btn:hover {
            background: #1e3a8a;
        }

        .footer {
            background: #f8fafc;
            padding: 16px;
            text-align: center;
            font-size: 12px;
            color: #64748b;
        }

        .footer a {
            color: var(--sky-blue);
            text-decoration: none;
        }

        .footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body class="antialiased">
    <div class="login-container">
        <div class="login-card">

            <!-- Header with Logo -->
            <div class="header">
                <div class="logo">
                    <div>
                        <span class="logo-text logo-sky">SKY</span>
                        <span class="logo-text logo-digital">DIGITAL</span>
                    </div>
                    <div class="logo-subtitle">ADVERTISING</div>
                </div>
            </div>

            <!-- Form Content -->
            <div class="form-area">
                <h2 class="form-title">Sign in to your account</h2>

                <!-- Session Status -->
                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- Email -->
                    <div class="mb-6">
                        <label for="email" class="input-label">EMAIL</label>
                        <x-text-input 
                            id="email"
                            class="input-field"
                            type="email"
                            name="email"
                            :value="old('email')"
                            required
                            autofocus
                            autocomplete="username"
                            placeholder="Enter email" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-500 text-sm" />
                    </div>

                    <!-- Password -->
                    <div class="mb-6">
                        <label for="password" class="input-label">PASSWORD</label>
                        <x-text-input 
                            id="password"
                            class="input-field"
                            type="password"
                            name="password"
                            required
                            autocomplete="current-password"
                            placeholder="Enter password" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-500 text-sm" />
                    </div>

                    <!-- Remember Me -->
                    <div class="mb-8">
                        <label for="remember_me" class="remember-label">
                            <input 
                                id="remember_me" 
                                type="checkbox" 
                                class="w-4 h-4 rounded border-gray-300 text-sky-600 focus:ring-sky-500"
                                name="remember">
                            <span>Remember me</span>
                        </label>
                    </div>

                    <!-- Sign In Button -->
                    <button type="submit" class="sign-in-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7-7 7" />
                        </svg>
                        Sign In
                    </button>
                </form>
            </div>

            <!-- Footer -->
            <div class="footer">
                Software Developed by <a href="https://addsmint.com" target="_blank">AddsMint.com</a>
            </div>

        </div>
    </div>
</body>
</html>