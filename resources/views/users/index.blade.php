@extends('layouts.app')

@section('content')
    <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
        <div>
            <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight">User Management</h1>
            <p class="text-lg text-gray-600 mt-1">Manage system access and roles.</p>
        </div>
        
        <button onclick="openModal('addUserModal')" 
                class="bg-gradient-to-r from-blue-600 to-blue-800 hover:from-blue-700 hover:to-blue-900 text-white font-bold py-3 px-6 rounded-xl shadow-lg flex items-center text-lg transition transform hover:scale-105">
            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
            Add New User
        </button>
    </div>

    @if(session('success'))
        <div id="successMessage" class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 shadow-md rounded-r flex items-center justify-between">
            <p class="font-bold">{{ session('success') }}</p>
            <button onclick="this.parentElement.remove()" class="text-green-500 hover:text-green-700"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-200">
        <table class="min-w-full leading-normal">
            <thead>
                <tr class="bg-gray-800 text-white text-left text-sm font-extrabold uppercase tracking-wider border-b border-gray-900">
                    <th class="px-5 py-4">User Name</th>
                    <th class="px-5 py-4">Email Address</th>
                    <th class="px-5 py-4 text-center">Role</th>
                    <th class="px-5 py-4 text-center">Date Created</th>
                    <th class="px-5 py-4 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="text-gray-900 text-sm">
                @foreach($users as $user)
                    <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                        <td class="px-5 py-4 flex items-center">
                            <div class="h-10 w-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold mr-3 border border-blue-200">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                            <span class="font-bold">{{ $user->name }}</span>
                        </td>
                        <td class="px-5 py-4 font-medium text-gray-600">{{ $user->email }}</td>
                        <td class="px-5 py-4 text-center">
                            @if($user->role == 'super_admin')
                                <span class="bg-purple-100 text-purple-700 px-3 py-1 rounded-full text-xs font-bold uppercase border border-purple-200">Super Admin</span>
                            @else
                                <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-bold uppercase border border-blue-200">Station Admin</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-center text-gray-500">{{ $user->created_at->format('M d, Y') }}</td>
                        <td class="px-5 py-4 text-center flex justify-center space-x-2">
                            <button onclick="openEditUserModal({{ $user->id }}, '{{ $user->name }}', '{{ $user->email }}', '{{ $user->role }}')" class="p-2 bg-orange-50 text-orange-600 hover:bg-orange-100 rounded-lg border border-orange-200 transition" title="Edit">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 00 2 2h11a2 2 0 00 2-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </button>
                            
                            @if($user->id != 1)
                                <button onclick="openDeleteUserModal({{ $user->id }})" class="p-2 bg-red-50 text-red-600 hover:bg-red-100 rounded-lg border border-red-200 transition" title="Delete">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div id="addUserModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden z-50 flex items-center justify-center">
        <div class="relative p-8 border w-full max-w-lg shadow-2xl rounded-3xl bg-white">
            <h3 class="text-2xl font-extrabold text-gray-900 mb-6">Add New User</h3>
            <form action="{{ route('users.store') }}" method="POST" autocomplete="off">
                @csrf
                <div class="space-y-4">
                    <div><label class="block text-xs font-bold text-gray-700 uppercase mb-1">Full Name</label><input type="text" name="name" required class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 outline-none"></div>
                    <div><label class="block text-xs font-bold text-gray-700 uppercase mb-1">Email</label><input type="email" name="email" required class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 outline-none"></div>
                    <div><label class="block text-xs font-bold text-gray-700 uppercase mb-1">Password</label><input type="password" name="password" required class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 outline-none"></div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Role</label>
                        <div class="relative">
                            <select name="role" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 outline-none appearance-none bg-white">
                                <option value="admin">Station Admin</option>
                                <option value="super_admin">Super Admin</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-500 pointer-events-none"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7"></path></svg></div>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end space-x-3 mt-8">
                    <button type="button" onclick="closeModal('addUserModal')" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl font-bold">Cancel</button>
                    <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-xl font-bold shadow-lg">Create User</button>
                </div>
            </form>
        </div>
    </div>

    <div id="editUserModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden z-50 flex items-center justify-center">
        <div class="relative p-8 border w-full max-w-lg shadow-2xl rounded-3xl bg-white">
            <h3 class="text-2xl font-extrabold text-gray-900 mb-6">Edit User</h3>
            <form id="editUserForm" method="POST" autocomplete="off">
                @csrf @method('PUT')
                <div class="space-y-4">
                    <div><label class="block text-xs font-bold text-gray-700 uppercase mb-1">Full Name</label><input type="text" id="edit_name" name="name" required class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 outline-none"></div>
                    <div><label class="block text-xs font-bold text-gray-700 uppercase mb-1">Email</label><input type="email" id="edit_email" name="email" required class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 outline-none"></div>
                    <div><label class="block text-xs font-bold text-gray-700 uppercase mb-1">Password (Leave blank to keep)</label><input type="password" name="password" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 outline-none" placeholder="********"></div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Role</label>
                        <div class="relative">
                            <select id="edit_role" name="role" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 outline-none appearance-none bg-white">
                                <option value="admin">Station Admin</option>
                                <option value="super_admin">Super Admin</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-500 pointer-events-none"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7"></path></svg></div>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end space-x-3 mt-8">
                    <button type="button" onclick="closeModal('editUserModal')" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl font-bold">Cancel</button>
                    <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-xl font-bold shadow-lg">Update User</button>
                </div>
            </form>
        </div>
    </div>

    <div id="deleteUserModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden z-50 flex items-center justify-center">
        <div class="relative p-8 border w-full max-w-md shadow-2xl rounded-2xl bg-white text-center">
            <h3 class="text-xl font-bold text-gray-900 mb-2">Confirm Deletion</h3>
            <p class="text-gray-500 mb-6">Are you sure you want to remove this user access?</p>
            <form id="deleteUserForm" method="POST">
                @csrf @method('DELETE')
                <div class="flex justify-center space-x-3"><button type="button" onclick="closeModal('deleteUserModal')" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg font-bold">Cancel</button><button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-lg font-bold">Yes, Delete</button></div>
            </form>
        </div>
    </div>

    <script>
        function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
        function closeModal(id) { document.getElementById(id).classList.add('hidden'); }
        function openEditUserModal(id, name, email, role) {
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_email').value = email;
            document.getElementById('edit_role').value = role;
            document.getElementById('editUserForm').action = `/users/${id}`;
            openModal('editUserModal');
        }
        function openDeleteUserModal(id) {
            document.getElementById('deleteUserForm').action = `/users/${id}`;
            openModal('deleteUserModal');
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            const msg = document.getElementById('successMessage');
            if (msg) setTimeout(() => { msg.classList.add('opacity-0'); setTimeout(() => msg.remove(), 1000); }, 3000);
        });
    </script>
@endsection