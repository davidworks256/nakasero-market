<x-guest-layout title="Create your account">
    <x-slot:subtitle>Join Nakasero Market as a buyer or seller.</x-slot:subtitle>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <div>
            <x-input-label for="name" value="Full name" />
            <x-text-input id="name" class="mt-1" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="e.g. Sarah Nabbanja" />
            <x-input-error :messages="$errors->get('name')" class="mt-1" />
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <x-input-label for="email" value="Email" />
                <x-text-input id="email" class="mt-1" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="you@example.com" />
                <x-input-error :messages="$errors->get('email')" class="mt-1" />
            </div>
            <div>
                <x-input-label for="phone" value="Phone number" />
                <x-text-input id="phone" class="mt-1" type="tel" name="phone" :value="old('phone')" autocomplete="tel" placeholder="07XX XXX XXX" />
                <x-input-error :messages="$errors->get('phone')" class="mt-1" />
            </div>
        </div>

        <div x-data="{ role: @js(old('role', request('role') === 'farmer' ? 'farmer' : 'buyer')) }">
            <x-input-label value="I want to" />
            <div class="mt-1 grid grid-cols-2 gap-3">
                <label class="flex items-center gap-2 rounded-lg border px-4 py-3 cursor-pointer transition"
                       :class="role === 'buyer' ? 'border-black bg-gray-100 ring-1 ring-black' : 'border-gray-300 hover:border-gray-400'">
                    <input type="radio" name="role" value="buyer" x-model="role" class="accent-black">
                    <span class="text-sm font-medium text-gray-900">Buy produce</span>
                </label>
                <label class="flex items-center gap-2 rounded-lg border px-4 py-3 cursor-pointer transition"
                       :class="role === 'farmer' ? 'border-black bg-gray-100 ring-1 ring-black' : 'border-gray-300 hover:border-gray-400'">
                    <input type="radio" name="role" value="farmer" x-model="role" class="accent-black">
                    <span class="text-sm font-medium text-gray-900">Sell my produce</span>
                </label>
            </div>
            <x-input-error :messages="$errors->get('role')" class="mt-1" />

            <div x-show="role === 'farmer'" x-cloak class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="stall" value="Market stall" />
                    <x-text-input id="stall" class="mt-1" type="text" name="stall" :value="old('stall')" placeholder="e.g. Shed 3, Stall 12" />
                    <x-input-error :messages="$errors->get('stall')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="location" value="Location" />
                    <x-text-input id="location" class="mt-1" type="text" name="location" :value="old('location')" placeholder="e.g. Nakasero, Bugolobi" />
                    <x-input-error :messages="$errors->get('location')" class="mt-1" />
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <x-input-label for="password" value="Password" />
                <x-text-input id="password" class="mt-1" type="password" name="password" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-1" />
            </div>
            <div>
                <x-input-label for="password_confirmation" value="Confirm password" />
                <x-text-input id="password_confirmation" class="mt-1" type="password" name="password_confirmation" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
            </div>
        </div>

        <div>
            <label class="flex items-start gap-2 text-sm text-gray-600 cursor-pointer">
                <input type="checkbox" name="consent" value="1" @checked(old('consent')) class="mt-0.5 h-4 w-4 rounded border-gray-300 accent-black">
                <span>I agree that my name, phone and market stall may be shared with people I contact or order from, as described in the <a href="{{ route('privacy') }}" class="underline hover:text-gray-900">privacy notice</a>.</span>
            </label>
            <x-input-error :messages="$errors->get('consent')" class="mt-1" />
        </div>

        <div class="flex items-center justify-between pt-2">
            <a class="text-sm text-gray-600 underline hover:text-gray-900" href="{{ route('login') }}">Already registered?</a>
            <x-primary-button>{{ __('Register') }}</x-primary-button>
        </div>
    </form>
</x-guest-layout>
