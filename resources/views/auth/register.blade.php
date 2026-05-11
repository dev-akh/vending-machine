@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 via-purple-50 to-pink-50 py-12 px-4 sm:px-6 lg:px-8">
    <!-- Background Elements -->
    <div class="absolute inset-0 overflow-hidden">
        <div class="absolute -top-10 -right-10 w-40 h-40 bg-blue-300 rounded-full opacity-20 blur-xl"></div>
        <div class="absolute -bottom-10 -left-10 w-60 h-60 bg-purple-300 rounded-full opacity-20 blur-xl"></div>
        <div class="absolute top-1/3 right-1/3 w-32 h-32 bg-pink-300 rounded-full opacity-10 blur-2xl"></div>
    </div>

    <div class="relative z-10 max-w-md w-full">
        <!-- Glass Card -->
        <div class="backdrop-blur-xl bg-white/80 shadow-2xl rounded-2xl border border-white/20 p-8">
            <!-- Logo Section -->
            <div class="text-center mb-8">
                <div class="mx-auto h-16 w-16 flex items-center justify-center rounded-2xl gradient-bg shadow-lg transform hover:scale-105 transition-transform duration-300">
                    <i class="fas fa-user-plus text-white text-2xl"></i>
                </div>
                <h2 class="mt-6 text-3xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                    Create Account
                </h2>
                <p class="mt-2 text-gray-600">
                    Join the vending machine management system
                </p>
            </div>

            <!-- Session Messages -->
            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <strong>Registration Failed</strong>
                    </div>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Registration Form -->
            <form class="space-y-6" method="POST" action="{{ route('register') }}">
                @csrf
                
                <!-- Name Field -->
                <div class="group">
                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-user mr-2 text-blue-500"></i>Full Name
                    </label>
                    <div class="relative">
                        <input id="name" 
                               name="name" 
                               type="text" 
                               autocomplete="name" 
                               required 
                               value="{{ old('name') }}"
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 bg-white/50 backdrop-blur-sm"
                               placeholder="John Doe">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                            <i class="fas fa-user text-gray-400 group-focus-within:text-blue-500 transition-colors"></i>
                        </div>
                    </div>
                </div>

                <!-- Email Field -->
                <div class="group">
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-envelope mr-2 text-blue-500"></i>Email Address
                    </label>
                    <div class="relative">
                        <input id="email" 
                               name="email" 
                               type="email" 
                               autocomplete="email" 
                               required 
                               value="{{ old('email') }}"
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 bg-white/50 backdrop-blur-sm"
                               placeholder="you@example.com">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                            <i class="fas fa-at text-gray-400 group-focus-within:text-blue-500 transition-colors"></i>
                        </div>
                    </div>
                </div>

                <!-- Password Field -->
                <div class="group">
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-lock mr-2 text-blue-500"></i>Password
                    </label>
                    <div class="relative">
                        <input id="password" 
                               name="password" 
                               type="password" 
                               autocomplete="new-password" 
                               required 
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 bg-white/50 backdrop-blur-sm"
                               placeholder="•••••••••"
                               oninput="checkPasswordStrength()">
                        <button type="button" onclick="togglePassword('password')" class="absolute inset-y-0 right-0 flex items-center pr-3">
                            <i id="passwordToggle" class="fas fa-eye text-gray-400 group-focus-within:text-blue-500 transition-colors cursor-pointer"></i>
                        </button>
                    </div>
                    <!-- Password Strength Indicator -->
                    <div class="mt-2">
                        <div class="flex items-center justify-between text-xs mb-1">
                            <span class="text-gray-600">Password Strength</span>
                            <span id="strengthText" class="font-medium">Enter password</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div id="strengthBar" class="h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                        </div>
                    </div>
                </div>

                <!-- Confirm Password Field -->
                <div class="group">
                    <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-lock mr-2 text-blue-500"></i>Confirm Password
                    </label>
                    <div class="relative">
                        <input id="password_confirmation" 
                               name="password_confirmation" 
                               type="password" 
                               autocomplete="new-password" 
                               required 
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 bg-white/50 backdrop-blur-sm"
                               placeholder="•••••••••"
                               oninput="checkPasswordMatch()">
                        <button type="button" onclick="togglePassword('password_confirmation')" class="absolute inset-y-0 right-0 flex items-center pr-3">
                            <i id="confirmPasswordToggle" class="fas fa-eye text-gray-400 group-focus-within:text-blue-500 transition-colors cursor-pointer"></i>
                        </button>
                    </div>
                    <!-- Match Indicator -->
                    <div id="matchIndicator" class="mt-2 text-xs hidden">
                        <span id="matchText" class="font-medium"></span>
                    </div>
                </div>

                <!-- Terms and Submit -->
                <div class="space-y-4">
                    <div class="flex items-center">
                        <input id="terms" 
                               name="terms" 
                               type="checkbox" 
                               required 
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="terms" class="ml-2 text-sm text-gray-700 cursor-pointer">
                            I agree to the 
                            <a href="#" class="text-blue-600 hover:text-blue-500 font-medium">Terms and Conditions</a>
                        </label>
                    </div>

                    <button type="submit" 
                            class="w-full py-3 px-4 text-white font-semibold rounded-xl gradient-bg shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-300 flex items-center justify-center">
                        <i class="fas fa-user-plus mr-2"></i>
                        Create Account
                    </button>
                </div>
            </form>

            <!-- Divider -->
            <div class="relative my-6">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-200"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-4 bg-white/80 backdrop-blur-xl text-gray-500">Already have an account?</span>
                </div>
            </div>

            <!-- Login Link -->
            <div class="text-center">
                <a href="{{ route('login') }}" 
                   class="text-blue-600 hover:text-blue-500 font-medium transition-colors inline-flex items-center">
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    Sign in instead
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function togglePassword(fieldId) {
    const passwordInput = document.getElementById(fieldId);
    const toggleIcon = document.getElementById(fieldId + 'Toggle');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
    }
}

