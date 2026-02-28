<template>
    <div class="h-screen flex bg-gray-900 text-gray-100 font-sans overflow-hidden">
        
        <!-- Sidebar -->
        <aside class="w-64 bg-gray-800 border-r border-gray-700 flex flex-col h-full z-10">
            <div class="p-5 border-b border-gray-700 bg-gray-900/50">
                <h2 class="text-xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-pink-500">
                    Aliscore DataHub
                </h2>
                <p class="text-xs text-gray-400 mt-1 uppercase tracking-widest font-medium">Solo Lectura & Update</p>
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
        <main class="flex-1 flex flex-col h-full bg-gray-900 relative">
            <div class="absolute inset-0 bg-gradient-to-br from-purple-900/10 to-transparent pointer-events-none"></div>

            <!-- Top Header -->
            <header class="h-16 border-b border-gray-800 bg-gray-900/80 backdrop-blur-md flex items-center justify-between px-6 z-10">
                <div class="flex items-center gap-3">
                    <h1 class="text-lg font-bold">Tabla: <span class="text-purple-400">{{ currentTable }}</span></h1>
                    <span class="bg-gray-800 text-xs px-2 py-1 rounded text-gray-400 border border-gray-700">{{ tableData.total }} registros</span>
                </div>
            </header>

            <!-- Table Area -->
            <div class="flex-1 overflow-auto custom-scrollbar p-6 z-10 block">
                <div class="bg-gray-800/80 backdrop-blur border border-gray-700 rounded-xl overflow-hidden shadow-2xl relative block w-max min-w-full">
                    <table class="w-full text-left text-sm whitespace-nowrap">
                        <thead class="bg-gray-900/60 uppercase tracking-widest text-xs text-gray-400 border-b border-gray-700">
                            <tr>
                                <th class="p-3 font-semibold sticky left-0 bg-gray-900 z-10 border-r border-gray-700 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.5)]">Acciones</th>
                                <th v-for="col in columns" :key="col" class="p-3 font-semibold">{{ col }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-800">
                            <tr v-for="row in tableData.data" :key="JSON.stringify(row)" class="hover:bg-gray-700/40 transition group">
                                <td class="p-3 sticky left-0 bg-gray-800 group-hover:bg-gray-700 border-r border-gray-700 z-10 w-24">
                                    <button @click="editRow(row)" class="flex items-center gap-1 text-xs text-blue-400 hover:text-blue-300 font-medium px-2 py-1 rounded hover:bg-blue-400/10 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                        Editar
                                    </button>
                                </td>
                                <td v-for="col in columns" :key="col" class="p-3 text-gray-300">
                                    <span v-if="row[col] === null" class="text-gray-600 italic">NULL</span>
                                    <span v-else class="truncate max-w-[250px] inline-block" :title="row[col]">{{ formatValue(row[col]) }}</span>
                                </td>
                            </tr>
                            <tr v-if="tableData.data.length === 0">
                                <td :colspan="columns.length + 1" class="p-8 text-center text-gray-500 italic">No hay registros en esta tabla.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div class="h-16 border-t border-gray-800 bg-gray-900/80 backdrop-blur-md flex items-center justify-between px-6 z-10">
                <span class="text-sm text-gray-400">Página {{ tableData.current_page }} de {{ tableData.last_page }}</span>
                <div class="flex space-x-1">
                    <component 
                        v-for="(link, i) in tableData.links" 
                        :key="i" 
                        :is="link.url ? Link : 'span'" 
                        :href="link.url"
                        class="px-3 py-1.5 rounded text-sm transition border"
                        :class="[link.active ? 'bg-purple-600 border-purple-500 text-white' : 'bg-gray-800 border-gray-700 text-gray-400 hover:bg-gray-700', !link.url && 'opacity-50 cursor-not-allowed']"
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
</template>

<script setup>
import { ref, computed } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';

const props = defineProps({
    currentTable: String,
    tables: Array,
    columns: Array,
    tableData: Object,
    primaryKey: String
});

const isEditing = ref(false);
const originalRow = ref(null);

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
