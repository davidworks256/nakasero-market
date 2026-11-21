<x-layouts.dashboard title="Account Profile">
    <div class="space-y-6 max-w-3xl">
        <div class="p-4 sm:p-6 bg-white border border-gray-200 rounded-xl shadow-sm">
            <div class="max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <div class="p-4 sm:p-6 bg-white border border-gray-200 rounded-xl shadow-sm">
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <div class="p-4 sm:p-6 bg-white border border-gray-200 rounded-xl shadow-sm">
            <div class="max-w-xl">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-layouts.dashboard>
