<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Pakistan Grammar School')</title>
    <link rel="icon" type="image/png" href="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT4INo84NyYsKwGYOhW1pZL-6hH4r76HLaBcA&s">
    <link rel="apple-touch-icon" href="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT4INo84NyYsKwGYOhW1pZL-6hH4r76HLaBcA&s">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --sidebar-bg: #0b5a2a;
            --sidebar-dark: #064620;
            --sidebar-soft: #12703a;
            --shell-bg: #f4faf3;
            --ink: #1d2d1f;
            --line: #d4ead4;
            --card: #ffffff;
            --primary: #0f7a35;
            --muted: #6f8570;
            --surface-shadow: 0 10px 30px -18px rgba(10, 90, 42, 0.28);
            --surface-shadow-hover: 0 18px 36px -20px rgba(10, 90, 42, 0.35);
            --ease: cubic-bezier(0.2, 0.7, 0.2, 1);
            --fast: 180ms;
            --normal: 280ms;
        }

        * {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Figtree', system-ui, sans-serif;
            background: var(--shell-bg);
            color: var(--ink);
            text-rendering: optimizeLegibility;
            -webkit-font-smoothing: antialiased;
        }

        .app-shell {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 260px 1fr;
        }

        .sidebar {
            background: linear-gradient(180deg, var(--sidebar-bg), var(--sidebar-dark));
            color: #e8fff0;
            padding: 18px 14px;
            display: flex;
            flex-direction: column;
            gap: 14px;
            position: relative;
            z-index: 25;
        }

        .brand {
            border: 1px solid rgba(255, 255, 255, 0.16);
            border-radius: 12px;
            padding: 10px;
            background: rgba(255, 255, 255, 0.05);
            text-align: center;
        }

        .brand img {
            width: 44px;
            height: 44px;
            object-fit: contain;
            border-radius: 8px;
            background: #ffffff;
            padding: 3px;
            margin: 0 auto 8px;
        }

        .sidebar-watermark {
            display: none;
            position: absolute;
            top: 120px;
            left: 50%;
            transform: translateX(-50%);
            width: 260px;
            height: 260px;
            opacity: 0.07;
            pointer-events: none;
            z-index: 0;
        }

        .sidebar-watermark img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            filter: grayscale(1);
        }

        @media (max-width: 740px) {
            .brand img {
                width: 56px;
                height: 56px;
                margin-bottom: 10px;
            }

            .sidebar-watermark {
                display: block;
            }

            .mobile-top-logo {
                display: block !important;
            }
        }

        .mobile-top-logo {
            display: none;
            width: 34px;
            height: 34px;
            object-fit: contain;
            border-radius: 8px;
            padding: 2px;
            background: rgba(15, 122, 53, 0.06);
            border: 1px solid var(--line);
            filter: drop-shadow(0 10px 18px rgba(15, 122, 53, 0.18));
        }

        .brand h1 {
            margin: 0;
            font-size: 17px;
            line-height: 1.25;
            font-weight: 700;
        }

        .brand p {
            margin: 2px 0 0;
            color: #c5f5d3;
            font-size: 11px;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .menu-group {
            margin-top: 4px;
        }

        .menu-title {
            font-size: 11px;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: #b5ebc6;
            margin: 8px 8px;
            font-weight: 700;
        }

        .menu-link {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin: 6px 0;
            padding: 9px 10px;
            border-radius: 10px;
            text-decoration: none;
            color: #e4ffe8;
            border: 1px solid transparent;
            font-size: 14px;
            font-weight: 600;
            transition:
                transform var(--fast) var(--ease),
                background var(--normal) var(--ease),
                border-color var(--normal) var(--ease),
                box-shadow var(--normal) var(--ease);
        }

        .menu-link:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(255, 255, 255, 0.1);
            transform: translateX(2px);
            box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.05);
        }

        .menu-link.active {
            background: linear-gradient(90deg, rgba(255, 255, 255, 0.2), rgba(255, 255, 255, 0.08));
            border-color: rgba(255, 255, 255, 0.3);
        }

        .menu-left {
            display: inline-flex;
            align-items: center;
            gap: 9px;
        }

        .menu-dot {
            width: 9px;
            height: 9px;
            border-radius: 50%;
            background: #95ebb0;
        }

        .menu-tag {
            font-size: 10px;
            font-weight: 700;
            color: #0f632d;
            background: #f8d56d;
            border-radius: 999px;
            padding: 2px 7px;
            text-transform: uppercase;
        }

        .content-shell {
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        .topbar {
            background: #ffffff;
            border-bottom: 1px solid var(--line);
            padding: 14px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            position: sticky;
            top: 0;
            z-index: 10;
            backdrop-filter: blur(4px);
        }

        .topbar > div:first-child {
            min-width: 0;
            flex: 1 1 auto;
        }

        .topbar h2 {
            margin: 0;
            font-size: 28px;
            font-weight: 800;
            color: #17361d;
        }

        @media (max-width: 900px) {
            .topbar h2 {
                font-size: clamp(1.15rem, 2.8vw, 1.65rem);
            }
        }

        .topbar-right {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
            justify-content: flex-end;
            min-width: 0;
            flex: 1 1 auto;
        }

        /* Alerts: host must not clip the panel; panel sizes to viewport */
        .alerts-host {
            position: relative;
            flex: 0 0 auto;
            min-width: 0;
            max-width: 100%;
        }

        .alerts-btn {
            position: relative;
            max-width: 100%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .alerts-badge {
            position: absolute;
            top: -7px;
            right: -7px;
            background: #ef4444;
            color: #fff;
            border-radius: 999px;
            font-size: 11px;
            min-width: 18px;
            height: 18px;
            padding: 0 5px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            line-height: 1;
        }

        .alerts-badge.is-hidden {
            display: none !important;
        }

        .alerts-dropdown {
            display: flex;
            flex-direction: column;
            position: absolute;
            right: 0;
            left: auto;
            transform-origin: top right;
            margin-top: 8px;
            width: min(360px, calc(100vw - 24px));
            max-width: calc(100vw - 24px);
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 12px;
            box-shadow: 0 14px 22px rgba(15, 122, 53, 0.15);
            z-index: 60;
            overflow: hidden;
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
            transform: scale(0.985);
            transition:
                opacity var(--normal) var(--ease),
                transform var(--normal) var(--ease),
                visibility var(--normal) var(--ease);
        }

        .alerts-dropdown.is-open {
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
            transform: scale(1);
        }

        .alerts-dropdown.is-open.is-fixed {
            display: flex;
        }

        .alerts-dropdown.is-fixed {
            position: fixed;
            left: 12px;
            right: 12px;
            width: auto;
            max-width: none;
            margin-top: 0;
            max-height: calc(100vh - 24px);
            max-height: calc(100dvh - 24px);
            display: flex;
            flex-direction: column;
        }

        .alerts-dropdown.is-fixed .alerts-dropdown-list {
            max-height: min(240px, 38vh);
            flex: 1 1 auto;
        }

        .alerts-dropdown-head {
            padding: 10px 12px;
            border-bottom: 1px solid #e8f3e8;
            font-weight: 700;
            color: #1f3f24;
        }

        .alerts-dropdown-list {
            max-height: min(320px, 55vh);
            overflow: auto;
            -webkit-overflow-scrolling: touch;
        }

        .alerts-dropdown-foot {
            display: block;
            text-align: center;
            padding: 10px;
            font-weight: 700;
            color: var(--primary);
            text-decoration: none;
            border-top: 1px solid #f1f8f2;
        }

        .alerts-item {
            display: block;
            padding: 10px 12px;
            border-bottom: 1px solid #f1f8f2;
            text-decoration: none;
            min-width: 0;
        }

        .alerts-item-title {
            font-size: 12px;
            color: #4a6d4f;
            font-weight: 700;
        }

        .alerts-item-body {
            font-size: 13px;
            color: #1f3f24;
            word-wrap: break-word;
            overflow-wrap: anywhere;
        }

        .alerts-item-meta {
            font-size: 11px;
            color: #7b927e;
            margin-top: 2px;
        }

        .alerts-empty {
            padding: 12px;
            color: #708772;
            font-size: 13px;
        }

        .date-chip,
        .action-chip {
            border: 1px solid var(--line);
            border-radius: 10px;
            padding: 8px 11px;
            background: #ffffff;
            font-size: 13px;
            font-weight: 600;
            color: #47624a;
            text-decoration: none;
            transition:
                transform var(--fast) var(--ease),
                box-shadow var(--normal) var(--ease),
                background var(--normal) var(--ease),
                border-color var(--normal) var(--ease);
        }

        .action-chip:hover {
            transform: translateY(-1px);
            box-shadow: var(--surface-shadow);
        }

        .action-chip.primary {
            background: var(--primary);
            color: #ffffff;
            border-color: var(--primary);
        }

        .action-chip.logout {
            background: #fff6f6;
            border-color: #ffdada;
            color: #a64444;
        }

        .page-content {
            padding: 16px;
            animation: fade-in-up var(--normal) var(--ease);
        }

        .list-pagination {
            width: 100%;
            max-width: 100%;
            margin-top: 4px;
        }

        /* Default paginator: vendor.pagination.ui (registered in AppServiceProvider) */
        .pg-nav {
            margin-top: 14px;
            padding: 12px 12px 10px;
            background: linear-gradient(180deg, #fbfefb 0%, #f2faf3 100%);
            border: 1px solid var(--line);
            border-radius: 12px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            width: 100%;
            max-width: 100%;
            box-sizing: border-box;
        }

        .pg-bar {
            display: flex;
            flex-direction: row;
            flex-wrap: nowrap;
            align-items: stretch;
            justify-content: space-between;
            gap: 8px;
        }

        .pg-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            min-height: 44px;
            padding: 0 12px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            border: 1px solid #c5e3c9;
            background: #ffffff;
            color: #1a4d22;
            flex: 1 1 0;
            min-width: 0;
            box-sizing: border-box;
            transition:
                background var(--fast) var(--ease),
                border-color var(--fast) var(--ease),
                transform var(--fast) var(--ease),
                box-shadow var(--fast) var(--ease);
            -webkit-tap-highlight-color: transparent;
        }

        .pg-btn__icon {
            font-size: 1.15rem;
            line-height: 1;
            font-weight: 800;
            opacity: 0.85;
        }

        .pg-btn__label {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .pg-btn--ghost:hover {
            background: #eef9ef;
            border-color: var(--primary);
        }

        .pg-btn--primary {
            background: linear-gradient(90deg, #0f7a35, #17a34a);
            color: #ffffff;
            border-color: #0c682d;
            box-shadow: 0 4px 12px -6px rgba(15, 122, 53, 0.45);
        }

        .pg-btn--primary:hover {
            filter: brightness(1.04);
            transform: translateY(-1px);
        }

        .pg-btn--disabled {
            opacity: 0.5;
            cursor: not-allowed;
            background: #f4f6f4;
            color: #7a8f7d;
            border-color: #e0ebe0;
        }

        .pg-status {
            flex: 0 0 auto;
            align-self: center;
            font-size: 12px;
            font-weight: 700;
            color: #56735a;
            text-align: center;
            white-space: nowrap;
            padding: 0 6px;
            line-height: 1.3;
        }

        .pg-status strong {
            color: #17361d;
            font-weight: 800;
        }

        .pg-meta {
            margin: 0;
            font-size: 11px;
            font-weight: 600;
            color: #6f8570;
            text-align: center;
            line-height: 1.4;
        }

        .pg-meta strong {
            color: #315233;
            font-weight: 800;
        }

        .pg-chips {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
            gap: 6px;
            padding-top: 2px;
            max-width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: thin;
        }

        .pg-chip {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 40px;
            min-height: 40px;
            padding: 0 10px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            border: 1px solid #d4ead4;
            background: #ffffff;
            color: #2a5a32;
            transition:
                background var(--fast) var(--ease),
                border-color var(--fast) var(--ease),
                transform var(--fast) var(--ease);
            -webkit-tap-highlight-color: transparent;
        }

        .pg-chip:hover {
            background: #eef9ef;
            border-color: var(--primary);
        }

        .pg-chip--current {
            background: linear-gradient(90deg, #0f7a35, #17a34a);
            color: #ffffff;
            border-color: #0c682d;
            cursor: default;
        }

        .pg-chip--ellipsis {
            min-width: 36px;
            border-style: dashed;
            color: #7a8f7d;
            background: transparent;
            cursor: default;
        }

        @media (max-width: 520px) {
            .pg-bar {
                flex-wrap: wrap;
            }

            .pg-status {
                order: -1;
                flex: 1 0 100%;
                padding-bottom: 4px;
            }

            .pg-btn {
                flex: 1 1 calc(50% - 4px);
                min-height: 48px;
            }
        }

        .module-footer {
            margin-top: auto;
            padding: 14px 16px;
            border-top: 1px solid var(--line);
            color: #47624a;
            font-weight: 700;
            text-align: center;
            background: rgba(255, 255, 255, 0.35);
            backdrop-filter: blur(2px);
        }

        .module-footer a {
            color: var(--primary);
            font-weight: 800;
            text-decoration: none;
            border-bottom: 1px solid transparent;
            transition: border-color var(--fast) var(--ease), color var(--fast) var(--ease);
        }

        .module-footer a:hover {
            border-bottom-color: rgba(15, 122, 53, 0.45);
        }

        /* Page-specific header actions: compact grid on small screens */
        .header-actions-slot {
            display: inline-flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 8px;
            min-width: 0;
        }

        .card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 14px;
            box-shadow: var(--surface-shadow);
            transition:
                transform var(--normal) var(--ease),
                box-shadow var(--normal) var(--ease);
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: var(--surface-shadow-hover);
        }

        button,
        input,
        select,
        textarea,
        .btn,
        .tab-link {
            transition:
                transform var(--fast) var(--ease),
                box-shadow var(--normal) var(--ease),
                border-color var(--normal) var(--ease),
                background var(--normal) var(--ease);
        }

        .btn:hover,
        button:hover:not(:disabled),
        .tab-link:hover {
            transform: translateY(-1px);
            box-shadow: var(--surface-shadow);
        }

        input:focus,
        select:focus,
        textarea:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(22, 103, 255, 0.12);
        }

        .sidebar-toggle {
            display: none;
            border: 1px solid var(--line);
            border-radius: 10px;
            background: #fff;
            color: #2c4d2f;
            padding: 7px 11px;
            font-weight: 700;
            cursor: pointer;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .sidebar-toggle svg {
            width: 20px;
            height: 20px;
            display: inline-block;
        }

        .sidebar-toggle-label {
            display: inline;
        }

        .sidebar-overlay {
            display: none;
        }

        @keyframes fade-in-up {
            from {
                opacity: 0;
                transform: translateY(8px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 1080px) {
            .app-shell {
                grid-template-columns: 1fr;
            }

            .sidebar {
                position: fixed;
                top: 0;
                left: 0;
                bottom: 0;
                width: min(84vw, 320px);
                transform: translateX(-100%);
                transition: transform var(--normal) var(--ease);
                box-shadow: 0 18px 35px rgba(0, 0, 0, 0.25);
                overflow: auto;
            }

            body.sidebar-open .sidebar {
                transform: translateX(0);
            }

            .sidebar-overlay {
                display: block;
                position: fixed;
                inset: 0;
                background: rgba(15, 23, 42, 0.45);
                opacity: 0;
                pointer-events: none;
                transition: opacity var(--normal) var(--ease);
                z-index: 20;
            }

            body.sidebar-open .sidebar-overlay {
                opacity: 1;
                pointer-events: auto;
            }

            .sidebar-toggle {
                display: inline-flex;
            }
        }

        @media (max-width: 740px) {
            .topbar {
                padding: 12px;
                align-items: stretch;
                flex-direction: column;
                gap: 10px;
                backdrop-filter: none;
                background: rgba(255, 255, 255, 0.98);
            }

            .topbar h2 {
                font-size: clamp(1.05rem, 4.2vw, 1.45rem);
                line-height: 1.2;
                word-break: break-word;
            }

            .topbar > div:first-child {
                min-width: 0;
                align-items: flex-start;
            }

            .topbar-right {
                width: 100%;
                display: flex;
                flex-direction: column;
                align-items: stretch;
                gap: 8px;
            }

            .topbar-right > .date-chip {
                width: 100%;
                text-align: center;
                box-sizing: border-box;
            }

            .topbar-right > .alerts-host {
                width: 100%;
            }

            .topbar-right > .alerts-host .alerts-btn {
                width: 100%;
                box-sizing: border-box;
            }

            .topbar-right > form {
                width: 100%;
                display: flex;
                margin: 0;
                min-width: 0;
            }

            .topbar-right > form .action-chip {
                width: 100%;
                justify-content: center;
            }

            /* Loose controls (not wrapped): stack — prefer .header-actions-slot for multiple buttons */
            .topbar-right > :not(.date-chip):not(.alerts-host):not(form):not(.header-actions-slot) {
                width: 100%;
                min-width: 0;
                display: flex;
                flex-direction: column;
                gap: 8px;
            }

            .topbar-right .action-chip:not(.alerts-btn) {
                width: 100%;
                justify-content: center;
                box-sizing: border-box;
            }

            .topbar-right > .header-actions-slot {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(min(100%, 140px), 1fr));
                gap: 8px;
                width: 100%;
            }

            .topbar-right > .header-actions-slot .action-chip {
                width: 100%;
                min-height: 40px;
                padding: 8px 10px;
                font-size: 12px;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .topbar-right > .header-actions-slot > form {
                width: 100%;
                margin: 0;
                display: flex;
                min-width: 0;
            }

            .topbar-right > .header-actions-slot > form .action-chip {
                width: 100%;
                justify-content: center;
            }

            .page-content {
                padding: 10px;
            }

            .module-footer {
                padding: 12px 10px;
                font-size: 12px;
                line-height: 1.35;
            }

            .sidebar-toggle {
                padding: 9px 10px;
                border-radius: 12px;
                background: rgba(15, 122, 53, 0.06);
            }

            .sidebar-toggle-label {
                display: none;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            *,
            *::before,
            *::after {
                animation: none !important;
                transition: none !important;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    @php
        $unreadAlerts = \App\Models\ActivityLog::whereNull('read_at')->latest()->take(8)->get();
        $unreadAlertsCount = \App\Models\ActivityLog::whereNull('read_at')->count();
    @endphp
    <div class="app-shell">
        <div class="sidebar-overlay" id="sidebarOverlay"></div>
        <aside class="sidebar">
            <div class="brand">
                <img
                    src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT4INo84NyYsKwGYOhW1pZL-6hH4r76HLaBcA&s"
                    alt="Pakistan Grammar School logo"
                    loading="lazy"
                    decoding="async">
                <h1>Pakistan Grammar School</h1>
                <p>Quetta - ERP System</p>
            </div>

            <div class="sidebar-watermark" aria-hidden="true">
                <img
                    src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT4INo84NyYsKwGYOhW1pZL-6hH4r76HLaBcA&s"
                    alt=""
                    loading="lazy"
                    decoding="async">
            </div>

            <div class="menu-group">
                <div class="menu-title">Overview</div>
                <a href="{{ route('dashboard') }}" class="menu-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <span class="menu-left"><span class="menu-dot"></span>Dashboard</span>
                </a>
            </div>

            <div class="menu-group">
                <div class="menu-title">Academics</div>
                <a href="{{ route('students.index') }}" class="menu-link {{ request()->routeIs('students.*') ? 'active' : '' }}">
                    <span class="menu-left"><span class="menu-dot"></span>Student Registration</span>
                    <span class="menu-tag">New</span>
                </a>
                <a href="{{ route('fee-vouchers.index') }}" class="menu-link {{ request()->routeIs('fee-vouchers.*') ? 'active' : '' }}">
                    <span class="menu-left"><span class="menu-dot"></span>Fee Vouchers</span>
                </a>
            </div>

            <div class="menu-group">
                <div class="menu-title">Commerce</div>
                <a href="{{ route('products.index') }}" class="menu-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                    <span class="menu-left"><span class="menu-dot"></span>Products</span>
                </a>
                <a href="{{ route('invoices.index') }}" class="menu-link {{ request()->routeIs('invoices.*') ? 'active' : '' }}">
                    <span class="menu-left"><span class="menu-dot"></span>Invoices / Sales</span>
                </a>
            </div>

            <div class="menu-group">
                <div class="menu-title">HR & Finance</div>
                <a href="{{ route('staff-salaries.index') }}" class="menu-link {{ request()->routeIs('staff-salaries.*') ? 'active' : '' }}">
                    <span class="menu-left"><span class="menu-dot"></span>Staff Salaries</span>
                </a>
                <a href="{{ route('expenses.index') }}" class="menu-link {{ request()->routeIs('expenses.*') ? 'active' : '' }}">
                    <span class="menu-left"><span class="menu-dot"></span>Expenses</span>
                </a>
                <a href="{{ route('reports.index') }}" class="menu-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                    <span class="menu-left"><span class="menu-dot"></span>Reports</span>
                </a>
            </div>

            <div style="margin-top:auto; border:1px solid rgba(255,255,255,.12); background:rgba(255,255,255,.05); border-radius:12px; padding:8px; color:#c5f5d3; font-size:12px; font-weight:700;">
                Logged in as: {{ auth()->user()->name }}
            </div>
        </aside>

        <div class="content-shell">
            <header class="topbar">
                <div style="display:flex; align-items:center; gap:10px;">
                    <button id="sidebarToggle" class="sidebar-toggle" type="button" aria-label="Open menu">
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path d="M4 6H20" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            <path d="M4 12H20" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            <path d="M4 18H20" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                        <span class="sidebar-toggle-label">Menu</span>
                    </button>
                    <img class="mobile-top-logo" src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT4INo84NyYsKwGYOhW1pZL-6hH4r76HLaBcA&s" alt="Pakistan Grammar School logo">
                    <h2>@yield('page_heading', 'Dashboard Overview')</h2>
                </div>
                <div class="topbar-right">
                    <span class="date-chip">{{ $todayLabel ?? now()->format('l, d F Y') }}</span>
                    <div class="alerts-host">
                        <button id="alertsButton" type="button" class="action-chip alerts-btn" title="Alerts" aria-label="Alerts">🔔
                            <span id="alertsCount" class="alerts-badge {{ $unreadAlertsCount > 0 ? '' : 'is-hidden' }}">{{ $unreadAlertsCount }}</span>
                        </button>
                        <div id="alertsDropdown" class="alerts-dropdown" role="region" aria-label="Notifications">
                            <div class="alerts-dropdown-head">Recent Notifications</div>
                            <div id="alertsList" class="alerts-dropdown-list">
                                @forelse ($unreadAlerts as $alert)
                                    <a href="{{ route('alerts.index') }}" class="alerts-item">
                                        <div class="alerts-item-title">{{ $alert->action }}</div>
                                        <div class="alerts-item-body">{{ $alert->description }}</div>
                                        <div class="alerts-item-meta">{{ $alert->created_at->diffForHumans() }}</div>
                                    </a>
                                @empty
                                    <div class="alerts-empty">No new notifications.</div>
                                @endforelse
                            </div>
                            <a href="{{ route('alerts.index') }}" class="alerts-dropdown-foot">View all alerts</a>
                        </div>
                    </div>
                    @yield('header_actions')
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="action-chip logout" title="Logout" aria-label="Logout">⎋</button>
                    </form>
                </div>
            </header>

            <main class="page-content">
                @if (session('status'))
                    <div class="card" style="padding: 12px 14px; margin-bottom: 14px; color: #0f7a35; font-weight: 600;">
                        {{ session('status') }}
                    </div>
                @endif
                @yield('content')
            </main>
            <footer class="module-footer">
                Software developed by:
                <a href="https://addsmint.com" target="_blank" rel="noopener noreferrer">AddsMint.com</a>
            </footer>
        </div>
    </div>
    @stack('scripts')
    <script>
        (function () {
            const body = document.body;
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            const button = document.getElementById('alertsButton');
            const dropdown = document.getElementById('alertsDropdown');
            const countEl = document.getElementById('alertsCount');

            const closeSidebar = () => body.classList.remove('sidebar-open');
            const openSidebar = () => body.classList.add('sidebar-open');
            const toggleSidebar = () => body.classList.toggle('sidebar-open');

            sidebarToggle?.addEventListener('click', toggleSidebar);
            sidebarOverlay?.addEventListener('click', closeSidebar);

            if (!button || !dropdown || !countEl) return;

            let opened = false;
            const mobileMax = 740;
            let lastRenderedCount = null;

            const closeDropdown = () => {
                opened = false;
                dropdown.classList.remove('is-open', 'is-fixed');
                dropdown.style.top = '';
            };

            const syncDropdownLayout = () => {
                if (!opened) return;
                if (window.innerWidth <= mobileMax) {
                    dropdown.classList.add('is-fixed');
                    const rect = button.getBoundingClientRect();
                    dropdown.style.top = `${Math.round(rect.bottom + 8)}px`;
                } else {
                    dropdown.classList.remove('is-fixed');
                    dropdown.style.top = '';
                }
            };

            const renderCount = (count) => {
                if (count === lastRenderedCount) return;
                lastRenderedCount = count;
                countEl.textContent = count;
                countEl.classList.toggle('is-hidden', count <= 0);
            };

            const refreshCount = async () => {
                if (document.visibilityState !== 'visible') return;
                try {
                    const res = await fetch('{{ route('alerts.unread-count') }}', { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                    const data = await res.json();
                    const count = Number(data.count || 0);
                    renderCount(count);
                } catch (_) {}
            };

            const markAllRead = async () => {
                try {
                    await fetch('{{ route('alerts.mark-read') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify({}),
                    });
                } catch (_) {}
            };

            button.addEventListener('click', async (e) => {
                e.stopPropagation();
                if (dropdown.classList.contains('is-open')) {
                    closeDropdown();
                    return;
                }
                dropdown.classList.add('is-open');
                opened = true;
                syncDropdownLayout();
                await markAllRead();
                await refreshCount();
            });

            document.addEventListener('click', (e) => {
                if (!dropdown.contains(e.target) && !button.contains(e.target)) {
                    closeDropdown();
                }
            });

            window.addEventListener('resize', () => {
                if (window.innerWidth > 1080) closeSidebar();
                syncDropdownLayout();
            });

            window.addEventListener('scroll', () => syncDropdownLayout(), { passive: true });

            // Poll less frequently and only when the tab is visible.
            const pollMs = window.innerWidth <= mobileMax ? 45000 : 60000;
            refreshCount();
            setInterval(() => refreshCount(), pollMs);
        })();
    </script>
</body>
</html>
