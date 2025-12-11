<x-app-layout>


    <div class="py-12">


        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 border-b">
                <h3 class="text-lg font-semibold">Generate Short URL</h3>
            </div>
            <div class="p-6">
                <form action="{{ route('short-urls.store') }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label for="original_url" class="block text-sm font-medium text-gray-700 mb-2">
                            Long URL
                        </label>
                        <input type="url" name="original_url" id="original_url"
                            placeholder="https://example.com/very/long/url" value="{{ old('original_url') }}"
                            class="w-full border-gray-300 rounded-md shadow-sm" required>
                        @error('original_url')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <button type="submit"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded">
                            Generate
                        </button>
                    </div>
                </form>
            </div>
        </div>

        @error('short_code')
            <p style="color:red; font-size:12px; margin-top:5px;">{{ $message }}</p>
        @enderror
    </div>


</x-app-layout>
