<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex-1">
                <h1 class="text-3xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-pink-500 to-rose-600">
                    Acceso de Clientes a Promociones
                </h1>
                <p class="text-gray-500 mt-1">Controla qué clientes tienen acceso a cada promoción específica.</p>
            </div>
        </template>

        <div class="min-h-screen bg-gray-50 text-gray-800 p-8 font-sans pb-24">

            <!-- Flash -->
            <div v-if="$page.props.flash?.success"
                 class="mb-6 flex items-center gap-3 bg-green-50 border border-green-200 text-green-700 rounded-xl px-5 py-3 shadow-sm">
                <svg class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span class="font-medium">{{ $page.props.flash.success }}</span>
            </div>

            <!-- Tabs -->
            <div class="flex space-x-1 border-b border-gray-200 mb-8">
                <button v-for="tab in tabs" :key="tab.id"
                    @click="activeTab = tab.id"
                    :class="['px-6 py-3 text-sm font-semibold border-b-2 transition-all -mb-px',
                             activeTab === tab.id
                               ? 'border-pink-500 text-pink-600'
                               : 'border-transparent text-gray-500 hover:text-gray-700']">
                    {{ tab.label }}
                </button>
            </div>

            <!-- ══════════════════════════════════════════════════════════ -->
            <!-- TAB 1 — PROMOCIONES (control de acceso por promoción)      -->
            <!-- ══════════════════════════════════════════════════════════ -->
            <section v-show="activeTab === 'promociones'">

                <div class="mb-5 bg-blue-50 border border-blue-200 rounded-xl px-5 py-3 text-sm text-blue-700 flex gap-2 items-start">
                    <svg class="w-5 h-5 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        Activa <strong>Acceso restringido</strong> en una promoción para que solo los clientes asignados puedan verla en la app.
                        Si está desactivado, la promoción es visible para todos.
                    </div>
                </div>

                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    <div v-for="promo in promociones" :key="promo.id"
                         class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5 flex flex-col gap-3">

                        <div class="flex items-start justify-between gap-2">
                            <div class="flex-1 min-w-0">
                                <p class="text-base font-extrabold text-gray-800 truncate">{{ promo.nombre }}</p>
                                <p class="text-xs text-gray-400 mt-0.5 truncate">{{ promo.descripcion || 'Sin descripción' }}</p>
                            </div>
                            <span :class="['px-2.5 py-1 rounded-full text-xs font-bold uppercase shrink-0',
                                           tipoColor(promo.tipo)]">
                                {{ promo.tipo }}
                            </span>
                        </div>

                        <div class="flex items-center gap-3 text-sm text-gray-500">
                            <span v-if="promo.promo_price" class="font-bold text-pink-600 text-lg">
                                {{ promo.promo_price }}%
                            </span>
                            <span class="text-xs font-mono bg-gray-50 border border-gray-200 px-2 py-0.5 rounded">
                                {{ formatFecha(promo.fecha_inicio) }} → {{ formatFecha(promo.fecha_fin) }}
                            </span>
                        </div>

                        <!-- Contador de clientes asignados -->
                        <div class="text-xs text-gray-500 flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                            </svg>
                            <span>
                                <strong class="text-gray-700">{{ promo.clientes_count }}</strong>
                                {{ promo.clientes_count === 1 ? 'cliente asignado' : 'clientes asignados' }}
                            </span>
                        </div>

                        <!-- Toggle acceso restringido -->
                        <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                            <div>
                                <p class="text-xs font-semibold text-gray-600">Acceso restringido</p>
                                <p class="text-xs text-gray-400">
                                    {{ promo.acceso_restringido ? 'Solo clientes asignados' : 'Pública para todos' }}
                                </p>
                            </div>
                            <button @click="toggleRestringida(promo)"
                                    :class="['relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none',
                                             promo.acceso_restringido ? 'bg-pink-500' : 'bg-gray-200']">
                                <span :class="['pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
                                               promo.acceso_restringido ? 'translate-x-5' : 'translate-x-0']">
                                </span>
                            </button>
                        </div>
                    </div>
                </div>

                <p v-if="!promociones.length" class="text-center text-gray-400 italic py-12">
                    No hay promociones del tipo distinto a GRUPO_PRECIO.
                </p>
            </section>

            <!-- ══════════════════════════════════════════════════════════ -->
            <!-- TAB 2 — ASIGNACIONES (clientes ↔ promociones)             -->
            <!-- ══════════════════════════════════════════════════════════ -->
            <section v-show="activeTab === 'asignaciones'">
                <div class="grid lg:grid-cols-3 gap-8">

                    <!-- Formulario de asignación -->
                    <div class="lg:col-span-1">
                        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 sticky top-6">
                            <h2 class="text-lg font-bold text-gray-800 mb-5 flex items-center gap-2">
                                <span class="w-2 h-5 rounded bg-pink-500"></span>
                                Asignar cliente a promoción
                            </h2>

                            <form @submit.prevent="asignar" class="space-y-4">

                                <!-- Buscador de cliente -->
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Buscar cliente</label>
                                    <input v-model="buscarCliente" @input="buscarDebounced"
                                           type="text" placeholder="Nombre o código..."
                                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-pink-400 focus:border-transparent outline-none" />
                                </div>

                                <!-- Resultados de búsqueda -->
                                <div v-if="todosClientes.length && buscarCliente"
                                     class="border border-gray-200 rounded-lg overflow-hidden max-h-40 overflow-y-auto">
                                    <button v-for="c in todosClientes" :key="c.id"
                                            type="button"
                                            @click="seleccionarCliente(c)"
                                            :class="['w-full text-left px-3 py-2 text-sm hover:bg-pink-50 transition border-b border-gray-100 last:border-0',
                                                     asignarForm.id_cliente === c.id ? 'bg-pink-50 font-semibold text-pink-700' : 'text-gray-700']">
                                        <span class="font-mono text-xs text-gray-400 mr-2">{{ c.cliente }}</span>
                                        {{ c.razon_social }}
                                    </button>
                                </div>

                                <!-- Cliente seleccionado -->
                                <div v-if="clienteSeleccionado"
                                     class="flex items-center justify-between gap-2 bg-pink-50 border border-pink-200 rounded-lg px-3 py-2 text-sm text-pink-700">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                        </svg>
                                        <span class="font-semibold truncate">{{ clienteSeleccionado.razon_social }}</span>
                                    </div>
                                    <button type="button" @click="clienteSeleccionado = null; asignarForm.id_cliente = null"
                                            class="text-pink-400 hover:text-pink-600 shrink-0">✕</button>
                                </div>

                                <!-- Selección de promoción -->
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Promoción</label>
                                    <select v-model="asignarForm.id_promocion" required
                                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-pink-400 focus:border-transparent outline-none">
                                        <option value="" disabled>Selecciona una promoción...</option>
                                        <option v-for="p in promocionesRestringidas" :key="p.id" :value="p.id">
                                            {{ p.nombre }}
                                            <template v-if="p.promo_price"> — {{ p.promo_price }}%</template>
                                            ({{ p.tipo }})
                                        </option>
                                    </select>
                                    <p v-if="!promocionesRestringidas.length" class="text-xs text-amber-600 mt-1">
                                        Activa "Acceso restringido" en al menos una promoción desde la pestaña anterior.
                                    </p>
                                </div>

                                <!-- Fechas de vigencia -->
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Desde</label>
                                        <input v-model="asignarForm.fecha_inicio" type="date"
                                               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-pink-400 outline-none" />
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Hasta</label>
                                        <input v-model="asignarForm.fecha_fin" type="date"
                                               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-pink-400 outline-none" />
                                    </div>
                                </div>

                                <button type="submit"
                                        :disabled="!asignarForm.id_cliente || !asignarForm.id_promocion"
                                        class="w-full py-2.5 bg-pink-600 hover:bg-pink-500 disabled:opacity-40 disabled:cursor-not-allowed text-white font-bold rounded-xl shadow transition">
                                    Asignar acceso
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Tabla de asignaciones activas -->
                    <div class="lg:col-span-2">
                        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                                <h2 class="font-bold text-gray-800">Clientes con acceso asignado</h2>
                                <span class="text-xs text-gray-400">Hoy: {{ today }}</span>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="w-full text-sm text-left">
                                    <thead class="bg-gray-50 text-xs uppercase tracking-wider text-gray-500 border-b border-gray-200">
                                        <tr>
                                            <th class="px-5 py-3">Cliente</th>
                                            <th class="px-5 py-3">Promoción</th>
                                            <th class="px-5 py-3">Tipo</th>
                                            <th class="px-5 py-3">Vigencia</th>
                                            <th class="px-5 py-3 text-right">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        <tr v-for="a in asignaciones.data" :key="a.id"
                                            class="hover:bg-gray-50 transition">
                                            <td class="px-5 py-3">
                                                <div class="font-semibold text-gray-800">{{ a.cliente?.razon_social }}</div>
                                                <div class="text-xs text-gray-400 font-mono">{{ a.cliente?.cliente }}</div>
                                            </td>
                                            <td class="px-5 py-3">
                                                <span class="font-semibold text-gray-800">{{ a.promocion?.nombre }}</span>
                                                <div v-if="a.promocion?.promo_price" class="text-xs text-pink-600 font-bold">
                                                    {{ a.promocion.promo_price }}%
                                                </div>
                                            </td>
                                            <td class="px-5 py-3">
                                                <span :class="['px-2 py-0.5 rounded-full text-xs font-bold', tipoColor(a.promocion?.tipo)]">
                                                    {{ a.promocion?.tipo }}
                                                </span>
                                            </td>
                                            <td class="px-5 py-3 text-xs text-gray-500 font-mono">
                                                <div>{{ formatFecha(a.fecha_inicio) }}</div>
                                                <div>{{ formatFecha(a.fecha_fin) }}</div>
                                            </td>
                                            <td class="px-5 py-3 text-right">
                                                <button @click="remover(a.id)"
                                                        class="text-red-500 hover:text-red-400 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-lg text-xs font-bold transition">
                                                    Quitar
                                                </button>
                                            </td>
                                        </tr>
                                        <tr v-if="!asignaciones.data?.length">
                                            <td colspan="5" class="px-5 py-10 text-center text-gray-400 italic">
                                                No hay asignaciones de clientes a promociones aún.
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Paginación -->
                            <div v-if="asignaciones.last_page > 1"
                                 class="px-5 py-4 border-t border-gray-100 flex items-center justify-between text-sm text-gray-500 bg-gray-50/50">
                                <span>Página {{ asignaciones.current_page }} de {{ asignaciones.last_page }}</span>
                                <div class="flex gap-1">
                                    <component v-for="(link, i) in asignaciones.links" :key="i"
                                               :is="link.url ? Link : 'span'"
                                               :href="link.url"
                                               class="px-3 py-1.5 rounded text-sm border transition"
                                               :class="[link.active ? 'bg-pink-600 border-pink-500 text-white' : 'bg-white border-gray-300 text-gray-600 hover:bg-gray-50', !link.url && 'opacity-50 cursor-not-allowed']"
                                               v-html="link.label">
                                    </component>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps({
    promociones:    Array,
    asignaciones:   Object,
    todosClientes:  Array,
    filters:        Object,
});

