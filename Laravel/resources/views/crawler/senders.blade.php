<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight"> سوابق درخواست های ارسالی</h2>
    </x-slot>

    <div class="container mx-auto p-6">

        <!-- Use flex to align cards horizontally -->
        <div class="flex flex-wrap gap-6 justify-start">
            @foreach ($senders as $index => $item)
                <div class="w-80 bg-white rounded-2xl shadow-lg border border-gray-200 p-6 flex flex-col">

                    <!-- Header -->
                    <div class="flex justify-between items-center border-b pb-3 mb-4">
                        <div class="text-lg font-semibold text-gray-800">
                            {{ $senders->firstItem()+ $index. '_' . ($item->crawler['title'] ?? '') }}
                        </div>
                        <div class="text-sm text-gray-500">
                            {{ $item->last_used_at?->diffForHumans()}}
                        </div>
                    </div>

                    <!-- Stats -->
                    <div class="grid grid-cols-2 gap-4 text-center">
                        <!-- وضعیت -->
                        <div class="p-2 rounded-lg bg-gray-50 shadow-sm">
                            @if ($item->status == 'success')
                                <div class="text-green-600 text-xl font-extrabold">موفق</div>
                            @elseif($item->status == 'failed')
                                <div class="text-red-600 text-xl font-extrabold">خطا</div>
                            @elseif($item->status == 'running')
                                <div class="text-yellow-600 text-xl font-extrabold">در حال اجرا</div>
                            @elseif($item->status == 'queued')
                                <div class="text-cyan-600 text-xl font-extrabold">در صف اجرا</div>
                            @endif
                            <div class="text-gray-600 text-xs mt-1">وضعیت</div>
                        </div>

                        <!-- مرحله -->
                        <div class="p-2 rounded-lg bg-gray-50 shadow-sm">
                            <div class="text-lg font-bold">{{ $item->step }}</div>
                            <div class="text-gray-600 text-xs mt-1">مرحله</div>
                        </div>

                        <!-- کل آدرس‌ها -->
                        <div class="p-2 rounded-lg bg-gray-50 shadow-sm">
                            <div class="text-lg font-bold">{{ $total = count($item->urls) }}</div>
                            <div class="text-gray-600 text-xs mt-1">لینک</div>
                        </div>

                        <!-- خطا -->
                        <div class="p-2 rounded-lg bg-gray-50 shadow-sm">
                            <div class="text-lg font-bold text-red-600">
                                {{ $item->failed_url != null ? ($failed = count($item->failed_url)) : ($failed = 0) }}
                            </div>
                            <div class="text-gray-600 text-xs mt-1">خطا</div>
                        </div>

                        <!-- موفق -->
                        <div class="p-2 rounded-lg bg-gray-50 shadow-sm">
                            <div class="text-lg font-bold text-green-600">
                                {{ $success = $item->counts['success'] ?? ($success = 0) }}
                            </div>
                            <div class="text-gray-600 text-xs mt-1">موفق</div>
                        </div>

                        <!-- تکراری -->
                        <div class="p-2 rounded-lg bg-gray-50 shadow-sm">
                            <div class="text-lg font-bold text-amber-600">
                                {{ $repeated = $item->counts['repeated'] ?? 0 }}
                            </div>
                            <div class="text-gray-600 text-xs mt-1">تکراری</div>
                        </div>

                        <!-- محتوای جدید -->
                        <div class="p-2 rounded-lg bg-gray-50 shadow-sm">
                            <div class="text-lg font-bold text-zinc-600">
                                {{ $changed = $item->counts['changed'] ?? 0 }}
                            </div>
                            <div class="text-gray-600 text-xs mt-1">محتوای تغییر یافته</div>
                        </div>

                        <!-- محتوای جدید -->
                        <div class="p-2 rounded-lg bg-gray-50 shadow-sm">
                            <div class="text-lg font-bold text-zinc-600">
                                {{ $changed = $item->counts['new'] ?? 0 }}
                            </div>
                            <div class="text-gray-600 text-xs mt-1">محتوای جدید</div>
                        </div>
                    </div>

                    <!-- Progress "Battery Bar" -->
                    <div class="mt-6">
                        @php
                            $percent = $success + $failed > 0 ? round(($success / ($success + $failed)) * 100, 1) : 0;
                            if ($percent >= 70) {
                                $barColor = 'from-green-400 to-green-600';
                            } elseif ($percent >= 40) {
                                $barColor = 'from-yellow-400 to-yellow-600';
                            } else {
                                $barColor = 'from-red-400 to-red-600';
                            }
                        @endphp

                        <div class="relative w-full h-6 bg-gray-200 rounded-xl overflow-hidden shadow-inner">
                            <!-- Battery head -->
                            <div class="absolute right-[-6px] top-[4px] w-2 h-4 bg-gray-400 rounded-sm"></div>

                            <!-- Fill -->
                            <div class="h-full bg-gradient-to-r {{ $barColor }} transition-all duration-700"
                                style="width: {{ $percent }}%"></div>
                        </div>

                        <div class="text-sm text-gray-600 mt-2 text-right">
                            پیشرفت: {{ $percent }}%
                        </div>
                    </div>

                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="flex justify-center items-center mt-6 pt-6">
            {{ $senders->links() }}
        </div>
    </div>
</x-app-layout>
