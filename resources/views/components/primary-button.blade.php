<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-black border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-gray-800 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-black focus:ring-offset-2 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
