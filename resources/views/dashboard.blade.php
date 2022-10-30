<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>
    <script src="https://cdn.tailwindcss.com"></script>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="mt-3">
                        <ul class="grid grid-cols-1 divide-y divide-gray-200">
                            @foreach($locations as $location)
                            <li class="py-3">
                                <div class="font-bold">{{ $location->place_name }}</div>
                                <div class="text-gray-500">{{ $location->address_name }}</div>
                                <div class="text-gray-900 mt-2">{{ $location->memo }}</div>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
