<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-4">
                <img src="/images/logo.png" alt="Company Logo" class="h-10 w-auto object-contain bg-gray-800 p-1 rounded" onerror="this.src='https://via.placeholder.com/150x50?text=LOGO'" />
                <div>
                    <h1 class="text-3xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-emerald-500 to-teal-600">
                        Directorio de Clientes
                    </h1>
                    <p class="text-gray-500 mt-1">Administra los usuarios, balances y sus direcciones de entrega.</p>
                </div>
            </div>
        </template>

        <div class="min-h-screen bg-gray-50 text-gray-800 p-8 font-sans">
        <div class="mb-6 flex flex-col md:flex-row gap-3">
            <div class="flex-1 min-w-[200px] relative">
                <input v-model="searchQuery" @keyup.enter="applySearch" type="text" placeholder="Buscar por código, razón social o denominación..." 
                       class="w-full bg-gray-800 border border-gray-700 rounded-lg text-sm text-white focus:ring-teal-500 focus:border-teal-500 px-4 py-2.5" />
            </div>
            
            <div class="flex-none w-full md:w-32 relative">
                <input v-model="rutaFilter" @keyup.enter="applySearch" type="text" placeholder="Ruta..." 
                       class="w-full bg-gray-800 border border-gray-700 rounded-lg text-sm text-white focus:ring-teal-500 focus:border-teal-500 px-4 py-2.5" title="Filtrar por Ruta Preventa / Reparto" />
            </div>

            <div class="flex-none w-full md:w-40 relative">
                <input v-model="ctepfrFilter" @keyup.enter="applySearch" type="text" placeholder="CTEPFR Codigo..." 
                       class="w-full bg-gray-800 border border-gray-700 rounded-lg text-sm text-white focus:ring-teal-500 focus:border-teal-500 px-4 py-2.5" title="Filtrar por código en direcciones (CTEPFR_CODIGO_K)" />
            </div>

            <div class="flex gap-2">
                <button @click="applySearch" class="bg-teal-600 hover:bg-teal-500 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition shadow-lg cursor-pointer h-full whitespace-nowrap">
                    Buscar
                </button>
                <button v-if="searchQuery || rutaFilter || ctepfrFilter" @click="clearSearch" class="bg-gray-700 hover:bg-gray-600 text-gray-300 px-5 py-2.5 rounded-lg text-sm font-medium transition cursor-pointer h-full whitespace-nowrap">
                    Limpiar
                </button>
            </div>
        </div>

        <main class="grid lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 overflow-hidden rounded-xl border border-gray-800 bg-gray-800/50 shadow-2xl backdrop-blur-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm whitespace-nowrap">
                        <thead class="uppercase tracking-wider border-b border-gray-700 bg-gray-900/50 text-gray-400">
                            <tr>
                                <th class="p-4 font-medium">Cod. Cliente</th>
                                <th class="p-4 font-medium">Nombre / Razón Social</th>
                                <th class="p-4 font-medium">Contacto</th>
                                <th class="p-4 font-medium text-right">MV Coins</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-800">
                            <tr v-for="cliente in clientes.data" :key="cliente.id" 
                                @click="selectCliente(cliente)"
                                :class="[
                                    'cursor-pointer transition hover:bg-gray-700/50', 
                                    selectedCliente?.id === cliente.id ? 'bg-gray-700' : ''
                                ]">
                                <td class="p-4 font-mono text-teal-400">{{ cliente.cliente }}</td>
                                <td class="p-4">
                                    <p class="font-medium text-gray-100">{{ cliente.razon_social || cliente.cliente }}</p>
                                    <p class="text-xs text-gray-400 truncate w-48">{{ cliente.denominacion_comercial }}</p>
                                </td>
                                <td class="p-4">
                                    <div class="flex flex-col gap-1">
                                        <div v-if="cliente.email" class="flex items-center gap-2 text-gray-300">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                            {{ cliente.email }}
                                        </div>
                                        <div v-if="cliente.telefono" class="flex items-center gap-2 text-gray-400 text-xs">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                            {{ cliente.telefono }}
                                        </div>
                                    </div>
                                </td>
                                <td class="p-4 text-right">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-yellow-400/10 text-yellow-400 border border-yellow-400/20">
                                        {{ parseFloat(cliente.mv_coins || 0).toFixed(2) }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- Pagination -->
                <div class="p-4 border-t border-gray-700 flex items-center justify-between text-sm text-gray-400">
                    <div>Mostrando página {{ clientes.current_page }} de {{ clientes.last_page }}</div>
                    <div class="flex space-x-2">
                        <a v-for="(link, idx) in clientes.links" :key="idx" :href="link.url"
                           :class="['px-3 py-1 rounded-md transition', link.active ? 'bg-teal-600 text-white' : 'bg-gray-800 hover:bg-gray-700']"
                           v-html="link.label"></a>
                    </div>
                </div>
            </div>

            <div class="bg-gray-800 rounded-xl border border-gray-700 shadow-xl p-6 relative overflow-hidden">
                <div class="absolute -bottom-24 -left-24 w-48 h-48 bg-teal-600/10 blur-3xl rounded-full pointer-events-none"></div>

                <div v-if="selectedCliente">
                    <h2 class="text-xl font-bold mb-4 flex items-center">
                        <span class="bg-teal-500 w-2 h-6 rounded mr-3"></span>
                        Direcciones Vinculadas
                    </h2>
                    
                    <div class="mb-4 p-3 bg-gray-900/50 rounded-lg border border-gray-700/50 text-sm">
                        <p class="text-gray-300 font-medium">{{ selectedCliente.razon_social || selectedCliente.cliente }}</p>
                        <p class="text-gray-500 text-xs mt-1">RFC: {{ selectedCliente.rfc || 'N/A' }}</p>
                    </div>

                    <ul class="space-y-4 max-h-[500px] overflow-y-auto pr-2 custom-scrollbar">
                        <li v-for="dir in selectedCliente.direcciones" :key="dir.id" class="p-4 rounded-lg bg-gray-900/40 border border-gray-700/30 hover:border-teal-500/30 transition shadow-sm">
                            <div class="flex justify-between items-start mb-2">
                                <span class="px-2 py-0.5 text-[10px] uppercase font-bold tracking-wider rounded bg-gray-700 text-teal-300">
                                    {{ dir.CTEDIR_TIPOFIS || 'DIRECCIÓN' }}
                                </span>
                                <span class="text-xs text-gray-500 font-mono">{{ dir.CTEDIR_CODIGO_K }}</span>
                            </div>
                            <p class="text-sm text-gray-200">
                                {{ dir.CTEDIR_CALLE }} {{ dir.CTEDIR_CALLENUMEXT }} <span v-if="dir.CTEDIR_CALLENUMINT">Int: {{ dir.CTEDIR_CALLENUMINT }}</span>
                            </p>
                            <p class="text-xs text-gray-400 mt-1">Col. {{ dir.CTEDIR_COLONIA }}, C.P. {{ dir.CTEDIR_CP }}</p>
                            <p class="text-xs text-gray-400 mt-1 italic">{{ dir.MAPEDO_CODIGO_K }} - {{ dir.MAPMUN_CODIGO_K }}</p>
                        </li>
                        <li v-if="!selectedCliente.direcciones?.length" class="text-gray-500 text-sm text-center py-8">
                            Este cliente no tiene direcciones registradas.
                        </li>
                    </ul>
                </div>
                <div v-else class="h-full flex flex-col items-center justify-center text-gray-500">
                    <svg class="w-16 h-16 mb-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    <p>Selecciona un cliente para explorar direcciones</p>
                </div>
            </div>
        </main>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    clientes: Object,
    filters: Object
});

const searchQuery = ref(props.filters?.search || '');
const rutaFilter = ref(props.filters?.ruta || '');
const ctepfrFilter = ref(props.filters?.ctepfr_codigo || '');

const applySearch = () => {
    router.get(route('clientes.index'), { 
        search: searchQuery.value,
        ruta: rutaFilter.value,
        ctepfr_codigo: ctepfrFilter.value,
    }, { preserveState: true, replace: true, preserveScroll: true });
};

const clearSearch = () => {
    searchQuery.value = '';
    rutaFilter.value = '';
    ctepfrFilter.value = '';
    applySearch();
};

const selectedCliente = ref(null);

const selectCliente = (cliente) => {
    selectedCliente.value = cliente;
};
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background-color: #4b5563;
    border-radius: 20px;
}
</style>
