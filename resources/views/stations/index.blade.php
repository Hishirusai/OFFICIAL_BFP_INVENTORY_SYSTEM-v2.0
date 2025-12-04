@extends('layouts.app')

@section('content')
    <div class="flex flex-col md:flex-row justify-between items-end mb-8 gap-4">
        <div>
            <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight">Stations & Units</h1>
            <p class="text-lg text-gray-600 mt-1">Manage all fire stations and units.</p>
        </div>

        <div class="flex flex-col md:flex-row gap-3 w-full md:w-auto items-end">
            
            <form action="{{ route('stations.index') }}" method="GET" class="w-full md:w-96 relative" autocomplete="off">
                <div class="relative group">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-blue-500 z-10">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </span>
                    
                    <input type="hidden" id="strictCodeInput" name="strict_code_search" value="{{ request('strict_code_search') }}">

                    <input type="text" id="itemSearchInput" name="item_search" value="{{ request('item_search') }}" 
                           class="w-full py-3 pl-10 pr-10 text-gray-700 bg-blue-50 border border-blue-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm transition placeholder-blue-400" 
                           placeholder="Search Item (Name, Code, Type)..."
                           oninput="document.getElementById('strictCodeInput').value = ''">
                    
                    <div id="searchSpinner" class="absolute inset-y-0 right-10 flex items-center hidden">
                        <svg class="animate-spin h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>

                    @if(request('item_search'))
                        <a href="{{ route('stations.index') }}" 
                           class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-red-600 z-10 transition duration-200"
                           title="Clear Item Search">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </a>
                    @endif
                </div>

                <div id="suggestionsList" class="absolute z-50 w-full bg-white border border-gray-200 rounded-lg shadow-xl mt-1 hidden max-h-60 overflow-y-auto"></div>
            </form>

            <div class="relative w-full md:w-64">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path></svg>
                </span>
                
                <input type="text" id="searchInput" onkeyup="filterStations()" autocomplete="off"
                       class="w-full py-3 pl-10 pr-10 text-gray-700 bg-white border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500 shadow-sm transition" 
                       placeholder="Search Station Name...">

                <button id="clearStationBtn" onclick="clearStationFilter()" 
                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-red-600 hidden z-10 transition duration-200"
                        title="Clear Filter">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <button onclick="openModal('addStationModal')" 
                    class="bg-gradient-to-r from-emerald-700 to-emerald-900 hover:from-emerald-500 hover:to-emerald-700 text-white font-bold py-3 px-6 rounded-xl shadow-lg transform hover:scale-105 transition flex items-center text-lg border border-green-700 whitespace-nowrap justify-center w-full md:w-auto">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Add Station
            </button>
        </div>
    </div>



    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
            <div class="bg-blue-600 rounded-2xl p-6 shadow-lg text-white flex flex-col justify-between relative overflow-hidden">
                <div class="relative z-10">
                    <p class="text-xs font-bold tracking-widest uppercase opacity-80">Total Items</p>
                    <h2 class="text-4xl font-extrabold mt-2" id="cardTotalQty">
                        {{ number_format($totalMatchedQuantity) }}
                    </h2>
                </div>
                <div class="absolute top-4 right-4 bg-white/20 p-3 rounded-xl backdrop-blur-sm">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                </div>
            </div>

            <div class="bg-gray-900 rounded-2xl p-6 shadow-lg text-white flex flex-col justify-between relative overflow-hidden">
                <div class="relative z-10">
                    <p class="text-xs font-bold tracking-widest uppercase opacity-80">Total Value</p>
                    <h2 class="text-4xl font-extrabold mt-2" id="cardTotalVal">
                        ₱{{ number_format($totalMatchedCost, 2) }}
                    </h2>
                </div>
                <div class="absolute top-4 right-4 bg-white/20 p-3 rounded-xl backdrop-blur-sm">
                    <span class="font-bold text-xl">₱</span>
                </div>
            </div>
        </div>

    @if(session('success'))
        <div id="successMessage" class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 p-4 mb-8 shadow-sm rounded-r flex items-center justify-between transition-opacity duration-1000 ease-out">
            <div><p class="font-bold text-lg">Success</p><p>{{ session('success') }}</p></div>
            <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
        </div>
    @endif

    <div id="stationsGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($stations as $station)
            @php
                $isMain = $station->id == 1;
                $mainStyle = "border-t-4 border-red-600 border border-gray-200 shadow-lg hover:shadow-xl hover:border-red-600";
                $subStyle  = "border-t-4 border-orange-500 border border-gray-200 shadow-md hover:shadow-lg hover:border-orange-500";
                $iconBg = $isMain ? 'bg-gradient-to-br from-red-500 to-red-700 text-white' : 'bg-gradient-to-br from-orange-400 to-orange-600 text-white';
            @endphp

            <div class="station-card relative rounded-2xl transition duration-300 group bg-white {{ $isMain ? $mainStyle : $subStyle }}"
                data-name="{{ strtolower($station->name) }}" 
                data-location="{{ strtolower($station->location ?? '') }}"
                data-qty="{{ $station->matched_quantity ?? 0 }}"
                data-val="{{ $station->matched_cost ?? 0 }}">
                
                <a href="{{ route('stations.show', [
                    'station' => $station->id, 
                    'item_search' => request('item_search'),
                    'strict_code_search' => request('strict_code_search')
                ]) }}" class="block p-6 pb-20 h-full w-full">
                    
                    <div class="absolute top-4 right-4 w-8 h-8 rounded-full flex items-center justify-center transition {{ $isMain ? 'text-red-500' : 'text-orange-500' }}">
                        @if($isMain)<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                        @else<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>@endif
                    </div>

                    <div class="flex items-start mb-4">
                        <div class="flex-shrink-0 h-14 w-14 rounded-xl flex items-center justify-center font-extrabold text-xl shadow-md {{ $iconBg }}">
                            @if($isMain) MS @else S{{ $loop->iteration - 1 }} @endif
                        </div>
                        <div class="ml-4 pr-10"> 
                            <h3 class="text-xl font-extrabold leading-tight {{ $isMain ? 'text-red-800' : 'text-gray-800' }}">{{ $station->name }}</h3>
                            <div class="flex items-center mt-2 text-sm font-medium text-gray-500"><svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>{{ $station->location ?? 'No Location' }}</div>
                        </div>
                    </div>

                    @if(isset($isSearchingItems) && $isSearchingItems)
                    <div class="mt-4 bg-blue-300 border border-blue-100 rounded-lg p-3 flex items-center justify-between">
                        <span class="text-sm font-bold text-blue-600 uppercase tracking-wide">Quantity Found</span>
                        <span class="text-xl font-extrabold text-blue-700 bg-white px-3 py-1 rounded shadow-sm border border-blue-100">
                            {{ number_format($station->matched_quantity ?? 0) }}
                        </span>
                    </div>
                    @endif
                </a>

                <div class="absolute bottom-4 right-4 flex space-x-2 z-10">
                    <button onclick="openEditModal({{ $station->id }}, '{{ $station->name }}', '{{ $station->location }}')" class="p-2 rounded-xl transition tooltip border bg-gradient-to-r from-blue-50 to-blue-100 text-blue-600 border-blue-200 hover:from-blue-500 hover:to-blue-600 hover:text-white shadow-sm" title="Edit"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 00 2 2h11a2 2 0 00 2-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg></button>
                    @if($isMain)<span class="p-2 text-red-300 cursor-not-allowed border border-gray-200 bg-gray-50 rounded-xl"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg></span>@else<button onclick="openDeleteModal({{ $station->id }})" class="p-2 rounded-xl transition border bg-gradient-to-r from-red-50 to-red-100 text-red-500 border-red-200 hover:from-red-500 hover:to-red-600 hover:text-white shadow-sm" title="Delete"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>@endif
                </div>
            </div>
        @empty
            <div class="col-span-4 text-center py-20">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gray-200 mb-6"><svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg></div>
                <h3 class="text-xl font-bold text-gray-900">No stations found</h3>
                @if(isset($isSearchingItems) && $isSearchingItems)<p class="text-gray-500 mt-2 text-lg">No items matched your search.</p><a href="{{ route('stations.index') }}" class="mt-4 inline-block text-blue-600 hover:underline font-bold">Clear Search</a>@else<p class="text-gray-500 mt-2 text-lg">Get started by adding a new station.</p>@endif
            </div>
        @endforelse
    </div>
    
    <div id="noResultsMessage" class="hidden text-center py-20"><h3 class="text-xl font-bold text-gray-500">No matching stations found</h3></div>

    <div id="addStationModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm overflow-y-auto h-full w-full hidden z-50 flex items-center justify-center">
        <div class="relative p-8 border w-full max-w-md shadow-2xl rounded-2xl bg-white">
            <h3 class="text-2xl font-extrabold text-gray-900 mb-6">Add New Station</h3>
            <form action="{{ route('stations.store') }}" method="POST" autocomplete="off" novalidate>
                @csrf
                <div class="mb-5"><label class="block text-gray-700 text-sm font-bold mb-2 uppercase">Station Name</label><input type="text" name="name" required value="{{ old('name') }}" oninput="clearError(this)" class="w-full px-4 py-3 rounded-lg bg-gray-50 border border-gray-300 outline-none transition" placeholder="Enter station">@error('name')<p class="error-msg text-red-500 text-xs italic mt-2 font-bold">{{ $message }}</p>@enderror</div>
                <div class="mb-6"><label class="block text-gray-700 text-sm font-bold mb-2 uppercase">Location</label><input type="text" name="location" required value="{{ old('location') }}" oninput="clearError(this)" class="w-full px-4 py-3 rounded-lg bg-gray-50 border border-gray-300 outline-none transition" placeholder="Enter location">@error('location')<p class="error-msg text-red-500 text-xs italic mt-2 font-bold">{{ $message }}</p>@enderror</div>
                <div class="flex justify-end space-x-3"><button type="button" onclick="closeModal('addStationModal')" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg font-bold hover:bg-gray-300 transition">Cancel</button><button type="submit" class="px-6 py-2 bg-red-700 text-white rounded-lg font-bold hover:bg-red-800 transition shadow-lg">Save</button></div>
            </form>
        </div>
    </div>
    <div id="editStationModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm overflow-y-auto h-full w-full hidden z-50 flex items-center justify-center"><div class="relative p-8 border w-full max-w-md shadow-2xl rounded-2xl bg-white"><h3 class="text-2xl font-extrabold text-gray-900 mb-6">Edit Station</h3><form id="editForm" method="POST" autocomplete="off" novalidate> @csrf @method('PUT') <div class="mb-5"><label class="block text-gray-700 text-sm font-bold mb-2 uppercase">Station Name</label><input type="text" id="editName" name="name" required class="w-full px-4 py-3 rounded-lg bg-gray-50 border border-gray-300 outline-none transition"></div> <div class="mb-6"><label class="block text-gray-700 text-sm font-bold mb-2 uppercase">Location</label><input type="text" id="editLocation" name="location" required class="w-full px-4 py-3 rounded-lg bg-gray-50 border border-gray-300 outline-none transition"></div> <div class="flex justify-end space-x-3"><button type="button" onclick="closeModal('editStationModal')" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg font-bold hover:bg-gray-300 transition">Cancel</button><button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg font-bold hover:bg-blue-700 transition shadow-lg">Update</button></div> </form></div></div>
    <div id="deleteStationModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm overflow-y-auto h-full w-full hidden z-50 flex items-center justify-center"><div class="relative p-8 border w-full max-w-md shadow-2xl rounded-2xl bg-white text-center"> <h3 class="text-xl font-bold text-gray-900 mb-2">Delete Station?</h3> <p class="text-gray-500 mb-6">Are you sure?</p> <form id="deleteForm" method="POST"> @csrf @method('DELETE') <div class="flex justify-center space-x-3"><button type="button" onclick="closeModal('deleteStationModal')" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg font-bold hover:bg-gray-300 transition">Cancel</button><button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-lg font-bold hover:bg-red-700 transition shadow-lg">Yes, Delete</button></div> </form> </div></div>

    <script>
        const searchInput = document.getElementById('itemSearchInput');
        const suggestionsList = document.getElementById('suggestionsList');
        const spinner = document.getElementById('searchSpinner');
        const strictCodeInput = document.getElementById('strictCodeInput');
        let timeout = null;

        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const query = this.value;
                clearTimeout(timeout);
                if (query.length < 2) { suggestionsList.classList.add('hidden'); return; }

                timeout = setTimeout(() => {
                    spinner.classList.remove('hidden');
                    fetch(`{{ route('stations.autocomplete') }}?query=${query}`)
                        .then(response => response.json())
                        .then(data => {
                            spinner.classList.add('hidden');
                            suggestionsList.innerHTML = '';
                            if (data.length > 0) {
                                suggestionsList.classList.remove('hidden');
                                data.forEach(item => {
                                    const div = document.createElement('div');
                                    div.classList.add('px-4', 'py-3', 'cursor-pointer', 'hover:bg-blue-50', 'border-b', 'border-gray-100', 'transition', 'text-gray-700');
                                    div.innerHTML = `<div class="flex justify-between items-center"><span class="font-bold text-gray-900">${item.name}</span><span class="text-xs font-mono bg-blue-100 text-blue-800 px-2 py-1 rounded ml-2">${item.type}</span></div><div class="text-xs text-gray-400 mt-1">Code: ${item.product_code}</div>`;
                                    div.addEventListener('click', () => { searchInput.value = `${item.name} (${item.type})`; strictCodeInput.value = item.product_code; suggestionsList.classList.add('hidden'); searchInput.form.submit(); });
                                    suggestionsList.appendChild(div);
                                });
                            } else { suggestionsList.classList.add('hidden'); }
                        })
                        .catch(err => { console.error(err); spinner.classList.add('hidden'); });
                }, 300);
            });
            document.addEventListener('click', function(e) { if (!searchInput.contains(e.target) && !suggestionsList.contains(e.target)) { suggestionsList.classList.add('hidden'); } });
        }

        // Standard Modal Functions
        function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
        function closeModal(id) { document.getElementById(id).classList.add('hidden'); }
        function openEditModal(id, name, loc) { document.getElementById('editName').value=name; document.getElementById('editLocation').value=loc; document.getElementById('editForm').action="/stations/"+id; openModal('editStationModal'); }
        function openDeleteModal(id) { document.getElementById('deleteForm').action="/stations/"+id; openModal('deleteStationModal'); }
        function clearError(i) { i.classList.remove('border-red-500'); i.classList.add('border-gray-300'); if(i.nextElementSibling) i.nextElementSibling.style.display='none'; }
        
        // --- NEW HELPER FUNCTIONS FOR CARDS ---
        function formatNumber(num) {
            return num.toLocaleString('en-US');
        }
        function formatCurrency(num) {
            return '₱' + num.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        // --- UPDATED FILTER FUNCTION ---
        function filterStations() {
            let input = document.getElementById('searchInput');
            let filter = input.value.toLowerCase();
            let clearBtn = document.getElementById('clearStationBtn');
            let cards = document.getElementsByClassName('station-card');
            
            let visibleCount = 0;
            let currentTotalQty = 0;
            let currentTotalVal = 0;

            // Toggle Clear Button Visibility
            if (filter.length > 0) {
                clearBtn.classList.remove('hidden');
            } else {
                clearBtn.classList.add('hidden');
            }

            for(let c of cards) {
                // Check visibility
                if(c.dataset.name.includes(filter) || c.dataset.location.includes(filter)) { 
                    c.style.display = ""; 
                    visibleCount++;

                    // ✅ SUM UP VISIBLE CARDS
                    // We read the data-qty and data-val we added to the HTML
                    currentTotalQty += parseFloat(c.dataset.qty) || 0;
                    currentTotalVal += parseFloat(c.dataset.val) || 0;
                } 
                else { 
                    c.style.display = "none"; 
                }
            }

            // ✅ UPDATE THE TOP CARDS
            const cardQtyElement = document.getElementById('cardTotalQty');
            const cardValElement = document.getElementById('cardTotalVal');

            if(cardQtyElement) cardQtyElement.innerText = formatNumber(currentTotalQty);
            if(cardValElement) cardValElement.innerText = formatCurrency(currentTotalVal);

            document.getElementById('noResultsMessage').classList.toggle('hidden', visibleCount === 0);
        }

        // Function to clear the client-side filter
        function clearStationFilter() {
            let input = document.getElementById('searchInput');
            input.value = ''; // Clear text
            filterStations(); // Re-run filter to show all
            input.focus(); // Keep focus
        }
        
        document.addEventListener('DOMContentLoaded', () => {
            let msg = document.getElementById('successMessage');
            if(msg) setTimeout(() => msg.remove(), 4000);
        });
    </script>
    @if($errors->any()) <script> openModal('addStationModal'); </script> @endif
@endsection