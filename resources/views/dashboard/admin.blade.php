<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Client Admin Dashboard
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Generate Short URL Form -->
            @unless(auth()->user()->isSuperAdmin())
                <a href="{{ route('short-urls.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Create New URL
                </a>
            @endunless

            <!-- Generated Short URLs -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b flex justify-between items-center">
                    <h3 class="text-lg font-semibold">Generated Short URLs</h3>
                    <button onclick="downloadReport()" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                        Download
                    </button>
                </div>
                <div class="p-6">
                    @if(session('success'))
                        <div id="flash-message" class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded">
                            {{ session('success') }}

                            @if(session('short_url'))
                                <div class="mt-2">
                                    <strong>Short URL:</strong>
                                    <a href="{{ session('short_url') }}" target="_blank" class="text-blue-600 underline">
                                        {{ session('short_url') }}
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endif


                    @if($companyUrls->count() > 0)
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
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
                                        Created On
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($companyUrls as $url)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <a href="{{ url($url->short_code) }}" target="_blank" class="text-blue-600 hover:underline">
                                            {{ url($url->short_code) }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 truncate max-w-md" title="{{ $url->original_url }}">
                                            {{ Str::limit($url->original_url, 50) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $url->clicks }}
                                    </td>
                               
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $url->created_at->format('d M Y') }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <!-- Pagination -->
                        <div class="mt-4">
                            {{ $companyUrls->links() }}
                        </div>
                    @else
                        <p class="text-gray-500">No URLs created yet in your company!</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        function downloadReport() {
            alert('Download functionality will export URL statistics to CSV/Excel file.');
            // Implementation will be added later
        }
    </script>
</x-app-layout>
