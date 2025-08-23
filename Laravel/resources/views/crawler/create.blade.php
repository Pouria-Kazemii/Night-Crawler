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
                        @error('title')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-bold text-gray-700">توضیحات</label>
                        <textarea name="description" id="description"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Crawler Type -->
                    <div>
                        <label for="crawler_type" class="block text-sm font-bold text-gray-700">نوع خزنده</label>
                        <select name="crawler_type" id="crawler_type" x-model="type"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500"
                            required>
                            <option value="">انتخاب کنید</option>
                            @foreach ($crawlerTypes['all_steps'] as $typeOption)
                                <option value="{{ $typeOption }}" @selected(old('crawler_type') === $typeOption)>{{ $typeOption }}
                                </option>
                            @endforeach
                        </select>
                        @error('crawler_type')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Crawler Status -->
                    <div>
                        <label for="crawler_status" class="block text-sm font-bold text-gray-700">وضعیت خزنده</label>
                        <select name="crawler_status" id="crawler_status"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">
                            <option value="">انتخاب کنید</option>
                            @foreach (['paused' => 'متوقف'] as $key => $label)
                                <option value="{{ $key }}" @selected(old('crawler_status') === $key)>{{ $label }}
                                </option>
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
                        @error('base_url')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Start URLs -->
                    <div>
                        <label for="start_urls" class="block text-sm font-bold text-gray-700">آدرس‌های شروع (با کاما جدا
                            شود)</label>
                        <input type="text" name="start_urls[]" id="start_urls" value="{{ old('start_urls.0') }}"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">
                        @error('start_urls')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- URL Pattern -->
                    <div>
                        <label for="url_pattern" class="block text-sm font-bold text-gray-700">الگوی URL</label>
                        <input type="text" name="url_pattern" id="url_pattern" value="{{ old('url_pattern') }}"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500"
                            placeholder="/page/{id}">
                        @error('url_pattern')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Range: start / end -->
                    <div class="flex gap-4">
                        <div class="w-1/2">
                            <label for="range_start" class="block text-sm font-bold text-gray-700">شروع (start)</label>
                            <input type="number" name="range[start]" id="range_start" value="{{ old('range.start') }}"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">
                            @error('range.start')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="w-1/2">
                            <label for="range_end" class="block text-sm font-bold text-gray-700">پایان (end)</label>
                            <input type="number" name="range[end]" id="range_end" value="{{ old('range.end') }}"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">
                            @error('range.end')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Schedule -->
                    <div>
                        <label for="schedule" class="block text-sm font-bold text-gray-700">زمان‌ بندی بر حسب دقیقه</label>
                        <input id="schedule" type="number" name="schedule" placeholder="60"
                            value="{{ old('schedule') }}"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">
                        @error('schedule')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Crawl Delay -->
                    <div>
                        <label for="crawl_delay" class="block text-sm font-bold text-gray-700">تاخیر خزش (ثانیه)</label>
                        <input type="number" name="crawl_delay" id="crawl_delay" value="{{ old('crawl_delay') }}"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">
                        @error('crawl_delay')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Two_step Config -->
                    <div x-show="type === 'two_step'">
                        <div>
                            <label for="two_step_first" class="block text-sm font-bold text-gray-700">نوع خزنده مرحله
                                اول</label>
                            <select name="two_step[first]" id="two_step_first" x-model="two_step_first"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">
                                <option value="">انتخاب کنید</option>
                                @foreach ($crawlerTypes['first_step'] as $typeOption)
                                    <option value="{{ $typeOption }}" @selected(old('two_step.first') === $typeOption)>
                                        {{ $typeOption }}</option>
                                @endforeach
                            </select>
                            @error('two_step.first')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="two_step_second" class="block text-sm font-bold text-gray-700 mt-1">نوع خزنده
                                مرحله دوم</label>
                            <select name="two_step[second]" id="two_step_second" x-model="two_step_second"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">
                                <option value="">انتخاب کنید</option>
                                @foreach ($crawlerTypes['second_step'] as $typeOption)
                                    <option value="{{ $typeOption }}" @selected(old('two_step.second') === $typeOption)>
                                        {{ $typeOption }}</option>
                                @endforeach
                            </select>
                            @error('two_step.second')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>


                    <!-- Seed Config -->
                    <template x-if="isType('seed')">
                        <div>
                            <label for="link_filter_rules" class="mt-1 block text-sm font-bold text-gray-700">قوانین
                                فیلتر
                                لینک (با کاما جدا شود)</label>
                            <input type="text" name="link_filter_rules[]" id="link_filter_rules"
                                value="{{ old('link_filter_rules.0') }}"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">
                            @error('link_filter_rules')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </template>


                    <!-- Link Selector -->
                    <div x-show="needsLinkSelector">
                        <label for="link_selector" class="block text-sm font-bold text-gray-700">انتخاب کننده
                            لینک</label>
                        <input type="text" name="link_selector" id="link_selector"
                            value="{{ old('link_selector') }}"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500"
                            placeholder="مثال:div.mt-1.block">
                        @error('link_selector')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>


                    <!-- Selectors Section -->
                    <div class="mb-6" x-show="needsSelectors">

                        <label class="block text-sm font-bold text-gray-700 mb-2">انتخاب کننده‌ها</label>

                        <div id="selectors-container">
                            @php
                                $oldSelectors = old('selectors', [
                                    ['key' => 'title', 'selector' => '', 'full_html' => false],
                                ]);
                            @endphp

                            @foreach ($oldSelectors as $index => $selector)
                                <div class="selector-group grid grid-cols-12 gap-2 items-end mb-2">
                                    <div class="col-span-4">
                                        <label class="block text-xs text-gray-500 mb-1">کلید</label>
                                        <input type="text" name="selectors[{{ $index }}][key]"
                                            value="{{ $selector['key'] ?? '' }}"
                                            class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 text-sm"
                                            placeholder="مثال: title">
                                    </div>

                                    <div class="col-span-4">
                                        <label class="block text-xs text-gray-500 mb-1">سلکتور</label>
                                        <input type="text" name="selectors[{{ $index }}][selector]"
                                            value="{{ $selector['selector'] ?? '' }}"
                                            class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 text-sm"
                                            placeholder="مثال: h1.title">
                                    </div>

                                    <div class="col-span-3">
                                        <label class="block text-xs text-gray-500 mb-1">دریافت HTML کامل</label>
                                        <input type="checkbox" name="selectors[{{ $index }}][full_html]"
                                            value="1" {{ !empty($selector['full_html']) ? 'checked' : '' }}
                                            class="rounded border-gray-300 text-red-600 shadow-sm focus:ring-red-500">
                                    </div>

                                    <div class="col-span-1">
                                        @if ($index >= 1)
                                            <button type="button"
                                                class="remove-selector bg-red-100 text-red-600 p-2 rounded-md hover:bg-red-200 text-sm">
                                                حذف
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <button type="button" id="add-selector"
                            class="mt-2 bg-gray-100 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-200 text-sm">
                            + افزودن انتخاب کننده جدید
                        </button>

                        @error('selectors')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const container = document.getElementById('selectors-container');
                            const addButton = document.getElementById('add-selector');
                            let selectorCount = {{ count($oldSelectors) }};

                            addButton.addEventListener('click', function() {
                                const newGroup = document.createElement('div');
                                newGroup.className = 'selector-group grid grid-cols-12 gap-2 items-end mb-2';
                                newGroup.innerHTML = `
                                <div class="col-span-4">
                                    <label class="block text-xs text-gray-500 mb-1">کلید</label>
                                    <input type="text" name="selectors[${selectorCount}][key]"
                                        class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 text-sm"
                                        placeholder="مثال: title" required>
                                </div>

                                <div class="col-span-4">
                                    <label class="block text-xs text-gray-500 mb-1">سلکتور</label>
                                    <input type="text" name="selectors[${selectorCount}][selector]"
                                        class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 text-sm"
                                        placeholder="مثال: h1.title" required>
                                </div>

                                <div class="col-span-3">
                                    <label class="block text-xs text-gray-500 mb-1">دریافت HTML کامل</label>
                                    <input type="checkbox" name="selectors[${selectorCount}][full_html]" value="1"
                                        class="rounded border-gray-300 text-red-600 shadow-sm focus:ring-red-500">
                                </div>

                                <div class="col-span-1">
                                    <button type="button" class="remove-selector bg-red-100 text-red-600 p-2 rounded-md hover:bg-red-200 text-sm">
                                        حذف
                                    </button>
                                </div>
                            `;
                                container.appendChild(newGroup);
                                selectorCount++;
                            });

                            container.addEventListener('click', function(e) {
                                if (e.target.classList.contains('remove-selector')) {
                                    e.target.closest('.selector-group').remove();
                                    const groups = container.querySelectorAll('.selector-group');
                                    groups.forEach((group, index) => {
                                        group.querySelector('[name*="[key]"]').name = `selectors[${index}][key]`;
                                        group.querySelector('[name*="[selector]"]').name =
                                            `selectors[${index}][selector]`;
                                        const checkbox = group.querySelector('[name*="[full_html]"]');
                                        if (checkbox) {
                                            checkbox.name = `selectors[${index}][full_html]`;
                                        }
                                    });
                                    selectorCount = groups.length;
                                }
                            });
                        });
                    </script>


                    <!-- Pagination Config -->
                    <template x-if="isType('paginated')">
                        <div>
                            <label for="next_page_selector" class="block text-sm font-bold text-gray-700">سلکتور
                                صفحه
                                بعد</label>
                            <input type="text" name="pagination_rule[next_page_selector]" id="next_page_selector"
                                value="{{ old('pagination_rule.next_page_selector') }}"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">
                            @error('pagination_rule.next_page_selector')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror

                            <label for="pagination_limit" class="block text-sm font-bold text-gray-700 mt-4">حداکثر
                                صفحات</label>
                            <input type="number" name="pagination_rule[limit]" id="pagination_limit"
                                value="{{ old('pagination_rule.limit') }}"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">
                            @error('pagination_rule.limit')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </template>

                    <!-- Dynamic Config -->
                    <template x-if="isType('dynamic')">
                        <div>
                            <label for="dynamic_limit" class="block text-sm font-bold text-gray-700">تعداد بارگیری
                                های
                                مجدد</label>
                            <input type="number" name="dynamic_limit" id="dynamic_limit"
                                value="{{ old('dynamic_limit') }}"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">
                            @error('dynamic_limit')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </template>


                    <!-- Auth Config -->
                    <template x-if="isType('authenticated')">
                        <div>
                            <label class="block text-sm font-bold text-gray-700">آدرس ورود</label>
                            <input type="url" name="auth[login_url]" value="{{ old('auth.login_url') }}"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">
                            @error('auth.login_url')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror

                            <label class="block text-sm font-bold text-gray-700 mt-4">نام کاربری</label>
                            <input type="text" name="auth[username]" value="{{ old('auth.username') }}"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">
                            @error('auth.username')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror

                            <label class="block text-sm font-bold text-gray-700 mt-4">رمز عبور</label>
                            <input type="password" name="auth[password]" value="{{ old('auth.password') }}"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">
                            @error('auth.password')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror

                            <label class="block text-sm font-bold text-gray-700 mt-3">اطلاعات انتخاب کننده نام
                                کاربری</label>
                            <input type="text" name="auth[username_selector]"
                                value="{{ old('auth.username_selector') }}"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500"
                                placeholder="html_tag[tag_attribute_name='tag_attribute_value'] برای مثال : input[name='login']">
                            @error('auth.username_selector')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror

                            <label class="block text-sm font-bold text-gray-700 mt-3">اطلاعات انتخاب کننده
                                رمزعبور</label>
                            <input type="text" name="auth[password_selector]"
                                value="{{ old('auth.password_selector') }}"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500"
                                placeholder="html_tag[tag_attribute_name='tag_attribute_value'] برای مثال : input[name='password']">
                            @error('auth.password_selector')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </template>


                    <!-- API Config --> <!-- TODO -->
                    <template x-if="isType('api')">
                        <div>
                            <label for="api_endpoint" class="block text-sm font-bold text-gray-700">Endpoint</label>
                            <input type="url" name="api_config[endpoint]" id="api_endpoint"
                                value="{{ old('api_config.endpoint') }}"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">
                            @error('api_config.endpoint')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror

                            <label for="api_method" class="block text-sm font-bold text-gray-700 mt-4">Method</label>
                            <input type="text" name="api_config[method]" id="api_method"
                                value="{{ old('api_config.method') ?? 'GET' }}"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">
                            @error('api_config.method')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror

                            <label for="api_token" class="block text-sm font-bold text-gray-700 mt-4">توکن
                                (اختیاری)</label>
                            <input type="text" name="api_config[token]" id="api_token"
                                value="{{ old('api_config.token') }}"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">
                            @error('api_config.token')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </template>


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
            const selectors = @json($crawlerTypes['selector']);
            const link_selector = @json($crawlerTypes['link_selector']);

            return {
                type: '',
                two_step_first: '',
                two_step_second: '',

                get needsSelectors() {
                    return selectors.includes(this.type) ||
                        selectors.includes(this.two_step_first) ||
                        selectors.includes(this.two_step_second);
                },

                get needsLinkSelector() {
                    return link_selector.includes(this.type) ||
                        link_selector.includes(this.two_step_first) ||
                        link_selector.includes(this.two_step_second);
                },

                // This one is not a getter but a method; fixed signature and access
                isType(variable) {
                    return this.type === variable ||
                        this.two_step_first === variable ||
                        this.two_step_second === variable;
                }
            };
        }
    </script>
</x-app-layout>
