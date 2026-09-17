<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            Add Staff
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <form method="POST" action="{{ route('users.store') }}">
                    @csrf

                    <div>
                        <x-input-label for="name" value="Full Name" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                            :value="old('name')" required autofocus />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="email" value="Email" />
                        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
                            :value="old('email')" required />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="role" value="Role" />
                        <select id="role" name="role" required
                            class="mt-1 block w-full border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm">
                            <option value="">-- Select Role --</option>
                            <option value="staff" {{ old('role') === 'staff' ? 'selected' : '' }}>Staff</option>
                            <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                        <x-input-error :messages="$errors->get('role')" class="mt-2" />
                    </div>

                    <p class="mt-4 text-xs text-slate-500 ">
                        The staff member will receive an email to set their own password.
                        The initial password will never be sent via email.
                    </p>

                    <div class="flex items-center justify-end mt-6">
                        <a href="{{ route('users.index') }}"
                            class="text-sm text-slate-600 mr-4">Cancel</a>
                        <x-primary-button>Create Staff</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
