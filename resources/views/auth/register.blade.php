<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
</head>
<body>
<h2>Register</h2>
@if(session('success'))
    <p style="color: green;">{{ session('success') }}</p>
@endif
<form action="{{ route('register') }}" method="POST">
    @csrf
    <label>Name:</label>
    <input type="text" name="name" required>
    @error('name') <p style="color: red;">{{ $message }}</p> @enderror

    <label>Email:</label>
    <input type="email" name="email" required>
    @error('email') <p style="color: red;">{{ $message }}</p> @enderror

    <label>Password:</label>
    <input type="password" name="password" required>
    @error('password') <p style="color: red;">{{ $message }}</p> @enderror

    <label>Confirm Password:</label>
    <input type="password" name="password_confirmation" required>

    <button type="submit">Register</button>
</form>
</body>
</html>
