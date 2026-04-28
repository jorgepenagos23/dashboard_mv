<template>
    <div :style="{ backgroundColor: 'var(--app-bg, #111827)', color: 'var(--app-text, #f3f4f6)' }" class="min-h-screen p-8 font-sans pb-24 transition-colors duration-300">
        <header class="mb-8 flex justify-between items-end">
            <div>
                <h1 class="text-3xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-indigo-400">
                    Gestión de Pedidos & Analíticas
                </h1>
                <p class="text-gray-400 mt-2">Visualiza ventas en tiempo real, tendencias y desempeño de productos.</p>
            </div>
        </header>

        <!-- Tabs -->
        <div class="flex space-x-4 border-b border-gray-700 mb-6 font-semibold">
            <button @click="activeTab = 'pedidos'" :class="['py-2 px-6 border-b-2 transition-all', activeTab === 'pedidos' ? 'border-blue-500 text-blue-400' : 'border-transparent text-gray-400 hover:text-gray-200']">
                Dashboard de Pedidos
            </button>
            <button @click="activeTab = 'sistema'" :class="['py-2 px-6 border-b-2 transition-all', activeTab === 'sistema' ? 'border-amber-500 text-amber-400' : 'border-transparent text-gray-400 hover:text-gray-200']">
                SISTEMA (Análisis Excel)
            </button>
        </div>

        <div v-show="activeTab === 'pedidos'">
            <!-- Seccion de Metricas y Graficos -->
        <section class="grid lg:grid-cols-3 gap-6 mb-8">
            
            <!-- Grafico Ventas por Mes -->
            <div :style="{ backgroundColor: 'var(--app-card, #1f2937)', borderColor: 'var(--app-card, #374151)' }" class="rounded-xl border shadow-xl p-6 relative overflow-hidden transition-colors duration-300">
                <div class="absolute -top-10 -right-10 w-32 h-32 bg-blue-600/10 blur-3xl rounded-full pointer-events-none"></div>
                <h3 class="font-bold text-lg text-white mb-4 flex items-center gap-2">
                    <span class="w-1.5 h-6 rounded bg-blue-500"></span> Total Ventas por Mes
                </h3>
                
                <div class="h-48 flex items-end gap-3 mt-6 pt-6 border-b border-gray-700/50 pb-2 relative">
                    <!-- Barras -->
                    <div v-for="mes in ventasPorMes" :key="mes.mes" class="flex-1 flex flex-col items-center justify-end h-full group relative cursor-pointer pt-6">
                        <!-- Tooltip -->
                        <div class="absolute -top-10 bg-gray-900 border border-gray-700 text-xs px-2 py-1 rounded shadow-lg opacity-0 group-hover:opacity-100 transition whitespace-nowrap z-10 pointer-events-none">
                            {{ formatMonth(mes.mes) }}<br/>
                            <span class="font-bold text-blue-400">${{ parseFloat(mes.total_ventas).toLocaleString('en-US', {minimumFractionDigits: 2}) }}</span>
                        </div>
                        
                        <!-- Barra -->
                        <div class="w-full bg-gradient-to-t from-blue-600 to-indigo-400 rounded-t-sm transition-all duration-500 group-hover:from-blue-500 group-hover:to-indigo-300"
                             :style="{ height: getVentasHeight(mes.total_ventas) + '%' }">
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-between mt-2 px-1">
                    <div v-for="mes in ventasPorMes" :key="mes.mes" class="flex-1 text-center text-[10px] text-gray-400 uppercase tracking-widest truncate" :title="formatMonth(mes.mes)">
                        {{ formatMonth(mes.mes).substring(0,3) }}
                    </div>
                </div>
            </div>

            <!-- Productos Más Vendidos -->
            <div :style="{ backgroundColor: 'var(--app-card, #1f2937)', borderColor: 'var(--app-card, #374151)' }" class="rounded-xl border shadow-xl p-6 relative overflow-hidden transition-colors duration-300">
                <div class="absolute -bottom-10 -left-10 w-32 h-32 bg-emerald-600/10 blur-3xl rounded-full pointer-events-none"></div>
                <h3 class="font-bold text-lg text-white mb-4 flex items-center gap-2">
                    <span class="w-1.5 h-6 rounded bg-emerald-500"></span> Más Vendidos (Top 5)
                </h3>
                
                <ul class="space-y-4 mt-4">
                    <li v-for="(prod, i) in masVendidos" :key="prod.nombre" class="relative group">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-10 h-10 rounded bg-gray-900 border border-gray-700 flex items-center justify-center shrink-0 overflow-hidden">
                                <img v-if="prod.foto" :src="prod.foto" class="w-full h-full object-cover" alt="prod" />
                                <span v-else class="text-xs text-gray-600 font-bold uppercase">{{ prod.nombre.substring(0,2) }}</span>
                            </div>
                            <div class="flex-1 min-w-0 flex justify-between items-center text-sm">
                                <span class="font-medium text-gray-200 truncate pr-4" :title="prod.nombre">{{ i + 1 }}. {{ prod.nombre_corto || prod.nombre }}</span>
                                <span class="font-bold text-emerald-400 shrink-0">{{ prod.total_vendido }} ud.</span>
                            </div>
                        </div>
                        <div class="w-full bg-gray-700 rounded-full h-1.5">
                            <div class="bg-emerald-500 h-1.5 rounded-full" :style="{ width: getProdWidth(prod.total_vendido, masVendidos[0].total_vendido) + '%' }"></div>
                        </div>
                    </li>
                    <li v-if="!masVendidos.length" class="text-sm text-gray-500 italic py-4">No hay datos suficientes.</li>
                </ul>
            </div>

            <!-- Productos Menos Vendidos -->
            <div :style="{ backgroundColor: 'var(--app-card, #1f2937)', borderColor: 'var(--app-card, #374151)' }" class="rounded-xl border shadow-xl p-6 relative overflow-hidden transition-colors duration-300">
                <div class="absolute top-1/2 left-1/2 w-48 h-48 bg-rose-600/10 -translate-x-1/2 -translate-y-1/2 blur-3xl rounded-full pointer-events-none"></div>
                <h3 class="font-bold text-lg text-white mb-4 flex items-center gap-2">
                    <span class="w-1.5 h-6 rounded bg-rose-500"></span> Menos Vendidos (Top 5)
                </h3>
                
                <ul class="space-y-4 mt-4">
                    <li v-for="(prod, i) in menosVendidos" :key="prod.nombre" class="relative group">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-10 h-10 rounded bg-gray-900 border border-gray-700 flex items-center justify-center shrink-0 overflow-hidden">
                                <img v-if="prod.foto" :src="prod.foto" class="w-full h-full object-cover" alt="prod" />
                                <span v-else class="text-xs text-gray-600 font-bold uppercase">{{ prod.nombre.substring(0,2) }}</span>
                            </div>
                            <div class="flex-1 min-w-0 flex justify-between items-center text-sm">
                                <span class="font-medium text-gray-300 truncate pr-4" :title="prod.nombre">{{ i + 1 }}. {{ prod.nombre_corto || prod.nombre }}</span>
                                <span class="font-bold text-rose-400 shrink-0">{{ prod.total_vendido }} ud.</span>
                            </div>
                        </div>
                        <div class="w-full bg-gray-700 rounded-full h-1.5">
                            <div class="bg-rose-500 h-1.5 rounded-full" :style="{ width: getProdWidth(prod.total_vendido, masVendidos[0]?.total_vendido || prod.total_vendido) + '%' }"></div>
                        </div>
                    </li>
                    <li v-if="!menosVendidos.length" class="text-sm text-gray-500 italic py-4">No hay datos suficientes.</li>
                </ul>
            </div>
            
        </section>

        <!-- Area Principal (Lista de Pedidos y Detalles) -->
        <main class="grid lg:grid-cols-3 gap-8">
            
            <!-- Lista de Pedidos -->
            <div :style="{ backgroundColor: 'var(--app-card, #1f2937)', borderColor: 'var(--app-card, #374151)' }" class="lg:col-span-2 overflow-hidden rounded-xl border shadow-2xl backdrop-blur-sm transition-colors duration-300">
                <div class="p-4 border-b border-gray-700 bg-gray-900/50">
                    <h3 class="font-bold text-lg text-white flex items-center gap-2">
                        <span class="w-2 h-5 rounded bg-indigo-500"></span> Historial de Pedidos
                    </h3>
                </div>
                <div class="overflow-x-auto min-h-[400px]">
                    <table class="w-full text-left text-sm whitespace-nowrap">
                        <thead class="uppercase tracking-wider border-b border-gray-700 bg-gray-900/40 text-gray-400">
                            <tr>
                                <th class="p-4 font-medium">ID Pedido</th>
                                <th class="p-4 font-medium">Cliente ID</th>
                                <th class="p-4 font-medium">Fecha</th>
                                <th class="p-4 font-medium">Estado</th>
                                <th class="p-4 font-medium text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-800 text-gray-300">
                            <tr v-for="pedido in pedidos.data" :key="pedido.id" 
                                @click="selectPedido(pedido)"
                                :class="[
                                    'cursor-pointer transition hover:bg-gray-700/50', 
                                    selectedPedido?.id === pedido.id ? 'bg-indigo-900/30 border-l-2 border-indigo-500' : 'border-l-2 border-transparent'
                                ]">
                                <td class="p-4 font-semibold text-blue-400">#{{ pedido.id }}</td>
                                <td class="p-4 text-gray-300">{{ pedido.cliente_id }}</td>
                                <td class="p-4 text-gray-400 text-xs">{{ formatDate(pedido.fecha_pedido) }}</td>
                                <td class="p-4">
                                    <span class="px-2 py-1 text-[10px] uppercase rounded-full bg-indigo-500/20 text-indigo-300 font-bold border border-indigo-500/30">
                                        {{ pedido.estado || 'Procesando' }}
                                    </span>
                                </td>
                                <td class="p-4 text-right font-bold text-gray-100">${{ parseFloat(pedido.total).toLocaleString('en-US', {minimumFractionDigits: 2}) }}</td>
                            </tr>
                            <tr v-if="pedidos.data.length === 0">
                                <td colspan="5" class="p-8 text-center text-gray-500 italic">No hay pedidos disponibles.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="p-4 border-t border-gray-700 flex items-center justify-between text-sm text-gray-400 bg-gray-900/50">
                    <div>Página {{ pedidos.current_page }} de {{ pedidos.last_page }}</div>
                    <div class="flex space-x-1">
                        <component v-for="(link, i) in pedidos.links" :key="i" :is="link.url ? 'a' : 'span'" :href="link.url"
                            class="px-3 py-1.5 rounded text-sm transition border"
                            :class="[link.active ? 'bg-indigo-600 border-indigo-500 text-white' : 'bg-gray-800 border-gray-700 text-gray-400 hover:bg-gray-700', !link.url && 'opacity-50 cursor-not-allowed']"
                            v-html="link.label">
                        </component>
                    </div>
                </div>
            </div>

            <!-- Detalle del Pedido -->
            <div :style="{ backgroundColor: 'var(--app-card, #1f2937)', borderColor: 'var(--app-card, #374151)' }" class="rounded-xl border shadow-xl p-6 relative overflow-hidden h-max sticky top-6 transition-colors duration-300">
                <!-- Background decoration -->
                <div class="absolute -top-24 -right-24 w-48 h-48 bg-indigo-600/20 blur-3xl rounded-full pointer-events-none"></div>

                <div v-if="selectedPedido">
                    <h2 class="text-xl font-bold mb-4 flex items-center text-white">
                        <span class="bg-gradient-to-b from-blue-400 to-indigo-500 w-2.5 h-6 rounded mr-3"></span>
                        Detalles del Pedido #{{ selectedPedido.id }}
                    </h2>
                    
                    <div class="mb-6 bg-gray-900 border border-gray-700 p-4 rounded-xl shadow-inner">
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div class="flex flex-col">
                                <span class="text-[10px] text-gray-500 uppercase font-black tracking-widest mb-1">MV Coins Usados</span>
                                <span class="font-bold font-mono text-yellow-400 text-lg">{{ selectedPedido.mv_coins_usados || 0 }}</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-[10px] text-gray-500 uppercase font-black tracking-widest mb-1">MV Coins Ganados</span>
                                <span class="font-bold font-mono text-emerald-400 text-lg">{{ selectedPedido.mv_coins_ganados || 0 }}</span>
                            </div>
                        </div>
                    </div>

                    <h3 class="font-bold text-gray-300 text-sm mb-4 border-b border-gray-700 pb-2 uppercase tracking-wider">Productos Comprados</h3>
                    <ul class="space-y-3 max-h-[500px] overflow-y-auto pr-2 custom-scrollbar">
                        <li v-for="detalle in selectedPedido.detalles" :key="detalle.producto_id" class="flex gap-4 items-center bg-gray-900/50 p-3 rounded-xl border border-gray-700/50 hover:bg-gray-700/50 transition">
                            <!-- Image placeholder -->
                            <div class="w-12 h-12 rounded bg-gray-800 flex items-center justify-center border border-gray-700 shrink-0 overflow-hidden">
                                <img v-if="detalle.producto?.foto" :src="detalle.producto?.foto" class="w-full h-full object-cover" alt="prod" />
                                <svg v-else class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-gray-100 truncate" :title="detalle.producto?.nombre">{{ detalle.producto?.nombre || 'Producto Desconocido' }}</p>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-mono bg-gray-800 text-gray-400 border border-gray-700">SKU: {{ detalle.sku }}</span>
                                    <span class="text-xs font-medium text-gray-400">× {{ detalle.cantidad }}</span>
                                </div>
                            </div>
                            <div class="text-right shrink-0">
                                <p class="font-bold text-blue-400">${{ parseFloat(detalle.subtotal).toLocaleString('en-US', {minimumFractionDigits: 2}) }}</p>
                            </div>
                        </li>
                        <li v-if="!selectedPedido.detalles.length" class="text-gray-500 text-sm italic py-4 text-center">Sin detalles registrados.</li>
                    </ul>
                </div>
                <div v-else class="h-full flex flex-col items-center justify-center text-gray-500 py-16">
                    <div class="w-20 h-20 rounded-full bg-gray-900/50 border border-gray-700 flex items-center justify-center mb-4">
                        <svg class="w-10 h-10 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                    </div>
                    <p class="font-medium">Selecciona un pedido</p>
                    <p class="text-xs text-gray-600 mt-1">Para visualizar todos los productos</p>
                </div>
            </div>

        </main>
        </div> <!-- Fin de tab pedidos -->

        <!-- TAB: SISTEMA (Excel Upload) -->
        <div v-show="activeTab === 'sistema'" class="space-y-8 animate-fade-in-up">
            <div :style="{ backgroundColor: 'var(--app-card, #1f2937)', borderColor: 'var(--app-card, #374151)' }" class="rounded-xl border shadow-xl p-8 relative overflow-hidden transition-colors duration-300">
                <div class="absolute -top-24 -right-24 w-64 h-64 bg-amber-600/20 blur-3xl rounded-full pointer-events-none"></div>
                <h2 class="text-2xl font-bold mb-4 flex items-center text-white">
                    <span class="bg-gradient-to-b from-amber-400 to-amber-600 w-3 h-8 rounded mr-3"></span> Cargar Histórico Analítico (Excel / CSV)
                </h2>
                <p class="text-sm text-gray-400 mb-8 max-w-2xl">Sube el reporte de ventas y cobranza expedido desde el sistema central administrativo. Extraeremos de inmediato métricas e Insights para mostrarte los Canales, Ejecutivos y Productos más rentables del periodo.</p>
                
                <form @submit.prevent="uploadExcel" class="flex flex-col md:flex-row items-end gap-6 bg-gray-900/50 p-6 rounded-xl border border-gray-700/50">
                    <div class="flex-1 w-full">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Archivo Base de Datos / Sistema</label>
                        <input type="file" @change="e => sysForm.excel_file = e.target.files[0]" accept=".xlsx, .xls, .csv" required class="w-full text-sm text-gray-300 file:mr-4 file:py-2.5 file:px-6 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-amber-500/20 file:text-amber-400 focus:outline-none file:cursor-pointer hover:file:bg-amber-500/30 file:transition-all bg-gray-900 border border-gray-700 rounded-lg">
                        <p class="text-[10px] text-gray-500 mt-2">Formatos soportados: .xlsx, .csv (Max. 20MB)</p>
                    </div>
                    <button type="submit" :disabled="sysForm.processing" class="w-full md:w-auto px-8 py-3 bg-gradient-to-r from-amber-600 to-orange-500 hover:from-amber-500 hover:to-orange-400 text-white font-bold rounded-lg shadow-lg transition-all flex items-center justify-center gap-3 disabled:opacity-50">
                        <span v-if="sysForm.processing" class="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                        <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                        Procesar Base de Datos
                    </button>
                </form>
                
                <div v-if="$page.props.flash?.error" class="mt-6 p-4 bg-red-500/20 border border-red-500/50 rounded-lg text-red-400 text-sm font-medium flex gap-3 items-center">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    {{ $page.props.flash.error }}
                </div>
            </div>

            <!-- Resultados del Excel Mágico -->
            <div v-if="$page.props.flash?.system_analysis" class="grid lg:grid-cols-3 gap-6 animate-fade-in-up transition-all duration-500">
                
                <!-- Resumen Financiero -->
                <div :style="{ backgroundColor: 'var(--app-card, #1f2937)', borderColor: 'var(--app-card, #374151)' }" class="rounded-xl border p-8 flex flex-col items-center justify-center text-center shadow-xl transition-colors duration-300">
                    <div class="w-20 h-20 rounded-full bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center mb-6">
                        <svg class="w-10 h-10 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <p class="text-xs text-emerald-400 uppercase tracking-widest font-bold mb-2">Ingresos Consolidados</p>
                    <h4 class="text-4xl font-black text-white">${{ parseFloat($page.props.flash.system_analysis.totalMonto || 0).toLocaleString('en-US', {minimumFractionDigits:2}) }}</h4>
                    <p class="text-sm text-gray-500 mt-2">Monto Total Analizado en Reporte</p>

                    <div class="mt-8 bg-gray-900 border border-gray-700 rounded-lg w-full p-4 flex justify-between items-center text-left">
                        <div>
                            <p class="text-[10px] text-gray-500 uppercase tracking-widest">Filas Procesadas</p>
                            <p class="font-mono text-xl font-bold text-amber-500">{{ parseFloat($page.props.flash.system_analysis.totalRows).toLocaleString('en-US') }}</p>
                        </div>
                        <div class="w-10 h-10 rounded-full bg-amber-500/20 flex items-center justify-center border border-amber-500/30">
                            <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path></svg>
                        </div>
                    </div>
                </div>

                <!-- Ventas por Canal -->
                <div :style="{ backgroundColor: 'var(--app-card, #1f2937)', borderColor: 'var(--app-card, #374151)' }" class="rounded-xl border p-6 shadow-xl relative overflow-hidden transition-colors duration-300">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-blue-500/10 blur-3xl rounded-full"></div>
                    <h4 class="font-bold text-white mb-6 border-b border-gray-700 pb-3 flex items-center gap-2">
                        <span class="w-1.5 h-5 bg-blue-500 rounded"></span> Top Canales de Venta
                    </h4>
                    <ul class="space-y-4 relative z-10 w-full pr-2 overflow-y-auto max-h-[300px] custom-scrollbar">
                        <li v-for="(monto, canal, i) in $page.props.flash.system_analysis.ventasPorCanal" :key="canal" class="relative group">
                            <div class="flex justify-between items-center mb-2 text-sm">
                                <span class="text-gray-200 font-bold truncate pr-2">{{ i + 1 }}. {{ canal }}</span>
                                <span class="text-blue-400 font-bold shrink-0">${{ parseFloat(monto).toLocaleString('en-US', {minimumFractionDigits:2}) }}</span>
                            </div>
                            <div class="w-full bg-gray-700 rounded-full h-1.5">
                                <div class="bg-blue-500 h-1.5 rounded-full" :style="{ width: getSysBarWidth(monto, $page.props.flash.system_analysis.ventasPorCanal) + '%' }"></div>
                            </div>
                        </li>
                    </ul>
                </div>

                <!-- Ventas por Ejecutivo -->
                <div :style="{ backgroundColor: 'var(--app-card, #1f2937)', borderColor: 'var(--app-card, #374151)' }" class="rounded-xl border p-6 shadow-xl relative overflow-hidden transition-colors duration-300">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-purple-500/10 blur-3xl rounded-full"></div>
                    <h4 class="font-bold text-white mb-6 border-b border-gray-700 pb-3 flex items-center gap-2">
                        <span class="w-1.5 h-5 bg-purple-500 rounded"></span> Top Ejecutivos / Vendedores
                    </h4>
                    <ul class="space-y-4 relative z-10 w-full pr-2 overflow-y-auto max-h-[300px] custom-scrollbar">
                        <li v-for="(monto, ejec, i) in $page.props.flash.system_analysis.ventasPorEjecutivo" :key="ejec" class="relative group">
                            <div class="flex justify-between items-center mb-2 text-sm">
                                <span class="text-gray-200 font-bold truncate pr-2">{{ i + 1 }}. {{ ejec }}</span>
                                <span class="text-purple-400 font-bold shrink-0">${{ parseFloat(monto).toLocaleString('en-US', {minimumFractionDigits:2}) }}</span>
                            </div>
                            <div class="w-full bg-gray-700 rounded-full h-1.5">
                                <div class="bg-purple-500 h-1.5 rounded-full" :style="{ width: getSysBarWidth(monto, $page.props.flash.system_analysis.ventasPorEjecutivo) + '%' }"></div>
                            </div>
                        </li>
                    </ul>
                </div>
                
                <!-- Top Productos Excel -->
                <div :style="{ backgroundColor: 'var(--app-card, #1f2937)', borderColor: 'var(--app-card, #374151)' }" class="rounded-xl border p-6 shadow-xl lg:col-span-3 transition-colors duration-300">
                    <h4 class="font-bold text-lg text-white mb-6 border-b border-gray-700 pb-3 flex items-center gap-2">
                        <span class="w-2 h-5 bg-teal-500 rounded"></span> Insights: Productos Más Solicitados en Sistema
                    </h4>
                    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div v-for="(cantidad, prod, i) in $page.props.flash.system_analysis.topProductos" :key="prod" class="bg-gray-900 border border-gray-700 rounded-xl p-4 flex gap-4 items-center">
                            <div class="w-10 h-10 rounded-full bg-teal-500/10 border border-teal-500/20 text-teal-400 flex justify-center items-center font-black">
                                #{{ i + 1 }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-gray-200 truncate">{{ prod }}</p>
                                <p class="text-xs text-teal-500 font-mono mt-0.5">{{ parseFloat(cantidad).toLocaleString() }} unidades</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div> <!-- Fin de Tab Sistema -->

    </div>
