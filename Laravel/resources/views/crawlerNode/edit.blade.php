<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('ویرایش پروکسی') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-md rounded-lg p-6">
              
                <form method="POST" action="{{ route('crawl-nodes.update', $crawlerNode ) }}" class="rtl text-right space-y-4">
                    @csrf
                    @method('PUT')

                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-bold text-gray-700">نام</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $crawlerNode->name) }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500" />
                        @error('name')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- IP Address -->
                    <div>
                        <label for="ip_address" class="block text-sm font-bold text-gray-700">آدرس IP</label>
                        <input type="text" id="ip_address" name="ip_address" value="{{ old('ip_address', $crawlerNode->ip_address) }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500" />
                        @error('ip_address')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Port -->
                    <div>
                        <label for="port" class="block text-sm font-bold text-gray-700">پورت</label>
                        <input type="number" id="port" name="port" value="{{ old('port', $crawlerNode->port) }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500" />
                        @error('port')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Protocol -->
                    <div>
                        <label for="protocol" class="block text-sm font-bold text-gray-700">پروتکل</label>
                        <select id="protocol" name="protocol"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">
                            <option value="">انتخاب کنید</option>
                            @foreach (['HTTP', 'HTTPS', 'SOCKS5'] as $protocol)
                                <option value="{{ $protocol }}" @selected(old('protocol', $crawlerNode->protocol) === $protocol)>
                                    {{ $protocol }}
                                </option>
                            @endforeach
                        </select>
                        @error('protocol')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-bold text-gray-700">وضعیت</label>
                        <select id="status" name="status"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">
                            <option value="">انتخاب کنید</option>
                            @foreach (['active' => 'فعال', 'inactive' => 'غیرفعال', 'banned' => 'مسدود', 'down' => 'خاموش'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('status', $crawlerNode->status) === $value)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('status')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Is Verified -->
                    <div>
                        <label for="is_verified" class="block text-sm font-bold text-gray-700">تأیید شده؟</label>
                        <select id="is_verified" name="is_verified"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">
                            <option value="1" @selected(old('is_verified', $crawlerNode->is_verified) == '1')>بله</option>
                            <option value="0" @selected(old('is_verified', $crawlerNode->is_verified) == '0')>خیر</option>
                        </select>
                        @error('is_verified')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Location -->
                    <div>
                        <label for="location" class="block text-sm font-bold text-gray-700">موقعیت</label>
                        <input type="text" id="location" name="location" value="{{ old('location', $crawlerNode->location) }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500" />
                        @error('location')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-start mt-6">
                        <button type="submit"
                                class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded transition">
                            ذخیره تغییرات
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
