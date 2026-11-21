<x-layouts.admin title="Categories">
    <x-slot name="header">Categories</x-slot>

    <div class="mt-6 grid lg:grid-cols-2 gap-6">
        <div class="bg-white border border-gray-200 rounded-lg p-5">
            <h3 class="text-sm font-semibold text-gray-900">Add category</h3>
            <form method="POST" action="{{ route('admin.categories.store') }}" class="mt-3 flex gap-2">
                @csrf
                <x-text-input name="name" placeholder="e.g. Fish" required class="flex-1" />
                <x-primary-button>Add</x-primary-button>
            </form>
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div class="bg-white border border-gray-200 rounded-lg divide-y divide-gray-100">
            @foreach ($categories as $category)
                <form method="POST" action="{{ route('admin.categories.update', $category) }}" class="flex items-center gap-2 p-3">
                    @csrf
                    @method('PATCH')
                    <input type="text" name="name" value="{{ $category->name }}" class="flex-1 rounded-lg border-gray-300 text-sm focus:border-black focus:ring-black">
                    <span class="text-xs text-gray-500 whitespace-nowrap">{{ $category->listings_count }} listings</span>
                    <button class="px-3 py-1.5 text-xs font-semibold border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">Rename</button>
                </form>
                <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm('Delete this category?')" class="px-3 pb-3 -mt-1">
                    @csrf
                    @method('DELETE')
                    <button class="text-xs text-red-600 hover:underline">Delete</button>
                </form>
            @endforeach
        </div>
    </div>

    <div class="mt-6">{{ $categories->links() }}</div>
</x-layouts.admin>
