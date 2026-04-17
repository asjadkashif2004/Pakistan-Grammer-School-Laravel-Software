<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Create Account - {{ config('app.name', 'Sky Digital Advertising') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --primary: #1e3a8a;     /* Deep elegant navy */
            --accent: #3b82f6;      /* Soft professional blue */
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Figtree', system-ui, sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            color: #1e2937;
        }

        .auth-card {
            width: 100%;
            max-width: 380px;
            background: #ffffff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.35);
        }

        /* Header */
        .header {
            background: linear-gradient(to right, var(--primary), #334155);
            padding: 32px 0 28px;
            text-align: center;
        }

        .logo-placeholder {
            height: 72px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Form Area */
        .form-area {
            padding: 36px 40px 42px;
        }

        .form-title {
            text-align: center;
            color: #64748b;
            font-size: 14.5px;
            font-weight: 600;
            letter-spacing: 1.2px;
            margin-bottom: 28px;
            text-transform: uppercase;
        }

        .input-group {
            margin-bottom: 20px;
        }

        .input-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #475569;
            margin-bottom: 7px;
            letter-spacing: 0.6px;
        }

        .input-field {
            width: 100%;
            padding: 13px 16px;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            font-size: 15px;
            transition: all 0.25s ease;
        }

        .input-field:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.12);
        }

        .sign-in-btn {
            width: 100%;
            background: linear-gradient(to right, var(--primary), var(--accent));
            color: white;
            border: none;
            padding: 14px;
            border-radius: 10px;
            font-size: 15.5px;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 8px 16px rgba(30, 58, 138, 0.25);
        }

        .sign-in-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 12px 20px rgba(30, 58, 138, 0.35);
        }

        .footer {
            text-align: center;
            padding: 18px 0 24px;
            font-size: 13.5px;
            color: #94a3b8;
        }

        .footer a {
            color: var(--accent);
            text-decoration: none;
            font-weight: 500;
        }

        .footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="auth-card">

        <!-- Header with Logo Placeholder -->
        <div class="header">
            <div class="logo-placeholder">
                <!-- Add your actual logo here -->
                <img src="YOUR_LOGO_PATH_HERE" alt="Sky Digital Advertising" style="max-height: 68px; width: auto;">
            </div>
        </div>

        <!-- Form Content -->
        <div class="form-area">
            <h2 class="form-title">Create your account</h2>

            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <!-- Full Name -->
                <div class="input-group">
                    <label for="name" class="input-label">FULL NAME</label>
                    <x-text-input 
                        id="name"
                        class="input-field"
                        type="text"
                        name="name"
                        :value="old('name')"
                        required
                        autofocus
                        autocomplete="name"
                        placeholder="Enter your full name" />
                    <x-input-error :messages="$errors->get('name')" class="mt-1.5 text-red-500 text-sm" />
                </div>

                <!-- Email -->
                <div class="input-group">
                    <label for="email" class="input-label">EMAIL ADDRESS</label>
                    <x-text-input 
                        id="email"
                        class="input-field"
                        type="email"
                        name="email"
                        :value="old('email')"
                        required
                        autocomplete="username"
                        placeholder="Enter your email" />
                    <x-input-error :messages="$errors->get('email')" class="mt-1.5 text-red-500 text-sm" />
                </div>

                <!-- Password -->
                <div class="input-group">
                    <label for="password" class="input-label">PASSWORD</label>
                    <x-text-input 
                        id="password"
                        class="input-field"
                        type="password"
                        name="password"
                        required
                        autocomplete="new-password"
                        placeholder="Create a strong password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-1.5 text-red-500 text-sm" />
                </div>

                <!-- Confirm Password -->
                <div class="input-group">
                    <label for="password_confirmation" class="input-label">CONFIRM PASSWORD</label>
                    <x-text-input 
                        id="password_confirmation"
                        class="input-field"
                        type="password"
                        name="password_confirmation"
                        required
                        autocomplete="new-password"
                        placeholder="Confirm your password" />
                </div>

                <!-- Submit Button -->
                <button type="submit" class="sign-in-btn">
                    Create Account
                </button>
            </form>
        </div>

        <!-- Footer -->
        <div class="footer">
            Already have an account? 
            <a href="{{ route('login') }}">Sign in here</a>
        </div>

    </div>

</body>
</html>