function checkPasswordStrength() {
    const password = document.getElementById('password').value;
    const strengthBar = document.getElementById('strengthBar');
    const strengthText = document.getElementById('strengthText');
    
    let strength = 0;
    let color = '';
    let text = '';
    
    if (password.length >= 8) strength++;
    if (password.match(/[a-z]+/)) strength++;
    if (password.match(/[A-Z]+/)) strength++;
    if (password.match(/[0-9]+/)) strength++;
    if (password.match(/[$@#&!]+/)) strength++;
    
    switch (strength) {
        case 0:
        case 1:
            color = 'bg-red-500';
            text = 'Weak';
            break;
        case 2:
        case 3:
            color = 'bg-yellow-500';
            text = 'Medium';
            break;
        case 4:
        case 5:
            color = 'bg-green-500';
            text = 'Strong';
            break;
    }
    
    strengthBar.style.width = (strength * 20) + '%';
    strengthBar.className = `h-2 rounded-full transition-all duration-300 ${color}`;
    strengthText.textContent = text;
    strengthText.className = `font-medium ${color.replace('bg-', 'text-')}`;
    
    checkPasswordMatch();
}

function checkPasswordMatch() {
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('password_confirmation').value;
    const matchIndicator = document.getElementById('matchIndicator');
    const matchText = document.getElementById('matchText');
    
    if (confirmPassword.length > 0) {
        matchIndicator.classList.remove('hidden');
        
        if (password === confirmPassword) {
            matchText.textContent = '✓ Passwords match';
            matchText.className = 'font-medium text-green-600';
        } else {
            matchText.textContent = '✗ Passwords do not match';
            matchText.className = 'font-medium text-red-600';
        }
    } else {
        matchIndicator.classList.add('hidden');
    }
}
</script>
@endsection
