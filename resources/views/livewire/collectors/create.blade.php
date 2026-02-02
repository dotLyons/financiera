<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Registrar Nuevo Cobrador</h2>
                <p class="text-sm text-gray-500 mt-1">Cree un usuario operativo para la aplicación móvil.</p>
            </div>
        </div>

        <div class="bg-white border border-gray-200 shadow-sm rounded-lg overflow-hidden max-w-3xl mx-auto">
            <form wire:submit.prevent="save">

                <div class="p-8 space-y-6">

                    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-blue-700">
                                    Por seguridad y simplicidad en esta demo, la contraseña se generará automáticamente
                                    como:
                                    <span class="font-bold font-mono">cobrador</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <x-label for="name" value="Nombre Completo" />
                        <x-input id="name" type="text" class="mt-1 block w-full" wire:model="name" autofocus />
                        <x-input-error for="name" class="mt-2" />
                    </div>

                    <div>
                        <x-label for="email" value="Correo Electrónico (Usuario de Acceso)" />
                        <x-input id="email" type="email" class="mt-1 block w-full" wire:model="email" />
                        <x-input-error for="email" class="mt-2" />
                    </div>

                </div>

                <div class="bg-gray-50 px-8 py-4 flex items-center justify-end border-t border-gray-200">
                    <a href="{{ route('collectors.index') }}"
                        class="mr-4 text-sm font-medium text-gray-600 hover:text-gray-900 underline decoration-gray-300 underline-offset-4 transition">
                        Cancelar
                    </a>

                    <x-button wire:loading.attr="disabled" class="ml-3">
                        {{ __('Registrar Cobrador') }}
                    </x-button>
                </div>

            </form>
        </div>
    </div>
</div>
