<template>
    <div class="min-h-screen bg-gray-900 text-gray-100 p-8 font-sans">
        <header class="mb-8">
            <h1 class="text-3xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-orange-400 to-pink-500">
                Inventario y Precios
            </h1>
            <p class="text-gray-400 mt-2">Monitorea el nivel de existencias por almacén y las listas de precios actuales.</p>
        </header>

        <main class="space-y-6">
            <!-- Search & Filter Controls -->
            <div class="flex flex-col md:flex-row gap-4 items-center mb-6">
                <div class="w-full md:w-1/3">
                    <input v-model="filtersForm.search" type="text" placeholder="Buscar por código SKU o nombre..." 
                           class="w-full bg-gray-800 border border-gray-700 text-white text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block p-2.5" 
                           @keyup.enter="applyFilters">
                </div>
                <div class="w-full md:w-1/4">
                    <select v-model="filtersForm.linea_prod_id" 
                            class="w-full bg-gray-800 border border-gray-700 text-white text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block p-2.5"
                            @change="applyFilters">
                        <option value="">Todas las líneas</option>
                        <option v-for="linea in lineas" :key="linea" :value="linea">{{ linea }}</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button @click="applyFilters" class="px-4 py-2 bg-orange-600 hover:bg-orange-500 text-white rounded-lg text-sm font-medium transition cursor-pointer">
                        Buscar
                    </button>
                    <button @click="clearFilters" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-gray-300 rounded-lg text-sm font-medium transition cursor-pointer">
                        Limpiar
                    </button>
                </div>
            </div>

            <div class="overflow-hidden rounded-xl border border-gray-800 bg-gray-800/50 shadow-2xl backdrop-blur-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm whitespace-nowrap">
                        <thead class="uppercase tracking-wider border-b border-gray-700 bg-gray-900/50 text-gray-400">
                            <tr>
                                <th class="p-4 font-medium w-16">Imagen</th>
                                <th class="p-4 font-medium">Producto (SKU)</th>
                                <th class="p-4 font-medium">Disponibilidad (Stock)</th>
                                <th class="p-4 font-medium">Listas de Precios</th>
                                <th class="p-4 font-medium text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-800">
                            <tr v-for="producto in productos.data" :key="producto.id" class="hover:bg-gray-700/30 transition group">
                                <td class="p-4">
                                    <img :src="producto.foto || 'https://via.placeholder.com/64/374151/9ca3af?text=NA'" 
                                         class="w-12 h-12 rounded-lg object-cover border border-gray-700 group-hover:border-orange-500/50 transition" 
                                         alt="Prod img">
                                </td>
                                <td class="p-4">
                                    <p class="font-bold text-gray-100">{{ producto.nombre }}</p>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="text-xs font-mono bg-gray-900 px-2 py-0.5 rounded text-orange-400 border border-gray-700">
                                            {{ producto.sku }}
                                        </span>
                                        <span class="text-xs text-gray-500">{{ producto.pround }}</span>
                                    </div>
                                </td>
                                <td class="p-4">
                                    <div v-if="producto.stocks?.length" class="flex flex-wrap gap-2">
                                        <div v-for="stock in producto.stocks" :key="stock.id" 
                                             class="flex items-center gap-1.5 bg-gray-900/60 border border-gray-700/80 px-2 py-1 rounded text-xs">
                                            <span class="text-gray-400">{{ stock.lugar }}:</span>
                                            <span :class="{'text-red-400': stock.cantidad <= 0, 'text-green-400': stock.cantidad > 0}" class="font-bold">
                                                {{ stock.cantidad }}
                                            </span>
                                        </div>
                                    </div>
                                    <p v-else class="text-xs text-gray-500 italic">Sin registros de stock</p>
                                </td>
                                <td class="p-4">
                                    <div v-if="producto.prices?.length" class="flex flex-col gap-1.5">
                                        <div v-for="price in producto.prices" :key="price.id"
                                             class="flex justify-between items-center bg-gray-900/40 px-2 py-1 rounded text-xs">
                                            <span class="text-pink-400 font-medium">Lista {{ price.price_list_id }}</span>
                                            <span class="font-bold text-gray-200">${{ parseFloat(price.price).toFixed(2) }}</span>
                                        </div>
                                    </div>
                                    <div v-else class="text-xs">
                                        <span class="text-gray-500 italic">Sin precios extra - Base: </span>
                                        <span class="font-bold text-gray-300">${{ parseFloat(producto.precio_1 || 0).toFixed(2) }}</span>
                                    </div>
                                </td>
                                <td class="p-4 text-center">
                                    <button @click="openAddStockModal(producto)" 
                                            class="bg-orange-600 hover:bg-orange-500 text-white px-3 py-1.5 rounded-lg text-sm font-medium transition cursor-pointer">
                                        + Stock
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="p-4 border-t border-gray-700 flex items-center justify-between text-sm text-gray-400">
                    <div>Página {{ productos.current_page }} de {{ productos.last_page }}</div>
                    <div class="flex space-x-2">
                        <a v-for="(link, idx) in productos.links" :key="idx" :href="link.url"
                           :class="['px-3 py-1 rounded-md transition', link.active ? 'bg-orange-600 text-white' : 'bg-gray-800 hover:bg-gray-700']"
                           v-html="link.label"></a>
                    </div>
                </div>
            </div>
        </main>

        <!-- Add Stock Modal -->
        <div v-if="isModalOpen" class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto overflow-x-hidden bg-black/60 backdrop-blur-sm">
            <div class="relative w-full max-w-md p-4">
                <div class="relative bg-gray-900 rounded-2xl shadow-2xl border border-gray-700 overflow-hidden">
                    <!-- Header -->
                    <div class="flex items-center justify-between p-5 border-b border-gray-800 bg-gray-800/50">
                        <h3 class="text-xl font-bold text-gray-100">
                            Añadir Stock
                        </h3>
                        <button @click="closeAddStockModal" class="text-gray-400 hover:text-white bg-gray-800 hover:bg-gray-700 rounded-lg text-sm p-2 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                    
                    <!-- Body -->
                    <div class="p-6 space-y-4">
                        <div v-if="selectedProduct" class="bg-gray-800/50 p-3 rounded-lg border border-gray-700">
                            <p class="text-sm text-gray-400">Producto:</p>
                            <p class="font-bold text-gray-200">{{ selectedProduct.nombre }} <span class="text-xs text-orange-400 ml-1">({{ selectedProduct.sku }})</span></p>
                        </div>

                        <form @submit.prevent="submitAddStock" class="space-y-4">
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-300">Almacén (Lugar ej. PDTG)</label>
                                <input v-model="form.lugar" type="text" class="bg-gray-800 border border-gray-700 text-white text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5" placeholder="Escribe el código del lugar" required>
                                <p v-if="form.errors.lugar" class="mt-2 text-sm text-red-500">{{ form.errors.lugar }}</p>
                            </div>
                            
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-300">Cantidad a añadir</label>
                                <input v-model="form.cantidad" type="number" min="1" class="bg-gray-800 border border-gray-700 text-white text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2.5" required>
                                <p v-if="form.errors.cantidad" class="mt-2 text-sm text-red-500">{{ form.errors.cantidad }}</p>
                            </div>
                            
                            <div class="flex justify-end gap-3 pt-4 border-t border-gray-800">
                                <button type="button" @click="closeAddStockModal" class="px-4 py-2 text-sm font-medium text-gray-300 bg-gray-800 border border-gray-700 rounded-lg hover:bg-gray-700 hover:text-white transition">Cancelar</button>
                                <button type="submit" :disabled="form.processing" class="px-4 py-2 text-sm font-medium text-white bg-orange-600 rounded-lg hover:bg-orange-500 focus:ring-4 focus:ring-orange-500/50 transition disabled:opacity-50">
                                    Guardar Stock
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { useForm, router } from '@inertiajs/vue3';

