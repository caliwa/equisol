<div wire:init='MediatorInitialized' class="animate-window" x-data="{
        escapeEnabled: null,
        loadingSpinnerEnabled: null,
        modalStack: [],
        modalStackCount: $wire.entangle('modalStackCount').live,
        checkInterval: null,
        topModalId: null,
        isProcessingEscape: $wire.entangle('isProcessingEscape').live,
        lastEscapeTime: 0,
        activeIcon: Math.floor(Math.random() * 3),
        loadingSpinner(event) {
            this.loadingSpinnerEnabled = true;
            this.blockInteractions(event);
        },
        blockInteractions(event) {
            event.preventDefault();
            event.target.blur();
            this.escapeEnabled = false;
            this.addTabTrapListener();
        },
        addTabTrapListener() {
            document.addEventListener('keydown', this.trapTabKey);
        },
        removeTabTrapListener() {
            document.removeEventListener('keydown', this.trapTabKey);
        },
        trapTabKey(e) {
            const key = e.key;

            if ((key === 'Tab' || key === 'Escape') && !this.escapeEnabled) {
                e.stopPropagation();
                e.preventDefault();
            }
        },
        blockClick(event) {
            if (!this.escapeEnabled) {
                event.stopPropagation();
                event.preventDefault();
            }
        },
        closeTopModal() {
            const now = Date.now();
            // Verificar si han pasado al menos 150ms desde la última ejecución
            if (now - this.lastEscapeTime < 150) return;
            if (this.isProcessingEscape) return;
            
            this.isProcessingEscape = true;
            this.lastEscapeTime = now;
            
            if (this.escapeEnabled && this.modalStack.length > 0) {
                this.topModalId = this.modalStack[this.modalStack.length - 1];
                $wire.dispatch('MediatorSetModalFalse', [this.topModalId]);
                
                if(this.topModalId === 'isVisibleConfirmValidationModal'){
                    this.removeTabTrapListener();
                }
            }
            setTimeout(() => {
                this.isProcessingEscape = false;
            }, 150);
        },
        handleEnter(event) {
            event.preventDefault();
            event.target.blur();
            this.blockInteractions(event);
            this.startCheckingEscapeEnabled(event.target);
        },
        startCheckingEscapeEnabled(inputElement) {
            if (this.checkInterval) {
                clearInterval(this.checkInterval);
            }
            this.checkInterval = setInterval(() => {
                if (this.escapeEnabled) {
                    clearInterval(this.checkInterval);
                    inputElement.focus();
                }
            }, 100);
        },

        modalDichotomicHeading: '',
        modalDichotomicMessage: '',
        modalDichotomicMethod: '',
        modalDichotomicParam: null,
        modalDichotomicBtnText: '',
        prepareDichotomic({ method, param, heading, message, modalDichotomicBtnText}) {
            this.modalDichotomicMethod = method;
            this.modalDichotomicParam = param;
            this.modalDichotomicHeading = heading;
            this.modalDichotomicMessage = message;
            this.modalDichotomicBtnText = modalDichotomicBtnText;
            const modal = $flux.modal('dichotomic-modal');
            if (modal) {
                modal.show();
            } else {
                console.warn('Modal no encontrado');
            }
        },

        modalConfirmValidationHeading: '• ATENCIÓN',
        modalConfirmValidationMessage: $wire.entangle('modalConfirmValidationMessage').live,

    }"
    x-on:escape-enabled.window="
        escapeEnabled = false;
        escapeEnabled = true;
        loadingSpinnerEnabled = false;
    "
    x-on:escape-disabled.window="
        escapeEnabled = false;
        loadingSpinnerEnabled = true;
    "
    x-init="
        setInterval(() => { activeIcon = Math.floor(Math.random() * 3) }, 3000);
        escapeEnabled = true;
        loadingSpinnerEnabled = false;
        {{-- $watch('modalStack', () => {
            modalStackCount = modalStack.length;
        }); --}}
        document.addEventListener('click', e => blockClick(e), true);

    "
