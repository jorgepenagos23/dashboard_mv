<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-4">
                <img src="/images/logo.png" alt="Company Logo" class="h-10 w-auto object-contain bg-gray-800 p-1 rounded" onerror="this.src='https://via.placeholder.com/150x50?text=LOGO'" />
                <div>
                    <h1 class="text-3xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-orange-500 to-pink-600">
                        Inventario y Precios
                    </h1>
                    <p class="text-gray-500 mt-1">Monitorea el nivel de existencias por almacén y las listas de precios actuales.</p>
                </div>
            </div>
        </template>

        <div class="min-h-screen bg-gray-50 text-gray-800 p-8 font-sans">
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
                    <button v-if="$page.props.auth.user.is_admin" @click="openCreateProductModal" class="px-4 py-2 bg-green-600 hover:bg-green-500 text-white rounded-lg text-sm font-medium transition cursor-pointer">
                        + Crear Producto
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
                                    <div class="flex flex-col sm:flex-row justify-center gap-2">
                                        <button @click="openManageStockModal(producto)" 
                                                class="bg-orange-600 hover:bg-orange-500 text-white px-3 py-1.5 rounded-lg text-sm font-medium transition cursor-pointer">
                                            ⚙ Stock
                                        </button>
                                        <button v-if="$page.props.auth.user.is_admin" @click="openEditProductModal(producto)" 
                                                class="bg-blue-600 hover:bg-blue-500 text-white px-3 py-1.5 rounded-lg text-sm font-medium transition cursor-pointer">
                                            ✎ Editar
                                        </button>
                                        <button v-if="$page.props.auth.user.is_admin" @click="openEditPriceModal(producto)" 
                                                class="bg-pink-600 hover:bg-pink-500 text-white px-3 py-1.5 rounded-lg text-sm font-medium transition cursor-pointer">
                                            ✎ Precios
                                        </button>
                                        <button v-if="$page.props.auth.user.email === 'jorgepenagos50@gmail.com'" @click="confirmDelete(producto.id)" 
                                                class="bg-red-600 hover:bg-red-500 text-white px-3 py-1.5 rounded-lg text-sm font-medium transition cursor-pointer">
                                            🗑️
                                        </button>
                                    </div>
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

        <!-- Manage Stock Modal -->
        <div v-if="isStockModalOpen" class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto overflow-x-hidden bg-black/60 backdrop-blur-sm">
            <div class="relative w-full max-w-lg p-4">
                <div class="relative bg-gray-900 rounded-2xl shadow-2xl border border-gray-700 overflow-hidden">
                    <!-- Header -->
                    <div class="flex items-center justify-between p-5 border-b border-gray-800 bg-gray-800/50">
                        <h3 class="text-xl font-bold text-gray-100">
                            Gestionar Stock
                        </h3>
                        <button @click="closeManageStockModal" class="text-gray-400 hover:text-white bg-gray-800 hover:bg-gray-700 rounded-lg text-sm p-2 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                    
                    <!-- Body -->
                    <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto">
                        <div v-if="selectedProduct" class="bg-gray-800/50 p-3 rounded-lg border border-gray-700">
                            <p class="text-sm text-gray-400">Producto:</p>
                            <p class="font-bold text-gray-200">{{ selectedProduct.nombre }} <span class="text-xs text-orange-400 ml-1">({{ selectedProduct.sku }})</span></p>
                        </div>

                        <form @submit.prevent="submitManageStock" class="space-y-4">
                            <div v-if="stockForm.stocks.length > 0" class="space-y-3">
                                <h4 class="text-sm font-medium text-gray-400">Ubicaciones Actuales</h4>
                                <div v-for="(stockItem, idx) in stockForm.stocks" :key="idx" class="flex gap-3 items-end">
                                    <div class="flex-1">
                                        <label class="block mb-1 text-xs font-medium text-gray-400">Almacén (Lugar)</label>
                                        <input v-model="stockForm.stocks[idx].lugar" type="text" class="bg-gray-800 border border-gray-700 text-white text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2" placeholder="Ej. PDTG" required>
                                    </div>
                                    <div class="w-1/3">
                                        <label class="block mb-1 text-xs font-medium text-gray-400">Cantidad Total</label>
                                        <input v-model="stockForm.stocks[idx].cantidad" type="number" min="0" class="bg-gray-800 border border-gray-700 text-white text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 block w-full p-2" required>
                                    </div>
                                    <button type="button" @click="removeStockRow(idx)" class="mb-0.5 p-2 bg-red-600/20 hover:bg-red-600/40 text-red-500 rounded-lg transition" title="Quitar de esta vista (para conservar 0, simplemente digite 0)">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </div>
                            </div>
                            <div v-else class="text-sm text-gray-500 italic">No hay stock registrado.</div>

                            <button type="button" @click="addNewStockRow" class="text-sm text-orange-400 hover:text-orange-300 font-medium flex items-center gap-1 transition">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                Agregar nueva ubicación
                            </button>
                            
                            <div class="flex justify-end gap-3 pt-4 border-t border-gray-800">
                                <button type="button" @click="closeManageStockModal" class="px-4 py-2 text-sm font-medium text-gray-300 bg-gray-800 border border-gray-700 rounded-lg hover:bg-gray-700 hover:text-white transition">Cancelar</button>
                                <button type="submit" :disabled="stockForm.processing" class="px-4 py-2 text-sm font-medium text-white bg-orange-600 rounded-lg hover:bg-orange-500 focus:ring-4 focus:ring-orange-500/50 transition disabled:opacity-50">
                                    Guardar Cambios
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Prices Modal -->
        <div v-if="isPriceModalOpen" class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto overflow-x-hidden bg-black/60 backdrop-blur-sm">
            <div class="relative w-full max-w-md p-4">
                <div class="relative bg-gray-900 rounded-2xl shadow-2xl border border-gray-700 overflow-hidden">
                    <!-- Header -->
                    <div class="flex items-center justify-between p-5 border-b border-gray-800 bg-gray-800/50">
                        <h3 class="text-xl font-bold text-gray-100">
                            Editar Precios
                        </h3>
                        <button @click="closeEditPriceModal" class="text-gray-400 hover:text-white bg-gray-800 hover:bg-gray-700 rounded-lg text-sm p-2 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                    
                    <!-- Body -->
                    <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto">
                        <div v-if="selectedProductForPrice" class="bg-gray-800/50 p-3 rounded-lg border border-gray-700">
                            <p class="text-sm text-gray-400">Producto:</p>
                            <p class="font-bold text-gray-200">{{ selectedProductForPrice.nombre }} <span class="text-xs text-orange-400 ml-1">({{ selectedProductForPrice.sku }})</span></p>
                        </div>

                        <form @submit.prevent="submitEditPrice" class="space-y-4">
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-300">Precio Base (Precio 1)</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">$</span>
                                    </div>
                                    <input v-model="priceForm.precio_1" type="number" step="0.01" min="0" class="pl-7 bg-gray-800 border border-gray-700 text-white text-sm rounded-lg focus:ring-pink-500 focus:border-pink-500 block w-full p-2.5" required>
                                </div>
                                <p v-if="priceForm.errors.precio_1" class="mt-2 text-sm text-red-500">{{ priceForm.errors.precio_1 }}</p>
                            </div>
                            
                            <div v-if="priceForm.extra_prices.length > 0" class="pt-2 border-t border-gray-800">
                                <h4 class="text-sm font-medium text-gray-400 mb-3">Listas de Precios Adicionales</h4>
                                <div v-for="(extra, index) in priceForm.extra_prices" :key="index" class="mb-3">
                                    <label class="block mb-1 text-xs font-medium text-gray-400">Lista {{ extra.price_list_id }}</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-xs">$</span>
                                        </div>
                                        <input v-model="priceForm.extra_prices[index].price" type="number" step="0.01" min="0" class="pl-7 bg-gray-800 border border-gray-700 text-white text-sm rounded-lg focus:ring-pink-500 focus:border-pink-500 block w-full p-2.5" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex justify-end gap-3 pt-4 border-t border-gray-800">
                                <button type="button" @click="closeEditPriceModal" class="px-4 py-2 text-sm font-medium text-gray-300 bg-gray-800 border border-gray-700 rounded-lg hover:bg-gray-700 hover:text-white transition">Cancelar</button>
                                <button type="submit" :disabled="priceForm.processing" class="px-4 py-2 text-sm font-medium text-white bg-pink-600 rounded-lg hover:bg-pink-500 focus:ring-4 focus:ring-pink-500/50 transition disabled:opacity-50">
                                    Guardar Precios
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Product Details Modal -->
        <div v-if="isEditProductModalOpen" class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto overflow-x-hidden bg-black/60 backdrop-blur-sm">
            <div class="relative w-full max-w-md p-4">
                <div class="relative bg-gray-900 rounded-2xl shadow-2xl border border-gray-700 overflow-hidden">
                    <!-- Header -->
                    <div class="flex items-center justify-between p-5 border-b border-gray-800 bg-gray-800/50">
                        <h3 class="text-xl font-bold text-gray-100">
                            Editar Producto
                        </h3>
                        <button @click="closeEditProductModal" class="text-gray-400 hover:text-white bg-gray-800 hover:bg-gray-700 rounded-lg text-sm p-2 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                    
                    <!-- Body -->
                    <div class="p-6 space-y-4">
                        <form @submit.prevent="submitEditProduct" class="space-y-4">
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-300">Nombre / Descripción</label>
                                <input v-model="editProductForm.nombre" type="text" class="bg-gray-800 border border-gray-700 text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                                <p v-if="editProductForm.errors.nombre" class="mt-2 text-sm text-red-500">{{ editProductForm.errors.nombre }}</p>
                            </div>
                            
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-300">Identificador SKU</label>
                                <input v-model="editProductForm.sku" type="text" class="bg-gray-800 border border-gray-700 text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                                <p v-if="editProductForm.errors.sku" class="mt-2 text-sm text-red-500">{{ editProductForm.errors.sku }}</p>
                            </div>

                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-300">URL de Imagen (Opcional)</label>
                                <input v-model="editProductForm.foto" type="url" placeholder="https://..." class="bg-gray-800 border border-gray-700 text-white text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                <p v-if="editProductForm.errors.foto" class="mt-2 text-sm text-red-500">{{ editProductForm.errors.foto }}</p>
                            </div>
                            
                            <div class="flex justify-end gap-3 pt-4 border-t border-gray-800">
                                <button type="button" @click="closeEditProductModal" class="px-4 py-2 text-sm font-medium text-gray-300 bg-gray-800 border border-gray-700 rounded-lg hover:bg-gray-700 hover:text-white transition">Cancelar</button>
                                <button type="submit" :disabled="editProductForm.processing" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-500 focus:ring-4 focus:ring-blue-500/50 transition disabled:opacity-50">
                                    Guardar Cambios
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create Product Modal -->
        <div v-if="isCreateProductModalOpen" class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto overflow-x-hidden bg-black/60 backdrop-blur-sm">
            <div class="relative w-full max-w-4xl p-4">
                <div class="relative bg-gray-900 rounded-2xl shadow-2xl border border-gray-700 overflow-hidden">
                    <!-- Header -->
                    <div class="flex items-center justify-between p-5 border-b border-gray-800 bg-gray-800/50">
                        <h3 class="text-xl font-bold text-gray-100">
                            Crear Nuevo Producto
                        </h3>
                        <button @click="closeCreateProductModal" class="text-gray-400 hover:text-white bg-gray-800 hover:bg-gray-700 rounded-lg text-sm p-2 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                    
                    <!-- Body -->
                    <div class="p-6 max-h-[75vh] overflow-y-auto custom-scrollbar">
                        <form @submit.prevent="submitCreateProduct" class="space-y-6">
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                <!-- Col 1: Información Principal -->
                                <div class="space-y-4 lg:col-span-3">
                                    <h4 class="text-md font-semibold text-teal-400 border-b border-gray-800 pb-2">Información Principal</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div class="md:col-span-2">
                                            <label class="block mb-2 text-sm font-medium text-gray-300">Nombre / Descripción <span class="text-red-500">*</span></label>
                                            <input v-model="createProductForm.nombre" type="text" class="bg-gray-800 border border-gray-700 text-white text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5" required>
                                            <p v-if="createProductForm.errors.nombre" class="mt-2 text-sm text-red-500">{{ createProductForm.errors.nombre }}</p>
                                        </div>
                                        <div>
                                            <label class="block mb-2 text-sm font-medium text-gray-300">SKU <span class="text-red-500">*</span></label>
                                            <input v-model="createProductForm.sku" type="text" class="bg-gray-800 border border-gray-700 text-white text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5" required>
                                            <p v-if="createProductForm.errors.sku" class="mt-2 text-sm text-red-500">{{ createProductForm.errors.sku }}</p>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div>
                                            <label class="block mb-2 text-sm font-medium text-gray-300">Nombre Corto</label>
                                            <input v-model="createProductForm.nombre_corto" type="text" class="bg-gray-800 border border-gray-700 text-white text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5">
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="block mb-2 text-sm font-medium text-gray-300">URL de Imagen</label>
                                            <input v-model="createProductForm.foto" type="url" placeholder="https://..." class="bg-gray-800 border border-gray-700 text-white text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5">
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Col 2: Clasificación -->
                                <div class="space-y-4">
                                    <h4 class="text-md font-semibold text-teal-400 border-b border-gray-800 pb-2">Clasificación</h4>
                                    <div>
                                        <label class="block mb-2 text-sm font-medium text-gray-300">Línea</label>
                                        <select v-model="createProductForm.linea_prod_id" class="bg-gray-800 border border-gray-700 text-white text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5">
                                            <option value="">Ninguna</option>
                                            <option v-for="linea in lineas" :key="linea" :value="linea">{{ linea }}</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block mb-2 text-sm font-medium text-gray-300">Sublínea</label>
                                        <input v-model="createProductForm.sublinea_prod_id" type="text" class="bg-gray-800 border border-gray-700 text-white text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5">
                                    </div>
                                    <div>
                                        <label class="block mb-2 text-sm font-medium text-gray-300">Marca</label>
                                        <input v-model="createProductForm.marca_prod_id" type="text" class="bg-gray-800 border border-gray-700 text-white text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5">
                                    </div>
                                    <div>
                                        <label class="block mb-2 text-sm font-medium text-gray-300">Multimarca</label>
                                        <input v-model="createProductForm.MULTIMARCA" type="text" class="bg-gray-800 border border-gray-700 text-white text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5">
                                    </div>
                                </div>

                                <!-- Col 3: Unidades y Presentación -->
                                <div class="space-y-4">
                                    <h4 class="text-md font-semibold text-teal-400 border-b border-gray-800 pb-2">Unidades y Stock</h4>
                                    <div>
                                        <label class="block mb-2 text-sm font-medium text-gray-300">Unidad de Medida (PROUND)</label>
                                        <input v-model="createProductForm.pround" type="text" placeholder="PZA, CJA, KG..." class="bg-gray-800 border border-gray-700 text-white text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5">
                                    </div>
                                    <div>
                                        <label class="block mb-2 text-sm font-medium text-gray-300">Presentación</label>
                                        <input v-model="createProductForm.presentacion_prod_id" type="text" class="bg-gray-800 border border-gray-700 text-white text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5">
                                    </div>
                                    <div>
                                        <label class="block mb-2 text-sm font-medium text-gray-300">Factor de Conversión</label>
                                        <input v-model="createProductForm.PRODUC_FACTCONVER3" type="number" step="0.01" class="bg-gray-800 border border-gray-700 text-white text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5">
                                    </div>
                                    <div>
                                        <label class="block mb-2 text-sm font-medium text-gray-300">Pzas. Mínimas Venta</label>
                                        <input v-model="createProductForm.PRODUC_PZAMINVTA" type="number" min="1" class="bg-gray-800 border border-gray-700 text-white text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5">
                                    </div>
                                </div>

                                <!-- Col 4: Precios e Impuestos -->
                                <div class="space-y-4">
                                    <h4 class="text-md font-semibold text-teal-400 border-b border-gray-800 pb-2">Precios e Impuestos</h4>
                                    <div>
                                        <label class="block mb-2 text-sm font-medium text-gray-300">Precio Base 1 <span class="text-red-500">*</span></label>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <span class="text-gray-500 sm:text-sm">$</span>
                                            </div>
                                            <input v-model="createProductForm.precio_1" type="number" step="0.01" min="0" class="pl-7 bg-gray-800 border border-gray-700 text-white text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5" required>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block mb-2 text-sm font-medium text-gray-300">VTA_PRECIO (Opcional)</label>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <span class="text-gray-500 sm:text-sm">$</span>
                                            </div>
                                            <input v-model="createProductForm.VTAPRE_PRECIO" type="number" step="0.01" min="0" class="pl-7 bg-gray-800 border border-gray-700 text-white text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5">
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <label class="block mb-2 text-xs font-medium text-gray-300">IVA (16%)</label>
                                            <input v-model="createProductForm.iva16" type="number" step="0.01" placeholder="0.16" class="bg-gray-800 border border-gray-700 text-white text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5">
                                        </div>
                                        <div>
                                            <label class="block mb-2 text-xs font-medium text-gray-300">IEPS</label>
                                            <input v-model="createProductForm.ieps" type="number" step="0.01" placeholder="0.00" class="bg-gray-800 border border-gray-700 text-white text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5">
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <label class="block mb-2 text-xs font-medium text-gray-300">Vigencia</label>
                                            <input v-model="createProductForm.vigencia" type="date" class="bg-gray-800 border border-gray-700 text-white text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5">
                                        </div>
                                        <div>
                                            <label class="block mb-2 text-xs font-medium text-gray-300">AAI</label>
                                            <input v-model="createProductForm.AAI" type="text" placeholder="0.00" class="bg-gray-800 border border-gray-700 text-white text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="lg:col-span-3">
                                    <label class="block mb-2 text-sm font-medium text-gray-300">Descripción Proveedor (Opcional)</label>
                                    <textarea v-model="createProductForm.proveedor_desc1" rows="2" class="bg-gray-800 border border-gray-700 text-white text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5"></textarea>
                                </div>
                            </div>

                            <div class="flex justify-end gap-3 pt-4 border-t border-gray-800">
                                <button type="button" @click="closeCreateProductModal" class="px-4 py-2 text-sm font-medium text-gray-300 bg-gray-800 border border-gray-700 rounded-lg hover:bg-gray-700 hover:text-white transition">Cancelar</button>
                                <button type="submit" :disabled="createProductForm.processing" class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-500 focus:ring-4 focus:ring-green-500/50 transition disabled:opacity-50">
                                    Crear Producto
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

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

