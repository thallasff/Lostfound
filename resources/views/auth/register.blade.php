@extends('layouts.guest')

@section('content')
<x-auth-card>

    <form method="POST" action="{{ route('register') }}" id="formRegister">
        @csrf

        {{-- Username --}}
        <div class="mt-4">
            <x-input-label for="username" :value="__('Username')" />
            <x-text-input id="username" class="block mt-1 w-full"
                type="text" name="username" required autofocus oninput="checkUsername()" />
            <p id="usernameFeedback" class="text-sm mt-1 invisible"></p>
            <x-input-error :messages="$errors->get('username')" class="mb-3" />
        </div>

        {{-- Email --}}
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" required />
            <x-input-error :messages="$errors->get('email')" class="mb-3" />
        </div>

{{-- Password --}}
<div class="mt-4 relative">
    <x-input-label for="password" :value="__('Password')" />

    <div class="relative">
        <x-text-input id="password" class="block mt-1 w-full pr-10"
            type="password" name="password" required
            autocomplete="new-password" oninput="validatePassword()" />

        {{-- Tombol Mata --}}
        <span id="togglePass" onclick="toggleRegisterPass()"
            class="absolute inset-y-0 right-3 top-1/2 -translate-y-1/2 cursor-pointer text-gray-600 select-none">
            👁️
        </span>
    </div>

    <ul class="text-sm mt-2" id="pwRules">
        <li id="ruleLength" class="text-orange-500">• Minimal 8 karakter</li>
        <li id="ruleUpper" class="text-orange-500">• Ada huruf kapital</li>
        <li id="ruleLower" class="text-orange-500">• Ada huruf kecil</li>
        <li id="ruleNumber" class="text-orange-500">• Ada angka</li>
    </ul>

    <x-input-error :messages="$errors->get('password')" class="mb-3" />
</div>


{{-- Confirm Password --}}
<div class="mt-4 relative">
    <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

    <div class="relative">
        <x-text-input id="password_confirmation" class="block mt-1 w-full pr-10"
            type="password" name="password_confirmation" required oninput="checkMatch()" />

        {{-- Tombol Mata --}}
        <span id="toggleConfirmPass" onclick="toggleConfirmPass()"
            class="absolute inset-y-0 right-3 top-1/2 -translate-y-1/2 cursor-pointer text-gray-600 select-none">
            👁️
        </span>
    </div>

    <p id="matchStatus" class="text-sm mt-2 text-orange-500">• Password belum sama</p>

    <x-input-error :messages="$errors->get('password_confirmation')" class="mb-3" />
</div>

        {{-- Button --}}
        <x-primary-button class="mt-4 w-full" id="registerBtn" disabled
            x-bind:class="{'opacity-50 cursor-not-allowed': $el.disabled}">
        {{ __('Register') }}
        </x-primary-button>


        {{-- Link ke login --}}
        <p class="text-center text-sm text-slate-400 mt-4">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="underline text-orange-500 hover:text-orange-300">Login</a>
        </p>

    </form>

    {{-- Pop-up jika gagal --}}
    @if ($errors->any())
    <script>
        alert("Gagal register:\n{{ implode('\n', $errors->all()) }}");
    </script>
    @endif

</x-auth-card>

{{-- ====================== JS VALIDATION ====================== --}}
<script>
let usernameValid = false;
let passwordValid = false;
let matchValid = false;

// ====== CHECK USERNAME REALTIME ======
function checkUsername() {
    const username = document.getElementById("username").value;
    const feedback = document.getElementById("usernameFeedback");

    if (username.length < 3) {
        feedback.textContent = "Minimal 3 karakter";
        feedback.className = "text-sm mt-1 text-orange-500";
        usernameValid = false;
        updateSubmit();
        return;
    }

    fetch("/check-username?username=" + username)
        .then(res => res.json())
        .then(data => {
            if (data.exists) {
                feedback.textContent = "Username sudah digunakan";
                feedback.className = "text-sm mt-1 text-red-500";
                usernameValid = false;
            } else {
                feedback.textContent = "Username tersedia ✓";
                feedback.className = "text-sm mt-1 text-green-500";
                usernameValid = true;
            }
            updateSubmit();
        });

    feedback.classList.remove("invisible");
}

// ===== PASSWORD RULE CHECK =====
function validatePassword() {
    const pw = document.getElementById("password").value;

    checkRule(pw.length >= 8, "ruleLength");
    checkRule(/[A-Z]/.test(pw), "ruleUpper");
    checkRule(/[a-z]/.test(pw), "ruleLower");
    checkRule(/[0-9]/.test(pw), "ruleNumber");

    passwordValid = (
        pw.length >= 8 &&
        /[A-Z]/.test(pw) &&
        /[a-z]/.test(pw) &&
        /[0-9]/.test(pw)
    );

    checkMatch();
}

function checkRule(condition, id) {
    const el = document.getElementById(id);
    if (condition) el.className = "text-green-500";
    else el.className = "text-orange-500";
}

// ===== CHECK PASSWORD MATCH =====
function checkMatch() {
    const pw = document.getElementById("password").value;
    const confirm = document.getElementById("password_confirmation").value;
    const status = document.getElementById("matchStatus");

    if (pw === confirm && confirm !== "") {
        status.textContent = "✓ Password cocok";
        status.className = "text-sm mt-2 text-green-500";
        matchValid = true;
    } else {
        status.textContent = "✗ Password tidak cocok";
        status.className = "text-sm mt-2 text-orange-500";
        matchValid = false;
    }

    updateSubmit();
}

// ===== ENABLE/DISABLE BUTTON =====
function updateSubmit() {
    document.getElementById("registerBtn").disabled = !(usernameValid && passwordValid && matchValid);
}

// ==== Toggle Password ==== //
function toggleRegisterPass() {
    const pw = document.getElementById("password");
    const btn = document.getElementById("togglePass");
    if (pw.type === "password") {
        pw.type = "text";
        btn.textContent = "🙈";
    } else {
        pw.type = "password";
        btn.textContent = "👁️";
    }
}

function toggleConfirmPass() {
    const pw = document.getElementById("password_confirmation");
    const btn = document.getElementById("toggleConfirmPass");
    if (pw.type === "password") {
        pw.type = "text";
        btn.textContent = "🙈";
    } else {
        pw.type = "password";
        btn.textContent = "👁️";
    }
}


</script>

@endsection
