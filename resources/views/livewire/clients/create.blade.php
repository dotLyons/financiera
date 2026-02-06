<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Nuevo Cliente</h2>
                <p class="text-sm text-gray-500 mt-1">Complete la ficha para dar de alta un nuevo deudor en el sistema.</p>
            </div>
        </div>

        <div class="bg-white border border-gray-200 shadow-sm rounded-lg overflow-hidden">
            <form wire:submit.prevent="save">

                <div class="p-8 space-y-8">

                    <div>
                        <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Información Personal</h3>
                        <div class="grid grid-cols-12 gap-6">

                            <div class="col-span-12 sm:col-span-6">
                                <x-label for="first_name" value="Nombre" />
                                <x-input id="first_name" type="text" class="mt-1 block w-full"
                                    wire:model="first_name" autofocus />
                                <x-input-error for="first_name" class="mt-2" />
                            </div>

                            <div class="col-span-12 sm:col-span-6">
                                <x-label for="last_name" value="Apellido" />
                                <x-input id="last_name" type="text" class="mt-1 block w-full"
                                    wire:model="last_name" />
                                <x-input-error for="last_name" class="mt-2" />
                            </div>

                            <div class="col-span-12 sm:col-span-6">
                                <x-label for="dni" value="CUIT / DNI" />
                                <x-input id="dni" type="text" class="mt-1 block w-full bg-gray-50"
                                    wire:model="dni" placeholder="Sin puntos ni guiones" />
                                <x-input-error for="dni" class="mt-2" />
                            </div>

                            <div class="col-span-12 sm:col-span-6">
                                <x-label for="rubro" value="Rubro / Ocupación" />
                                <x-input id="rubro" type="text" class="mt-1 block w-full"
                                    wire:model="rubro" placeholder="Ej: Comercio, Empleado Público..." />
                                <x-input-error for="rubro" class="mt-2" />
                            </div>

                        </div>
                    </div>

                    <div class="border-t border-gray-100"></div>

                    <div>
                        <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Ubicación y Contacto</h3>
                        <div class="grid grid-cols-12 gap-6">

                            <div class="col-span-12 sm:col-span-6">
                                <x-label for="phone" value="Teléfono / WhatsApp" />
                                <x-input id="phone" type="text" class="mt-1 block w-full" wire:model="phone"
                                    placeholder="Ej: 385..." />
                                <x-input-error for="phone" class="mt-2" />
                            </div>

                            <div class="col-span-12 sm:col-span-6">
                                <x-label for="reference_phone" value="Teléfono de Referencia (Opcional)" />
                                <x-input id="reference_phone" type="text" class="mt-1 block w-full" wire:model="reference_phone"
                                    placeholder="Familiar, Amigo o Laboral" />
                                <x-input-error for="reference_phone" class="mt-2" />
                            </div>

                            <div class="col-span-12">
                                <x-label for="address" value="Dirección Principal (Domicilio de Cobro)" />
                                <x-input id="address" type="text" class="mt-1 block w-full" wire:model="address"
                                    placeholder="Calle, Número, Barrio..." />
                                <x-input-error for="address" class="mt-2" />
                            </div>

                            <div class="col-span-12">
                                <x-label for="second_address" value="Dirección Laboral / Alternativa (Opcional)" />
                                <x-input id="second_address" type="text" class="mt-1 block w-full" wire:model="second_address"
                                    placeholder="Otra dirección donde se pueda ubicar al cliente" />
                                <x-input-error for="second_address" class="mt-2" />
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-gray-100"></div>

                    <div>
                        <x-label for="notes" value="Notas Internas / Observaciones (Opcional)" />
                        <div class="mt-1">
                            <textarea id="notes" wire:model="notes" rows="3"
                                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md"
                                placeholder="Referencia de la casa, horarios de preferencia, etc..."></textarea>
                        </div>
                        <x-input-error for="notes" class="mt-2" />
                    </div>

                </div>

                <div class="bg-gray-50 px-8 py-4 flex items-center justify-end border-t border-gray-200">
                    <a href="{{ route('clients.index') }}"
                        class="mr-4 text-sm font-medium text-gray-600 hover:text-gray-900 underline decoration-gray-300 underline-offset-4 transition">
                        Cancelar
                    </a>

                    <x-button wire:loading.attr="disabled" class="ml-3">
                        {{ __('Guardar Cliente') }}
                    </x-button>
                </div>

            </form>
        </div>
    </div>
</div>
