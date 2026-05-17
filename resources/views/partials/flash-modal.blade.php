@if(session('success') || session('error'))
    @php
        $isSuccess = session()->has('success');
        $flashMessage = $isSuccess ? session('success') : session('error');
        $flashTitle = $isSuccess ? 'Success' : 'Error';
    @endphp
    <div id="flashModal"
         class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm transition-opacity duration-500 ease-out"
         role="alertdialog"
         aria-live="polite"
         aria-labelledby="flashModalTitle"
         aria-describedby="flashModalMessage">
        <div class="flash-modal-card relative w-full max-w-md rounded-2xl bg-white shadow-2xl border border-gray-200 overflow-hidden transform transition-all duration-500 ease-out scale-100">
            <div class="px-8 pt-8 pb-6 text-center {{ $isSuccess ? 'bg-gradient-to-br from-emerald-50 to-white' : 'bg-gradient-to-br from-red-50 to-white' }}">
                <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full {{ $isSuccess ? 'bg-emerald-100 text-emerald-600' : 'bg-red-100 text-red-600' }}">
                    @if($isSuccess)
                        <svg class="h-9 w-9" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                        </svg>
                    @else
                        <svg class="h-9 w-9" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    @endif
                </div>
                <h3 id="flashModalTitle" class="text-2xl font-extrabold text-gray-900 mb-2">{{ $flashTitle }}</h3>
                <p id="flashModalMessage" class="text-gray-600 font-medium leading-relaxed">{{ $flashMessage }}</p>
            </div>
            <div class="px-8 pb-8 flex justify-center">
                <button type="button"
                        id="flashModalDismiss"
                        class="px-8 py-3 rounded-xl font-bold text-white shadow-lg transition transform hover:-translate-y-0.5 {{ $isSuccess ? 'bg-gradient-to-r from-emerald-600 to-emerald-800 hover:from-emerald-700 hover:to-emerald-900' : 'bg-gradient-to-r from-red-600 to-red-800 hover:from-red-700 hover:to-red-900' }}">
                    OK
                </button>
            </div>
        </div>
    </div>
    <script>
        (function () {
            const modal = document.getElementById('flashModal');
            if (!modal) return;

            const dismiss = function () {
                modal.classList.add('opacity-0');
                const card = modal.querySelector('.flash-modal-card');
                if (card) {
                    card.classList.add('scale-95', 'opacity-0');
                }
                setTimeout(function () { modal.remove(); }, 500);
            };

            document.getElementById('flashModalDismiss')?.addEventListener('click', dismiss);
            modal.addEventListener('click', function (e) {
                if (e.target === modal) dismiss();
            });

            setTimeout(dismiss, 5000);
        })();
    </script>
@endif
