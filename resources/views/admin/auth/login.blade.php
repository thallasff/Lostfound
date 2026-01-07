<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-50 flex items-center justify-center px-4">

<div class="w-full max-w-md bg-white rounded-2xl shadow p-6 border">
    <h1 class="text-xl font-semibold">Login Admin</h1>
    <p class="text-sm text-gray-500 mt-1">Masuk ke panel /admin</p>

    <form method="POST" action="{{ route('admin.login.submit') }}" class="mt-6 space-y-4">
        @csrf

        <div>
            <label class="text-sm font-medium">Username</label>
            <input name="username" value="{{ old('username') }}"
                   class="mt-1 w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring"
                   required autofocus>
            @error('username') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="text-sm font-medium">Password</label>
            <input type="password" name="password"
                   class="mt-1 w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring"
                   required>
            @error('password') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <button class="w-full bg-orange-500 hover:bg-orange-600 text-white rounded-lg py-2 font-semibold">
            Login
        </button>
    </form>
</div>

</body>
</html>
