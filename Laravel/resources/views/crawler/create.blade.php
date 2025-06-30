<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">ایجاد خزنده جدید</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-md rounded-lg p-6" x-data="crawlerForm()" x-init="type = '{{ old('crawler_type') }}'">
                <form method="POST" action="{{ route('crawler.store') }}" class="rtl text-right space-y-4">
                    @csrf

                    <!-- Title -->
                    <div>
                        <label for="title" class="block text-sm font-bold text-gray-700">عنوان</label>
                        <input type="text" name="title" id="title" value="{{ old('title') }}"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">
                        @error('title') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-bold text-gray-700">توضیحات</label>
                        <textarea name="description" id="description"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">{{ old('description') }}</textarea>
                        @error('description') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Crawler Type -->
                    <div>
                        <label for="crawler_type" class="block text-sm font-bold text-gray-700">نوع خزنده</label>
                        <select name="crawler_type" id="crawler_type" x-model="type"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500" required>
                            <option value="">انتخاب کنید</option>
                            @foreach(['static', 'dynamic', 'paginated', 'authenticated', 'api', 'seed'] as $typeOption)
                                <option value="{{ $typeOption }}" @selected(old('crawler_type') === $typeOption)>{{ $typeOption }}</option>
                            @endforeach
                        </select>
                        @error('crawler_type') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Crawler Status -->
                    <div>
                        <label for="crawler_status" class="block text-sm font-bold text-gray-700">وضعیت خزنده</label>
                        <select name="crawler_status" id="crawler_status"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">
                            <option value="">انتخاب کنید</option>
                            @foreach(['active' => 'فعال', 'paused' => 'متوقف', 'completed' => 'تکمیل‌شده', 'error' => 'خطا'] as $key => $label)
                                <option value="{{ $key }}" @selected(old('crawler_status') === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('crawler_status')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    
                    <!-- Base URL -->
                    <div>
                        <label for="base_url" class="block text-sm font-bold text-gray-700">Base URL</label>
                        <input type="url" name="base_url" id="base_url" value="{{ old('base_url') }}"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">
                        @error('base_url') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Start URLs -->
                    <div>
                        <label for="start_urls" class="block text-sm font-bold text-gray-700">آدرس‌های شروع (با کاما جدا شود)</label>
                        <input type="text" name="start_urls[]" id="start_urls" value="{{ old('start_urls.0') }}"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">
                        @error('start_urls') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Selectors -->
                    <div x-show="needsSelectors">
                        <label for="selectors" class="block text-sm font-bold text-gray-700">Selectors (JSON)</label>
                        <textarea name="selectors" id="selectors"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500"
                            placeholder='{"title": "h1", "content": ".body"}'>{{ old('selectors') }}</textarea>
                        @error('selectors') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Pagination Rule -->
                    <div x-show="type === 'paginated'">
                        <label for="pagination_rule" class="block text-sm font-bold text-gray-700">Pagination Rule (JSON)</label>
                        <textarea name="pagination_rule" id="pagination_rule"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500"
                            placeholder='{"next_page_selector": ".next", "max_pages": 10}'>{{ old('pagination_rule') }}</textarea>
                        @error('pagination_rule') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Auth -->
                    <div x-show="type === 'authenticated'">
                        <label for="auth" class="block text-sm font-bold text-gray-700">Auth (JSON)</label>
                        <textarea name="auth" id="auth"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500"
                            placeholder='{"login_url": "...", "credentials": {"username": "...", "password": "..."}}'>{{ old('auth') }}</textarea>
                        @error('auth') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- API Config -->
                    <div x-show="type === 'api'">
                        <label for="api_config" class="block text-sm font-bold text-gray-700">API Config (JSON)</label>
                        <textarea name="api_config" id="api_config"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500"
                            placeholder='{"endpoint": "https://...", "method": "GET"}'>{{ old('api_config') }}</textarea>
                        @error('api_config') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Schedule -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700">زمان‌بندی</label>
                        <div class="flex gap-2">
                            <input type="text" name="schedule[frequency]" placeholder="daily" value="{{ old('schedule.frequency') }}"
                                class="w-1/2 border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">
                            <input type="time" name="schedule[time]" value="{{ old('schedule.time') }}"
                                class="w-1/2 border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">
                        </div>
                        @error('schedule.frequency') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        @error('schedule.time') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Max Depth -->
                    <div>
                        <label for="max_depth" class="block text-sm font-bold text-gray-700">عمق خزش (اختیاری)</label>
                        <input type="number" name="max_depth" id="max_depth" value="{{ old('max_depth') }}"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">
                        @error('max_depth') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Crawl Delay -->
                    <div>
                        <label for="crawl_delay" class="block text-sm font-bold text-gray-700">تاخیر خزش (ثانیه)</label>
                        <input type="number" name="crawl_delay" id="crawl_delay" value="{{ old('crawl_delay') }}"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">
                        @error('crawl_delay') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Link Filter Rules -->
                    <div>
                        <label for="link_filter_rules" class="block text-sm font-bold text-gray-700">قوانین فیلتر لینک (با کاما جدا شود)</label>
                        <input type="text" name="link_filter_rules[]" id="link_filter_rules" value="{{ old('link_filter_rules.0') }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">
                        @error('link_filter_rules') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Submit -->
                    <div class="flex justify-start mt-6">
                        <button type="submit"
                                class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded transition">
                            ذخیره خزنده
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Alpine Script -->
    <script>
        function crawlerForm() {
            return {
                type: '',
                get needsSelectors() {
                    return ['static', 'dynamic', 'paginated', 'authenticated'].includes(this.type);
                }
            };
        }
    </script>
</x-app-layout>
