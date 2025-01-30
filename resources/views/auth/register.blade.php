<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <!-- Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex items-center justify-center min-h-screen bg-gray-100">

<div class="w-full max-w-md bg-white p-8 shadow-lg rounded-lg">
    <h2 class="text-2xl font-bold text-center text-gray-700 mb-6">Register</h2>

    @if(session('success'))
        <p class="text-green-500 text-center">{{ session('success') }}</p>
    @endif

    <form action="{{ route('register') }}" method="POST" class="space-y-4">
        @csrf

        <!-- Name -->
        <div>
            <label class="block text-gray-600">Name:</label>
            <input type="text" name="name" required value="{{ old('name') }}"
                   class="w-full px-4 py-2 border rounded-lg focus:ring focus:ring-blue-200">
            @error('name')
            <p class="text-red-500 text-sm">{{ $message }}</p>
            @enderror
        </div>

        <!-- Email -->
        <div>
            <label class="block text-gray-600">Email:</label>
            <input type="email" name="email" required value="{{ old('email') }}"
                   class="w-full px-4 py-2 border rounded-lg focus:ring focus:ring-blue-200">
            @error('email')
            <p class="text-red-500 text-sm">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div>
            <label class="block text-gray-600">Password:</label>
            <input type="password" name="password" required
                   class="w-full px-4 py-2 border rounded-lg focus:ring focus:ring-blue-200">
            @error('password')
            <p class="text-red-500 text-sm">{{ $message }}</p>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div>
            <label class="block text-gray-600">Confirm Password:</label>
            <input type="password" name="password_confirmation" required
                   class="w-full px-4 py-2 border rounded-lg focus:ring focus:ring-blue-200">
        </div>

        <!-- Submit Button -->
        <button type="submit"
                class="w-full bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600 transition">
            Register
        </button>
    </form>
</div>

</body>
</html>