</template>

<script setup>
import { ref, watch, onMounted, computed } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    pedidos: Object,
    ventasPorMes: Array,
    masVendidos: Array,
    menosVendidos: Array,
});

defineOptions({ layout: DashboardLayout });

// Tabs
const activeTab = ref('pedidos');

// System form
const sysForm = useForm({
    excel_file: null
});

const uploadExcel = () => {
    sysForm.post(route('pedidos.upload_system'), {
        preserveScroll: true,
        onSuccess: () => {
            sysForm.reset('excel_file');
        }
    });
};

const getSysBarWidth = (value, obj) => {
    if (!obj) return 0;
    const max = Math.max(...Object.values(obj).map(v => parseFloat(v)));
    if (max === 0) return 0;
    return Math.max(2, (parseFloat(value) / max) * 100);
};

const searchQuery = ref('');
const selectedPedido = ref(null);

const pedidosFiltrados = ref(props.pedidos.data || []);

watch(searchQuery, (newVal) => {
    if (!newVal) {
        pedidosFiltrados.value = props.pedidos.data;
        return;
    }
    const q = newVal.toLowerCase();
    pedidosFiltrados.value = props.pedidos.data.filter(p => 
        p.id.toString().includes(q) || 
        (p.cliente && p.cliente.razon_social && p.cliente.razon_social.toLowerCase().includes(q))
    );
});

