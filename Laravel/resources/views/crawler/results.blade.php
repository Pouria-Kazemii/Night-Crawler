<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('نتایج خزشها') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <table class="w-full rtl:text-right text-sm text-gray-800 table-auto">
                    <thead class="bg-red-600 text-white">
                        <tr>
                            <th class="px-4 py-2">#</th>
                            <th class="px-4 py-2">آدرس</th>
                            <th class="px-4 py-2">آدرس نهایی</th>
                            <th class="px-4 py-2">محتوا</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-800">
                        @forelse ($results as $index => $result)
                            <tr class="border-t hover:bg-gray-50">
                                <td class="px-4 py-2 align-middle">
                                    {{ $results->firstItem() + $index }}
                                </td>

                                <!-- URL -->
                                <td class="px-4 py-2 align-middle max-w-xs truncate" title="{{ $result->url }}">
                                    {{ $result->url }}
                                </td>

                                <!-- Final URL -->
                                <td class="px-4 py-2 align-middle max-w-xs truncate" title="{{ $result->final_url }}">
                                    {{ $result->final_url }}
                                </td>

                                <!-- Contents -->
                                <!-- Contents -->
                                <td class="px-4 py-2 align-middle">
                                    @if (!empty($result->content))
                                        <button onclick="toggleContent('content-{{ $loop->index }}')"
                                            class="text-blue-600 underline hover:text-blue-800">
                                            نمایش محتوا
                                        </button>

                                        <div id="content-{{ $loop->index }}"
                                            class="hidden mt-2 max-h-60 overflow-y-auto p-2 border rounded bg-gray-50 text-sm break-words">
                                            @if (is_array($result->content))
                                                @if (array_is_list($result->content))
                                                    {{-- Case 1 : array => array => object of values --}}
                                                    <dl>
                                                        @foreach ($result->content as $number => $values)
                                                            @if (is_array($values))
                                                                <dt class="font-extrabold mb-2 mt-2">شماره ی
                                                                    {{ $number + 1 }}</dt>
                                                                <dd class="ml-4">
                                                                    <ul class="list-none list-inside space-y-1">
                                                                        @foreach ($values as $key => $val)
                                                                            <li>
                                                                                <div
                                                                                    class="flex flex-col sm:flex-row mb-4 pb-4 border-b border-dashed border-gray-300 last:border-b-0 last:mb-0 last:pb-0">
                                                                                    <span
                                                                                        class="font-medium text-gray-800 text-left">{{ $key }}
                                                                                        : </span>
                                                                                    <span
                                                                                        class="text-gray-700 flex-grow text-right">{{ $val }}</span>
                                                                                </div>
                                                                            </li>
                                                                        @endforeach
                                                                    </ul>
                                                                </dd>
                                                            @else
                                                                {{-- Case 2 : array => values) --}}
                                                                <ul class="list-disc list-inside space-y-1">
                                                                    <li>
                                                                        <div
                                                                            class="flex flex-col sm:flex-row mb-4 pb-4 border-b border-dashed border-gray-300 last:border-b-0 last:mb-0 last:pb-0">
                                                                            <span
                                                                                class="font-semibold text-gray-800 sm:w-40 sm:text-left mb-1 sm:mb-0">{{ $values }}
                                                                                : </span>
                                                                        </div>
                                                                    </li>
                                                                </ul>
                                                            @endif
                                                        @endforeach
                                                    </dl>
                                                @else
                                                    {{-- Case 3: Object (key => array of values) --}}
                                                    <dl>
                                                        @foreach ($result->content as $field => $values)
                                                            <dt class="font-semibold mt-2">{{ $field }}:</dt>
                                                            <dd class="ml-4">
                                                                <ul class="list-none list-inside space-y-1">
                                                                    @if (is_array($values))
                                                                        @foreach ($values as $val)
                                                                            <li>{{ $val }}</li>
                                                                        @endforeach
                                                                    @else
                                                                        <li>{{ $values }}</li>
                                                                    @endif
                                                                </ul>
                                                            </dd>
                                                        @endforeach
                                                    </dl>
                                                @endif
                                            @else
                                                {{-- Case 3: Plain string or number --}}
                                                <div>{{ $result->content }}</div>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>


                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-4 py-2 text-center text-gray-500">
                                    داده‌ای یافت نشد.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <!-- Pagination -->
                <div class="flex justify-center items-center mt-6 pt-6">
                    {{ $results->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- JS for toggle -->
    <script>
        function toggleContent(id) {
            const el = document.getElementById(id);
            if (el) el.classList.toggle('hidden');
        }
    </script>
</x-app-layout>