>
    <flux:modal
        x-data="{ isLoadingConfirmValidationModal: false }" 
        name="confirm-validation-modal" class="min-w-[22rem]" x-on:close="isLoadingConfirmValidationModal = false; escapeEnabled = true;">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg" x-text="modalConfirmValidationHeading"></flux:heading>
                <flux:text class="mt-2">
                    <span x-text="modalConfirmValidationMessage"></span>
                </flux:text>
            </div>
            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost">Continuar</flux:button>
                </flux:modal.close>

                <flux:modal.close>
                    <flux:button
                        variant="primary"
                        color="red"
                    >
                        Cerrar
                    </flux:button>
                </flux:modal.close>
            </div>
        </div>
    </flux:modal>
    {{-- <div id="bg-validation-input" 
            class="fixed inset-0 flex items-center justify-center bg-white/50" 
            style="z-index: 2147483647 !important; position: fixed !important;" 
            x-show="!escapeEnabled">
            </div> --}}

    {{-- <div id="bg-validation-input" style="z-index: 2147483647;"  x-show="!escapeEnabled" class="absolute inset-0 flex items-center justify-center bg-white/50  "></div> --}}

    <div id="bg-validation-input"
        x-show="loadingSpinnerEnabled"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-90"
        class="fixed inset-0 fade-in-scale flex items-center justify-center dark:bg-black/30 bg-white/30 select-none" style="z-index: 2147483647;">
        <div class="absolute top-0 bottom-0 left-0 right-0 flex items-center justify-center">
            <div class="flex items-center">
                <flux:icon.loading />
            </div>
        </div>
    </div> 
    <div
        wire:offline
        x-data="{ showButton: false }"
        x-effect="
            let timer;

            timer = setTimeout(() => showButton = true, 30000);

            return () => {
                if (timer) clearTimeout(timer);
            }
        "
        class="fixed inset-0 flex items-center justify-center z-50 bg-gray-900 bg-opacity-50"
    >
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-white dark:bg-gray-800 rounded-lg p-8 max-w-sm w-full mx-auto text-center shadow-xl">
            <div class="flex items-center justify-center mb-4">
                <i class="fas fa-exclamation-triangle text-4xl text-yellow-500 mr-3"></i>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Sin conexión</h2>
            </div>
            <p class="text-gray-600 dark:text-gray-300 mb-4">
                Vaya, tu dispositivo ha perdido la conexión. La página web que estás viendo está fuera de línea.
            </p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                Este aviso desaparecerá automáticamente cuando se restablezca la conexión.
            </p>
            <button 
                x-show="showButton"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform scale-90"
                x-transition:enter-end="opacity-100 transform scale-100"
                onclick="window.location.reload()" 
                class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-sm transition duration-300"
            >
                Recargar cotizador (reinicio)
            </button>
        </div>
    </div>

    @if($isProcessingEscape)
        <div
            @if(config('modalescapeeventlistener.is_active'))
                class="fixed bottom-4 left-4 z-999 bg-gray-900 bg-opacity-80 text-white font-bold px-3 py-1.5 rounded-lg shadow-lg transition-opacity duration-300 opacity-100"
            @else
                class="fixed bottom-4 left-4 z-999 bg-gray-900 bg-opacity-80 text-white font-bold px-3 py-1.5 rounded-lg shadow-lg transition-opacity duration-300 opacity-0"
            @endif
            >
            @if(config('modalescapeeventlistener.is_active'))
                <div class="flex items-centser">
                    <span class="text-2xl mr-2">⎵</span>
                    <span class="text-2xl">ESC detectado</span>
                </div>
            @endif
        </div>
    @endif

    <livewire:calculation.quoter.import-factor-calculator-component wire:key="ifcc-1"/>

</div>