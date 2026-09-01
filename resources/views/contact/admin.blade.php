@extends('layouts.main')

@section('title', 'Contact Administrator | Anugerah Penyelidikan')

@section('content')
<div class="w-full max-w-2xl">
    <!-- Headline and Subtext -->
    <div class="text-center mb-8">
        <h1 class="text-gray-900 text-3xl font-bold tracking-tight mb-3">Contact System Administrator</h1>
        <p class="text-gray-600 text-base">Get in touch with the system administrator for support and access requests.</p>
    </div>

    <!-- Contact Card -->
    <div class="bg-white rounded-xl shadow-xl border border-gray-100 overflow-hidden">
        <!-- Visual Header for Card -->
        <div class="h-32 bg-primary relative overflow-hidden flex items-center justify-center">
            <div class="absolute inset-0 opacity-10 bg-[radial-gradient(circle_at_center,_var(--tw-gradient-stops))] from-white via-transparent to-transparent"></div>
            <span class="material-symbols-outlined text-white text-5xl">support_agent</span>
        </div>

        <div class="p-8">
            <!-- Contact Information -->
            <div class="space-y-6">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center">
                        <span class="material-symbols-outlined text-primary">email</span>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900 mb-1">Email Support</h3>
                        <p class="text-gray-600">admin@utem.edu.my</p>
                        <p class="text-sm text-gray-500 mt-1">For general inquiries and technical support</p>
                    </div>
                </div>

                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center">
                        <span class="material-symbols-outlined text-primary">phone</span>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900 mb-1">Phone Support</h3>
                        <p class="text-gray-600">+60 6-270 4000</p>
                        <p class="text-sm text-gray-500 mt-1">Monday - Friday, 8:30 AM - 5:30 PM</p>
                    </div>
                </div>

                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center">
                        <span class="material-symbols-outlined text-primary">location_on</span>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900 mb-1">Office Location</h3>
                        <p class="text-gray-600">Research Management Center</p>
                        <p class="text-gray-600">Universiti Teknikal Malaysia Melaka</p>
                        <p class="text-sm text-gray-500 mt-1">Hang Tuah Jaya, 76100 Durian Tunggal, Melaka</p>
                    </div>
                </div>
            </div>

            <!-- Request Form -->
            <div class="mt-8 pt-8 border-t border-gray-100">
                <h3 class="font-semibold text-gray-900 mb-4">Submit Access Request</h3>
                <form action="#" class="space-y-4" method="POST">
                    @csrf
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2" for="name">
                            Full Name
                        </label>
                        <input 
                            class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all" 
                            id="name" 
                            name="name" 
                            placeholder="Enter your full name" 
                            required 
                            type="text"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2" for="email">
                            Email Address
                        </label>
                        <input 
                            class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all" 
                            id="email" 
                            name="email" 
                            placeholder="Enter your email address" 
                            required 
                            type="email"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2" for="department">
                            Department/Faculty
                        </label>
                        <input 
                            class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all" 
                            id="department" 
                            name="department" 
                            placeholder="Enter your department or faculty" 
                            required 
                            type="text"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2" for="message">
                            Request Details
                        </label>
                        <textarea 
                            class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all" 
                            id="message" 
                            name="message" 
                            placeholder="Describe your access request or support needs..." 
                            required 
                            rows="4"
                        ></textarea>
                    </div>

                    <button 
                        class="w-full bg-primary hover:bg-primary/90 text-white font-bold py-3.5 px-4 rounded-lg transition-all shadow-md active:transform active:scale-[0.98] flex items-center justify-center gap-2" 
                        type="submit"
                    >
                        <span class="material-symbols-outlined text-xl">send</span>
                        Submit Request
                    </button>
                </form>
            </div>

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
