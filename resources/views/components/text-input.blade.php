@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-black focus:ring-black']) }}>
