@extends('layouts.guest')

@section('content')
<div class="w-full max-w-md bg-white/90 backdrop-blur-md shadow-lg rounded-xl p-8">
    <h2 class="text-2xl font-bold text-center font-staatliches text-gray-800 mb-6">ADMIN LOGIN</h2>

    @if($errors->any())
        <div class="text-red-600 text-sm text-center mb-4">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="relative mb-6">
            <input type="email" name="email" required placeholder="Email" 
                class="w-full border-b border-gray-300 bg-transparent py-2 pr-10 text-gray-700 placeholder-gray-400 focus:outline-none focus:border-green-500" />
            <i class="fas fa-user absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
        </div>

        <div class="relative mb-6">
            <input type="password" name="password" required placeholder="Password" 
                class="w-full border-b border-gray-300 bg-transparent py-2 pr-10 text-gray-700 placeholder-gray-400 focus:outline-none focus:border-green-500" />
            <i class="fas fa-lock absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
        </div>

        <button type="submit"
            class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2 rounded-full transition duration-300">
            Login
        </button>
    </form>
</div>
@endsection
