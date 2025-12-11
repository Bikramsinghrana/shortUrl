<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Short URL Details
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-2">{{ $shortUrl->title ?? 'Untitled' }}</h3>
                        <p class="text-gray-600">{{ $shortUrl->description }}</p>
                    </div>

                    <div class="space-y-4">
                        <!-- Short URL -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Short URL</label>
                            <div class="flex items-center">
                                <input type="text" value="{{ url($shortUrl->short_code) }}"
                                       class="flex-1 border-gray-300 rounded-md shadow-sm" readonly>
                                <button onclick="copyToClipboard('{{ url($shortUrl->short_code) }}')"
                                        class="ml-2 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    Copy
                                </button>
                            </div>
                        </div>

                        <!-- Original URL -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Original URL</label>
                            <a href="{{ $shortUrl->original_url }}" target="_blank" class="text-blue-600 hover:underline break-all">
                                {{ $shortUrl->original_url }}
                            </a>
                        </div>

                        <!-- Stats -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Total Clicks</label>
                                <p class="text-2xl font-bold">{{ $shortUrl->clicks }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <span class="px-3 py-1 rounded {{ $shortUrl->is_active ? 'bg-green-200' : 'bg-red-200' }}">
                                    {{ $shortUrl->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </div>

                        <!-- Created Info -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Created By</label>
                            <p>{{ $shortUrl->user->name }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Created At</label>
                            <p>{{ $shortUrl->created_at->format('M d, Y H:i') }}</p>
                        </div>

                        @if($shortUrl->expires_at)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Expires At</label>
                                <p>{{ $shortUrl->expires_at->format('M d, Y H:i') }}</p>
                            </div>
                        @endif
                    </div>

                    <!-- Actions -->
                    <div class="mt-6 flex space-x-4">
                        <a href="{{ route('short-urls.index') }}" class="text-gray-600 hover:text-gray-900">Back to List</a>
                        @unless(auth()->user()->isSuperAdmin())
                            <a href="{{ route('short-urls.edit', $shortUrl) }}" class="text-blue-600 hover:text-blue-900">Edit</a>
                        @endunless
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                alert('Short URL copied to clipboard!');
            });
        }
    </script>
</x-app-layout>
