<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Send Invitation
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('invitations.store') }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700">
                                Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" id="name"
                                   value="{{ old('name') }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                                   required>
                            @error('name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="mb-4">
                            <label for="email" class="block text-sm font-medium text-gray-700">
                                Email Address <span class="text-red-500">*</span>
                            </label>
                            <input type="email" name="email" id="email"
                                   value="{{ old('email') }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                                   required>
                            @error('email')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Role -->
                        <div class="mb-4">
                            <label for="role" class="block text-sm font-medium text-gray-700">
                                Role <span class="text-red-500">*</span>
                            </label>
                            <select name="role" id="role"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                                    required>
                                <option value="">Select a role...</option>
                                @foreach($availableRoles as $role)
                                    <option value="{{ $role }}" {{ old('role') == $role ? 'selected' : '' }}>
                                        {{ $role }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        @if(Auth::user()->isSuperAdmin())
                            <!-- Company Selection for SuperAdmin -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Company <span class="text-red-500">*</span>
                                </label>

                                <div class="space-y-2">
                                    <label class="flex items-center">
                                        <input type="radio" name="company_option" value="new"
                                               {{ old('company_option', 'new') == 'new' ? 'checked' : '' }}
                                               class="mr-2" onchange="toggleCompanyFields()">
                                        <span class="text-sm text-gray-700">Create New Company</span>
                                    </label>

                                    <label class="flex items-center">
                                        <input type="radio" name="company_option" value="existing"
                                               {{ old('company_option') == 'existing' ? 'checked' : '' }}
                                               class="mr-2" onchange="toggleCompanyFields()">
                                        <span class="text-sm text-gray-700">Select Existing Company</span>
                                    </label>
                                </div>
                                @error('company_option')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- New Company Name -->
                            <div class="mb-4" id="new_company_field" style="{{ old('company_option', 'new') == 'new' ? '' : 'display:none;' }}">
                                <label for="company_name" class="block text-sm font-medium text-gray-700">
                                    Company Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="company_name" id="company_name"
                                       value="{{ old('company_name') }}"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                @error('company_name')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Existing Company Selection -->
                            <div class="mb-4" id="existing_company_field" style="{{ old('company_option') == 'existing' ? '' : 'display:none;' }}">
                                <label for="company_id" class="block text-sm font-medium text-gray-700">
                                    Select Company <span class="text-red-500">*</span>
                                </label>
                                <select name="company_id" id="company_id"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                    <option value="">Select a company..</option>
                                    @foreach($companies as $company)
                                        <option value="{{ $company->id }}" {{ old('company_id') == $company->id ? 'selected' : '' }}>
                                            {{ $company->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('company_id')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <script>
                                function toggleCompanyFields() {
                                    const option = document.querySelector('input[name="company_option"]:checked').value;
                                    document.getElementById('new_company_field').style.display = option === 'new' ? 'block' : 'none';
                                    document.getElementById('existing_company_field').style.display = option === 'existing' ? 'block' : 'none';
                                }
                            </script>
                        @endif

                
                        <div class="flex items-center justify-end">
                            <a href="{{ route('invitations.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">Cancel</a>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Send Invitation
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