const isStockModalOpen = ref(false);
const selectedProduct = ref(null);

const stockForm = useForm({
    product_id: '',
    stocks: []
});

const openManageStockModal = (producto) => {
    selectedProduct.value = producto;
    stockForm.product_id = producto.id;
    stockForm.stocks = producto.stocks ? producto.stocks.map(s => ({
        lugar: s.lugar,
        cantidad: s.cantidad
    })) : [];
    isStockModalOpen.value = true;
};

const closeManageStockModal = () => {
    isStockModalOpen.value = false;
    selectedProduct.value = null;
    stockForm.reset();
    stockForm.clearErrors();
};

const addNewStockRow = () => {
    stockForm.stocks.push({ lugar: '', cantidad: 0 });
};

const removeStockRow = (idx) => {
    stockForm.stocks.splice(idx, 1);
};

const submitManageStock = () => {
    stockForm.post(route('inventario.update_stock'), {
        preserveScroll: true,
        onSuccess: () => closeManageStockModal(),
    });
};

const isPriceModalOpen = ref(false);
const selectedProductForPrice = ref(null);

const priceForm = useForm({
    product_id: '',
    precio_1: 0,
    extra_prices: []
});

const openEditPriceModal = (producto) => {
    selectedProductForPrice.value = producto;
    priceForm.product_id = producto.id;
    priceForm.precio_1 = parseFloat(producto.precio_1 || 0).toFixed(2);
    
    // Convert existing extra prices to editable array
    priceForm.extra_prices = producto.prices ? producto.prices.map(p => ({
        price_list_id: p.price_list_id,
        price: parseFloat(p.price || 0).toFixed(2)
    })) : [];
    
    isPriceModalOpen.value = true;
};

