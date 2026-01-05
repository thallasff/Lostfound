@props(['disabled' => false])

<input {{ $attributes->merge([
    'class' => 'border-gray-300 focus:border-orange-500 focus:ring-orange-500 rounded-md shadow-sm text-gray-900 focus:placeholder-orange-500'
]) }}>
