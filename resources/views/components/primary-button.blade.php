<button {{ $attributes->merge([
    'class' => 'w-full py-2 mt-4 bg-orange-500 hover:bg-orange-600 text-white rounded-lg font-semibold transition'
]) }}>
    {{ $slot }}
</button>
