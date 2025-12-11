<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Short URL') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('short-urls.update', $shortUrl) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Original URL -->
                        <div class="mb-4">
                            <label for="original_url" class="block text-sm font-medium text-gray-700">
                                Original URL <span class="text-red-500">*</span>
                            </label>
                            <input type="url" name="original_url" id="original_url"
                                   value="{{ old('original_url', $shortUrl->original_url) }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                                   required>
                            @error('original_url')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Title -->
                        <div class="mb-4">
                            <label for="title" class="block text-sm font-medium text-gray-700">
                                Title (Optional)
                            </label>
                            <input type="text" name="title" id="title"
                                   value="{{ old('title', $shortUrl->title) }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            @error('title')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>


                        <!-- Status -->
                        <div class="mb-4">
                            <label class="flex items-center">
                                <input type="checkbox" name="is_active" value="1"
                                       {{ old('is_active', $shortUrl->is_active) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-blue-600 shadow-sm">
                                <span class="ml-2 text-sm text-gray-700">Active</span>
                            </label>
                        </div>

                        <!-- Expiration Date -->
                        <div class="mb-4">
                            <label for="expires_at" class="block text-sm font-medium text-gray-700">
                                Expiration Date (Optional)
                            </label>
                            <input type="datetime-local" name="expires_at" id="expires_at"
                                   value="{{ old('expires_at', $shortUrl->expires_at?->format('Y-m-d\TH:i')) }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            @error('expires_at')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <div class="flex items-center justify-end">
                            <a href="{{ route('short-urls.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">Cancel</a>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Update Short URL
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
