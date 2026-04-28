<template>
    <div class="min-h-screen bg-gray-900 text-gray-100 p-8 font-sans">
        <header class="mb-8 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 to-teal-400">
                    Bolsa de Trabajo
                </h1>
                <p class="text-gray-400 mt-2">Administra las vacantes publicadas en la página pública.</p>
            </div>
            <button @click="openCreateModal" class="bg-teal-600 hover:bg-teal-500 text-white px-5 py-2.5 rounded-lg text-sm font-bold transition shadow-lg flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                Nueva Vacante
            </button>
        </header>

        <main class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 rounded-xl shadow-2xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="uppercase tracking-wider border-b border-gray-700 bg-gray-900/50 text-gray-400">
                        <tr>
                            <th class="p-4 font-medium w-16">Estado</th>
                            <th class="p-4 font-medium">Título de la Vacante</th>
                            <th class="p-4 font-medium">Ubicación</th>
                            <th class="p-4 font-medium text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800">
                        <tr v-for="vacancy in vacancies.data" :key="vacancy.id" class="hover:bg-gray-700/30 transition">
                            <td class="p-4">
                                <span v-if="vacancy.is_active" class="inline-flex w-3 h-3 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.8)]" title="Activa"></span>
                                <span v-else class="inline-flex w-3 h-3 rounded-full bg-red-500" title="Inactiva"></span>
                            </td>
                            <td class="p-4">
                                <p class="font-bold text-gray-100">{{ vacancy.title }}</p>
                                <p class="text-xs text-gray-500 mt-1 max-w-md truncate">{{ vacancy.description }}</p>
                            </td>
                            <td class="p-4 text-gray-300">
                                <div class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    {{ vacancy.location }}
                                </div>
                            </td>
                            <td class="p-4 text-right space-x-2">
                                <button @click="openEditModal(vacancy)" class="bg-blue-600 hover:bg-blue-500 text-white px-3 py-1.5 rounded text-xs font-medium transition">
                                    Editar
                                </button>
                                <button @click="confirmDelete(vacancy.id)" class="bg-red-600 hover:bg-red-500 text-white px-3 py-1.5 rounded text-xs font-medium transition">
                                    Eliminar
                                </button>
                            </td>
                        </tr>
                        <tr v-if="vacancies.data.length === 0">
                            <td colspan="4" class="p-8 text-center text-gray-500 italic">No hay vacantes registradas.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="p-4 border-t border-gray-700 bg-gray-900/50 flex items-center justify-between text-sm text-gray-400">
                <div>Mostrando página {{ vacancies.current_page }} de {{ vacancies.last_page }}</div>
                <div class="flex space-x-1">
                    <component v-for="(link, idx) in vacancies.links" :key="idx" :is="link.url ? Link : 'span'" :href="link.url"
                               :class="['px-3 py-1 rounded transition border', link.active ? 'bg-teal-600 border-teal-500 text-white' : 'bg-gray-800 border-gray-700 hover:bg-gray-700', !link.url && 'opacity-50']"
                               v-html="link.label"></component>
                </div>
            </div>
        </main>

        <!-- Form Modal -->
        <div v-if="isModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
            <div class="bg-gray-800 border border-gray-700 rounded-xl shadow-2xl w-full max-w-2xl overflow-hidden flex flex-col max-h-[90vh]">
                <div class="p-5 border-b border-gray-700 bg-gray-900/50 flex justify-between items-center">
                    <h3 class="text-xl font-bold flex items-center gap-2">
                        <span class="w-2 h-6 rounded bg-teal-500"></span>
                        {{ editMode ? 'Editar Vacante' : 'Nueva Vacante' }}
                    </h3>
                    <button @click="closeModal" class="text-gray-400 hover:text-white transition">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <div class="p-6 overflow-y-auto custom-scrollbar space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-1">Título de la vacante</label>
                        <input v-model="form.title" type="text" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-white focus:ring-teal-500 focus:border-teal-500" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-1">Ubicación</label>
                        <input v-model="form.location" type="text" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-white focus:ring-teal-500 focus:border-teal-500" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-1">Descripción</label>
                        <textarea v-model="form.description" rows="3" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-white focus:ring-teal-500 focus:border-teal-500" required></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-1">Requisitos</label>
                        <textarea v-model="form.requirements" rows="4" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-white focus:ring-teal-500 focus:border-teal-500" required></textarea>
                        <p class="text-xs text-gray-500 mt-1">Usa saltos de línea para separar requisitos.</p>
                    </div>

                    <div class="flex items-center gap-3 pt-2">
                        <input v-model="form.is_active" type="checkbox" id="is_active" class="w-5 h-5 rounded border-gray-600 text-teal-500 focus:ring-teal-500 bg-gray-900">
                        <label for="is_active" class="text-sm font-medium text-gray-300 cursor-pointer">Vacante Activa (Visible al público)</label>
                    </div>
                </div>

                <div class="p-5 border-t border-gray-700 bg-gray-900/50 flex justify-end gap-3">
                    <button @click="closeModal" class="px-5 py-2 rounded-lg text-sm font-medium text-gray-400 hover:text-white hover:bg-gray-700 transition">Cancelar</button>
                    <button @click="submit" :disabled="form.processing" class="px-5 py-2 rounded-lg text-sm font-medium bg-teal-600 hover:bg-teal-500 text-white transition disabled:opacity-50">Guardar Vacante</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { useForm, Link, router } from '@inertiajs/vue3';

const props = defineProps({
    vacancies: Object
});

const isModalOpen = ref(false);
const editMode = ref(false);
const currentId = ref(null);

const form = useForm({
    title: '',
    description: '',
    requirements: '',
    location: '',
    is_active: true,
});

const openCreateModal = () => {
    editMode.value = false;
    currentId.value = null;
    form.reset();
    isModalOpen.value = true;
};

const openEditModal = (vacancy) => {
    editMode.value = true;
    currentId.value = vacancy.id;
    form.title = vacancy.title;
    form.description = vacancy.description;
    form.requirements = vacancy.requirements;
    form.location = vacancy.location;
    form.is_active = Boolean(vacancy.is_active);
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
    form.reset();
};

const submit = () => {
    if (editMode.value) {
        form.put(route('vacantes.update', currentId.value), {
            preserveScroll: true,
            onSuccess: () => closeModal(),
        });
    } else {
        form.post(route('vacantes.store'), {
            preserveScroll: true,
            onSuccess: () => closeModal(),
        });
    }
};

const confirmDelete = (id) => {
    if (confirm('¿Estás seguro de que deseas eliminar esta vacante?')) {
        router.delete(route('vacantes.destroy', id), {
            preserveScroll: true,
        });
    }
};
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { width: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background-color: #4b5563; border-radius: 20px; }
</style>
