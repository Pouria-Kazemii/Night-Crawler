<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('messages.Dashboard') }}
        </h2>
    </x-slot>

    <!-- You're logged in card -->
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("messages.You're logged in!") }}
                </div>
            </div>
        </div>
    </div>

    <!-- Crawl Node Buttons -->
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h2 class="text-lg font-bold text-black mb-4">{{ __("لیست پروکسی‌ها") }}</h2>

                    <div class="flex flex-wrap gap-4">
                        <a href="{{ route('crawl-nodes.index') }}"
                           class="inline-block px-4 py-2 bg-red-600 text-white font-semibold rounded hover:bg-red-700 transition">
                            مشاهده لیست
                        </a>

                        <a href="{{ route('crawl-nodes.create') }}"
                           class="inline-block px-4 py-2 bg-red-600 text-white font-semibold rounded hover:bg-red-700 transition">
                            افزودن
                        </a>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h2 class="text-lg font-bold text-black mb-4">{{ __("لیست خزشگرها") }}</h2>

                    <div class="flex flex-wrap gap-4">
                        <a href="{{ route('crawler.index') }}"
                           class="inline-block px-4 py-2 bg-indigo-600 text-white font-semibold rounded hover:bg-indigo-700 transition">
                            مشاهده لیست
                        </a>

                        <a href="{{ route('crawler.create') }}"
                           class="inline-block px-4 py-2 bg-indigo-600 text-white font-semibold rounded hover:bg-indigo-700 transition">
                            افزودن
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
