<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ __('Units') }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-end mb-4">
                <a href="{{ route('units.create') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded">+ Add Unit</a>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if(session('success'))<div class="mb-4 text-green-600">{{ session('success') }}</div>@endif

                    @if($units->count())
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th class="px-6 py-3"></th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($units as $unit)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $unit->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <a href="{{ route('units.edit', $unit) }}" class="text-primary-600 me-2">Edit</a>
                                            <form action="{{ route('units.destroy', $unit) }}" method="POST" class="inline">@csrf @method('DELETE')<button class="text-red-600" onclick="return confirm('Delete unit?')">Delete</button></form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="mt-4">{{ $units->links() }}</div>
                    @else
                        <div class="text-center text-gray-400">No units yet. Click <a href="{{ route('units.create') }}" class="text-primary-600">Add Unit</a> to get started.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
