<x-layouts.admin title="Users">
    <x-slot name="header">Users</x-slot>

    <form method="GET" class="mt-4 flex gap-2">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Search name, email or phone"
               class="flex-1 max-w-md rounded-lg border-gray-300 text-sm focus:border-black focus:ring-black">
        <select name="role" class="rounded-lg border-gray-300 text-sm focus:border-black focus:ring-black">
            <option value="">All roles</option>
            @foreach (['farmer', 'buyer', 'admin'] as $role)
                <option value="{{ $role }}" @selected(request('role') === $role)>{{ $role === 'farmer' ? 'Seller' : ucfirst($role) }}</option>
            @endforeach
        </select>
        <button class="px-4 py-2 bg-black text-white text-sm font-semibold rounded-lg">Search</button>
    </form>

    <div class="mt-4 bg-white border border-gray-200 rounded-lg divide-y divide-gray-100">
        @foreach ($users as $user)
            <div class="flex flex-col sm:flex-row sm:items-center gap-3 p-4">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-900 truncate">
                        {{ $user->name }}
                        @if ($user->isFarmer() && $user->isVerifiedFarmer())
                            <span class="ml-1 text-xs text-green-700 font-semibold">✓ verified</span>
                        @elseif ($user->isFarmer())
                            <span class="ml-1 text-xs text-yellow-700 font-semibold">pending</span>
                        @endif
                        @if ($user->status === 'suspended')
                            <span class="ml-1 text-xs text-red-600 font-semibold">suspended</span>
                        @endif
                    </p>
                    <p class="text-xs text-gray-500">{{ $user->email }} · {{ $user->phone ?: 'no phone' }} · {{ $user->role === 'farmer' ? 'Seller' : ucfirst($user->role) }} · joined {{ $user->created_at->format('M j, Y') }}</p>
                </div>
                <div class="flex flex-wrap gap-2 shrink-0">
                    @if ($user->isFarmer() && ! $user->isVerifiedFarmer())
                        <form method="POST" action="{{ route('admin.users.verify', $user) }}">
                            @csrf
                            <button class="px-3 py-1.5 text-xs font-semibold bg-black text-white rounded-lg hover:bg-gray-800">Verify</button>
                        </form>
                    @endif
                    @if ($user->status === 'active' && ! $user->isAdmin())
                        <form method="POST" action="{{ route('admin.users.suspend', $user) }}" onsubmit="return confirm('Suspend {{ addslashes($user->name) }}? They will be signed out and blocked from logging in.')">
                            @csrf
                            <button class="px-3 py-1.5 text-xs font-semibold border border-red-300 text-red-600 rounded-lg hover:bg-red-50">Suspend</button>
                        </form>
                    @elseif ($user->status === 'suspended')
                        <form method="POST" action="{{ route('admin.users.activate', $user) }}">
                            @csrf
                            <button class="px-3 py-1.5 text-xs font-semibold border border-green-300 text-green-700 rounded-lg hover:bg-green-50">Reactivate</button>
                        </form>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-6">{{ $users->links() }}</div>
</x-layouts.admin>