// ── Tabs ─────────────────────────────────────────────────────────────────────
const tabs = [
    { id: 'promociones',  label: 'Promociones'       },
    { id: 'asignaciones', label: 'Asignaciones'      },
];
const activeTab = ref('promociones');

const today = new Date().toLocaleDateString('es-MX', { day: '2-digit', month: 'short', year: 'numeric' });

// Solo las promociones con acceso_restringido=1 aparecen en el dropdown de asignación
const promocionesRestringidas = computed(() =>
    props.promociones.filter(p => p.acceso_restringido)
);

// ── TAB 1: Toggle acceso_restringido ──────────────────────────────────────────
const toggleRestringida = (promo) => {
    router.patch(route('promociones-clientes.restringir', promo.id), {
        acceso_restringido: !promo.acceso_restringido,
    }, { preserveScroll: true });
};

// ── TAB 2: Asignación de clientes ─────────────────────────────────────────────
const buscarCliente       = ref(props.filters?.buscar_cliente ?? '');
const clienteSeleccionado = ref(null);

const asignarForm = ref({
    id_cliente:   null,
    id_promocion: '',
    fecha_inicio: new Date().toISOString().split('T')[0],
    fecha_fin:    '2030-12-31',
});

let debounceTimer = null;
const buscarDebounced = () => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        router.get(route('promociones-clientes.index'),
            { buscar_cliente: buscarCliente.value },
            { preserveState: true, preserveScroll: true, replace: true }
        );
    }, 350);
};

