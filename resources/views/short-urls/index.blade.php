<x-app-layout>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @unless(auth()->user()->isSuperAdmin())
                <a href="{{ route('short-urls.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Create New URL
                </a>
            @endunless
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($shortUrls->count() > 0)
                        <table class="min-w-full">
                            <thead>
                                <tr>
                                    @if(auth()->user()->isSuperAdmin())
                                        <th class="text-left pb-2">Company</th>
                                    @endif
                                    @if(auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())
                                        <th class="text-left pb-2">User</th>
                                    @endif
                                    <th class="text-left pb-2">Title</th>
                                    <th class="text-left pb-2">Short URL</th>
                                    <th class="text-left pb-2">Original URL</th>
                                    <th class="text-left pb-2">Clicks</th>
                                    <th class="text-left pb-2">Status</th>
                                    <th class="text-left pb-2">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($shortUrls as $url)
                                <tr class="border-t">
                                    @if(auth()->user()->isSuperAdmin())
                                        <td class="py-3">{{ $url->company->name }}</td>
                                    @endif
                                    @if(auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())
                                        <td class="py-3">{{ $url->user->name }}</td>
                                    @endif
                                    <td class="py-3">{{ $url->title ?? 'Untitled' }}</td>
                                    <td class="py-3">
                                        <a href="{{ url($url->short_code) }}" target="_blank" class="text-blue-600 hover:underline">
                                            {{ substr(url($url->short_code), 0, 30) }}...
                                        </a>
                                    </td>
                                    <td class="py-3">{{ Str::limit($url->original_url, 40) }}</td>
                                    <td class="py-3">{{ $url->clicks }}</td>
                                    <td class="py-3">
                                        <span class="px-2 py-1 text-xs rounded {{ $url->is_active ? 'bg-green-200' : 'bg-red-200' }}">
                                            {{ $url->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="py-3">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('short-urls.show', $url) }}" class="text-blue-600 hover:underline">View</a>
                                            @unless(auth()->user()->isSuperAdmin())
                                                <a href="{{ route('short-urls.edit', $url) }}" class="text-yellow-600 hover:underline">Edit</a>
                                                <form action="{{ route('short-urls.destroy', $url) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:underline" onclick="return confirm('Are you sure?')">Delete</button>
                                                </form>
                                            @endunless
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="mt-4">
                            {{ $shortUrls->links() }}
                        </div>
                    @else
                        <p>No short URLs found. </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