onMounted(() => {
    pedidosFiltrados.value = props.pedidos.data;
});

const selectPedido = (pedido) => {
    selectedPedido.value = pedido;
};

const closeModal = () => {
    selectedPedido.value = null;
};

const formatDate = (dateStr) => {
    if (!dateStr) return 'N/A';
    return new Date(dateStr).toLocaleString('es-MX', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

const formatMonth = (str) => {
    if (!str) return 'N/A';
    const [year, month] = str.split('-');
    const date = new Date(year, parseInt(month) - 1, 1);
    return date.toLocaleString('es-MX', { month: 'long', year: 'numeric' });
};

const maxVentas = computed(() => {
    if (!props.ventasPorMes || props.ventasPorMes.length === 0) return 1;
    return Math.max(...props.ventasPorMes.map(m => parseFloat(m.total_ventas)));
});

const getVentasHeight = (total) => {
    const max = maxVentas.value;
    if (max === 0) return 0;
    // Set a minimum height of 5% so the bar is always visible
    return Math.max(5, (parseFloat(total) / max) * 100);
};

const getProdWidth = (total, max) => {
    if (!max || max == 0) return 0;
    return Math.max(2, (parseFloat(total) / parseFloat(max)) * 100);
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
    background-color: #374151; /* gray-700 */
    border-radius: 20px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background-color: #4b5563; /* gray-600 */
}
</style>
