<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('مشتریان') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-4 p-4 rounded bg-green-100 text-green-800 text-sm font-medium shadow">
                    {{ session('status') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 p-4 rounded bg-red-100 text-red-800 text-sm font-medium shadow">
                    {{ session('error') }}
                </div>
            @endif


            <div class="bg-white shadow-md rounded-lg overflow-hidden">


                <!-- Create Button Inside Container -->
                <div class="flex justify-start mb-4">
                    <a href="{{ route('customer.create') }}"
                        class="inline-block bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded transition">
                        ایجاد مشتری
                    </a>
                </div>
                <table class="w-full rtl:text-right text-sm text-gray-800">
                    <thead class="bg-red-600 text-white">
                        <tr>
                            <th class="px-4 py-2">#</th>
                            <th class="px-4 py-2">عنوان</th>
                            <th class="px-4 py-2">آیپی</th>
                            <th class="px-4 py-2">url</th>
                            <th class="px-4 py-2">اشتراک</th>
                            <th class="px-4 py-2">تاریخ انقضا</th>
                            <th class="px-4 py-2">توکن باقی مانده</th>
                            <th class="px-4 py-2 text-center">عملیات</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-800">
                        @forelse ($customers as $index => $customer)
                            <tr class="border-t hover:bg-gray-100">
                                <td class="px-4 py-2 align-middle">{{ $customers->firstItem() + $index }}</td>
                                <td class="px-4 py-2 align-middle">{{ $customer->name }}</td>
                                <td class="px-4 py-2 align-middle">{{ $customer->ip_address }}</td>
                                <td class="px-4 py-2 align-middle">{{ $customer->url }}</td>
                                <td class="px-4 py-2 align-middle">{{ $customer->subscribe->title }}</td>
                                <td class="px-4 py-2 align-middle">{{ $customer->expire_date_shamsi}}</td>
                                <td class="px-4 py-2 align-middle">
                                    @if ($customer->subscribe->available_token_count == 'unlimit')
                                        {{ __('بینهایت') }}
                                    @else
                                        {{ $customer->subscribe->available_token_count - $customer->token_used }}
                                    @endif

                                </td>
                                </td>
                                <td class="px-4 py-2 align-middle text-center">
                                    <div class="flex justify-center gap-2 rtl:flex-row-reverse">


                                        <!-- Delete Button -->
                                        <form action="{{ route('customer.delete', $customer) }}" method="POST"
                                            onsubmit="return confirm('آیا مطمئن هستید؟');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="bg-red-600 hover:bg-red-700 text-white text-sm px-3 py-1 rounded-md shadow transition">
                                                حذف
                                            </button>
                                        </form>

                                        <!-- Edit Button -->
                                        <a href="{{ route('customer.edit', $customer) }}"
                                            class="bg-yellow-500 hover:bg-yellow-600 text-white text-sm px-3 py-1 rounded-md shadow transition">
                                            ویرایش
                                        </a>

                                        <!-- Show Token Button + Popup -->
                                        <div x-data="{ open: false, showToast: false, toastMsg: '' }" class="relative">

                                            <!-- Trigger Button -->
                                            <button @click="open = true"
                                                class="bg-sky-500 hover:bg-sky-600 text-white text-sm px-3 py-1 rounded-md shadow transition">
                                                توکن
                                            </button>

                                            <!-- Popup Modal -->
                                            <div x-show="open" x-transition.opacity
                                                class="fixed inset-0 flex items-center justify-center bg-black/50 z-50">
                                                <div @click.away="open = false" x-transition.scale
                                                    class="relative bg-white rounded-2xl shadow-2xl p-6 w-[90%] max-w-md text-center">
                                                    <!-- ✅ Toast Notification (Inside Modal) -->
                                                    <div x-show="showToast" x-transition.opacity
                                                        class="absolute -top-10 left-1/2 -translate-x-1/2 bg-gray-800 text-white text-sm px-4 py-2 rounded-lg shadow-lg">
                                                        <span x-text="toastMsg"></span>
                                                    </div>

                                                    <h2 class="text-lg font-bold mb-3 text-gray-800">
                                                        توکن احراز هویت
                                                    </h2>

                                                    <p class="text-sm text-gray-600 mb-5">
                                                        این توکن برای احراز هویت و استفاده از وب سرویس استفاده می‌شود.
                                                    </p>

                                                    <!-- Token Display -->
                                                    <div
                                                        class="bg-gray-100 text-gray-800 text-xs font-mono rounded-md p-3 break-all border border-gray-200 mb-4 select-all">
                                                        {{ $customer->token }}
                                                    </div>

                                                    <!-- Buttons -->
                                                    <div class="flex justify-center gap-2">
                                                        <!-- Copy Button -->
                                                        <button
                                                            @click="
                                navigator.clipboard.writeText('{{ $customer->token }}');
                                toastMsg = 'توکن کپی شد ✅';
                                showToast = true;
                                setTimeout(() => showToast = false, 2000);
                            "
                                                            class="bg-emerald-500 hover:bg-emerald-600 text-white text-sm px-3 py-1 rounded-md shadow transition">
                                                            کپی توکن
                                                        </button>

                                                        <!-- Close Button -->
                                                        <button @click="open = false"
                                                            class="bg-red-600 hover:bg-red-700 text-white text-sm px-3 py-1 rounded-md shadow transition">
                                                            بستن
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Resubscribe Button + Popup -->
                                            <div x-data="{ openResubscribe: false }" class="inline-block">
                                                <!-- Trigger Button -->
                                                <button @click="openResubscribe = true"
                                                    class="bg-indigo-500 hover:bg-indigo-600 text-white text-sm px-3 py-1 rounded-md shadow transition">
                                                    تمدید اشتراک
                                                </button>

                                                <!-- Popup Modal -->
                                                <div x-show="openResubscribe" x-transition.opacity
                                                    class="fixed inset-0 flex items-center justify-center bg-black/50 z-50">
                                                    <div @click.away="openResubscribe = false" x-transition.scale
                                                        class="bg-white rounded-2xl shadow-2xl p-6 w-[90%] max-w-md text-center relative">

                                                        <h2 class="text-lg font-bold mb-4 text-gray-800">تمدید اشتراک
                                                            مشتری</h2>

                                                        <p class="text-sm text-gray-600 mb-5">
                                                            لطفاً اشتراک جدید را برای این مشتری انتخاب کنید.
                                                        </p>

                                                        <!-- Resubscribe Form -->
                                                        <form action="{{ route('customer.resubscribe', $customer) }}"
                                                            method="POST" class="space-y-4">
                                                            @method('PATCH')
                                                            @csrf
                                                            <div class="text-right">
                                                                <label for="subscribe_uct"
                                                                    class="block mb-2 text-sm font-medium text-gray-700">انتخاب
                                                                    اشتراک</label>
                                                                <select id="subscribe_uct" name="subscribe_uct"
                                                                    class="block w-full border-gray-300 rounded-lg shadow-sm text-sm focus:ring-red-500 focus:border-red-500">
                                                                    @foreach ($subscribes as $subscribe)
                                                                        <option value="{{ $subscribe->uct }}">
                                                                            {{ $subscribe->title }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>

                                                            <div class="flex justify-center gap-2 pt-2">
                                                                <button type="submit"
                                                                    class="bg-emerald-500 hover:bg-emerald-600 text-white text-sm px-4 py-2 rounded-md shadow transition">
                                                                    تایید
                                                                </button>

                                                                <button type="button" @click="openResubscribe = false"
                                                                    class="bg-red-600 hover:bg-red-700 text-white text-sm px-4 py-2 rounded-md shadow transition">
                                                                    بستن
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-4 py-2 text-center text-gray-500">مشتری یافت نشد</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <!-- Pagination -->
                <div class="flex justify-center items-center mt-6 pt-6">
                    {{ $customers->links() }}
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
