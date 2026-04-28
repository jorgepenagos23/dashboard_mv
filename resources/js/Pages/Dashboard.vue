<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                Dashboard
            </h2>
        </template>

        <div class="py-12 text-gray-100">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-6">
                <div :style="{ backgroundColor: 'var(--app-card, #1f2937)', borderColor: 'var(--app-card, #374151)' }" class="overflow-hidden border shadow-sm sm:rounded-lg transition-colors duration-300">
                    <div class="p-6">
                        ¡Has iniciado sesión satisfactoriamente!
                    </div>
                </div>

                <!-- Admin Section -->
                <div  :style="{ backgroundColor: 'var(--app-card, #1f2937)', borderColor: 'var(--app-card, #374151)' }" class="overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-l-indigo-500 transition-colors duration-300">
                    <div class="p-6">
                        <h3 class="text-lg font-bold mb-4 flex items-center gap-2">
                            <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path></svg>
                            Panel de Administración (Master)
                        </h3>
                        <p class="text-sm text-gray-500 mb-6">Como administrador tienes acceso exclusivo a configuraciones avanzadas del sistema.</p>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <Link :href="route('explorer.index')" class="block p-4 bg-black/20 border border-gray-600/50 rounded-lg hover:bg-indigo-900/40 hover:border-indigo-500/50 hover:shadow-md transition">
                                <h4 class="font-bold text-indigo-400 mb-1">DataHub Explorer</h4>
                                <p class="text-xs text-gray-400">Explorador y editor directo a base de datos (CRUD maestro).</p>
                            </Link>

                            <Link :href="route('inventario.index')" class="block p-4 bg-black/20 border border-gray-600/50 rounded-lg hover:bg-orange-900/40 hover:border-orange-500/50 hover:shadow-md transition">
                                <h4 class="font-bold text-orange-400 mb-1">Inventario y Precios</h4>
                                <p class="text-xs text-gray-400">Acceso a edición de precios y eliminación de productos.</p>
                            </Link>

                            <Link :href="route('vacantes.index')" class="block p-4 bg-black/20 border border-gray-600/50 rounded-lg hover:bg-emerald-900/40 hover:border-emerald-500/50 hover:shadow-md transition">
                                <h4 class="font-bold text-emerald-400 mb-1">Gestión de Vacantes</h4>
                                <p class="text-xs text-gray-400">Administra las ofertas de trabajo publicadas en la página externa.</p>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
