<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Editar Cliente</h2>
                <p class="text-sm text-gray-500 mt-1">Modifique la ficha técnica del deudor.</p>
            </div>
        </div>

        <div class="bg-white border border-gray-200 shadow-sm rounded-lg overflow-hidden">
            <form wire:submit.prevent="save">

                <div class="p-8 space-y-8">

                    <div>
                        <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Información Personal</h3>
                        <div class="grid grid-cols-12 gap-6">

                            <div class="col-span-4">
                                <x-label for="first_name" value="Nombre" />
                                <x-input id="first_name" type="text" class="mt-1 block w-full"
                                    wire:model="first_name" />
                                <x-input-error for="first_name" class="mt-2" />
                            </div>

                            <div class="col-span-4">
                                <x-label for="last_name" value="Apellido" />
                                <x-input id="last_name" type="text" class="mt-1 block w-full"
                                    wire:model="last_name" />
                                <x-input-error for="last_name" class="mt-2" />
                            </div>

                            <div class="col-span-4">
                                <x-label for="dni" value="CUIT / DNI" />
                                <x-input id="dni" type="text" class="mt-1 block w-full bg-gray-50"
                                    wire:model="dni" />
                                <x-input-error for="dni" class="mt-2" />
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-gray-100"></div>

                    <div>
                        <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Ubicación y Estado</h3>
                        <div class="grid grid-cols-12 gap-6">

                            <div class="col-span-3">
                                <x-label for="status" value="Estado Actual" />
                                <div class="relative">
                                    <select id="status" wire:model="status"
                                        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md shadow-sm">
                                        @foreach ($statuses as $st)
                                            <option value="{{ $st->value }}">{{ $st->label() }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <x-input-error for="status" class="mt-2" />
                            </div>

                            <div class="col-span-3">
                                <x-label for="phone" value="Teléfono / WhatsApp" />
                                <x-input id="phone" type="text" class="mt-1 block w-full" wire:model="phone" />
                                <x-input-error for="phone" class="mt-2" />
                            </div>

                            <div class="col-span-6">
                                <x-label for="address" value="Dirección Completa (Cobro)" />
                                <x-input id="address" type="text" class="mt-1 block w-full" wire:model="address" />
                                <x-input-error for="address" class="mt-2" />
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-gray-100"></div>

                    <div>
                        <x-label for="notes" value="Notas Internas / Observaciones" />
                        <div class="mt-1">
                            <textarea id="notes" wire:model="notes" rows="4"
                                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md"
                                placeholder="Escriba aquí detalles adicionales relevantes para la cobranza..."></textarea>
                        </div>
                        <x-input-error for="notes" class="mt-2" />
                    </div>

                </div>

                <div class="bg-gray-50 px-8 py-4 flex items-center justify-end border-t border-gray-200">
                    <a href="{{ route('clients.index') }}"
                        class="mr-4 text-sm font-medium text-gray-600 hover:text-gray-900 underline decoration-gray-300 underline-offset-4 transition">
                        Cancelar y Volver
                    </a>

                    <x-button wire:loading.attr="disabled" class="ml-3">
                        {{ __('Guardar Cambios') }}
                    </x-button>
                </div>

            </form>
        </div>
    </div>
</div>
