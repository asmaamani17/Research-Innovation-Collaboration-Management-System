@extends('layouts.main')

@section('title', 'Reset Password | Research, Innovation & Collaboration Management')

@section('content')
    <div class="reset-password-page">
        <section class="reset-password-card" aria-labelledby="reset-password-title">
            <header class="reset-password-header">
                <h1 id="reset-password-title">Choose a new password</h1>
                <p>Enter your account email and choose a new password of at least 12 characters. A memorable passphrase works well.</p>
            </header>

            @if ($errors->any())
                <div class="reset-message reset-message-error" role="alert">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.store') }}" class="reset-password-form">
                @csrf
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <div class="reset-field">
                    <label for="email">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}"
                        required autofocus autocomplete="email" class="@error('email') has-error @enderror">
                    @error('email')
                        <p class="reset-field-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="reset-field">
                    <label for="password">New password</label>
                    <input id="password" type="password" name="password" required minlength="12" autocomplete="new-password"
                        class="@error('password') has-error @enderror">
                    @error('password')
                        <p class="reset-field-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="reset-field">
                    <label for="password_confirmation">Confirm new password</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required
                        autocomplete="new-password">
                </div>

                <button type="submit" class="reset-submit">Reset password</button>
            </form>

            <a class="reset-back-link" href="{{ route('login') }}">Back to login</a>
        </section>
    </div>
@endsection

@push('styles')
    <style>
        .reset-password-page {
            width: 100%;
            max-width: 480px;
        }

        .reset-password-card {
            padding: 32px;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            background: #fff;
            box-shadow: 0 18px 48px rgba(0, 15, 48, 0.12);
        }

        .reset-password-header {
            margin-bottom: 24px;
        }

        .reset-password-header h1 {
            margin: 0 0 8px;
            color: #001a4d;
            font-size: 24px;
            font-weight: 600;
        }

        .reset-password-header p {
            margin: 0;
            color: #666b78;
            font-size: 14px;
            line-height: 1.5;
        }

        .reset-password-form {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .reset-field {
            display: flex;
            flex-direction: column;
            gap: 7px;
        }

        .reset-field label {
            color: #001a4d;
            font-size: 13px;
            font-weight: 600;
        }

        .reset-field input {
            width: 100%;
            min-height: 42px;
            padding: 10px 12px;
            border: 1px solid #cbd0da;
            border-radius: 8px;
            outline: none;
            color: #191c1e;
            font: inherit;
        }

        .reset-field input:focus {
            border-color: #3959b0;
            box-shadow: 0 0 0 3px rgba(57, 89, 176, 0.12);
        }

        .reset-field input.has-error {
            border-color: #c83532;
        }

        .reset-field-error {
            margin: 0;
            color: #b42318;
            font-size: 12px;
        }

        .reset-message {
            margin-bottom: 18px;
            padding: 12px;
            border-radius: 8px;
            font-size: 13px;
            line-height: 1.5;
        }

        .reset-message-error {
            border: 1px solid #fecaca;
            background: #fef2f2;
            color: #991b1b;
        }

        .reset-submit {
            min-height: 44px;
            padding: 11px 16px;
            border: 0;
            border-radius: 8px;
            background: #001a4d;
            color: #fff;
            cursor: pointer;
            font: inherit;
            font-weight: 600;
        }

        .reset-submit:hover {
            background: #184290;
        }

        .reset-back-link {
            display: block;
            margin-top: 20px;
            color: #3959b0;
            font-size: 13px;
            text-align: center;
            text-decoration: none;
        }

        .reset-back-link:hover {
            text-decoration: underline;
        }

        @media (max-width: 480px) {
            .reset-password-card {
                padding: 24px 20px;
            }
        }
    </style>
@endpush
