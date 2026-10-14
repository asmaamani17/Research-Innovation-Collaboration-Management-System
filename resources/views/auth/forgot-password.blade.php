@extends('layouts.main')

@section('title', 'Forgot Password | Research, Innovation & Collaboration Management')

@section('content')
    <div class="w-full max-w-[700px]">
        <div class="forgot-password-card">
            <section class="forgot-form-panel">
                <div class="forgot-form-content">
                    <header class="forgot-form-header">
                        <h2>Forgot password?</h2>
                        <p>{{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}</p>
                    </header>

                    @if (session('status'))
                        <div class="forgot-status" role="status">
                            <span class="material-symbols-outlined" aria-hidden="true">check_circle</span>
                            <p>{{ session('status') }}</p>
                        </div>
                    @endif

                    <form class="forgot-form" method="POST" action="{{ route('password.email') }}">
                        @csrf

                        <div class="forgot-field">
                            <label for="email">{{ __('Email') }}</label>
                            <div class="forgot-input-wrapper @error('email') has-error @enderror">
                                <span class="material-symbols-outlined" aria-hidden="true">mail</span>
                                <input id="email" type="email" name="email" value="{{ old('email') }}"
                                    placeholder="Enter your email address" required autofocus autocomplete="email">
                            </div>
                            @error('email')
                                <p class="forgot-error" role="alert">{{ $message }}</p>
                            @enderror
                        </div>

                        <button class="forgot-submit" type="submit">
                            {{ __('Email Password Reset Link') }}
                            <span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span>
                        </button>
                    </form>
                </div>

            </section>
        </div>

        <div class="forgot-navigation">
            <a class="forgot-navigation-link" href="{{ route('login') }}">
                <span class="material-symbols-outlined" aria-hidden="true">arrow_back</span>
                Back to Login
            </a>
            <a class="forgot-navigation-link" href="{{ url('https://rice.utem.edu.my/ms/') }}">
                <span class="material-symbols-outlined" aria-hidden="true">arrow_back</span>
                Back to RICE Portal
            </a>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .forgot-password-card {
            display: grid;
            grid-template-columns: 1fr;
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            overflow: hidden;
            border: 1px solid rgba(197, 198, 209, 0.45);
            border-radius: 14px;
            background: #fff;
            box-shadow: 0 18px 48px rgba(0, 15, 48, 0.16);
            animation: forgot-card-appear 0.5s ease-out both;
        }

        .forgot-form-panel {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 30px 26px 20px;
            background: #fff;
        }

        .forgot-form-header {
            margin-bottom: 24px;
        }

        .forgot-form-header h2 {
            margin: 0 0 7px;
            color: #001a4d;
            font-size: 22px;
            font-weight: 600;
            letter-spacing: -0.4px;
        }

        .forgot-form-header p {
            margin: 0;
            color: #666b78;
            font-size: 12px;
            line-height: 1.6;
        }

        .forgot-form {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .forgot-field {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .forgot-field label {
            color: #001a4d;
            font-size: 11px;
            font-weight: 600;
        }

        .forgot-input-wrapper {
            position: relative;
        }

        .forgot-input-wrapper > .material-symbols-outlined {
            position: absolute;
            top: 50%;
            left: 11px;
            color: #757780;
            font-size: 17px;
            pointer-events: none;
            transform: translateY(-50%);
        }

        .forgot-input-wrapper input {
            width: 100%;
            height: 40px;
            padding: 9px 11px 9px 38px;
            border: 1px solid transparent;
            border-radius: 8px;
            outline: none;
            background: #f2f4f6;
            color: #191c1e;
            font-family: inherit;
            font-size: 12px;
            transition: border-color 0.2s ease, background 0.2s ease, box-shadow 0.2s ease;
        }

        .forgot-input-wrapper input:focus {
            border-color: #3959b0;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(57, 89, 176, 0.12);
        }

        .forgot-input-wrapper.has-error input {
            border-color: #c83532;
        }

        .forgot-error {
            margin: 0;
            color: #c83532;
            font-size: 11px;
        }

        .forgot-status {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            margin-bottom: 14px;
            padding: 10px 12px;
            border: 1px solid #a7f3d0;
            border-radius: 8px;
            background: #ecfdf5;
            color: #166534;
            font-size: 11px;
            line-height: 1.5;
        }

        .forgot-status .material-symbols-outlined {
            font-size: 17px;
        }

        .forgot-status p {
            margin: 0;
        }

        .forgot-submit {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-height: 40px;
            padding: 10px 16px;
            border: 0;
            border-radius: 8px;
            background: #001a4d;
            box-shadow: 0 2px 8px rgba(0, 26, 77, 0.16);
            color: #fff;
            cursor: pointer;
            font-family: inherit;
            font-size: 12px;
            font-weight: 600;
            transition: background 0.2s ease, box-shadow 0.2s ease;
        }

        .forgot-submit:hover {
            background: #184290;
            box-shadow: 0 4px 12px rgba(0, 26, 77, 0.2);
        }

        .forgot-submit .material-symbols-outlined,
        .forgot-navigation-link .material-symbols-outlined {
            font-size: 15px;
        }

        .forgot-navigation {
            display: flex;
            justify-content: center;
            gap: 22px;
            margin-top: 12px;
        }

        .forgot-navigation-link {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            color: #3959b0;
            font-size: 11px;
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .forgot-navigation-link:hover {
            color: #001a4d;
        }

        @keyframes forgot-card-appear {
            from { opacity: 0; transform: translateY(18px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 640px) {
            .forgot-password-card {
                border-radius: 12px;
            }

            .forgot-form-panel {
                padding: 26px 22px 20px;
            }

            .forgot-navigation {
                flex-wrap: wrap;
                gap: 12px 20px;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .forgot-password-card {
                animation: none;
            }
        }
    </style>
@endpush
