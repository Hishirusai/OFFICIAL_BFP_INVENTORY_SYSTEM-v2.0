<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - BFP IMS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-screen flex items-center justify-center relative overflow-hidden bg-gray-900">

    <img src="/images/bfp-background.png" alt="Background" 
         class="absolute inset-0 w-full h-full object-cover">

    <div class="absolute inset-0 bg-black/40"></div>

    <div class="relative z-10 bg-black/50 p-10 rounded-3xl shadow-2xl w-full max-w-lg border border-white/10 backdrop-blur-md">
        
        <div class="flex justify-center space-x-8 mb-6">
            
            <div class="w-36 h-36 bg-white rounded-full overflow-hidden border-2 border-red-700 shadow-lg">
                <img src="/images/bfp-logo.png" alt="BFP Main Logo" class="w-full h-full object-cover">
            </div>

            <div class="w-36 h-36 bg-white rounded-full overflow-hidden border-2 border-orange-500 shadow-lg">
                <img src="/images/district-logo.png" alt="District Logo" class="w-full h-full object-cover">
            </div>
        </div>

        <div class="text-center mb-8">
            <h1 class="text-2xl font-extrabold text-white tracking-wider uppercase leading-tight drop-shadow-md">
                BFP Inventory Management System
            </h1>
            <p class="text-gray-200 text-sm mt-2 tracking-wide font-medium">Bureau of Fire Protection</p>
            
            <div class="mt-4">
                <span class="bg-orange-600/80 text-white border border-orange-400 px-5 py-1 rounded-full text-xs font-bold tracking-widest uppercase shadow-lg">
                    Admin Login
                </span>
            </div>
        </div>

        @if ($errors->any() && !$errors->has('email') && !$errors->has('password'))
            <div class="bg-red-900/60 border border-red-500 text-white p-3 rounded-lg mb-6 text-sm text-center backdrop-blur-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST">
            @csrf
            
            <div class="mb-5">
                <label class="block text-gray-300 font-bold mb-2 text-xs uppercase tracking-wider">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}"
                       class="w-full px-5 py-3 bg-white/10 border border-white/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent text-white placeholder-gray-400 transition shadow-inner backdrop-blur-sm"
                       placeholder="Enter your email">
                
                @error('email')
                    <p class="text-red-400 text-xs italic mt-2 font-bold shadow-black drop-shadow-md">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div class="mb-8">
                <label class="block text-gray-300 font-bold mb-2 text-xs uppercase tracking-wider">Password</label>
                <input type="password" name="password" 
                       class="w-full px-5 py-3 bg-white/10 border border-white/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent text-white placeholder-gray-400 transition shadow-inner backdrop-blur-sm"
                       placeholder="Enter your password">

                @error('password')
                    <p class="text-red-400 text-xs italic mt-2 font-bold shadow-black drop-shadow-md">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <button type="submit" 
                    class="w-full bg-gradient-to-r from-orange-600 to-red-700 hover:from-orange-500 hover:to-red-600 text-white font-extrabold py-4 rounded-xl transition duration-300 transform hover:scale-[1.02] shadow-xl tracking-widest uppercase border border-white/10">
                Login Access
            </button>
        </form>
        
        <div class="mt-8 text-center">
            <p class="text-[10px] text-gray-400 uppercase tracking-widest font-semibold">Authorized Personnel Only</p>
        </div>
    </div>

</body>
</html>