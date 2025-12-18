<x-app-layout>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">

                  @can('invite-users')
                    <a href="{{ route('invitations.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Invite New User
                    </a>
                  @endcan
                <div class="p-6">
                    @if($invitations->count() > 0)
                        <table class="min-w-full">
                            <thead>
                                <tr>
                                    <th class="text-left pb-2">Name</th>
                                    <th class="text-left pb-2">Email</th>
                                    <th class="text-left pb-2">Role</th>
                                    @if(auth()->user()->isSuperAdmin())
                                        <th class="text-left pb-2">Company</th>
                                    @endif
                                    <th class="text-left pb-2">Invited By</th>
                                    {{-- <th class="text-left pb-2">Status</th> --}}
                                    <th class="text-left pb-2">Invited At</th>
                                    <th class="text-left pb-2">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($invitations as $user)
                                <tr class="border-t">
                                    <td class="py-3">{{ $user->name }}</td>
                                    <td class="py-3">{{ $user->email }}</td>
                                    <td class="py-3">{{ $user->getRoleNames()->first() ?? 'N/A' }}</td>
                                    @if(auth()->user()->isSuperAdmin())
                                        <td class="py-3">{{ $user->company->name ?? 'N/A' }}</td>
                                    @endif
                                    <td class="py-3">{{ $user->invitedBy->name ?? 'System' }}</td>
                                    {{-- <td class="py-3">
                                        @if($user->invitation_status === '1')
                                            <span class="px-2 py-1 text-xs rounded bg-green-200 text-green-800">Active</span>
                                        @else
                                            <span class="px-2 py-1 text-xs rounded bg-yellow-200 text-yellow-800">Pending</span>
                                        @endif
                                    </td> --}}
                                    <td class="py-3">{{ $user->invited_at ? $user->invited_at->diffForHumans() : 'N/A' }}</td>
                                    <td class="py-3">
                                        <form action="{{ route('invitations.destroy', $user->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline" onclick="return confirm('Are you sure you want to remove this user?')">
                                                Remove
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="mt-4">
                            {{ $invitations->links() }}
                        </div>
                    @else
                        <p>No invited users found.</p>
                      
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
