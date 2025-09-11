<div>
    <div class="relative w-[95%] mx-auto pt-8">
        <div class="relative bg-white rounded-xl border border-gray-200 dark:bg-gray-800 overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 dark:from-gray-700 dark:to-gray-800">
                <div class="flex items-center justify-between p-6">
                    <div class="flex items-center space-x-3">
                        <div class="p-2 bg-white/20 rounded-lg">
                            <flux:icon.adjustments-vertical variant="solid" class="text-white" />
                        </div>
                        <flux:heading size="xl" class="text-white!" >
                            Configuración
                        </flux:he>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <a 
                        wire:ignore.self
                        href="/configuracion/usuarios"
                        wire:navigate
                        wire:loading.attr="disabled"
                        class="group relative bg-gradient-to-br from-emerald-50 to-emerald-100 dark:from-emerald-900/20 dark:to-emerald-800/20 rounded-xl p-6 cursor-pointer transition-all duration-300 hover:shadow-xl hover:scale-105 border border-emerald-200 dark:border-emerald-700/50 disabled:opacity-50 disabled:cursor-not-allowed">
                        <div class="absolute top-4 right-4 opacity-20 group-hover:opacity-40 transition-opacity">
                            <i class="fas fa-database text-3xl text-emerald-600"></i>
                        </div>
                        <div class="relative z-10">
                            <div class="flex items-center space-x-3 mb-4">
                                <div class="p-3 bg-emerald-500 rounded-lg shadow-lg">
                                    <flux:icon.users class="text-white" />
                                </div>
                                <div>
                                    <flux:heading size="xl">
                                        Usuarios
                                    </flux:heading>
                                    <flux:description>
                                        Gestión de usuarios
                                    </flux:description>
                                </div>
                            </div>
                            <flux:description size="lg">
                                Configuración y administración de usuarios del sistema.
                            </flux:description>
                            <flux:badge class="mt-1!" color="green">
                                Sistema
                            </flux:badge>
                        </div>
                    </a>

                    <!-- Gosem -->
                    <a  wire:ignore.self
                        href="/configuracion/roles"
                        wire:navigate
                        wire:loading.attr="disabled"
                        class="group relative bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-xl p-6 cursor-pointer transition-all duration-300 hover:shadow-xl hover:scale-105 border border-blue-200 dark:border-blue-700/50 disabled:opacity-50 disabled:cursor-not-allowed">
                        <div class="absolute top-4 right-4 opacity-20 group-hover:opacity-40 transition-opacity">
                            <i class="fas fa-chart-line text-3xl text-blue-600"></i>
                        </div>
                        <div class="relative z-10">
                            <div class="flex items-center space-x-3 mb-4">
                                <div class="p-3 bg-blue-500 rounded-lg shadow-lg">
                                    <flux:icon.document-currency-dollar class="text-white" />
                                </div>
                                <div>
                                    <flux:heading size="xl">
                                        Roles
                                    </flux:heading>
                                    <flux:description>
                                        Gestión de roles
                                    </flux:description>
                                </div>
                            </div>
                            <flux:description size="lg">
                                Sistema de gestión y asignación de roles para usuarios.
                            </flux:description>
                            <flux:badge class="mt-1!" color="sky">
                                Operaciones
                            </flux:badge>
                        </div>
                    </a>

                    <!-- Gestión Empresas -->
                    <div wire:ignore.self
                        href="/configuracion/permisos"
                        wire:navigate
                        wire:loading.attr="disabled"
                        wire:loading.attr="disabled"
                        class="group relative bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 rounded-xl p-6 cursor-pointer transition-all duration-300 hover:shadow-xl hover:scale-105 border border-purple-200 dark:border-purple-700/50 disabled:opacity-50 disabled:cursor-not-allowed">
                        <div class="absolute top-4 right-4 opacity-20 group-hover:opacity-40 transition-opacity">
                            <i class="fas fa-building text-3xl text-purple-600"></i>
                        </div>
                        <div class="relative z-10">
                            <div class="flex items-center space-x-3 mb-4">
                                <div class="p-3 bg-purple-500 rounded-lg shadow-lg">
                                    <flux:icon.document-currency-euro class="text-white" />
                                </div>
                                <div>
                                    <flux:heading size="xl">
                                        Permisos
                                    </flux:heading>
                                    <flux:description>
                                        Gestión de permisos
                                    </flux:description>
                                </div>
                            </div>
                            <flux:description size="lg">
                                Configuración y administración de permisos para usuarios.
                            </flux:description>
                            <flux:badge class="mt-1!" color="purple">
                                Administración
                            </flux:badge>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>