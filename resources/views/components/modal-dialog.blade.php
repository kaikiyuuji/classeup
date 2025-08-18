@props([
    'id' => 'modal-dialog',
    'title' => 'Confirmação',
    'type' => 'confirm', // confirm, alert, info, warning, error
    'confirmText' => 'Confirmar',
    'cancelText' => 'Cancelar',
    'confirmClass' => 'bg-red-600 hover:bg-red-700',
    'showCancel' => true
])

<!-- Modal Backdrop -->
<div id="{{ $id }}" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

        <!-- This element is to trick the browser into centering the modal contents. -->
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <!-- Modal panel -->
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <!-- Icon -->
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full sm:mx-0 sm:h-10 sm:w-10
                        @if($type === 'confirm' || $type === 'error') bg-red-100
                        @elseif($type === 'warning') bg-yellow-100
                        @elseif($type === 'info') bg-blue-100
                        @else bg-gray-100 @endif">
                        @if($type === 'confirm' || $type === 'error')
                            <!-- Exclamation icon -->
                            <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                        @elseif($type === 'warning')
                            <!-- Warning icon -->
                            <svg class="h-6 w-6 text-yellow-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                        @elseif($type === 'info')
                            <!-- Info icon -->
                            <svg class="h-6 w-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        @else
                            <!-- Default icon -->
                            <svg class="h-6 w-6 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        @endif
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            {{ $title }}
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                {{ $slot }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <!-- Confirm button -->
                <button type="button" id="{{ $id }}-confirm" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 {{ $confirmClass }} text-base font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                    {{ $confirmText }}
                </button>
                
                @if($showCancel)
                <!-- Cancel button -->
                <button type="button" id="{{ $id }}-cancel" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    {{ $cancelText }}
                </button>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
// Modal Dialog JavaScript functionality
window.ModalDialog = {
    show: function(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }
    },
    
    hide: function(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
    },
    
    confirm: function(options) {
        return new Promise((resolve) => {
            const modalId = options.id || 'modal-dialog-' + Date.now();
            const title = options.title || 'Confirmação';
            const message = options.message || 'Tem certeza?';
            const confirmText = options.confirmText || 'Confirmar';
            const cancelText = options.cancelText || 'Cancelar';
            const type = options.type || 'confirm';
            
            // Create modal HTML
            const modalHTML = `
                <div id="${modalId}" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                <div class="sm:flex sm:items-start">
                                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full ${type === 'confirm' || type === 'error' ? 'bg-red-100' : type === 'warning' ? 'bg-yellow-100' : type === 'info' ? 'bg-blue-100' : 'bg-gray-100'} sm:mx-0 sm:h-10 sm:w-10">
                                        <svg class="h-6 w-6 ${type === 'confirm' || type === 'error' ? 'text-red-600' : type === 'warning' ? 'text-yellow-600' : type === 'info' ? 'text-blue-600' : 'text-gray-600'}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                        </svg>
                                    </div>
                                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">${title}</h3>
                                        <div class="mt-2">
                                            <p class="text-sm text-gray-500">${message}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                <button type="button" id="${modalId}-confirm" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 ${type === 'confirm' || type === 'error' ? 'bg-red-600 hover:bg-red-700' : type === 'warning' ? 'bg-yellow-600 hover:bg-yellow-700' : 'bg-blue-600 hover:bg-blue-700'} text-base font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                                    ${confirmText}
                                </button>
                                <button type="button" id="${modalId}-cancel" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                    ${cancelText}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            // Add modal to body
            document.body.insertAdjacentHTML('beforeend', modalHTML);
            
            const modal = document.getElementById(modalId);
            const confirmBtn = document.getElementById(modalId + '-confirm');
            const cancelBtn = document.getElementById(modalId + '-cancel');
            
            // Show modal
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
            
            // Event handlers
            const cleanup = () => {
                modal.remove();
                document.body.classList.remove('overflow-hidden');
            };
            
            confirmBtn.addEventListener('click', () => {
                cleanup();
                resolve(true);
            });
            
            cancelBtn.addEventListener('click', () => {
                cleanup();
                resolve(false);
            });
            
            // Close on backdrop click
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    cleanup();
                    resolve(false);
                }
            });
            
            // Close on ESC key
            const escHandler = (e) => {
                if (e.key === 'Escape') {
                    document.removeEventListener('keydown', escHandler);
                    cleanup();
                    resolve(false);
                }
            };
            document.addEventListener('keydown', escHandler);
        });
    },
    
    alert: function(options) {
        const alertOptions = {
            ...options,
            confirmText: options.confirmText || 'OK',
            type: options.type || 'info'
        };
        
        return this.confirm(alertOptions);
    }
};

// Override native confirm and alert functions
window.confirmModal = function(message, title = 'Confirmação') {
    return ModalDialog.confirm({
        message: message,
        title: title,
        type: 'confirm'
    });
};

window.alertModal = function(message, title = 'Aviso', type = 'info') {
    return ModalDialog.alert({
        message: message,
        title: title,
        type: type
    });
};
</script>