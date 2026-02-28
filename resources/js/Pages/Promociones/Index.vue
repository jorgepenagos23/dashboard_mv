<template>
    <div class="min-h-screen bg-gray-900 text-gray-100 p-8 font-sans pb-24">
        
        <!-- SECTION 1: PROMOCIONES (Master) -->
        <header class="mb-6 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-red-400 to-orange-500">
                    Gestión de Promociones
                </h1>
                <p class="text-gray-400 mt-2">Crea, edita y administra todas tus ofertas y campañas.</p>
            </div>
            <button @click="openCreatePromoModal" class="px-5 py-2.5 bg-gradient-to-r from-red-600 to-orange-600 hover:from-red-500 hover:to-orange-500 text-white font-bold rounded-lg shadow-lg hover:shadow-red-500/20 transition-all flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Nueva Promoción
            </button>
        </header>

        <section class="mb-12">
            <div class="overflow-hidden rounded-xl border border-gray-800 bg-gray-800/50 shadow-2xl backdrop-blur-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm whitespace-nowrap">
                        <thead class="uppercase tracking-wider border-b border-gray-700 bg-gray-900/50 text-gray-400">
                            <tr>
                                <th class="p-4 font-medium w-32">Acciones</th>
                                <th class="p-4 font-medium">Nombre & Tipo</th>
                                <th class="p-4 font-medium">Fechas</th>
                                <th class="p-4 font-medium text-right">Precio Promocional</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-800">
                            <tr v-for="promo in promociones.data" :key="'p'+promo.id" class="hover:bg-gray-700/30 transition group">
                                <td class="p-4 flex gap-2">
                                    <button @click="openEditPromoModal(promo)" class="text-blue-400 hover:text-blue-300 bg-blue-500/10 p-2 rounded-lg transition" title="Editar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                    </button>
                                    <button @click="deletePromo(promo.id)" class="text-red-400 hover:text-red-300 bg-red-500/10 p-2 rounded-lg transition" title="Eliminar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </td>
                                <td class="p-4">
                                    <div class="flex items-center gap-3">
                                        <img v-if="promo.foto" :src="promo.foto" class="w-10 h-10 rounded border border-gray-700 object-cover" />
                                        <div v-else class="w-10 h-10 rounded border border-gray-700 bg-gray-800 flex items-center justify-center text-xs text-gray-500">IMG</div>
                                        <div>
                                            <p class="font-bold text-gray-100">{{ promo.nombre }} <span class="text-gray-500 text-xs font-mono ml-2">(ID:{{ promo.id }})</span></p>
                                            <span class="px-2 py-0.5 mt-1 inline-block text-[10px] font-bold uppercase rounded bg-orange-500/20 text-orange-400 border border-orange-500/30">
                                                {{ promo.tipo }}
                                            </span>
                                        </div>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1 truncate max-w-xs">{{ promo.descripcion }}</p>
                                </td>
                                <td class="p-4">
                                    <div class="text-xs space-y-1">
                                        <div class="flex items-center gap-1 text-green-400">
                                            <span>Inicia:</span> <span class="font-mono">{{ formatDateTime(promo.fecha_inicio) }}</span>
                                        </div>
                                        <div class="flex items-center gap-1 text-red-400">
                                            <span>Termina:</span> <span class="font-mono">{{ formatDateTime(promo.fecha_fin) }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-4 text-right">
                                    <span v-if="promo.promo_price" class="font-bold text-gray-200 text-lg">${{ parseFloat(promo.promo_price).toFixed(2) }}</span>
                                    <span v-else class="text-gray-500 italic">N/A</span>
                                </td>
                            </tr>
                            <tr v-if="promociones.data.length === 0">
                                <td colspan="4" class="p-8 text-center text-gray-500 italic">No hay promociones registradas aún.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-t border-gray-700 flex items-center justify-between text-sm text-gray-400">
                    <div>Página {{ promociones.current_page }} de {{ promociones.last_page }}</div>
                    <div class="flex space-x-1">
                        <component v-for="(link, i) in promociones.links" :key="i" :is="link.url ? Link : 'span'" :href="link.url"
                            class="px-3 py-1.5 rounded text-sm transition border"
                            :class="[link.active ? 'bg-orange-600 border-orange-500 text-white' : 'bg-gray-800 border-gray-700 text-gray-400 hover:bg-gray-700', !link.url && 'opacity-50 cursor-not-allowed']"
                            v-html="link.label">
                        </component>
                    </div>
                </div>
            </div>
        </section>


        <!-- SECTION 2: PRODUCTOS_PROMOCIONES (Details/Attach) -->
        <header class="mb-6 flex justify-between items-center border-t border-gray-800 pt-10">
            <div>
                <h2 class="text-2xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-indigo-500">
                    Productos en Promoción
                </h2>
                <p class="text-gray-400 mt-1">Vincula productos a tus promociones y dales MV Coins extra.</p>
            </div>
            <button @click="openCreateProdPromoModal" class="px-5 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold rounded-lg shadow-lg hover:shadow-blue-500/20 transition-all flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Vincular Producto
            </button>
        </header>

        <section>
            <div class="overflow-hidden rounded-xl border border-gray-800 bg-gray-800/50 shadow-2xl backdrop-blur-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm whitespace-nowrap">
                        <thead class="uppercase tracking-wider border-b border-gray-700 bg-gray-900/50 text-gray-400">
                            <tr>
                                <th class="p-4 font-medium w-32">Acciones</th>
                                <th class="p-4 font-medium">Promoción</th>
                                <th class="p-4 font-medium">Producto Incluido (SKU)</th>
                                <th class="p-4 font-medium text-right">MV Coins Extra</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-800">
                            <tr v-for="pp in productosPromociones.data" :key="'pp'+pp.id" class="hover:bg-gray-700/30 transition group">
                                <td class="p-4 flex gap-2">
                                    <button @click="openEditProdPromoModal(pp)" class="text-indigo-400 hover:text-indigo-300 bg-indigo-500/10 p-2 rounded-lg transition" title="Editar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                    </button>
                                    <button @click="deleteProdPromo(pp.id)" class="text-red-400 hover:text-red-300 bg-red-500/10 p-2 rounded-lg transition" title="Eliminar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </td>
                                <td class="p-4">
                                    <div v-if="pp.promocion">
                                        <p class="font-bold text-gray-200">{{ pp.promocion.nombre }}</p>
                                        <p class="text-xs text-gray-500 mt-0.5">ID Promo: {{ pp.id_promocion }}</p>
                                    </div>
                                    <div v-else class="text-red-400 font-mono text-xs">ID Promoción: {{ pp.id_promocion }} (Huérfana)</div>
                                </td>
                                <td class="p-4">
                                    <div v-if="pp.producto">
                                        <p class="font-medium text-gray-200">{{ pp.producto.nombre }}</p>
                                        <p class="text-xs text-indigo-400 font-mono mt-0.5">SKU: {{ pp.producto.sku || pp.id_producto }}</p>
                                    </div>
                                    <div v-else class="font-mono text-xs text-gray-400">ID Producto: {{ pp.id_producto }}</div>
                                </td>
                                <td class="p-4 text-right">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-bold bg-yellow-400/20 text-yellow-400 border border-yellow-400/30">
                                        +{{ pp.mvcoins_extra }}
                                    </span>
                                </td>
                            </tr>
                            <tr v-if="productosPromociones.data.length === 0">
                                <td colspan="4" class="p-8 text-center text-gray-500 italic">No hay productos vinculados a promociones.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-t border-gray-700 flex items-center justify-between text-sm text-gray-400">
                    <div>Página {{ productosPromociones.current_page }} de {{ productosPromociones.last_page }}</div>
                    <div class="flex space-x-1">
                        <component v-for="(link, i) in productosPromociones.links" :key="i" :is="link.url ? Link : 'span'" :href="link.url"
                            class="px-3 py-1.5 rounded text-sm transition border"
                            :class="[link.active ? 'bg-indigo-600 border-indigo-500 text-white' : 'bg-gray-800 border-gray-700 text-gray-400 hover:bg-gray-700', !link.url && 'opacity-50 cursor-not-allowed']"
                            v-html="link.label">
                        </component>
                    </div>
                </div>
            </div>
        </section>

        <!-- MODAL: PROMOCIONES (Master) -->
        <div v-if="isPromoModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
            <div class="bg-gray-800 border border-gray-700 rounded-xl shadow-2xl w-full max-w-2xl max-h-[90vh] flex flex-col overflow-hidden">
                <div class="p-5 border-b border-gray-700 flex justify-between items-center bg-gray-900/50">
                    <h3 class="text-xl font-bold flex items-center gap-2 text-white">
                        <span class="w-2.5 h-6 rounded bg-gradient-to-b from-red-500 to-orange-500"></span>
                        {{ editPromoMode ? 'Editar Promoción' : 'Nueva Promoción' }}
                    </h3>
                    <button @click="closePromoModal" class="text-gray-400 hover:text-white transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                <form @submit.prevent="submitPromoForm" class="flex-1 overflow-y-auto custom-scrollbar p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="space-y-1 md:col-span-2">
                            <label class="block text-xs font-medium text-gray-400 uppercase">Nombre</label>
                            <input v-model="formPromo.nombre" type="text" required class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 text-gray-200 focus:border-orange-500 focus:ring-1 focus:ring-orange-500" placeholder="Ej. Cyber Monday 2026"/>
                        </div>
                        <div class="space-y-1">
                            <label class="block text-xs font-medium text-gray-400 uppercase">Tipo</label>
                            <input v-model="formPromo.tipo" type="text" required class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 text-gray-200 focus:border-orange-500 focus:ring-1 focus:ring-orange-500" placeholder="Ej. Descuento, 2x1..."/>
                        </div>
                        <div class="space-y-1">
                            <label class="block text-xs font-medium text-gray-400 uppercase">Precio Promocional</label>
                            <input v-model="formPromo.promo_price" type="number" step="0.01" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 text-gray-200 focus:border-orange-500 focus:ring-1 focus:ring-orange-500" placeholder="Ej. 199.99"/>
                        </div>
                        <div class="space-y-1">
                            <label class="block text-xs font-medium text-gray-400 uppercase">Fecha de Inicio</label>
                            <input v-model="formPromo.fecha_inicio" type="datetime-local" required class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 text-gray-200 focus:border-orange-500 focus:ring-1 focus:ring-orange-500" />
                        </div>
                        <div class="space-y-1">
                            <label class="block text-xs font-medium text-gray-400 uppercase">Fecha de Fin</label>
                            <input v-model="formPromo.fecha_fin" type="datetime-local" required class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 text-gray-200 focus:border-orange-500 focus:ring-1 focus:ring-orange-500" />
                        </div>
                        <div class="space-y-1 md:col-span-2">
                            <label class="block text-xs font-medium text-gray-400 uppercase">Descripción</label>
                            <textarea v-model="formPromo.descripcion" rows="2" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 text-gray-200 focus:border-orange-500 focus:ring-1 focus:ring-orange-500" placeholder="Detalles de la promoción..."></textarea>
                        </div>
                        <div class="space-y-1">
                            <label class="block text-xs font-medium text-gray-400 uppercase">URL Foto (Opcional)</label>
                            <input v-model="formPromo.foto" type="text" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 text-gray-200 focus:border-orange-500 focus:ring-1 focus:ring-orange-500" />
                        </div>
                        <div class="space-y-1">
                            <label class="block text-xs font-medium text-gray-400 uppercase">URL Banner (Opcional)</label>
                            <input v-model="formPromo.foto_banner" type="text" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 text-gray-200 focus:border-orange-500 focus:ring-1 focus:ring-orange-500" />
                        </div>
                    </div>
                    <div class="mt-8 flex justify-end gap-3 pt-5 border-t border-gray-700">
                        <button type="button" @click="closePromoModal" class="px-4 py-2 rounded-lg text-sm font-medium text-gray-400 hover:text-white hover:bg-gray-700 transition">Cancelar</button>
                        <button type="submit" :disabled="formPromoRef.processing" class="px-5 py-2 rounded-lg text-sm font-medium bg-gradient-to-r from-orange-600 to-red-600 hover:from-orange-500 hover:to-red-500 text-white transition focus:ring-2 disabled:opacity-50 flex items-center gap-2">Guardar Promoción</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL: PRODUCTOS_PROMOCIONES (Details) -->
        <div v-if="isProdPromoModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
            <div class="bg-gray-800 border border-gray-700 rounded-xl shadow-2xl w-full max-w-md flex flex-col overflow-hidden">
                <div class="p-5 border-b border-gray-700 flex justify-between items-center bg-gray-900/50">
                    <h3 class="text-xl font-bold flex items-center gap-2 text-white">
                        <span class="w-2.5 h-6 rounded bg-gradient-to-b from-blue-500 to-indigo-500"></span>
                        {{ editProdPromoMode ? 'Editar Vínculo' : 'Vincular Producto' }}
                    </h3>
                    <button @click="closeProdPromoModal" class="text-gray-400 hover:text-white transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                <form @submit.prevent="submitProdPromoForm" class="p-6 space-y-5">
                    
                    <div class="space-y-1">
                        <label class="block text-xs font-medium text-gray-400 uppercase">Seleccionar Promoción Maestro</label>
                        <select v-model="formProdPromo.id_promocion" required class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 text-gray-200 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                            <option value="" disabled>-- Elige una promoción --</option>
                            <option v-for="prom in allPromotions" :key="prom.id" :value="prom.id">
                                ID: {{ prom.id }} | {{ prom.nombre }}
                            </option>
                        </select>
                    </div>

                    <div class="space-y-1">
                        <label class="block text-xs font-medium text-gray-400 uppercase">ID del Producto (SKU/PK)</label>
                        <input v-model="formProdPromo.id_producto" type="number" required class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 text-gray-200 font-mono focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500" placeholder="Ej. 1205" />
                        <p class="text-[10px] text-gray-500 mt-1">Ingresa directamente el ID interno del producto de la tabla `productos`.</p>
                    </div>

                    <div class="space-y-1">
                        <label class="block text-xs font-medium text-gray-400 uppercase">MV Coins Extra (Bono)</label>
                        <input v-model="formProdPromo.mvcoins_extra" type="number" required class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 text-gray-200 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500" placeholder="Ej. 50" />
                    </div>

                    <div class="mt-8 flex justify-end gap-3 pt-5 border-t border-gray-700">
                        <button type="button" @click="closeProdPromoModal" class="px-4 py-2 rounded-lg text-sm font-medium text-gray-400 hover:text-white hover:bg-gray-700 transition">Cancelar</button>
                        <button type="submit" :disabled="formProdPromoRef.processing" class="px-5 py-2 rounded-lg text-sm font-medium bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white transition focus:ring-2 disabled:opacity-50">Guardar Vínculo</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</template>

<script setup>
import { ref } from 'vue';
import { useForm, Link, router } from '@inertiajs/vue3';

const props = defineProps({
    promociones: Object,            // Paginated Promociones
    productosPromociones: Object,   // Paginated ProductoPromocion
    allPromotions: Array            // Just for the <select> element
});

// ============================================
// LOGIC FOR MASTER PROMOCIONES
// ============================================
const isPromoModalOpen = ref(false);
const editPromoMode = ref(false);
const editPromoId = ref(null);
const formPromoRef = useForm({
    nombre: '', tipo: '', descripcion: '', promo_price: '', fecha_inicio: '', fecha_fin: '', foto: '', foto_banner: ''
});
const formPromo = ref({...formPromoRef});

const formatForInput = (dateStr) => {
    if(!dateStr) return '';
    const date = new Date(dateStr);
    date.setMinutes(date.getMinutes() - date.getTimezoneOffset());
    return date.toISOString().slice(0, 16);
};

const openCreatePromoModal = () => {
    editPromoMode.value = false; editPromoId.value = null;
    formPromo.value = { nombre: '', tipo: '', descripcion: '', promo_price: '', fecha_inicio: '', fecha_fin: '', foto: '', foto_banner: '' };
    isPromoModalOpen.value = true;
};

const openEditPromoModal = (promo) => {
    editPromoMode.value = true; editPromoId.value = promo.id;
    formPromo.value = { ...promo, fecha_inicio: formatForInput(promo.fecha_inicio), fecha_fin: formatForInput(promo.fecha_fin) };
    isPromoModalOpen.value = true;
};
const closePromoModal = () => isPromoModalOpen.value = false;

const submitPromoForm = () => {
    Object.keys(formPromo.value).forEach(key => formPromoRef[key] = formPromo.value[key]);
    if (editPromoMode.value) formPromoRef.put(route('promociones.update', editPromoId.value), { preserveScroll: true, onSuccess: closePromoModal });
    else formPromoRef.post(route('promociones.store'), { preserveScroll: true, onSuccess: closePromoModal });
};

const deletePromo = (id) => {
    if (confirm('¿Eliminar promoción y todo vínculo de producto huérfano?')) router.delete(route('promociones.destroy', id), { preserveScroll: true });
};


// ============================================
// LOGIC FOR PRODUCTOS_PROMOCIONES (DETAILS)
// ============================================
const isProdPromoModalOpen = ref(false);
const editProdPromoMode = ref(false);
const editProdPromoId = ref(null);

const formProdPromoRef = useForm({
    id_producto: '', id_promocion: '', mvcoins_extra: ''
});
const formProdPromo = ref({...formProdPromoRef});

const openCreateProdPromoModal = () => {
    editProdPromoMode.value = false; editProdPromoId.value = null;
    formProdPromo.value = { id_producto: '', id_promocion: '', mvcoins_extra: 0 };
    isProdPromoModalOpen.value = true;
};

const openEditProdPromoModal = (pp) => {
    editProdPromoMode.value = true; editProdPromoId.value = pp.id;
    formProdPromo.value = { id_producto: pp.id_producto, id_promocion: pp.id_promocion, mvcoins_extra: pp.mvcoins_extra };
    isProdPromoModalOpen.value = true;
};
const closeProdPromoModal = () => isProdPromoModalOpen.value = false;

const submitProdPromoForm = () => {
    Object.keys(formProdPromo.value).forEach(key => formProdPromoRef[key] = formProdPromo.value[key]);
    if (editProdPromoMode.value) formProdPromoRef.put(route('productos-promociones.update', editProdPromoId.value), { preserveScroll: true, onSuccess: closeProdPromoModal });
    else formProdPromoRef.post(route('productos-promociones.store'), { preserveScroll: true, onSuccess: closeProdPromoModal });
};

const deleteProdPromo = (id) => {
    if (confirm('¿Desvincular este producto de la promoción?')) router.delete(route('productos-promociones.destroy', id), { preserveScroll: true });
};


// Utils
const formatDateTime = (dateStr) => {
    if (!dateStr) return 'N/A';
    return new Date(dateStr).toLocaleString('es-MX', { month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });
};
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { width: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background-color: #4b5563; border-radius: 20px; }
</style>