const closeEditPriceModal = () => {
    isPriceModalOpen.value = false;
    selectedProductForPrice.value = null;
    priceForm.reset();
    priceForm.clearErrors();
};

const submitEditPrice = () => {
    priceForm.post(route('inventario.update_price'), {
        preserveScroll: true,
        onSuccess: () => closeEditPriceModal(),
    });
};

const isEditProductModalOpen = ref(false);
const selectedProductToEdit = ref(null);

const editProductForm = useForm({
    nombre: '',
    sku: '',
    foto: '',
});

const openEditProductModal = (producto) => {
    selectedProductToEdit.value = producto;
    editProductForm.nombre = producto.nombre || '';
    editProductForm.sku = producto.sku || '';
    editProductForm.foto = producto.foto || '';
    isEditProductModalOpen.value = true;
};

const closeEditProductModal = () => {
    isEditProductModalOpen.value = false;
    selectedProductToEdit.value = null;
    editProductForm.reset();
    editProductForm.clearErrors();
};

const submitEditProduct = () => {
    if (!selectedProductToEdit.value) return;
    editProductForm.post(route('inventario.update_details', selectedProductToEdit.value.id), {
        preserveScroll: true,
        onSuccess: () => closeEditProductModal(),
    });
};

const confirmDelete = (id) => {
    if (confirm('¿Estás seguro de que deseas eliminar este producto por completo y todo su stock/precios vinculados? Esta acción no se puede deshacer.')) {
        router.delete(route('inventario.destroy', id), {
            preserveScroll: true,
        });
    }
};

const isCreateProductModalOpen = ref(false);

const getDefaultVigencia = () => {
    const d = new Date();
    return d.toISOString().split('T')[0];
};

const createProductForm = useForm({
    nombre: '',
    sku: '',
    foto: '',
    precio_1: 0,
    linea_prod_id: '',
    pround: 'PZA',
    nombre_corto: '',
    marca_prod_id: '',
    sublinea_prod_id: '',
    presentacion_prod_id: '',
    AAI: '0.00',
    iva16: 0.16,
    ieps: 0.00,
    vigencia: getDefaultVigencia(),
    proveedor_desc1: '',
    PRODUC_FACTCONVER3: 1,
    PRODUC_PZAMINVTA: 1,
    VTAPRE_PRECIO: null,
    MULTIMARCA: '',
});

const openCreateProductModal = () => {
    isCreateProductModalOpen.value = true;
};

const closeCreateProductModal = () => {
    isCreateProductModalOpen.value = false;
    createProductForm.reset();
    createProductForm.clearErrors();
};

const submitCreateProduct = () => {
    createProductForm.post(route('inventario.store'), {
        preserveScroll: true,
        onSuccess: () => closeCreateProductModal(),
    });
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
