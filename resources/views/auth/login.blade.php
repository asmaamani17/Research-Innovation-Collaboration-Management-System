@extends('layouts.main')

@section('title', 'Login System')

@section('content')
    <div class="w-full max-w-[700px]">
        <!-- Headline and Subtext -->
        <!-- <div class="text-center mb-8">
                <h1 class="text-gray-900 dark:text-white text-3xl font-bold tracking-tight mb-3">Sistem Pengurusan Anugerah Penyelidikan</h1>
                <p class="text-gray-600 dark:text-gray-400 text-base">Log masuk untuk menguruskan permohonan dan anugerah anda.</p>
            </div> -->

        <!-- Login Container -->
        <div class="login-container">
            <!-- Left Panel - Branding -->

            <div class="branding-panel">
                <div class="branding-glow branding-glow-top"></div>
                <div class="branding-glow branding-glow-bottom"></div>
                <div class="branding-spotlight"></div>

                <div class="branding-content">
                    <div class="university-logo">
                        <div class="logo-icon">R</div>
                        <div class="logo-text">
                            <h1>RICE Research</h1>
                            <p>Excellence in Innovation</p>
                        </div>
                    </div>

                    <div class="welcome-content">
                        <h2>Welcome to Research, Innovation &amp; Collaboration Management System</h2>
                        <p>
                            Manage research records, track awards and innovation outcomes, monitor community programs,
                            and review performance through one integrated platform for RICMS research teams.
                        </p>
                    </div>

                </div>
            </div>

            <!-- Right Panel - Login Form -->
            <div class="form-panel">
                <div class="form-header">
                    <h3>Sign In</h3>
                    <p>Enter your credentials to access your account</p>
                </div>

                <form class="login-form" method="POST" action="{{ route('login') }}">
                    @csrf

                    @if (session('status'))
                        <div class="login-success" role="status">{{ session('status') }}</div>
                    @endif

                    @if ($errors->any())
                        <div class="login-error" role="alert">
                            <span class="material-symbols-outlined" aria-hidden="true">error</span>
                            <div>
                                <p class="login-error-title">Login Failed</p>
                                <p>{{ $errors->first() }}</p>
                            </div>
                        </div>
                    @endif

                    <div class="form-group">
                        <label for="staff-id">IC Number</label>
                        <div class="input-wrapper @error('staff_id') error @enderror">
                            <span class="material-symbols-outlined input-leading-icon" aria-hidden="true">badge</span>
                            <input type="text" id="staff-id" name="staff_id" placeholder="e.g: B031910001" required
                                autocomplete="username" value="{{ old('staff_id') }}">
                        </div>
                        @error('staff_id')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="input-wrapper @error('password') error @enderror">
                            <span class="material-symbols-outlined input-leading-icon" aria-hidden="true">lock</span>
                            <input type="password" id="password" name="password" placeholder="Enter your password" required
                                autocomplete="current-password">
                        </div>
                        @error('password')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-options">
                        <div class="remember-me">
                            <input type="checkbox" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label for="remember">Remember me</label>
                        </div>
                        <a href="{{ route('password.request') }}" class="forgot-password">Forgot password?</a>
                    </div>

                    <button type="submit" class="submit-btn">Login</button>
                </form>

            </div>
        </div>
        <a class="back-link" href="{{ url('https://rice.utem.edu.my/ms/') }}">
            <span class="material-symbols-outlined" aria-hidden="true">arrow_back</span>
            Back to RICE Portal
        </a>
    </div>
@endsection

@push('scripts')
    <script>
        // Add any page-specific JavaScript here
        document.addEventListener('DOMContentLoaded', function () {
            console.log('Login page loaded');

            const brandingPanel = document.querySelector('.branding-panel');
            const spotlight = document.querySelector('.branding-spotlight');

            brandingPanel.addEventListener('pointerenter', function () {
                this.classList.add('is-active');
            });

            brandingPanel.addEventListener('pointermove', function (event) {
                const bounds = this.getBoundingClientRect();
                const x = event.clientX - bounds.left - spotlight.offsetWidth / 2;
                const y = event.clientY - bounds.top - spotlight.offsetHeight / 2;
                spotlight.style.transform = `translate3d(${x}px, ${y}px, 0)`;
            });

            brandingPanel.addEventListener('pointerleave', function () {
                this.classList.remove('is-active');
            });

            // Form validation
            document.querySelector('.login-form').addEventListener('submit', function (e) {
                const staffId = document.getElementById('staff-id').value;
                const password = document.getElementById('password').value;

                if (!staffId || !password) {
                    e.preventDefault();
                    alert('Please fill in all fields');
                    return;
                }
            });
        });
    </script>
@endpush

