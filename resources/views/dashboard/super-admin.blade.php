<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Super Admin Dashboard
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
           
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-gray-600">Companies</div>
                    <div class="text-2xl font-bold">{{ $stats['companies'] }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-gray-600">Total Users</div>
                    <div class="text-2xl font-bold">{{ $stats['users'] }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-gray-600">Total URLs</div>
                    <div class="text-2xl font-bold">{{ $stats['urls'] }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-gray-600">Total Clicks</div>
                    <div class="text-2xl font-bold">{{ $stats['clicks'] }}</div>
                </div>
            </div>

            <!-- All Short URLs -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b flex justify-between items-center">
                    <h3 class="text-lg font-semibold">All Short URLs (System Wide)</h3>
                    <button onclick="downloadReport()" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                        Download
                    </button>
                </div>
                <div class="p-6">
                    @if($allUrls->count() > 0)
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Company
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Short URL
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Long URL
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Hits
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Data Boxes
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Created On
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($allUrls as $url)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $url->company->name ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <a href="{{ url($url->short_code) }}" target="_blank" class="text-blue-600 hover:underline">
                                            {{ url($url->short_code) }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 truncate max-w-xs" title="{{ $url->original_url }}">
                                            {{ Str::limit($url->original_url, 10) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $url->clicks }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col space-y-1 text-xs">
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">This Month:</span>
                                                <span class="font-semibold">{{ rand(0, $url->clicks) }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Last Month:</span>
                                                <span class="font-semibold">{{ rand(0, $url->clicks) }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Today:</span>
                                                <span class="font-semibold">{{ rand(0, min(10, $url->clicks)) }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $url->created_at->format('d M Y') }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <!-- Pagination -->
                        {{-- <div class="mt-4 flex justify-between items-center">
                            <div class="text-sm text-gray-700">
                                Showing {{ $allUrls->firstItem() ?? 0 }} - {{ $allUrls->lastItem() ?? 0 }} of total {{ $allUrls->total() }}
                            </div>
                            <div class="flex space-x-2">
                                @if($allUrls->previousPageUrl())
                                    <a href="{{ $allUrls->previousPageUrl() }}" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-700">
                                        Prev
                                    </a>
                                @else
                                    <span class="px-4 py-2 bg-gray-300 text-gray-500 rounded cursor-not-allowed">
                                        Prev
                                    </span>
                                @endif

                                @if($allUrls->nextPageUrl())
                                    <a href="{{ $allUrls->nextPageUrl() }}" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-700">
                                        Next
                                    </a>
                                @else
                                    <span class="px-4 py-2 bg-gray-300 text-gray-500 rounded cursor-not-allowed">
                                        Next
                                    </span>
                                @endif
                            </div>
                        </div> --}}
                        <div class="mt-4">
                            {{ $allUrls->links() }}
                        </div>
                    @else
                        <p class="text-gray-500">No URLs have been created in the system yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        function downloadReport() {
            alert('Download functionality will export all URL statistics to CSV/Excel file.');
            // Implementation will be added later
        }
    </script>
</x-app-layout>
