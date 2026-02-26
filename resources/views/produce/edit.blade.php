<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Produce</h2>
    </x-slot>
    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="text-gray-700">
                    Edit is not used in this production flow.
                    Continue in
                    <a href="{{ route('produce.index') }}" class="text-primary-600">Produce</a>.
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
