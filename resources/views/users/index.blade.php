@extends('layouts.app')

@section('content')
    <style>
        /* Shake Animation for Errors */
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
        .shake-animation {
            animation: shake 0.3s ease-in-out;
        }
        
        /* Valid Input State (Green Border) */
        .valid-input {
            border-color: #10b981 !important; /* Emerald 500 */
            border-width: 2px !important;
            background-color: #f0fdf4 !important; /* Emerald 50 */
        }

        /* Error Input State (Red Border) */
        .error-input {
            border-color: #dc2626 !important; /* Red 600 */
            border-width: 2px !important;
            background-color: #fef2f2 !important; /* Red 50 */
        }
    </style>

    <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
        <div>
            <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight">User Management</h1>
            <p class="text-lg text-gray-600 mt-1">Manage system access and roles.</p>
        </div>
        
        @if(Auth::user()->role === 'super_admin')
        <button onclick="openModal('addUserModal')" 
                class="bg-gradient-to-r from-emerald-700 to-emerald-900 hover:from-emerald-500 hover:to-emerald-700 text-white font-bold py-3 px-6 rounded-xl shadow-lg flex items-center transition-all">
            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
            Add New User
        </button>
        @endif
    </div>

    @if(session('success'))
        <div id="successMessage" class="bg-emerald-100 border-l-4 border-emerald-500 text-emerald-700 p-4 mb-6 shadow-md rounded-r flex items-center justify-between">
            <p class="font-bold">{{ session('success') }}</p>
        </div>
    @endif
    @if(session('error'))
        <div id="errorMessage" class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 shadow-md rounded-r flex items-center justify-between">
            <p class="font-bold">{{ session('error') }}</p>
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-200">
        <table class="min-w-full leading-normal">
            <thead>
                <tr class="bg-gray-800 text-white text-left text-sm font-extrabold uppercase tracking-wider">
                    <th class="px-5 py-4 w-[25%]">User Name</th>
                    <th class="px-5 py-4 w-[25%]">Email Address</th>
                    <th class="px-5 py-4 w-[15%] text-center">Role</th>
                    <th class="px-5 py-4 w-[15%] text-center">Date Created</th>
                    <th class="px-5 py-4 w-[20%] text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="text-gray-900 text-sm">
                @forelse($users as $user)
                    <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                        <td class="px-5 py-4 font-bold text-gray-900">{{ $user->name }}</td>
                        <td class="px-5 py-4 font-bold text-gray-700">{{ $user->email }}</td>
                        <td class="px-5 py-4 text-center">
                            @if($user->role == 'super_admin')
                                <span class="bg-purple-100 text-purple-700 px-3 py-1 rounded-full text-xs font-bold uppercase border border-purple-200 shadow-sm">Super Admin</span>
                            @else
                                <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-bold uppercase border border-blue-200 shadow-sm">Admin</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-center font-bold text-gray-500">
                            {{ $user->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-5 py-4 text-center flex justify-center space-x-2">
                            <button onclick="openEditUserModal({{ $user->id }}, '{{ $user->name }}', '{{ $user->email }}', '{{ $user->role }}')" 
                                    class="px-3 py-1 bg-gradient-to-r from-orange-500 to-orange-700 hover:from-orange-600 hover:to-orange-800 text-white rounded-lg text-xs flex items-center font-bold transition shadow-md" 
                                    title="Edit User">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 00 2 2h11a2 2 0 00 2-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg> Edit
                            </button>
                            
                            @if(Auth::user()->role === 'super_admin' && $user->id != 1 && $user->id != Auth::id())
                                <button onclick="openDeleteUserModal({{ $user->id }})" 
                                        class="px-3 py-1 bg-gradient-to-r from-red-600 to-red-800 hover:from-red-700 hover:to-red-900 text-white rounded-lg text-xs flex items-center font-bold transition shadow-md" 
                                        title="Delete User">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg> Delete
                                </button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-8 text-center text-gray-500 italic">No users found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-gray-200">{{ $users->links() }}</div>

    <div id="addUserModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden z-50 flex items-center justify-center">
        <div class="relative p-8 border w-full max-w-lg shadow-2xl rounded-3xl bg-white">
            <h3 class="text-2xl font-extrabold text-gray-900 mb-6 border-b pb-4">Add New User</h3>
            <form action="{{ route('users.store') }}" method="POST" autocomplete="off">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 uppercase mb-2">UserName</label>
                        <input type="text" name="name" required class="w-full px-4 py-3 rounded-xl border border-gray-900 focus:ring-2 focus:ring-blue-500 outline-none shadow-inner bg-white text-gray-900 placeholder-gray-400" placeholder="Enter Username">
                        @error('name') <p class="text-red-500 text-xs font-bold mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 uppercase mb-2">Email Address</label>
                        <input type="email" name="email" required class="w-full px-4 py-3 rounded-xl border border-gray-900 focus:ring-2 focus:ring-blue-500 outline-none shadow-inner bg-white text-gray-900 placeholder-gray-400" placeholder="Enter Email">
                        @error('email') <p class="text-red-500 text-xs font-bold mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 uppercase mb-2">Password</label>
                        <input type="password" name="password" required class="w-full px-4 py-3 rounded-xl border border-gray-900 focus:ring-2 focus:ring-blue-500 outline-none shadow-inner bg-white text-gray-900 placeholder-gray-400" placeholder="Enter Password">
                        @error('password') <p class="text-red-500 text-xs font-bold mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 uppercase mb-2">Role</label>
                        <div class="relative">
                            <select name="role" class="w-full px-4 py-3 rounded-xl border border-gray-900 focus:ring-2 focus:ring-blue-500 outline-none appearance-none bg-white text-gray-900 shadow-inner cursor-pointer font-bold">
                                <option value="admin">Admin</option>
                                <option value="super_admin">Super Admin</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-900 pointer-events-none"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7"></path></svg></div>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end space-x-3 mt-8">
                    <button type="button" onclick="closeModal('addUserModal')" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl font-bold hover:bg-gray-300 transition">Cancel</button>
                    <button type="submit" class="px-6 py-3 bg-gradient-to-r from-emerald-600 to-emerald-800 text-white rounded-xl font-bold hover:from-emerald-700 hover:to-emerald-900 shadow-lg transition">Create User</button>
                </div>
            </form>
        </div>
    </div>

    <div id="editUserModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden z-50 flex items-center justify-center">
        <div class="relative p-8 border w-full max-w-2xl shadow-2xl rounded-3xl bg-white max-h-[90vh] overflow-y-auto">
            <h3 class="text-2xl font-extrabold text-gray-900 mb-6 border-b pb-4">Edit User Profile</h3>
            
            <form id="editUserForm" method="POST" autocomplete="off" novalidate>
                @csrf @method('PUT')
                <input type="hidden" name="edit_form" value="1">
                <input type="hidden" name="user_id" value="{{ old('user_id', $user->id ?? '') }}">
                <div class="space-y-6">
                    
                    <div class="bg-gray-50 p-6 rounded-2xl border border-gray-200 shadow-sm">
                        <h4 class="text-sm font-bold text-gray-900 uppercase mb-4 tracking-wide border-b border-gray-300 pb-2">User Details</h4>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 uppercase mb-2">Current Username</label>
                                <input type="text" id="display_current_name" readonly class="w-full px-4 py-3 rounded-xl border border-gray-300 bg-gray-200 text-gray-600 cursor-not-allowed text-sm font-bold shadow-inner">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 uppercase mb-2">New Username</label>
                                <input type="text" id="edit_name" name="name" required 
                                       class="w-full px-4 py-3 rounded-xl border border-gray-900 focus:ring-2 focus:ring-blue-500 outline-none shadow-inner bg-white text-gray-900 font-bold placeholder-gray-400 transition-colors @error('name') error-input @enderror" 
                                       placeholder="Officer Name">
                                <p class="error-text text-red-600 text-xs font-bold mt-1 hidden">Username is required</p>
                                @error('name') <p class="text-red-500 text-xs font-bold mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 uppercase mb-2">Current Email</label>
                                <input type="text" id="display_current_email" readonly class="w-full px-4 py-3 rounded-xl border border-gray-300 bg-gray-200 text-gray-600 cursor-not-allowed text-sm font-bold shadow-inner">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 uppercase mb-2">New Email</label>
                                <input type="email" id="edit_email" name="email" required 
                                       class="w-full px-4 py-3 rounded-xl border border-gray-900 focus:ring-2 focus:ring-blue-500 outline-none shadow-inner bg-white text-gray-900 font-bold placeholder-gray-400 transition-colors @error('email') error-input @enderror" 
                                       placeholder="email@bfp.gov.ph">
                                <p class="error-text text-red-600 text-xs font-bold mt-1 hidden">Email is required</p>
                                @error('email') <p class="text-red-500 text-xs font-bold mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <div id="security_check_section" class="bg-red-50 p-6 rounded-2xl border border-red-100 shadow-sm">
                        <h4 class="text-xs font-bold text-red-800 uppercase mb-4 flex items-center gap-2 border-b border-red-200 pb-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            Security Verification & Password Change
                        </h4>

                        <div class="mb-6">
                            <label class="block text-sm font-bold text-gray-700 uppercase mb-2">Current Password <span class="text-red-500">*</span></label>
                            
                            <div class="relative">
                                <input type="password" name="current_password" id="current_password" 
                                       class="w-full px-4 py-3 rounded-xl border border-gray-900 focus:ring-2 focus:ring-blue-500 outline-none shadow-inner bg-white pr-12 text-gray-900 font-bold placeholder-gray-400 transition-colors @error('current_password') shake-animation @enderror"
                                       placeholder="Enter Current Password">
                                <button type="button" onclick="togglePassword('current_password')" class="absolute inset-y-0 right-0 px-4 flex items-center text-gray-600 hover:text-black transition">
                                    <svg class="w-5 h-5 toggle-eye" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </button>
                            </div>
                            <p class="error-text text-red-600 text-xs font-bold mt-1 hidden">Current password is required to save changes</p>
                            @error('current_password') <p class="text-red-600 text-xs font-bold mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 border-t border-red-200 pt-4">
                            
                            <div>
                                <label class="block text-sm font-bold text-gray-700 uppercase mb-2">New Password (Optional)</label>
                                <div class="relative">
                                    <input type="password" name="new_password" id="new_password" 
                                           class="w-full px-4 py-3 rounded-xl border border-gray-900 focus:ring-2 focus:ring-blue-500 outline-none shadow-inner bg-white pr-12 text-gray-900 font-bold placeholder-gray-400 transition-colors @error('new_password') error-input @enderror" 
                                           placeholder="Enter New Password">
                                    <button type="button" onclick="togglePassword('new_password')" class="absolute inset-y-0 right-0 px-4 flex items-center text-gray-600 hover:text-black transition">
                                        <svg class="w-5 h-5 toggle-eye" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </button>
                                </div>
                                @error('new_password') <p class="text-red-600 text-xs font-bold mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 uppercase mb-2">Confirm New Password</label>
                                <div class="relative">
                                    <input type="password" name="new_password_confirmation" id="new_password_confirmation" 
                                           class="w-full px-4 py-3 rounded-xl border border-gray-900 focus:ring-2 focus:ring-blue-500 outline-none shadow-inner bg-white pr-12 text-gray-900 font-bold placeholder-gray-400 transition-colors" 
                                           placeholder="Confirm New Password">
                                    <button type="button" onclick="togglePassword('new_password_confirmation')" class="absolute inset-y-0 right-0 px-4 flex items-center text-gray-600 hover:text-black transition">
                                        <svg class="w-5 h-5 toggle-eye" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </button>
                                </div>
                                <p id="password_match_error" class="error-text text-red-600 text-xs font-bold mt-1 hidden">Passwords do not match</p>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="role" id="hidden_role_input">

                </div>
                
                <div class="flex justify-end space-x-3 mt-8 pt-4 border-t border-gray-100">
                    <button type="button" onclick="closeModal('editUserModal')" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl font-bold hover:bg-gray-300 transition text-sm">Cancel</button>
                    <button type="submit" id="updateUserBtn" class="px-8 py-3 bg-gradient-to-r from-blue-600 to-blue-800 hover:from-blue-700 hover:to-blue-900 text-white rounded-xl font-bold shadow-lg transition transform hover:-translate-y-0.5 text-sm">Update Profile</button>
                </div>
            </form>
        </div>
    </div>

    <div id="deleteUserModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden z-50 flex items-center justify-center">
        <div class="relative p-8 border w-full max-w-md shadow-2xl rounded-2xl bg-white text-center">
            <h3 class="text-xl font-bold text-gray-900 mb-2">Confirm Deletion</h3>
            <p class="text-gray-500 mb-6">Are you sure you want to delete this user?</p>
            <form id="deleteUserForm" method="POST">
                @csrf @method('DELETE')
                <div class="flex justify-center space-x-3"><button type="button" onclick="closeModal('deleteUserModal')" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl font-bold">Cancel</button><button type="submit" class="px-6 py-3 bg-red-600 text-white rounded-xl font-bold hover:bg-red-700">Yes, Delete</button></div>
            </form>
        </div>
    </div>

    <script>
        const currentUserId = {{ Auth::id() }};

        // --- Basic Modal Functions ---
        function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
        function closeModal(id) { document.getElementById(id).classList.add('hidden'); }
        
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const button = input.parentNode.querySelector(".toggle-eye");
            
            if (input.type === "password") {
                input.type = "text";
                button.classList.add("text-blue-600"); 
            } else {
                input.type = "password";
                button.classList.remove("text-blue-600");
            }
        }

        // --- Edit Modal Setup ---
        function openEditUserModal(id, name, email, role) {
            // Reset validation states
            document.querySelectorAll('.error-input').forEach(el => el.classList.remove('error-input'));
            document.querySelectorAll('.valid-input').forEach(el => el.classList.remove('valid-input'));
            document.querySelectorAll('.error-text').forEach(el => el.classList.add('hidden'));

            document.getElementById('display_current_name').value = name;
            document.getElementById('display_current_email').value = email;
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_email').value = email;
            
            const hiddenRole = document.getElementById('hidden_role_input');
            if(hiddenRole) hiddenRole.value = role;

            const securitySection = document.getElementById('security_check_section');
            const currentPass = document.getElementById('current_password');

            if (id === currentUserId) {
                securitySection.classList.remove('hidden');
                currentPass.required = true; 
                currentPass.value = ""; 
                document.getElementById('new_password').value = "";
                document.getElementById('new_password_confirmation').value = "";
            } else {
                securitySection.classList.add('hidden');
                currentPass.required = false;
            }

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
            const err = document.getElementById('errorMessage');
            if (err) setTimeout(() => { err.classList.add('opacity-0'); setTimeout(() => err.remove(), 1000); }, 4000);

            // 1. GENERIC INPUTS
            const genericInputs = document.querySelectorAll('#editUserForm input:not([readonly]):not([type="password"])');
            genericInputs.forEach(input => {
                input.addEventListener('input', function() {
                    this.classList.remove('shake-animation', 'error-input');
                    const container = this.closest('div');
                    let errorP = container.querySelector('.error-text');
                    if(errorP) errorP.classList.add('hidden');

                    if(this.value.trim().length > 0) {
                        this.classList.add('valid-input');
                    } else {
                        this.classList.remove('valid-input');
                    }
                });
            });

            // 2. OLD PASSWORD (Stays Neutral)
            const currentPassInput = document.getElementById('current_password');
            if(currentPassInput) {
                currentPassInput.addEventListener('input', function() {
                    this.classList.remove('shake-animation'); // Remove shake if typing
                    // Note: We are NOT adding valid-input or error-input here.
                    // It will remain the default gray/black border.
                });
            }

            // 3. NEW & CONFIRM PASSWORD MATCH LOGIC
            const newPassInput = document.getElementById('new_password');
            const confirmPassInput = document.getElementById('new_password_confirmation');
            const matchError = document.getElementById('password_match_error');

            function checkPasswordMatch() {
                const newVal = newPassInput.value;
                const confirmVal = confirmPassInput.value;

                confirmPassInput.classList.remove('valid-input', 'error-input', 'shake-animation');
                matchError.classList.add('hidden');

                // Green for New Password if text exists
                if(newVal.length > 0) newPassInput.classList.add('valid-input');
                else newPassInput.classList.remove('valid-input');

                // Match Logic for Confirm
                if(confirmVal.length > 0) {
                    if (newVal === confirmVal) {
                        confirmPassInput.classList.add('valid-input'); // Green
                    } else {
                        confirmPassInput.classList.add('error-input'); // Red
                        matchError.classList.remove('hidden');
                    }
                }
            }

            if(newPassInput && confirmPassInput) {
                newPassInput.addEventListener('input', checkPasswordMatch);
                confirmPassInput.addEventListener('input', checkPasswordMatch);
            }

            // 4. SUBMIT VALIDATION
            const form = document.getElementById('editUserForm');
            form.addEventListener('submit', function(e) {
                let isValid = true;
                
                // Check Required
                const requiredInputs = form.querySelectorAll('input[required]');
                requiredInputs.forEach(input => {
                    if(input.offsetParent !== null && input.value.trim() === '') {
                        e.preventDefault();
                        isValid = false;
                        
                        input.classList.remove('valid-input');
                        input.classList.add('error-input');
                        input.classList.add('shake-animation');
                        
                        let errorMsg = input.parentNode.querySelector('.error-text');
                        if (!errorMsg) errorMsg = input.parentNode.parentNode.querySelector('.error-text');
                        if (errorMsg) errorMsg.classList.remove('hidden');
                        
                        setTimeout(() => input.classList.remove('shake-animation'), 300);
                    }
                });

                // Check Match on Submit
                if (newPassInput && confirmPassInput && newPassInput.value !== confirmPassInput.value) {
                    e.preventDefault();
                    isValid = false;
                    confirmPassInput.classList.remove('valid-input');
                    confirmPassInput.classList.add('error-input');
                    confirmPassInput.classList.add('shake-animation');
                    matchError.classList.remove('hidden');
                    setTimeout(() => confirmPassInput.classList.remove('shake-animation'), 300);
                }
            });
        });
    </script>

    @if ($errors->any() && old('edit_form'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const userId = @json(old('user_id'));
            const name = @json(old('name'));
            const email = @json(old('email'));
            const role = @json(old('role'));

            // Call your modal function with old values
            openEditUserModal(userId, name, email, role);
        });</script> 
    @endif
@endsection