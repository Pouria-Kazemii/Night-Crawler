<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('خزشگرها') }}
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
                    <a href="{{ route('crawler.create') }}"
                        class="inline-block bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded transition">
                        ایجاد خزشگر جدید
                    </a>
                </div>
                <table class="w-full rtl:text-right text-sm text-gray-800">
                    <thead class="bg-red-600 text-white">
                        <tr>
                            <th class="px-4 py-2">#</th>
                            <th class="px-4 py-2">نام</th>
                            <th class="px-4 py-2">توضیحات</th>
                            <th class="px-4 py-2">وضعیت</th>
                            <th class="px-4 py-2">مدل</th>
                            <th class="px-4 py-2">آدرس اصلی</th>
                            <th class="px-4 py-2">آخرین استفاده</th>
                            <th class="px-4 py-2 text-center">عملیات</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-800">
                        @forelse ($crawlers as $index => $crawler)
                            <tr class="border-t hover:bg-gray-100">
                                <td class="px-4 py-2 align-middle">{{ $crawlers->firstItem() + $index }}</td>
                                <td class="px-4 py-2 align-middle">{{ $crawler->title }}</td>
                                <td class="px-4 py-2 align-middle">{{ $crawler->description }}</td>
                                <td class="px-4 py-2 align-middle">{{ $crawler->crawler_status }}</td>
                                <td class="px-4 py-2 align-middle">{{ $crawler->crawler_type }}</td>
                                <td class="px-4 py-2 align-middle">{{ $crawler->base_url }}</td>
                                <td class="px-4 py-2 align-middle">{{ $crawler->last_run_at?->diffForHumans() ?? '-' }}
                                </td>
                                <td class="px-4 py-2 align-middle text-center">
                                    <div class="flex justify-center gap-2 rtl:flex-row-reverse">

                                        <!-- Edit Button -->
                                        <a href="{{ route('crawler.edit', $crawler) }}"
                                            class="bg-yellow-500 hover:bg-yellow-600 text-white text-sm px-3 py-1 rounded transition">
                                            ویرایش
                                        </a>

                                        <!-- Go Button -->
                                        <form action="{{ route('crawler.go', $crawler) }}" method="POST"
                                            onsubmit="return confirm('آیا مطمئن هستید؟');">
                                            @csrf
                                            @method('POST')
                                            <button type="submit"
                                                class="bg-green-600 hover:bg-green-700 text-white text-sm px-3 py-1 rounded transition">
                                                شروع
                                            </button>
                                        </form>

                                        <a href="{{ route('crawler.results', $crawler) }}"
                                            class="bg-cyan-500 hover:bg-cyan-600 text-white text-sm px-3 py-1 rounded transition">
                                            نتایج
                                        </a>

                                        <a href="{{ route('crawler.senders', $crawler) }}"
                                            class="bg-purple-500 hover:bg-purple-600 text-white text-sm px-3 py-1 rounded transition">
                                            سوابق
                                        </a>

                                        <!-- Delete Button (no confirmation) -->
                                        <form action="{{ route('crawler.destroy', $crawler) }}" method="POST"
                                            onsubmit="return confirm('آیا مطمئن هستید؟');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="bg-red-600 hover:bg-red-700 text-white text-sm px-3 py-1 rounded transition">
                                                حذف
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-4 py-2 text-center text-gray-500">پروکسی یافت نشد.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <!-- Pagination -->
                <div class="flex justify-center items-center mt-6 pt-6">
                    {{ $crawlers->links() }}
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
