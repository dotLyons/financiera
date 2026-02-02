<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Editar Cobrador</h2>
                <p class="text-sm text-gray-500 mt-1">Modifique los datos de acceso o el estado del empleado.</p>
            </div>
        </div>

        <div class="bg-white border border-gray-200 shadow-sm rounded-lg overflow-hidden max-w-3xl mx-auto">
            <form wire:submit.prevent="save">

                <div class="p-8 space-y-6">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-label for="name" value="Nombre Completo" />
                            <x-input id="name" type="text" class="mt-1 block w-full" wire:model="name" />
                            <x-input-error for="name" class="mt-2" />
                        </div>

                        <div>
                            <x-label for="is_active" value="Estado de la Cuenta" />
                            <select id="is_active" wire:model="is_active"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="1">Activo (Puede acceder)</option>
                                <option value="0">Inactivo (Acceso bloqueado)</option>
                            </select>
                            <x-input-error for="is_active" class="mt-2" />
                        </div>
                    </div>

                    <div>
                        <x-label for="email" value="Correo Electrónico" />
                        <x-input id="email" type="email" class="mt-1 block w-full bg-gray-50"
                            wire:model="email" />
                        <p class="text-xs text-gray-500 mt-1">Este correo es el usuario de acceso al sistema móvil.</p>
                        <x-input-error for="email" class="mt-2" />
                    </div>

                </div>

                <div class="bg-gray-50 px-8 py-4 flex items-center justify-end border-t border-gray-200">
                    <a href="{{ route('collectors.index') }}"
                        class="mr-4 text-sm font-medium text-gray-600 hover:text-gray-900 underline decoration-gray-300 underline-offset-4 transition">
                        Cancelar
                    </a>

                    <x-button wire:loading.attr="disabled" class="ml-3">
                        {{ __('Guardar Cambios') }}
                    </x-button>
                </div>

            </form>
        </div>
    </div>
</div>
