<script setup>
import { ref, onMounted } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link, usePage } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);
const showAppearanceSettings = ref(false);

const page = usePage();
import { useTheme } from '@/Composables/useTheme.js';

const { isDark, primaryColor, secondaryColor, textColor, initTheme, setTheme, updateCssVariables } = useTheme();

onMounted(() => {
    initTheme();
});

const goBack = () => {
    if (window.history.length > 2) {
        window.history.back();
    } else {
        // Fallback si no hay historial previo interno
        window.location.href = route('dashboard');
    }
};
</script>

<template>
    <div>
        <div class="min-h-screen bg-gray-100">
            <nav
                class="border-b border-gray-100 bg-white"
            >
                <!-- Primary Navigation Menu -->
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex h-16 justify-between">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="flex shrink-0 items-center">
                                <Link :href="route('dashboard')">
                                    <ApplicationLogo
                                        class="block h-9 w-auto fill-current text-gray-800"
                                    />
                                </Link>
                            </div>

                            <!-- Navigation Links -->
                            <div
                                class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex"
                            >
                                <NavLink
                                    :href="route('dashboard')"
                                    :active="route().current('dashboard')"
                                >
                                    Dashboard
                                </NavLink>
                                <NavLink :href="route('promociones.index')" :active="route().current('promociones.*')">
                                    Promociones
                                </NavLink>
                                <NavLink :href="route('explorer.index')" :active="route().current('explorer.*')">
                                    DataHub
                                </NavLink>
                                <NavLink :href="route('pedidos.index')" :active="route().current('pedidos.*')">
                                    Pedidos
                                </NavLink>
                                <NavLink :href="route('clientes.index')" :active="route().current('clientes.*')">
                                    Clientes
                                </NavLink>
                                <NavLink :href="route('notificaciones.index')" :active="route().current('notificaciones.*')">
                                    Notificaciones
                                </NavLink>
                                <NavLink :href="route('inventario.index')" :active="route().current('inventario.*')">
                                    Inventario
                                </NavLink>
                            </div>
                        </div>

                        <div class="hidden sm:ms-6 sm:flex sm:items-center">
                            <!-- Settings Dropdown -->
                            <div class="relative ms-3">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button
                                                type="button"
                                                class="inline-flex items-center rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out hover:text-gray-700 focus:outline-none"
                                            >
                                                {{ $page.props.auth.user.name }}

                                                <svg
                                                    class="-me-0.5 ms-2 h-4 w-4"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <DropdownLink
                                            :href="route('profile.edit')"
                                        >
                                            Profile
                                        </DropdownLink>
                                        <button
                                            @click="showAppearanceSettings = true"
                                            class="block w-full px-4 py-2 text-start text-sm leading-5 text-gray-700 transition duration-150 ease-in-out hover:bg-gray-100 focus:bg-gray-100 focus:outline-none"
                                        >
                                            🎨 Apariencia UI
                                        </button>
                                        <DropdownLink
                                            :href="route('logout')"
                                            method="post"
                                            as="button"
                                        >
                                            Log Out
                                        </DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>

                        <!-- Hamburger -->
                        <div class="-me-2 flex items-center sm:hidden">
                            <button
                                @click="
                                    showingNavigationDropdown =
                                        !showingNavigationDropdown
                                "
                                class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 transition duration-150 ease-in-out hover:bg-gray-100 hover:text-gray-500 focus:bg-gray-100 focus:text-gray-500 focus:outline-none"
                            >
                                <svg
                                    class="h-6 w-6"
                                    stroke="currentColor"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        :class="{
                                            hidden: showingNavigationDropdown,
                                            'inline-flex':
                                                !showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        :class="{
                                            hidden: !showingNavigationDropdown,
                                            'inline-flex':
                                                showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div
                    :class="{
                        block: showingNavigationDropdown,
                        hidden: !showingNavigationDropdown,
                    }"
                    class="sm:hidden"
                >
                    <div class="space-y-1 pb-3 pt-2">
                        <ResponsiveNavLink
                            :href="route('dashboard')"
                            :active="route().current('dashboard')"
                        >
                            Dashboard
                        </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('promociones.index')" :active="route().current('promociones.*')">
                            Promociones
                        </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('explorer.index')" :active="route().current('explorer.*')">
                            DataHub
                        </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('pedidos.index')" :active="route().current('pedidos.*')">
                            Pedidos
                        </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('clientes.index')" :active="route().current('clientes.*')">
                            Clientes
                        </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('notificaciones.index')" :active="route().current('notificaciones.*')">
                            Notificaciones
                        </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('inventario.index')" :active="route().current('inventario.*')">
                            Inventario
                        </ResponsiveNavLink>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div
                        class="border-t border-gray-200 pb-1 pt-4"
                    >
                        <div class="px-4">
                            <div
                                class="text-base font-medium text-gray-800"
                            >
                                {{ $page.props.auth.user.name }}
                            </div>
                            <div class="text-sm font-medium text-gray-500">
                                {{ $page.props.auth.user.email }}
                            </div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink :href="route('profile.edit')">
                                Profile
                            </ResponsiveNavLink>
                            <ResponsiveNavLink
                                :href="route('logout')"
                                method="post"
                                as="button"
                            >
                                Log Out
                            </ResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header
                class="bg-white shadow"
                v-if="$slots.header"
            >
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8 flex items-center justify-between">
                    <slot name="header" />
                    
                    <button 
                        v-if="route().current() !== 'dashboard'"
                        @click="goBack" 
                        class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 active:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        Regresar
                    </button>
                </div>
            </header>

            <!-- Page Content -->
            <main :style="{ backgroundColor: 'var(--app-bg)', color: 'var(--app-text)' }" class="min-h-screen">
                <slot />
            </main>
        </div>

        <!-- Appearance Settings Modal -->
        <div v-if="showAppearanceSettings" class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto overflow-x-hidden bg-black/60 backdrop-blur-sm">
            <div class="relative w-full max-w-md p-4 animate-fade-in-up">
                <div class="relative bg-white rounded-2xl shadow-2xl border border-gray-200 overflow-hidden text-gray-900">
                    <div class="flex items-center justify-between p-5 border-b border-gray-100 bg-gray-50">
                        <h3 class="text-lg font-bold">
                            Personalizar Apariencia UI
                        </h3>
                        <button @click="showAppearanceSettings = false" class="text-gray-400 hover:text-gray-600 rounded-lg text-sm p-2 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                    
                    <div class="p-6 space-y-6">
                        <div>
                            <h4 class="text-sm font-bold text-gray-700 mb-3 uppercase tracking-wider">Temas Prediseñados</h4>
                            <div class="grid grid-cols-3 gap-3">
                                <button @click="setTheme('dark')" class="p-2 border rounded-lg hover:border-indigo-500 hover:shadow transition text-center focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                                    <div class="w-full h-8 bg-gray-900 rounded mb-1 border border-gray-700"></div>
                                    <span class="text-xs font-medium">Oscuro</span>
                                </button>
                                <button @click="setTheme('light')" class="p-2 border rounded-lg hover:border-indigo-500 hover:shadow transition text-center focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                                    <div class="w-full h-8 bg-gray-100 rounded mb-1 border border-gray-300"></div>
                                    <span class="text-xs font-medium">Claro</span>
                                </button>
                                <button @click="setTheme('blue')" class="p-2 border rounded-lg hover:border-indigo-500 hover:shadow transition text-center focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                                    <div class="w-full h-8 bg-slate-900 rounded mb-1 border border-slate-700"></div>
                                    <span class="text-xs font-medium">Azul System</span>
                                </button>
                            </div>
                        </div>

                        <div class="border-t border-gray-100 pt-5 space-y-4">
                            <h4 class="text-sm font-bold text-gray-700 uppercase tracking-wider">Ajuste Manual de Colores</h4>
                            
                            <div class="flex items-center justify-between">
                                <label class="text-sm font-medium text-gray-600">Color de Fondo General</label>
                                <input type="color" v-model="primaryColor" @input="updateCssVariables" class="w-10 h-10 rounded border border-gray-300 cursor-pointer p-0.5">
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <label class="text-sm font-medium text-gray-600">Color de Tarjetas / Superficies</label>
                                <input type="color" v-model="secondaryColor" @input="updateCssVariables" class="w-10 h-10 rounded border border-gray-300 cursor-pointer p-0.5">
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <label class="text-sm font-medium text-gray-600">Color de Texto (Contraste)</label>
                                <input type="color" v-model="textColor" @input="updateCssVariables" class="w-10 h-10 rounded border border-gray-300 cursor-pointer p-0.5">
                            </div>
                        </div>
                    </div>

                    <div class="p-4 border-t border-gray-100 bg-gray-50 flex justify-end">
                        <button @click="showAppearanceSettings = false" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-md text-sm font-medium transition shadow-sm">
                            Guardar Apariencia
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