const seleccionarCliente = (c) => {
    clienteSeleccionado.value = c;
    asignarForm.value.id_cliente = c.id;
    buscarCliente.value = '';
};

const asignar = () => {
    router.post(route('promociones-clientes.asignar'), asignarForm.value, {
        preserveScroll: true,
        onSuccess: () => {
            clienteSeleccionado.value = null;
            asignarForm.value = {
                id_cliente:   null,
                id_promocion: '',
                fecha_inicio: new Date().toISOString().split('T')[0],
                fecha_fin:    '2030-12-31',
            };
        },
    });
};

const remover = (id) => {
    if (!confirm('¿Quitar el acceso de este cliente a la promoción?')) return;
    router.delete(route('promociones-clientes.remover', id), { preserveScroll: true });
};

// ── Helpers ───────────────────────────────────────────────────────────────────
const formatFecha = (f) => f ? new Date(f).toLocaleDateString('es-MX') : '—';

const tipoColor = (tipo) => {
    const map = {
        DESCUENTO:   'bg-blue-100 text-blue-700 border border-blue-200',
        MULTICOMBO:  'bg-amber-100 text-amber-700 border border-amber-200',
        REGALO:      'bg-green-100 text-green-700 border border-green-200',
        PROMO_APP:   'bg-purple-100 text-purple-700 border border-purple-200',
    };
    return map[tipo] ?? 'bg-gray-100 text-gray-600 border border-gray-200';
};
</script>
