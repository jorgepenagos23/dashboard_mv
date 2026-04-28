<template>
    <div class="min-h-screen bg-gray-900 text-gray-100 p-8 font-sans pb-24">
        <header class="mb-6 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-teal-400 to-emerald-500">
                    Centro de Notificaciones (Push)
                </h1>
                <p class="text-gray-400 mt-2">Envía notificaciones personalizadas a los dispositivos registrados de tus clientes.</p>
            </div>
        </header>

        <!-- ALERTA DE CONFIGURACION -->
        <div v-if="!hasServiceAccount" class="mb-6 bg-red-900/50 border border-red-500 rounded-xl p-4 flex items-start gap-4 shadow-xl">
            <svg class="w-8 h-8 text-red-400 shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            <div>
                <h4 class="text-lg font-bold text-red-400">¡Falta el archivo de Credenciales de Firebase!</h4>
                <p class="text-gray-300 text-sm mt-1">
                    Para que el servidor de Node pueda enviar las notificaciones, necesitas guardar tu archivo <code>service-account.json</code> en la ruta:
                    <br><br>
                    <code class="bg-black/50 px-2 py-1 rounded text-red-200">storage/app/firebase-service-account.json</code>
                    <br><br>
                    <strong>¡Importante!</strong> Este archivo NO debe ser el <code>google-services.json</code> de la app Android. Debe ser el JSON de <em>Cuentas de Servicio</em> (Service Account Key) que se descarga desde la <a href="https://console.firebase.google.com/project/_/settings/serviceaccounts/adminsdk" target="_blank" class="underline hover:text-red-200">Configuración de Firebase -> Cuentas de Servicio -> "Generar nueva clave privada"</a>.
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- LISTA DE TOKENS -->
            <div class="lg:col-span-2 space-y-4">
                <div class="overflow-hidden rounded-xl border border-gray-800 bg-gray-800/50 shadow-2xl backdrop-blur-sm">
                    <div class="p-4 border-b border-gray-700 bg-gray-900/50 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                        <h3 class="font-bold text-lg text-white flex items-center gap-2">
                            <span class="w-2 h-5 rounded bg-teal-500"></span> Dispositivos Registrados
                        </h3>
                        
                        <div class="flex flex-col sm:flex-row items-start md:items-center gap-3 w-full md:w-auto">
                            <!-- Filtro de Grupo -->
                            <select v-model="filterGroup" class="bg-gray-900 border border-gray-700 text-sm text-gray-300 rounded-lg px-3 py-1.5 focus:ring-teal-500 focus:border-teal-500 w-full sm:w-auto">
                                <option value="">Todos los grupos (Rutas)</option>
                                <option v-for="grupo in availableGroups" :key="grupo" :value="grupo">
                                    Ruta: {{ grupo }}
                                </option>
                            </select>

                            <div class="flex items-center gap-3">
                                <label class="flex items-center gap-2 text-sm text-gray-300 cursor-pointer">
                                    <input type="checkbox" @change="toggleSelectFiltered" :checked="areFilteredSelected" class="rounded bg-gray-900 border-gray-600 text-teal-500 focus:ring-teal-500">
                                    Marcar visibles
                                </label>
                                <span class="bg-teal-500/20 text-teal-400 text-xs font-bold px-2 py-1 rounded-full border border-teal-500/30 whitespace-nowrap">
                                    {{ selectedTokens.length }} seleccionados
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="overflow-x-auto max-h-[60vh]">
                        <table class="w-full text-left text-sm whitespace-nowrap">
                            <thead class="uppercase tracking-wider border-b border-gray-700 bg-gray-900/40 text-gray-400 sticky top-0 z-10 backdrop-blur-md">
                                <tr>
                                    <th class="p-3 w-10 text-center">Sel.</th>
                                    <th class="p-3 font-medium">Ruta</th>
                                    <th class="p-3 font-medium">Cod. Cliente</th>
                                    <th class="p-3 font-medium">Razón Social / Nombre</th>
                                    <th class="p-3 font-medium">SO</th>
                                    <th class="p-3 font-medium">FCM Token</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-800 text-gray-300">
                                <tr v-for="t in filteredTokens" :key="t.id" class="hover:bg-gray-700/30 transition cursor-pointer" @click="toggleSelect(t.token)">
                                    <td class="p-3 text-center" @click.stop>
                                        <input type="checkbox" :value="t.token" v-model="selectedTokens" class="rounded bg-gray-900 border-gray-600 text-teal-500 focus:ring-teal-500 w-4 h-4">
                                    </td>
                                    <td class="p-3">
                                        <span class="px-2 py-0.5 text-xs font-bold text-teal-300 bg-teal-900/40 rounded border border-teal-800">
                                            {{ getTokenGroup(t) }}
                                        </span>
                                    </td>
                                    <td class="p-3 font-mono text-gray-400">
                                        {{ t.cliente ? t.cliente.cliente : 'N/A' }}
                                    </td>
                                    <td class="p-3">
                                        <div v-if="t.cliente" class="font-bold text-white">{{ getTokenRazonSocial(t) }}</div>
                                        <div v-else class="text-xs text-red-400">Usuario ID: {{ t.usuario_id }} (Invalido)</div>
                                    </td>
                                    <td class="p-3">
                                        <span class="px-2 py-0.5 uppercase text-[10px] font-bold rounded bg-gray-700 border border-gray-600">
                                            {{ t.dispositivo }}
                                        </span>
                                    </td>
                                    <td class="p-3 text-xs font-mono text-gray-500 max-w-[100px] truncate" :title="t.token">
                                        {{ t.token }}
                                    </td>
                                </tr>
                                <tr v-if="filteredTokens.length === 0">
                                    <td colspan="6" class="p-8 text-center text-gray-500 italic flex-col flex items-center gap-2">
                                        <svg class="w-8 h-8 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        No hay dispositivos en este grupo.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginacion Normal -->
                    <div class="p-4 border-t border-gray-700 flex items-center justify-between text-sm text-gray-400 bg-gray-900/40">
                        <div>Página {{ tokens.current_page }} de {{ tokens.last_page }}</div>
                        <div class="flex space-x-1">
                            <component v-for="(link, i) in tokens.links" :key="i" :is="link.url ? Link : 'span'" :href="link.url"
                                class="px-3 py-1.5 rounded text-sm transition border"
                                :class="[link.active ? 'bg-teal-600 border-teal-500 text-white' : 'bg-gray-800 border-gray-700 text-gray-400 hover:bg-gray-700', !link.url && 'opacity-50 cursor-not-allowed']"
                                v-html="link.label">
                            </component>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FORMULARIO DE ENVÍO -->
            <div class="lg:col-span-1">
                <div class="bg-gray-800 border border-gray-700 rounded-xl shadow-2xl p-6 sticky top-6">
                    <h3 class="text-xl font-bold flex items-center gap-2 text-white mb-6">
                        <span class="w-2.5 h-6 rounded bg-gradient-to-b from-teal-500 to-emerald-500"></span>
                        Redactar Mensaje
                    </h3>

                    <div v-if="$page.props.flash && $page.props.flash.success" class="mb-4 p-3 bg-emerald-500/20 border border-emerald-500/50 rounded-lg text-emerald-400 text-sm">
                        {{ $page.props.flash.success }}
                    </div>
                    <div v-if="$page.props.flash && $page.props.flash.error" class="mb-4 p-3 bg-red-500/20 border border-red-500/50 rounded-lg text-red-400 text-sm">
                        {{ $page.props.flash.error }}
                    </div>

                    <form @submit.prevent="sendNotification" class="space-y-4">
                        <div class="space-y-1">
                            <label class="block text-xs font-medium text-gray-400 uppercase">Título de la Notificación</label>
                            <input v-model="form.title" type="text" required class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 text-gray-200 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500" placeholder="Ej. ⏳ ¡Solo tienes 30 minutos!" />
                        </div>
                        
                        <div class="space-y-1">
                            <label class="block text-xs font-medium text-gray-400 uppercase">Cuerpo / Mensaje</label>
                            <textarea v-model="form.body" rows="4" required class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 text-gray-200 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500" placeholder="Ej. Queda media hora para meter tu pedido de hoy..."></textarea>
                        </div>
                        
                        <!-- PREVIEW -->
                        <div class="mt-4 p-4 bg-gray-900 border border-gray-700 rounded-lg relative overflow-hidden">
                            <div class="absolute inset-0 bg-gradient-to-br from-teal-900/20 to-transparent pointer-events-none"></div>
                            <p class="text-[10px] text-gray-500 font-bold uppercase tracking-wider mb-2">Vista Previa Android</p>
                            <div class="bg-gray-800 rounded-lg p-3 shadow-lg flex gap-3">
                                <div class="w-8 h-8 rounded-full bg-teal-500 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                                </div>
                                <div class="overflow-hidden">
                                    <p class="text-sm font-bold text-gray-100 truncate">{{ form.title || 'Título...' }}</p>
                                    <p class="text-xs text-gray-400 mt-0.5 line-clamp-2">{{ form.body || 'Mensaje de prueba...' }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-gray-700">
                            <button type="submit" :disabled="form.processing || selectedTokens.length === 0" class="w-full px-5 py-3 rounded-lg text-sm font-bold bg-gradient-to-r from-teal-500 to-emerald-500 hover:from-teal-400 hover:to-emerald-400 shadow-lg text-white transition focus:ring-2 focus:ring-teal-500 disabled:opacity-50 flex items-center justify-center gap-2">
                                <span v-if="form.processing" class="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                                <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                                Enviar a {{ selectedTokens.length }} Disp.
                            </button>
                            <p v-if="selectedTokens.length === 0" class="text-xs text-center text-red-400 mt-2 font-medium">Selecciona al menos 1 dispositivo</p>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';

const props = defineProps({
    tokens: Object,
    hasServiceAccount: Boolean
});

const selectedTokens = ref([]);
const filterGroup = ref('');

// Helper to get group (ruta)
const getTokenGroup = (t) => {
    if (t.cliente && t.cliente.direcciones && t.cliente.direcciones.length > 0) {
        return t.cliente.direcciones[0].CTEPFR_CODIGO_K || 'Sin Ruta';
    }
    return 'Sin Ruta';
};

// Helper for Razon Social
const getTokenRazonSocial = (t) => {
    if (t.cliente && t.cliente.direcciones && t.cliente.direcciones.length > 0) {
        return t.cliente.direcciones[0].CTECLI_RAZONSOCIAL || t.cliente.razon_social || t.cliente.cliente;
    }
    return t.cliente ? (t.cliente.razon_social || t.cliente.cliente) : 'N/A';
};

// Extract available groups from tokens payload
const availableGroups = computed(() => {
    if (!props.tokens || !props.tokens.data) return [];
    const groups = new Set();
    props.tokens.data.forEach(t => {
        const group = getTokenGroup(t);
        if (group && group !== 'Sin Ruta') groups.add(group);
    });
    return Array.from(groups).sort();
});

// Computed list based on group filter
const filteredTokens = computed(() => {
    if (!props.tokens || !props.tokens.data) return [];
    if (!filterGroup.value) return props.tokens.data;
    
    return props.tokens.data.filter(t => getTokenGroup(t) === filterGroup.value);
});

const form = useForm({
    tokens: [],
    title: '',
    body: ''
});

const toggleSelectFiltered = (e) => {
    if (e.target.checked) {
        selectedTokens.value = filteredTokens.value.map(t => t.token);
    } else {
        selectedTokens.value = [];
    }
};

const areFilteredSelected = computed(() => {
    return filteredTokens.value.length > 0 && selectedTokens.value.length === filteredTokens.value.length;
});

const toggleSelect = (tokenStr) => {
    const idx = selectedTokens.value.indexOf(tokenStr);
    if (idx > -1) selectedTokens.value.splice(idx, 1);
    else selectedTokens.value.push(tokenStr);
};

const sendNotification = () => {
    form.tokens = selectedTokens.value;
    form.post(route('notificaciones.send'), {
        preserveScroll: true,
        onSuccess: () => {
            // keep selections if they want to resend, or clear:
            // selectedTokens.value = [];
            form.reset('title', 'body');
        }
    });
};

const formatDateTime = (dateStr) => {
    if (!dateStr) return 'N/A';
    return new Date(dateStr).toLocaleString('es-MX', {
        month: 'short', day: 'numeric',
        year: 'numeric'
    });
};
</script>
