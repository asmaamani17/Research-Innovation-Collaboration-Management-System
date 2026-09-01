@extends('layouts.main')

@section('title', 'Reset Password | Anugerah Penyelidikan')

@section('content')
<div class="w-full max-w-[480px]">
    <!-- Headline and Subtext -->
    <div class="text-center mb-8">
        <h1 class="text-gray-900 text-3xl font-bold tracking-tight mb-3">Reset Password</h1>
        <p class="text-gray-600 text-base">Enter your email address to receive password reset instructions.</p>
    </div>

    <!-- Reset Card -->
    <div class="bg-white rounded-xl shadow-xl border border-gray-100 overflow-hidden">
        <!-- Visual Header for Card -->
        <div class="h-32 bg-primary relative overflow-hidden flex items-center justify-center">
            <div class="absolute inset-0 opacity-10 bg-[radial-gradient(circle_at_center,_var(--tw-gradient-stops))] from-white via-transparent to-transparent"></div>
            <span class="material-symbols-outlined text-white text-5xl">lock_reset</span>
        </div>

        <div class="p-8">
            <form action="#" class="space-y-6" method="POST">
                @csrf

                <!-- Email Field -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2" for="email">
                        Email Address
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                            <span class="material-symbols-outlined">email</span>
                        </span>
                        <input 
                            class="w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all" 
                            id="email" 
                            name="email" 
                            placeholder="Enter your email address" 
                            required 
                            type="email"
                        >
                    </div>
                </div>

                <!-- Submit Button -->
                <button 
                    class="w-full bg-primary hover:bg-primary/90 text-white font-bold py-3.5 px-4 rounded-lg transition-all shadow-md active:transform active:scale-[0.98] flex items-center justify-center gap-2" 
                    type="submit"
                >
                    <span class="material-symbols-outlined text-xl">send</span>
                    Send Reset Link
                </button>
            </form>

            <!-- Back to Login -->
            <div class="mt-6 text-center">
                <a class="text-primary text-sm font-medium hover:underline" href="{{ route('login') }}">
                    ← Back to Login
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
