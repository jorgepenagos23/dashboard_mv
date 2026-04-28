<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-4">
                <img src="/images/logo.png" alt="Company Logo" class="h-10 w-auto object-contain bg-gray-800 p-1 rounded" onerror="this.src='https://via.placeholder.com/150x50?text=LOGO'" />
                <div>
                    <h1 class="text-3xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-purple-500 to-pink-600">
                        Aliscore DataHub
                    </h1>
                    <p class="text-xs text-gray-500 mt-1 uppercase tracking-widest font-medium">Solo Lectura & Update</p>
                </div>
            </div>
        </template>

        <div class="h-screen flex bg-gray-50 text-gray-800 font-sans overflow-hidden relative">
            
            <!-- Mobile Sidebar Overlay -->
            <div v-if="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-black/50 z-20 md:hidden"></div>
            
            <!-- Sidebar -->
            <aside :class="['w-64 bg-white border-r border-gray-200 flex flex-col h-full z-30 shadow-sm absolute md:relative transition-transform duration-300', sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0']">
                <div class="p-4 border-b border-gray-200 bg-gray-50/50 flex justify-between items-center">
                    <h2 class="text-lg font-bold text-gray-700">Tablas</h2>
                    <button @click="sidebarOpen = false" class="md:hidden text-gray-500 hover:text-gray-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                <div class="flex-1 overflow-y-auto custom-scrollbar p-3 space-y-1">
                <Link 
                    v-for="table in tables" 
                    :key="table" 
                    :href="route('explorer.index', table)"
                    class="block px-3 py-2 rounded-lg text-sm transition font-medium"
                    :class="[
                        currentTable === table 
                            ? 'bg-purple-600/20 text-purple-400 border border-purple-500/30' 
                            : 'text-gray-400 hover:bg-gray-700/50 hover:text-gray-200'
                    ]"
                >
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                        {{ table }}
                    </div>
                </Link>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 flex flex-col h-full relative min-w-0">

            <!-- Top Header -->
            <header class="min-h-16 py-3 border-b border-gray-200 bg-white/80 backdrop-blur-md flex flex-col sm:flex-row items-start sm:items-center justify-between px-6 z-10 shadow-sm gap-4">
                <div class="flex items-center gap-3 w-full sm:w-auto">
                    <button @click="sidebarOpen = true" class="md:hidden text-gray-600 hover:text-gray-800 mr-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                    <h1 class="text-lg font-bold text-gray-800 truncate">Tabla: <span class="text-purple-600">{{ currentTable }}</span></h1>
                    <span class="bg-gray-100 text-xs px-2 py-1 rounded text-gray-600 border border-gray-300 whitespace-nowrap hidden sm:inline-block">{{ tableData.total }} registros</span>
                </div>

                <div class="flex w-full sm:w-auto items-center gap-2">
                    <div class="relative w-full sm:w-64 group">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-gray-400 group-focus-within:text-purple-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input
                            v-model="search"
                            type="text"
                            placeholder="Buscar en la tabla..."
                            class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-purple-500 focus:border-purple-500 sm:text-sm transition duration-150 ease-in-out"
                        />
                        <button v-if="search" @click="search = ''" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                </div>
            </header>

            <!-- Table Area -->
            <div class="flex-1 overflow-auto custom-scrollbar p-6 z-10 block bg-gray-50">
                <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-md relative block w-max min-w-full">
                    <table class="w-full text-left text-sm whitespace-nowrap text-gray-700">
                        <thead class="bg-gray-100/60 uppercase tracking-widest text-xs text-gray-500 border-b border-gray-200">
                            <tr>
                                <th class="p-3 font-semibold sticky left-0 bg-gray-100 z-10 border-r border-gray-200 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.1)]">Acciones</th>
                                <th v-for="col in columns" :key="col" class="p-3 font-semibold">{{ col }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr v-for="row in tableData.data" :key="JSON.stringify(row)" class="hover:bg-gray-50 transition group">
                                <td class="p-3 sticky left-0 bg-white group-hover:bg-gray-50 border-r border-gray-200 z-10 w-24">
                                    <button v-if="$page.props.auth.user.is_admin" @click="editRow(row)" class="flex items-center gap-1 text-xs text-blue-600 hover:text-blue-500 font-medium px-2 py-1 rounded hover:bg-blue-50 transition border border-transparent hover:border-blue-100">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                        Editar
                                    </button>
                                </td>
                                <td v-for="col in columns" :key="col" class="p-3">
                                    <span v-if="row[col] === null" class="text-gray-400 italic">NULL</span>
                                    <span v-else class="truncate max-w-[250px] inline-block font-mono text-[13px]" :title="row[col]">{{ formatValue(row[col]) }}</span>
                                </td>
                            </tr>
                            <tr v-if="tableData.data.length === 0">
                                <td :colspan="columns.length + 1" class="p-8 text-center text-gray-500 italic bg-white">No hay registros en esta tabla.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div class="border-t border-gray-200 bg-white/80 backdrop-blur-md px-6 py-4 z-10 flex flex-col sm:flex-row items-center justify-between gap-4">
                <span class="text-sm text-gray-500 w-full sm:w-auto text-center sm:text-left">
                    Mostrando del <span class="font-medium text-gray-900">{{ tableData.from || 0 }}</span> al
                    <span class="font-medium text-gray-900">{{ tableData.to || 0 }}</span> de
                    <span class="font-medium text-gray-900">{{ tableData.total }}</span> registros
                </span>
                <div class="flex flex-wrap justify-center sm:justify-end gap-1">
                    <component 
                        v-for="(link, i) in tableData.links" 
                        :key="i" 
                        :is="link.url ? Link : 'span'" 
                        :href="link.url"
                        class="px-3 py-1.5 rounded mb-1 sm:mb-0 text-sm transition border"
                        :class="[link.active ? 'bg-purple-600 border-purple-500 text-white' : 'bg-white border-gray-200 text-gray-600 hover:bg-gray-50', !link.url && 'opacity-50 cursor-not-allowed']"
                        v-html="link.label">
                    </component>
                </div>
            </div>
        </main>

        <!-- Edit Modal Overlay -->
        <div v-if="isEditing" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
            <div class="bg-gray-800 border border-gray-700 rounded-xl shadow-2xl w-full max-w-3xl max-h-[90vh] flex flex-col overflow-hidden">
                <div class="p-5 border-b border-gray-700 flex justify-between items-center bg-gray-900/50">
                    <h3 class="text-lg font-bold flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                        Editando registro en <span class="text-purple-400">{{ currentTable }}</span>
                    </h3>
                    <button @click="closeEdit" class="text-gray-400 hover:text-white transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <div class="p-6 overflow-y-auto flex-1 custom-scrollbar">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div v-for="col in columns" :key="col" class="space-y-1">
                            <label class="block text-xs font-medium text-gray-400 uppercase tracking-wide">
                                {{ col }} <span v-if="col === primaryKey" class="text-red-400 ml-1" title="Primary Key">*PK</span>
                            </label>
                            <!-- PK should be readonly for safety usually, but we allow if needed. We'll add visual cue -->
                            <input 
                                v-model="form[col]" 
                                type="text"
                                :class="[
                                    'w-full bg-gray-900/50 border rounded-lg px-3 py-2 text-sm text-gray-200 transition focus:outline-none focus:ring-2 focus:ring-purple-500/50',
                                    col === primaryKey ? 'border-red-900/30 bg-gray-900/20 text-gray-500 cursor-not-allowed' : 'border-gray-700 focus:border-purple-500'
                                ]"
                                :disabled="col === primaryKey" 
                            />
                        </div>
                    </div>
                </div>

                <div class="p-5 border-t border-gray-700 bg-gray-900/50 flex justify-end gap-3">
                    <button @click="closeEdit" class="px-4 py-2 rounded-lg text-sm font-medium text-gray-400 hover:text-white hover:bg-gray-700 transition">
                        Cancelar
                    </button>
                    <button @click="saveChanges" :disabled="formRef.processing" class="px-5 py-2 rounded-lg text-sm font-medium bg-blue-600 hover:bg-blue-500 text-white transition focus:ring-2 focus:ring-blue-500/50 disabled:opacity-50">
                        Guardar Cambios
                    </button>
                </div>
            </div>
        </div>

        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { useForm, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const debounce = (fn, delay) => {
    let timeoutId;
    return (...args) => {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => fn(...args), delay);
    };
};