@push('styles')
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --university-blue: #003087;
            --university-gold: #D4AF37;
            --deep-navy: #001a4d;
            --soft-cream: #faf8f3;
            --text-primary: #1a1a1a;
            --text-secondary: #666666;
            --border-light: #e5e5e5;
            --success-green: #2d7a4f;
            --error-red: #c83532;
        }

        .login-container {
            width: 100%;
            max-width: 700px;
            display: grid;
            grid-template-columns: 0.95fr 1.05fr;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            position: relative;
            z-index: 1;
            animation: slideUp 0.6s ease-out;
            margin: 0 auto;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes brandGradientShift {
            0% {
                background-position: 15% 50%;
            }

            50% {
                background-position: 65% 40%;
            }

            100% {
                background-position: 30% 70%;
            }
        }

        /* Left Panel - Branding */
        .branding-panel {
            background: linear-gradient(160deg, var(--university-blue) 0%, #0d3b9d 30%, var(--deep-navy) 100%);
            background-size: 220% 220%;
            background-position: 50% 50%;
            animation: brandGradientShift 12s ease-in-out infinite alternate;
            transition: background-position 0.25s ease-out;
            padding: 32px 20px;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }

        .branding-panel::after {
            content: '';
            position: absolute;
            top: var(--highlight-y, 0%);
            left: var(--highlight-x, 100%);
            width: 180px;
            height: 180px;
            background: radial-gradient(circle, rgba(212, 175, 55, 0.15) 0%, transparent 70%);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: top 0.12s ease-out, left 0.12s ease-out, background-position 0.12s ease-out;
            pointer-events: none;
        }

        .university-logo {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 24px;
            position: relative;
            z-index: 1;
        }

        .logo-icon {
            width: 40px;
            height: 40px;
            background: var(--university-gold);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', sans-serif;
            font-size: 24px;
            font-weight: bold;
            color: var(--deep-navy);
            box-shadow: 0 4px 12px rgba(212, 175, 55, 0.3);
        }

        .logo-text h1 {
            font-family: 'Poppins', sans-serif;
            font-size: 16px;
            line-height: 1.3;
            font-weight: normal;
            margin-bottom: 4px;
            letter-spacing: -0.5px;
        }

        .logo-text p {
            font-size: 13px;
            line-height: 1.4;
            opacity: 0.8;
            letter-spacing: 0.6px;
            text-transform: uppercase;
            font-weight: 300;
        }

        .welcome-content {
            position: relative;
            z-index: 1;
        }

        .welcome-content h2 {
            font-family: 'Poppins', sans-serif;
            font-size: 22px;
            line-height: 1.35;
            margin-bottom: 16px;
            animation: fadeIn 0.8s ease-out 0.3s both;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .welcome-content p {
            font-size: 13px;
            line-height: 1.55;
            opacity: 0.9;
            margin-bottom: 16px;
            animation: fadeIn 0.8s ease-out 0.5s both;
        }

        .features {
            list-style: none;
            animation: fadeIn 0.8s ease-out 0.7s both;
        }

        .features li {
            padding: 6px 0;
            padding-left: 22px;
            position: relative;
            opacity: 0.9;
            font-size: 13px;
            line-height: 1.5;
        }

        .features li::before {
            content: '✓';
            position: absolute;
            left: 0;
            color: var(--university-gold);
            font-weight: bold;
            font-size: 18px;
        }

        .branding-footer {
            position: relative;
            z-index: 1;
            opacity: 0.7;
            font-size: 11px;
            line-height: 1.6;
            animation: fadeIn 0.8s ease-out 0.9s both;
        }

        /* Right Panel - Login Form */
        .form-panel {
            padding: 32px 24px;
            background: var(--soft-cream);
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .form-header {
            margin-bottom: 24px;
        }

        .form-header h3 {
            font-family: 'Poppins', sans-serif;
            font-size: 26px;
            line-height: 1.25;
            color: var(--text-primary);
            margin-bottom: 8px;
        }

        .form-header p {
            color: var(--text-secondary);
            font-size: 13px;
            line-height: 1.5;
        }

        .login-form {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .form-group label {
            display: flex;
            align-items: center;
            font-size: 13px;
            line-height: 1.4;
            font-weight: 600;
            color: var(--text-primary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .input-wrapper {
            position: relative;
            transition: transform 0.3s ease;
        }

        .input-wrapper input {
            width: 100%;
            padding: 12px 14px;
            border: 2px solid var(--border-light);
            border-radius: 12px;
            font-size: 14px;
            line-height: 1.4;
            font-family: 'Inter', sans-serif;
            background: white;
            transition: all 0.3s ease;
            color: var(--text-primary);
        }

        .input-wrapper input:focus {
            outline: none;
            border-color: var(--university-blue);
            box-shadow: 0 0 0 4px rgba(0, 48, 135, 0.1);
        }

        .input-wrapper input::placeholder {
            color: #999;
        }

        .input-wrapper.error input {
            border-color: var(--error-red);
        }

        .input-icon {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-secondary);
            font-size: 20px;
        }

        .error-message {
            font-size: 13px;
            color: var(--error-red);
            margin-top: 4px;
        }

        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            flex-wrap: wrap;
            gap: 8px;
            margin: -8px 0 8px 0;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .remember-me input[type="checkbox"] {
            width: 16px;
            height: 16px;
            cursor: pointer;
            accent-color: var(--university-blue);
        }

        .remember-me label {
            font-size: 12px;
            line-height: 1.5;
            color: var(--text-secondary);
            cursor: pointer;
            text-transform: none;
            letter-spacing: normal;
            font-weight: 400;
        }

        .forgot-password {
            font-size: 12px;
            color: var(--university-blue);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
        }

        .forgot-password:hover {
            color: var(--deep-navy);
        }

        .submit-btn {
            padding: 14px 24px;
            background: linear-gradient(135deg, var(--university-blue) 0%, var(--deep-navy) 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(0, 48, 135, 0.3);
            position: relative;
            overflow: hidden;
        }

        .submit-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .submit-btn:hover::before {
            left: 100%;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 48, 135, 0.4);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        .divider {
            display: flex;
            align-items: center;
            gap: 16px;
            margin: 32px 0;
            color: var(--text-secondary);
            font-size: 13px;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--border-light);
        }

        .help-text {
            text-align: center;
            font-size: 14px;
            color: var(--text-secondary);
        }

        .help-text a {
            color: var(--university-blue);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s;
        }

        .help-text a:hover {
            color: var(--deep-navy);
        }

        /* Responsive Design */
        @media (max-width: 640px) {
            .login-container {
                grid-template-columns: 1fr;
            }

            .branding-panel {
                padding: 40px;
                min-height: 300px;
            }

            .welcome-content h2 {
                font-size: 24px;
            }

            .form-panel {
                padding: 40px;
            }
        }

        @media (max-width: 640px) {
            .login-container {
                border-radius: 0;
            }

            .branding-panel,
            .form-panel {
                padding: 30px 20px;
            }
        }

        .login-container {
            max-width: 750px;
            grid-template-columns: 42% 58%;
            border: 1px solid rgba(197, 198, 209, 0.45);
            border-radius: 14px;
            box-shadow: 0 18px 48px rgba(0, 15, 48, 0.16);
        }

        .branding-panel {
            min-height: 500px;
            padding: 26px 22px 20px;
            background: linear-gradient(135deg, #000c26 0%, #001a4d 34%, #0b2d74 68%, #00091e 100%);
            background-size: 260% 260%;
            animation: login-gradient-flow 16s ease infinite;
        }

        .branding-panel::after {
            display: none;
        }

        .branding-glow,
        .branding-spotlight {
            position: absolute;
            border-radius: 50%;
            pointer-events: none;
        }

        .branding-glow-top {
            width: 210px;
            height: 210px;
            top: -75px;
            left: -80px;
            background: rgba(91, 130, 226, 0.22);
            filter: blur(42px);
            animation: ambient-glow 9s ease-in-out infinite;
        }

        .branding-glow-bottom {
            width: 170px;
            height: 170px;
            right: -70px;
            bottom: -65px;
            background: rgba(212, 175, 55, 0.16);
            filter: blur(36px);
        }

        .branding-spotlight {
            z-index: 0;
            left: 0;
            top: 0;
            width: 220px;
            height: 220px;
            opacity: 0.25;
            background: radial-gradient(circle, rgba(255, 224, 136, 0.42) 0%, rgba(203, 167, 47, 0.24) 40%, transparent 72%);
            filter: blur(28px);
            transform: translate3d(35px, 100px, 0);
            transition: opacity 300ms ease;
            will-change: transform;
        }

        .branding-panel.is-active .branding-spotlight {
            opacity: 0.8;
        }

        .branding-content,
        .branding-footer {
            position: relative;
            z-index: 1;
        }

        .branding-content {
            display: flex;
            flex-direction: column;
            gap: 26px;
        }

        .university-logo {
            gap: 10px;
            margin: 0;
        }

        .logo-icon {
            flex: 0 0 40px;
            border: 1px solid rgba(255, 255, 255, 0.16);
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.1);
            color: #ffe088;
            box-shadow: none;
            backdrop-filter: blur(8px);
        }

        .logo-text h1 {
            margin: 0 0 3px;
            font-family: 'Inter', sans-serif;
            font-size: 16px;
            font-weight: 600;
            letter-spacing: -0.3px;
        }

        .logo-text p {
            color: #ffe088;
            font-size: 9px;
            font-weight: 600;
            letter-spacing: 1px;
        }

        .welcome-content h2 {
            margin: 20px 0 10px;
            font-family: 'Inter', sans-serif;
            font-size: 24px;
            font-weight: 500;
            line-height: 1.35;
        }

        .welcome-content p {
            margin: 0;
            color: #c7d4f4;
            font-text: medium;
            line-height: 1.65;
            text-align: justify;
        }

        .branding-footer {
            display: flex;
            flex-direction: column;
            gap: 5px;
            color: #c7d4f4;
            font-size: 10px;
            line-height: 1.5;
        }

        .branding-footer p {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .branding-footer .material-symbols-outlined {
            font-size: 13px;
        }

        .form-panel {
            justify-content: center;
            padding: 30px 26px 20px;
            background: #fff;
        }

        .form-header {
            margin-bottom: 20px;
        }

        .form-header h3 {
            margin-bottom: 5px;
            color: #001a4d;
            font-family: 'Inter', sans-serif;
            font-size: 22px;
            font-weight: 600;
            letter-spacing: -0.4px;
        }

        .form-header p {
            color: #666b78;
            font-size: 13px;
            line-height: 1.5;
        }

        .login-form {
            gap: 15px;
        }

        .form-group {
            gap: 6px;
        }

        .form-group label {
            color: #001a4d;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.2px;
            text-transform: none;
        }

        .input-wrapper {
            min-width: 0;
        }

        .input-wrapper input {
            height: 44px;
            padding: 9px 11px 9px 38px;
            border: 1px solid transparent;
            border-radius: 8px;
            background: #f2f4f6;
            font-size: 12px;
        }

        .input-wrapper:focus-within input {
            border-color: #3959b0;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(57, 89, 176, 0.12);
        }

        .input-leading-icon {
            position: absolute;
            z-index: 1;
            left: 11px;
            top: 50%;
            color: #757780;
            font-size: 17px;
            pointer-events: none;
            transform: translateY(-50%);
        }

        .input-wrapper.error input {
            border-color: var(--error-red);
        }

        .form-options {
            align-items: center;
            margin: 0;
        }

        .remember-me {
            gap: 7px;
        }

        .remember-me input[type="checkbox"] {
            width: 14px;
            height: 14px;
        }

        .remember-me label,
        .forgot-password {
            font-size: 12px;
            line-height: 1.5;
        }

        .forgot-password {
            white-space: nowrap;
        }

        .submit-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-height: 44px;
            padding: 11px 16px;
            border-radius: 8px;
            background: #001a4d;
            font-size: 12px;
            box-shadow: 0 2px 8px rgba(0, 26, 77, 0.16);
        }

        .submit-btn:hover {
            background: #184290;
            box-shadow: 0 4px 12px rgba(0, 26, 77, 0.2);
        }

        .login-error {
            display: flex;
            align-items: flex-start;
            gap: 9px;
            padding: 10px 12px;
            border: 1px solid #fecaca;
            border-radius: 8px;
            background: #fef2f2;
            color: #b42318;
            font-size: 11px;
            line-height: 1.5;
        }

        .login-success {
            padding: 10px 12px;
            border: 1px solid #a7f3d0;
            border-radius: 8px;
            background: #ecfdf5;
            color: #166534;
            font-size: 11px;
            line-height: 1.5;
        }

        .login-error > .material-symbols-outlined {
            font-size: 17px;
        }

        .login-error-title {
            margin-bottom: 2px;
            font-weight: 600;
        }

        .back-link {
            display: flex;
            align-items: center;
            gap: 5px;
            justify-content: center;
            width: max-content;
            margin: 12px auto 0;
            color: #3959b0;
            font-size: 11px;
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .back-link:hover {
            color: #001a4d;
        }

        .back-link .material-symbols-outlined {
            font-size: 15px;
        }

        @keyframes login-gradient-flow {
            0%, 100% { background-position: 0% 40%; }
            50% { background-position: 100% 60%; }
        }

        @keyframes ambient-glow {
            0%, 100% { transform: scale(1); opacity: 0.2; }
            50% { transform: scale(1.15) translate(8px, -12px); opacity: 0.38; }
        }

        @media (max-width: 640px) {
            .login-container {
                grid-template-columns: 1fr;
                border-radius: 12px;
            }

            .branding-panel {
                min-height: 0;
                gap: 28px;
                padding: 24px 22px;
            }

            .branding-content {
                gap: 20px;
            }

            .form-panel {
                padding: 26px 22px 20px;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .branding-panel,
            .branding-glow-top {
                animation: none;
            }

            .branding-spotlight {
                transition: none;
            }
        }
    </style>
@endpush