<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ __('Produce') }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-end mb-4">
                <a href="{{ route('produce.create') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded">+ New Produce</a>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if(session('success'))<div class="mb-4 text-green-600">{{ session('success') }}</div>@endif

                    @if($produce->count())
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                    <th class="px-6 py-3"></th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($produce as $item)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $item->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $item->category }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ Str::limit($item->description, 80) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <a href="{{ route('produce.edit', $item) }}" class="text-primary-600 me-2">Edit</a>
                                            <form action="{{ route('produce.destroy', $item) }}" method="POST" class="inline">@csrf @method('DELETE')<button class="text-red-600" onclick="return confirm('Delete produce?')">Delete</button></form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="mt-4">{{ $produce->links() }}</div>
                    @else
                        <div class="text-center text-gray-400">No produce yet. Click <a href="{{ route('produce.create') }}" class="text-primary-600">New Produce</a> to get started.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