const props = defineProps({
    productos: Object,
    lineas: Array,
    filters: Object
});

const filtersForm = ref({
    search: props.filters?.search || '',
    linea_prod_id: props.filters?.linea_prod_id || '',
});

const applyFilters = () => {
    router.get(route('inventario.index'), {
        search: filtersForm.value.search,
        linea_prod_id: filtersForm.value.linea_prod_id,
    }, { preserveState: true, replace: true, preserveScroll: true });
};

const clearFilters = () => {
    filtersForm.value.search = '';
    filtersForm.value.linea_prod_id = '';
    router.get(route('inventario.index'), {}, { preserveState: true, replace: true, preserveScroll: true });
};

const isModalOpen = ref(false);
const selectedProduct = ref(null);

const form = useForm({
    product_id: '',
    lugar: '',
    cantidad: 1,
});

const openAddStockModal = (producto) => {
    selectedProduct.value = producto;
    form.product_id = producto.id;
    form.lugar = '';
    form.cantidad = 1;
    isModalOpen.value = true;
};

const closeAddStockModal = () => {
    isModalOpen.value = false;
    selectedProduct.value = null;
    form.reset();
    form.clearErrors();
};

const submitAddStock = () => {
    form.post(route('inventario.add_stock'), {
        preserveScroll: true,
        onSuccess: () => closeAddStockModal(),
    });
};
</script>
