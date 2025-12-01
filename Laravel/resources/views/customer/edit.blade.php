<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('ایجاد مشتری جدید') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('customer.update', $customer) }}" class="rtl text-right space-y-4">
                @method('PUT')
                @csrf

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-bold text-gray-700">اسم</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $customer->name) }}"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">
                    @error('name')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- URL -->
                <div>
                    <label for="url" class="block text-sm font-bold text-gray-700">آدرس url</label>
                    <input type="text" name="url" id="url" value="{{ old('url', $customer->url) }}"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">
                    @error('url')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- IP Address -->
                <div>
                    <label for="ip_address" class="block text-sm font-bold text-gray-700">آیپی</label>
                    <input type="text" name="ip_address" id="ip_address"
                        value="{{ old('ip_address', $customer->ip_address) }}"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">
                    @error('ip_address')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Subscribe UCT -->
                <div>
                    <label for="subscribe_uct" class="block text-sm font-bold text-gray-700">اشتراک</label>
                    <select name="subscribe_uct" id="subscribe_uct"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500"
                        required>
                        <option value="">انتخاب کنید</option>
                        @foreach ($subscribes as $subscribe)
                            <option value="{{ $subscribe['uct'] }}" @selected(old('subscribe', $customer->subscribe_uct) === $subscribe['uct'])>
                                {{ $subscribe['title'] }}
                            </option>
                        @endforeach
                    </select>
                    @error('subscribe_uct')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Available Crawler Ids -->
                <label class="block text-sm font-bold text-gray-700 mb-2">خزشگرهای مجاز</label>

                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                    @foreach ($crawlers as $id => $title)
                        <label
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">
                            <input type="checkbox" name="available_crawler_ids[]" value="{{ $id }}"
                                @checked(in_array($id, old('available_crawler_ids', $customer->available_crawler_ids ?? [])))
                                class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                            <span class="mr-2 text-gray-700 text-sm">{{ $title }}</span>
                        </label>
                    @endforeach
                    @error('available_crawler_ids')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit -->
                <div class="flex justify-start mt-6">
                    <button type="submit"
                        class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded transition">
                        ذخیره
                    </button>
                </div>

            </form>
        </div>
    </div>
</x-app-layout>
