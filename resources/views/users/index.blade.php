<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                User Management
            </h2>
            <a href="{{ route('users.create') }}"
                class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-xs font-semibold uppercase tracking-wider text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2">
                + Add Staff
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            @if (session('status'))
                <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-emerald-800">
                    {{ session('status') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mb-4 rounded-lg border border-rose-200 bg-rose-50 p-4 text-rose-800">
                    {{ session('error') }}
                </div>
            @endif

            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50 text-slate-600 uppercase text-xs">
                        <tr>
                            <th class="px-6 py-3">Name</th>
                            <th class="px-6 py-3">Email</th>
                            <th class="px-6 py-3">Role</th>
                            <th class="px-6 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                            <tr class="border-t border-slate-200 transition-colors hover:bg-slate-50">
                                <td class="px-6 py-4 text-slate-900 ">{{ $user->name }}</td>
                                <td class="px-6 py-4 text-slate-900 ">{{ $user->email }}</td>
                                <td class="px-6 py-4">
                                    @if ($user->id === auth()->id())
                                        <span
                                            class="px-2 py-1 text-xs font-semibold rounded-full
            {{ $user->role === 'admin'
                ? 'bg-indigo-100 text-indigo-800 '
                : 'bg-slate-100 text-slate-800 ' }}">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    @elseif ($user->isSuperAdmin())
                                        <span
                                            class="px-2 py-1 text-xs font-semibold rounded-full bg-amber-100 text-amber-800 ">
                                            Superadmin
                                        </span>
                                    @else
                                        <form method="POST" action="{{ route('users.updateRole', $user) }}"
                                            onsubmit="return confirm('Change ' + '{{ $user->name }}' + '\'s role to ' + this.role.options[this.role.selectedIndex].text + '?')">
                                            @csrf
                                            @method('PATCH')
                                            <select name="role" onchange="this.form.submit()"
                                                class="text-xs border-slate-300 rounded-md focus:border-indigo-500 focus:ring-indigo-500">
                                                <option value="staff" {{ $user->role === 'staff' ? 'selected' : '' }}>
                                                    Staff</option>
                                                <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>
                                                    Admin</option>
                                            </select>
                                        </form>
                                    @endif
                                </td>

                                {{-- Delete column --}}
                                <td class="px-6 py-4 text-right">
                                    @can('delete', $user)
                                        <form method="POST" action="{{ route('users.destroy', $user) }}" class="inline"
                                            onsubmit="return confirm('Delete {{ $user->name }}\'s account?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="text-rose-600 hover:underline text-xs">
                                                Delete
                                            </button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-slate-500 ">
                                    No staff yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