const props = defineProps({
    currentTable: String,
    tables: Array,
    columns: Array,
    tableData: Object,
    primaryKey: String,
    filters: Object,
});

const isEditing = ref(false);
const originalRow = ref(null);
const sidebarOpen = ref(false);

const search = ref(props.filters?.search || '');

watch(search, debounce(function (value) {
    router.get(
        route('explorer.index', props.currentTable),
        { search: value },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        }
    );
}, 300));

// Form handling
const form = ref({});
const formRef = useForm({
    original: {},
    changes: {}
});

const formatValue = (val) => {
    if (typeof val === 'string' && val.length > 50) return val.substring(0, 50) + '...';
    return val;
};

const editRow = (row) => {
    // Deep clone to separate states
    originalRow.value = JSON.parse(JSON.stringify(row));
    // Populate form inputs
    const newForm = {};
    props.columns.forEach(col => {
        newForm[col] = row[col];
    });
    form.value = newForm;
    isEditing.value = true;
};

const closeEdit = () => {
    isEditing.value = false;
    form.value = {};
    originalRow.value = null;
};

const saveChanges = () => {
    // Determine what changed
    const changes = {};
    props.columns.forEach(col => {
        if (form.value[col] !== originalRow.value[col]) {
            changes[col] = form.value[col] === '' && originalRow.value[col] === null ? null : form.value[col];
        }
    });

    if (Object.keys(changes).length === 0) {
        alert("No se detectaron cambios.");
        closeEdit();
        return;
    }

    formRef.original = originalRow.value;
    formRef.changes = changes;

    formRef.post(route('explorer.update', props.currentTable), {
        preserveScroll: true,
        onSuccess: () => {
            alert("¡Actualizado exitosamente!");
            closeEdit();
        },
        onError: (err) => {
            alert("Error al actualizar: " + JSON.stringify(err));
        }
    });
};
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background-color: #4b5563; border-radius: 20px; }
.custom-scrollbar::-webkit-scrollbar-corner { background: transparent; }
</style>
