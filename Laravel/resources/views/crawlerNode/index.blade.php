<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('پروکسی ها') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-4 p-4 rounded bg-green-100 text-green-800 text-sm font-medium shadow">
                    {{ session('status') }}
                </div>
            @endif


            <div class="bg-white shadow-md rounded-lg overflow-hidden">


                <!-- Create Button Inside Container -->
                <div class="flex justify-start mb-4">
                    <a href="{{ route('crawl-nodes.create') }}"
                    class="inline-block bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded transition">
                        ایجاد پروکسی جدید
                    </a>
                </div>
                <table class="w-full rtl:text-right text-sm text-gray-800">
                    <thead class="bg-red-600 text-white">
                        <tr>
                            <th class="px-4 py-2">#</th>
                            <th class="px-4 py-2">نام</th>
                            <th class="px-4 py-2">IP</th>
                            <th class="px-4 py-2">پورت</th>
                            <th class="px-4 py-2">پروتکل</th>
                            <th class="px-4 py-2">وضعیت</th>
                            <th class="px-4 py-2">(ms) تاخیر</th>
                            <th class="px-4 py-2">آخرین تست سرعت</th>
                            <th class="px-4 py-2 text-center">عملیات</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-800">
                        @forelse ($crawlNodes as $index => $node)
                            <tr class="border-t hover:bg-gray-100">
                                <td class="px-4 py-2 align-middle">{{ $crawlNodes->firstItem() + $index }}</td>
                                <td class="px-4 py-2 align-middle">{{ $node->name }}</td>
                                <td class="px-4 py-2 align-middle">{{ $node->ip_address }}</td>
                                <td class="px-4 py-2 align-middle">{{ $node->port }}</td>
                                <td class="px-4 py-2 align-middle">{{ strtoupper($node->protocol) }}</td>
                                <td class="px-4 py-2 align-middle">{{ $node->status }}</td>
                                <td class="px-4 py-2 align-middle">ms {{ $node->latency }}</td>
                                <td class="px-4 py-2 align-middle">{{ $node->last_used_at?->diffForHumans() ?? '-' }}</td>
                                <td class="px-4 py-2 align-middle text-center">
                                    <div class="flex justify-center gap-2 rtl:flex-row-reverse">
                                        <!-- Edit Button -->
                                        <a href="{{ route('crawl-nodes.edit', $node) }}"
                                           class="bg-yellow-500 hover:bg-yellow-600 text-white text-sm px-3 py-1 rounded transition">
                                            ویرایش
                                        </a>

                                        <!-- Ping Test Button -->
                                        <a href="{{ route('crawl-nodes.ping', ['crawlerNode' => $node]) }}"
                                           class="bg-sky-500 hover:bg-sky-600 text-white text-sm px-3 py-1 rounded transition">
                                            تست سرعت
                                        </a>
                                        
                                        <!-- Delete Button (no confirmation) -->
                                        <form action="{{ route('crawl-nodes.destroy', $node) }}" method="POST" onsubmit="return confirm('آیا مطمئن هستید؟');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white text-sm px-3 py-1 rounded transition">
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
                    {{ $crawlNodes->links() }}
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
