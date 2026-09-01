@extends('layouts.main')

@section('title', 'Login System | Research Awards')

@section('content')
    <div class="w-full max-w-[920px]">
        <!-- Headline and Subtext -->
        <!-- <div class="text-center mb-8">
                <h1 class="text-gray-900 dark:text-white text-3xl font-bold tracking-tight mb-3">Sistem Pengurusan Anugerah Penyelidikan</h1>
                <p class="text-gray-600 dark:text-gray-400 text-base">Log masuk untuk menguruskan permohonan dan anugerah anda.</p>
            </div> -->

        <!-- Login Container -->
        <div class="login-container">
            <!-- Left Panel - Branding -->

            <div class="branding-panel">
                <div>
                    <div class="university-logo">
                        <div class="logo-icon">R</div>
                        <div class="logo-text">
                            <h1>RICE Research</h1>
                            <p>Excellence in Innovation</p>
                        </div>
                    </div>

                    <div class="welcome-content">
                        <h2>Welcome to Research, Innovation & Collaboration Management System</h2>
                        <p>Empowering academic excellence through optimized research management and recognition.</p>

                        <ul class="features">
                            <li>Submit and track research award applications</li>
                            <li>Access funding opportunities and grants</li>
                            <li>Monitor award status in real-time</li>
                            <li>Collaborate with research teams</li>
                            <li>View comprehensive analytics and reports</li>
                        </ul>
                    </div>
                </div>

                <div class="branding-footer">
                    <p> 2024 Universiti Teknikal Malaysia Melaka</p>
                    <p>All rights reserved • RAMS v2.4.1</p>
                </div>
            </div>

            <!-- Right Panel - Login Form -->
            <div class="form-panel">
                <div class="form-header">
                    <h3>Login</h3>
                    <p>Enter your credentials to access your account</p>
                </div>

                <form class="login-form" method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- General Error Message -->
                    @if ($errors->any())
                        <div
                            class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6 flex items-center gap-3">
                            <span class="material-symbols-outlined text-red-500">error</span>
                            <div>
                                <p class="font-medium">Login Failed</p>
                                <p class="text-sm">{{ $errors->first() }}</p>
                            </div>
                        </div>
                    @endif

                    <div class="form-group">
                        <label for="staff-id">
                            <span class="material-symbols-outlined"
                                style="font-size: 16px; vertical-align: middle; margin-right: 6px;">badge</span>
                            IC Number
                        </label>
                        <div class="input-wrapper @error('staff_id') error @enderror">
                            <input type="text" id="staff-id" name="staff_id" placeholder="e.g: B031910001" required
                                autocomplete="username" value="{{ old('staff_id') }}">
                        </div>
                        @error('staff_id')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password">
                            <span class="material-symbols-outlined"
                                style="font-size: 16px; vertical-align: middle; margin-right: 6px;">lock</span>
                            Password
                        </label>
                        <div class="input-wrapper @error('password') error @enderror">
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

                    <!-- <div class="divider">
                            <span>Need Help?</span>
                        </div>

                        <div class="help-text">
                            Don't have access? <a href="{{ route('contact.admin') }}">Contact IT Support</a>
                        </div> -->
                </form>
            </div>
        </div>

        <!-- Footer Link -->
        <div class="mt-8 text-center">
            <a class="inline-flex items-center gap-2 text-gray-500 dark:text-gray-400 text-sm hover:text-primary transition-colors"
                href="{{ url('/') }}">
                <span class="material-symbols-outlined text-sm">arrow_back</span>
                Back to Portal
            </a>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Add any page-specific JavaScript here
        document.addEventListener('DOMContentLoaded', function () {
            console.log('Login page loaded');

            // Add smooth focus animation
            document.querySelectorAll('input').forEach(input => {
                input.addEventListener('focus', function () {
                    this.parentElement.style.transform = 'scale(1.01)';
                });

                input.addEventListener('blur', function () {
                    this.parentElement.style.transform = 'scale(1)';
                });
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
            max-width: 920px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            background: white;
            border-radius: 24px;
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

        /* Left Panel - Branding */
        .branding-panel {
            background: linear-gradient(160deg, var(--university-blue) 0%, var(--deep-navy) 100%);
            padding: 60px;
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
            top: 0;
            right: 0;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(212, 175, 55, 0.15) 0%, transparent 70%);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translate(0, 0);
            }

            50% {
                transform: translate(-20px, -20px);
            }
        }

        .university-logo {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 60px;
            position: relative;
            z-index: 1;
        }

        .logo-icon {
            width: 60px;
            height: 60px;
            background: var(--university-gold);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', sans-serif;
            font-size: 32px;
            font-weight: bold;
            color: var(--deep-navy);
            box-shadow: 0 4px 12px rgba(212, 175, 55, 0.3);
        }

        .logo-text h1 {
            font-family: 'Poppins', sans-serif;
            font-size: 24px;
            font-weight: normal;
            margin-bottom: 4px;
            letter-spacing: -0.5px;
        }

        .logo-text p {
            font-size: 12px;
            opacity: 0.8;
            letter-spacing: 2px;
            text-transform: uppercase;
            font-weight: 300;
        }

        .welcome-content {
            position: relative;
            z-index: 1;
        }

        .welcome-content h2 {
            font-family: 'Poppins', sans-serif;
            font-size: 42px;
            line-height: 1.2;
            margin-bottom: 24px;
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
            font-size: 16px;
            line-height: 1.7;
            opacity: 0.9;
            margin-bottom: 40px;
            animation: fadeIn 0.8s ease-out 0.5s both;
        }

        .features {
            list-style: none;
            animation: fadeIn 0.8s ease-out 0.7s both;
        }

        .features li {
            padding: 12px 0;
            padding-left: 32px;
            position: relative;
            opacity: 0.9;
            font-size: 14px;
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
            font-size: 12px;
            animation: fadeIn 0.8s ease-out 0.9s both;
        }

        /* Right Panel - Login Form */
        .form-panel {
            padding: 60px;
            background: var(--soft-cream);
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .form-header {
            margin-bottom: 40px;
        }

        .form-header h3 {
            font-family: 'Poppins', sans-serif;
            font-size: 32px;
            color: var(--text-primary);
            margin-bottom: 8px;
        }

        .form-header p {
            color: var(--text-secondary);
            font-size: 15px;
        }

        .login-form {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .form-group label {
            font-size: 13px;
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
            padding: 16px 20px;
            border: 2px solid var(--border-light);
            border-radius: 12px;
            font-size: 15px;
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
            align-items: center;
            margin: -8px 0 8px 0;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .remember-me input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: var(--university-blue);
        }

        .remember-me label {
            font-size: 14px;
            color: var(--text-secondary);
            cursor: pointer;
            text-transform: none;
            letter-spacing: normal;
            font-weight: 400;
        }

        .forgot-password {
            font-size: 14px;
            color: var(--university-blue);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
        }

        .forgot-password:hover {
            color: var(--deep-navy);
        }

        .submit-btn {
            padding: 18px 32px;
            background: linear-gradient(135deg, var(--university-blue) 0%, var(--deep-navy) 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
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
        @media (max-width: 968px) {
            .login-container {
                grid-template-columns: 1fr;
            }

            .branding-panel {
                padding: 40px;
                min-height: 300px;
            }

            .welcome-content h2 {
                font-size: 32px;
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
    </style>
@endpush