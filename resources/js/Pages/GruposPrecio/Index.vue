<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-4">
                <div class="flex-1">
                    <h1 class="text-3xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-violet-500 to-purple-600">
                        Grupos de Precio & Descuentos
                    </h1>
                    <p class="text-gray-500 mt-1">Configura qué clientes tienen descuento y a qué productos aplica.</p>
                </div>
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
                               ? 'border-violet-500 text-violet-600'
                               : 'border-transparent text-gray-500 hover:text-gray-700']">
                    {{ tab.label }}
                </button>
            </div>

            <!-- ══════════════════════════════════════════════════════════ -->
            <!-- TAB 1 — GRUPOS (% de descuento)                           -->
            <!-- ══════════════════════════════════════════════════════════ -->
            <section v-show="activeTab === 'grupos'">

                <!-- Botón nuevo grupo -->
                <div class="flex justify-end mb-5">
                    <button @click="mostrarModalCrear = true"
                            class="flex items-center gap-2 px-5 py-2.5 bg-violet-600 hover:bg-violet-500 text-white text-sm font-bold rounded-xl shadow-md hover:shadow-lg transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                        </svg>
                        Nuevo Grupo
                    </button>
                </div>

                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    <div v-for="grupo in grupos" :key="grupo.id"
                         class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 flex flex-col gap-4">

                        <div class="flex items-center justify-between">
                            <span class="text-lg font-extrabold text-violet-700">{{ grupo.nombre }}</span>
                            <span class="px-3 py-1 rounded-full text-xs font-bold uppercase bg-violet-100 text-violet-600 border border-violet-200">
                                GRUPO_PRECIO
                            </span>
                        </div>

                        <p class="text-sm text-gray-500">{{ grupo.descripcion || 'Sin descripción' }}</p>

                        <div class="bg-violet-50 rounded-xl p-4 text-center">
                            <p class="text-xs text-violet-500 uppercase tracking-wider mb-1">Descuento actual</p>
                            <p class="text-4xl font-black text-violet-700">
                                {{ grupo.promo_price != null ? grupo.promo_price + '%' : '—' }}
                            </p>
                        </div>

                        <form @submit.prevent="guardarPorcentaje(grupo)" class="flex gap-2">
                            <input v-model="porcentajeEdit[grupo.id]"
                                   type="number" min="0" max="100" step="0.01"
                                   :placeholder="grupo.promo_price ?? 0"
                                   class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-violet-400 focus:border-transparent outline-none" />
                            <button type="submit"
                                    class="px-4 py-2 bg-violet-600 hover:bg-violet-500 text-white text-sm font-bold rounded-lg transition shrink-0">
                                Guardar
                            </button>
                        </form>
                    </div>
                </div>

                <p v-if="!grupos.length" class="text-center text-gray-400 italic py-12">
                    No hay grupos con tipo <code>GRUPO_PRECIO</code> en la tabla promociones.
                    Insértalos primero desde la sección de Promociones.
                </p>
            </section>

            <!-- ══════════════════════════════════════════════════════════ -->
            <!-- TAB 2 — REGLAS (qué productos aplican por grupo)           -->
            <!-- ══════════════════════════════════════════════════════════ -->
            <section v-show="activeTab === 'reglas'">

                <div class="mb-6 bg-amber-50 border border-amber-200 rounded-xl px-5 py-3 text-sm text-amber-700 flex gap-2 items-start">
                    <svg class="w-5 h-5 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <strong>Prioridad de reglas (mayor a menor):</strong>
                        SKUs excluidos → Sublíneas excluidas → SKUs incluidos → Sublíneas incluidas → Líneas incluidas → (todo vacío = aplica a todos).
                    </div>
                </div>

                <!-- Selector de grupo -->
                <div class="flex gap-3 mb-6 flex-wrap">
                    <button v-for="r in reglas" :key="r.grupo"
                            @click="reglaActiva = r.grupo"
                            :class="['px-5 py-2 rounded-full text-sm font-bold border transition',
                                     reglaActiva === r.grupo
                                       ? 'bg-violet-600 text-white border-violet-600 shadow'
                                       : 'bg-white text-gray-600 border-gray-300 hover:border-violet-400']">
                        {{ r.grupo === 'default' ? 'Default' : r.grupo }}
                    </button>
                </div>

                <!-- Editor de regla activa -->
                <div v-if="reglaSeleccionada" class="bg-white rounded-2xl border border-gray-200 shadow-sm p-8">
                    <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center gap-2">
                        <span class="w-2 h-6 rounded bg-violet-500"></span>
                        Reglas para <span class="text-violet-600 ml-1">{{ reglaActiva }}</span>
                    </h2>

                    <form @submit.prevent="guardarRegla" class="space-y-6">
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Líneas incluidas
                                    <span class="text-xs font-normal text-gray-400 ml-1">(vacío = todas)</span>
                                </label>
                                <TagInput v-model="reglaForm.lineas" placeholder="Ej: 101" color="violet" />
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Sublíneas incluidas
                                    <span class="text-xs font-normal text-gray-400 ml-1">(vacío = todas)</span>
                                </label>
                                <TagInput v-model="reglaForm.sublineas_inc" placeholder="Ej: REDBULL" color="blue" />
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Sublíneas excluidas
                                    <span class="text-xs font-normal text-gray-400 ml-1">(prioridad sobre incluidas)</span>
                                </label>
                                <TagInput v-model="reglaForm.sublineas_exc" placeholder="Ej: ELECTR" color="orange" />
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    SKUs incluidos específicos
                                    <span class="text-xs font-normal text-gray-400 ml-1">(vacío = todos)</span>
                                </label>
                                <TagInput v-model="reglaForm.skus_inc" placeholder="Ej: P1001" color="green" />
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    SKUs excluidos
                                    <span class="text-xs font-normal text-red-400 ml-1">(nunca reciben descuento)</span>
                                </label>
                                <TagInput v-model="reglaForm.skus_exc" placeholder="Ej: P2107" color="red" />
                            </div>
                        </div>

                        <!-- Tasas por línea -->
                        <div class="md:col-span-2 border-t border-gray-100 pt-4">
                            <div class="flex items-center justify-between mb-3">
                                <label class="block text-sm font-semibold text-gray-700">
                                    Tasas específicas por línea
                                    <span class="text-xs font-normal text-gray-400 ml-1">(ej: 141-NISSIN → 3%)</span>
                                </label>
                                <button type="button" @click="agregarTasaLinea"
                                        class="text-xs px-3 py-1 bg-amber-100 text-amber-700 hover:bg-amber-200 rounded-lg font-semibold transition">
                                    + Agregar
                                </button>
                            </div>
                            <div v-if="reglaForm.lineas_rates_lista.length" class="space-y-2">
                                <div v-for="(item, i) in reglaForm.lineas_rates_lista" :key="i"
                                     class="flex items-center gap-2">
                                    <input v-model="item.linea" type="text" placeholder="Línea (ej: 141-NISSIN)"
                                           class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm uppercase focus:ring-2 focus:ring-amber-400 outline-none" />
                                    <input v-model.number="item.rate" type="number" min="0" max="100" step="0.01" placeholder="%"
                                           class="w-24 rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-amber-400 outline-none" />
                                    <span class="text-gray-400 text-sm font-bold">%</span>
                                    <button type="button" @click="reglaForm.lineas_rates_lista.splice(i,1)"
                                            class="text-red-400 hover:text-red-600 text-lg leading-none px-1">×</button>
                                </div>
                            </div>
                            <p v-else class="text-xs text-gray-400 italic">Sin tasas por línea — usa el % base del grupo.</p>
                        </div>

                        <!-- Tasas por sublínea -->
                        <div class="md:col-span-2 border-t border-gray-100 pt-4">
                            <div class="flex items-center justify-between mb-3">
                                <label class="block text-sm font-semibold text-gray-700">
                                    Tasas específicas por sublínea
                                    <span class="text-xs font-normal text-gray-400 ml-1">(sobreescribe el % del grupo para esa sublínea)</span>
                                </label>
                                <button type="button" @click="agregarTasa"
                                        class="text-xs px-3 py-1 bg-violet-100 text-violet-700 hover:bg-violet-200 rounded-lg font-semibold transition">
                                    + Agregar
                                </button>
                            </div>
                            <div v-if="reglaForm.sublineas_rates_lista.length" class="space-y-2">
                                <div v-for="(item, i) in reglaForm.sublineas_rates_lista" :key="i"
                                     class="flex items-center gap-2">
                                    <input v-model="item.sublinea" type="text" placeholder="Sublínea (ej: CLAMATO)"
                                           class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm uppercase focus:ring-2 focus:ring-violet-400 outline-none" />
                                    <input v-model.number="item.rate" type="number" min="0" max="100" step="0.01" placeholder="%"
                                           class="w-24 rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-violet-400 outline-none" />
                                    <span class="text-gray-400 text-sm font-bold">%</span>
                                    <button type="button" @click="reglaForm.sublineas_rates_lista.splice(i,1)"
                                            class="text-red-400 hover:text-red-600 text-lg leading-none px-1">×</button>
                                </div>
                            </div>
                            <p v-else class="text-xs text-gray-400 italic">Sin tasas específicas — todos los productos del grupo usan el % base.</p>
                        </div>

                        <div class="flex justify-end pt-2">
                            <button type="submit"
                                    class="px-8 py-2.5 bg-violet-600 hover:bg-violet-500 text-white font-bold rounded-xl shadow-md hover:shadow-lg transition">
                                Guardar reglas de {{ reglaActiva }}
                            </button>
                        </div>
                    </form>
                </div>

                <p v-else-if="!reglas.length" class="text-center text-gray-400 italic py-12">
                    Aún no hay reglas en la base de datos. Ejecuta primero:<br/>
                    <code class="bg-gray-100 px-3 py-1 rounded text-sm mt-2 inline-block">
                        node scripts/migrar_grupo_precio_reglas.js
                    </code>
                </p>
            </section>

            <!-- ══════════════════════════════════════════════════════════ -->
            <!-- TAB 3 — ASIGNACIONES (clientes con grupo activo)           -->
            <!-- ══════════════════════════════════════════════════════════ -->
            <section v-show="activeTab === 'asignaciones'">
                <div class="grid lg:grid-cols-3 gap-8">

                    <!-- Formulario de asignación -->
                    <div class="lg:col-span-1">
                        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 sticky top-6">
                            <h2 class="text-lg font-bold text-gray-800 mb-5 flex items-center gap-2">
                                <span class="w-2 h-5 rounded bg-violet-500"></span>
                                Asignar grupo a cliente
                            </h2>

                            <form @submit.prevent="asignar" class="space-y-4">
                                <!-- Buscador -->
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Buscar cliente</label>
                                    <input v-model="buscarCliente" @input="buscarDebounced"
                                           type="text" placeholder="Nombre o código..."
                                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-violet-400 focus:border-transparent outline-none" />
                                </div>

                                <!-- Resultados -->
                                <div v-if="todosClientes.length && buscarCliente"
                                     class="border border-gray-200 rounded-lg overflow-hidden max-h-40 overflow-y-auto">
                                    <button v-for="c in todosClientes" :key="c.id"
                                            type="button"
                                            @click="seleccionarCliente(c)"
                                            :class="['w-full text-left px-3 py-2 text-sm hover:bg-violet-50 transition border-b border-gray-100 last:border-0',
                                                     asignarForm.id_cliente === c.id ? 'bg-violet-50 font-semibold text-violet-700' : 'text-gray-700']">
                                        <span class="font-mono text-xs text-gray-400 mr-2">{{ c.cliente }}</span>
                                        {{ c.razon_social }}
                                    </button>
                                </div>

                                <!-- Cliente seleccionado -->
                                <div v-if="clienteSeleccionado"
                                     class="flex items-center justify-between gap-2 bg-violet-50 border border-violet-200 rounded-lg px-3 py-2 text-sm text-violet-700">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/></svg>
                                        <span class="font-semibold truncate">{{ clienteSeleccionado.razon_social }}</span>
                                    </div>
                                    <button type="button" @click="clienteSeleccionado = null; asignarForm.id_cliente = null"
                                            class="text-violet-400 hover:text-violet-600 shrink-0">✕</button>
                                </div>

                                <!-- Grupo -->
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Grupo de precio</label>
                                    <select v-model="asignarForm.grupo" required
                                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-violet-400 focus:border-transparent outline-none">
                                        <option value="" disabled>Selecciona un grupo...</option>
                                        <option v-for="g in grupos" :key="g.id" :value="g.nombre">
                                            {{ g.nombre }} — {{ g.promo_price }}% descuento
                                        </option>
                                    </select>
                                </div>

                                <!-- Fechas -->
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Desde</label>
                                        <input v-model="asignarForm.fecha_inicio" type="date"
                                               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-violet-400 outline-none" />
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Hasta</label>
                                        <input v-model="asignarForm.fecha_fin" type="date"
                                               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-violet-400 outline-none" />
                                    </div>
                                </div>

                                <button type="submit"
                                        :disabled="!asignarForm.id_cliente || !asignarForm.grupo"
                                        class="w-full py-2.5 bg-violet-600 hover:bg-violet-500 disabled:opacity-40 disabled:cursor-not-allowed text-white font-bold rounded-xl shadow transition">
                                    Asignar grupo
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Tabla de asignaciones activas -->
                    <div class="lg:col-span-2">
                        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                                <h2 class="font-bold text-gray-800">Clientes con grupo activo</h2>
                                <span class="text-xs text-gray-400">Hoy: {{ today }}</span>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="w-full text-sm text-left">
                                    <thead class="bg-gray-50 text-xs uppercase tracking-wider text-gray-500 border-b border-gray-200">
                                        <tr>
                                            <th class="px-5 py-3">Cliente</th>
                                            <th class="px-5 py-3">Grupo</th>
                                            <th class="px-5 py-3">Descuento</th>
                                            <th class="px-5 py-3">Vigencia</th>
                                            <th class="px-5 py-3 text-right">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        <tr v-for="a in clientesConGrupo.data" :key="a.id"
                                            class="hover:bg-gray-50 transition">
                                            <td class="px-5 py-3">
                                                <div class="font-semibold text-gray-800">{{ a.cliente?.razon_social }}</div>
                                                <div class="text-xs text-gray-400 font-mono">{{ a.cliente?.cliente }}</div>
                                            </td>
                                            <td class="px-5 py-3">
                                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-violet-100 text-violet-700 border border-violet-200">
                                                    {{ a.promocion?.nombre }}
                                                </span>
                                            </td>
                                            <td class="px-5 py-3 font-bold text-violet-700">
                                                {{ a.promocion?.promo_price }}%
                                            </td>
                                            <td class="px-5 py-3 text-xs text-gray-500 font-mono">
                                                <div>{{ formatFecha(a.fecha_inicio) }}</div>
                                                <div>{{ formatFecha(a.fecha_fin) }}</div>
                                            </td>
                                            <td class="px-5 py-3 text-right">
                                                <button @click="remover(a.id_cliente)"
                                                        class="text-red-500 hover:text-red-400 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-lg text-xs font-bold transition">
                                                    Quitar
                                                </button>
                                            </td>
                                        </tr>
                                        <tr v-if="!clientesConGrupo.data?.length">
                                            <td colspan="5" class="px-5 py-10 text-center text-gray-400 italic">
                                                Ningún cliente tiene un grupo de precio activo hoy.
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Paginación -->
                            <div v-if="clientesConGrupo.last_page > 1"
                                 class="px-5 py-4 border-t border-gray-100 flex items-center justify-between text-sm text-gray-500 bg-gray-50/50">
                                <span>Página {{ clientesConGrupo.current_page }} de {{ clientesConGrupo.last_page }}</span>
                                <div class="flex gap-1">
                                    <component v-for="(link, i) in clientesConGrupo.links" :key="i"
                                               :is="link.url ? Link : 'span'"
                                               :href="link.url"
                                               class="px-3 py-1.5 rounded text-sm border transition"
                                               :class="[link.active ? 'bg-violet-600 border-violet-500 text-white' : 'bg-white border-gray-300 text-gray-600 hover:bg-gray-50', !link.url && 'opacity-50 cursor-not-allowed']"
                                               v-html="link.label">
                                    </component>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

        </div>

        <!-- ══════════════════════════════════════════════════════════ -->
        <!-- MODAL — Crear nuevo grupo de precio                        -->
        <!-- ══════════════════════════════════════════════════════════ -->
        <Teleport to="body">
            <Transition enter-active-class="transition duration-200 ease-out"
                        enter-from-class="opacity-0"
                        enter-to-class="opacity-100"
                        leave-active-class="transition duration-150 ease-in"
                        leave-from-class="opacity-100"
                        leave-to-class="opacity-0">
                <div v-if="mostrarModalCrear"
                     class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm px-4"
                     @click.self="cerrarModal">

                    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-8 relative">

                        <!-- Encabezado -->
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-xl font-extrabold text-gray-800 flex items-center gap-2">
                                <span class="w-2 h-6 rounded bg-violet-500"></span>
                                Nuevo Grupo de Precio
                            </h2>
                            <button @click="cerrarModal"
                                    class="text-gray-400 hover:text-gray-600 transition text-xl leading-none">&times;</button>
                        </div>

                        <!-- Formulario -->
                        <form @submit.prevent="crearGrupo" class="space-y-5">

                            <!-- Nombre -->
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">
                                    Nombre del grupo <span class="text-red-400">*</span>
                                </label>
                                <input v-model="nuevoGrupoForm.nombre"
                                       type="text" required maxlength="50"
                                       placeholder="Ej: GP7"
                                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-violet-400 focus:border-transparent outline-none uppercase" />
                            </div>

                            <!-- Descripción -->
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Descripción</label>
                                <input v-model="nuevoGrupoForm.descripcion"
                                       type="text" maxlength="255"
                                       placeholder="Ej: Clientes especiales canal moderno"
                                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-violet-400 focus:border-transparent outline-none" />
                            </div>

                            <!-- Porcentaje -->
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">
                                    Porcentaje de descuento <span class="text-red-400">*</span>
                                </label>
                                <div class="relative">
                                    <input v-model="nuevoGrupoForm.promo_price"
                                           type="number" required min="0" max="100" step="0.01"
                                           placeholder="Ej: 5"
                                           class="w-full rounded-lg border border-gray-300 px-3 py-2 pr-8 text-sm focus:ring-2 focus:ring-violet-400 focus:border-transparent outline-none" />
                                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 font-bold text-sm">%</span>
                                </div>
                            </div>

                            <!-- Sublíneas incluidas -->
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">
                                    Sublíneas incluidas
                                    <span class="text-xs font-normal normal-case text-gray-400 ml-1">(vacío = aplica a todos)</span>
                                </label>
                                <TagInput v-model="nuevoGrupoForm.sublineas_inc"
                                          placeholder="Ej: CLAMATO" color="blue" />
                                <p class="text-xs text-gray-400 mt-1">Escribe el código y presiona Enter para agregar.</p>
                            </div>

                            <!-- Acciones -->
                            <div class="flex gap-3 pt-2">
                                <button type="button" @click="cerrarModal"
                                        class="flex-1 py-2.5 rounded-xl border border-gray-300 text-sm font-semibold text-gray-600 hover:bg-gray-50 transition">
                                    Cancelar
                                </button>
                                <button type="submit"
                                        class="flex-1 py-2.5 rounded-xl bg-violet-600 hover:bg-violet-500 text-white text-sm font-bold shadow transition">
                                    Crear grupo
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </Transition>
        </Teleport>

    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import TagInput from '@/Components/TagInput.vue';

 const props = defineProps({
    grupos:            Array,
    reglas:            Array,
    clientesConGrupo:  Object,
    todosClientes:     Array,
    filters:           Object,
});

// ── Tabs ─────────────────────────────────────────────────────────────────────
const tabs = [
    { id: 'grupos',       label: 'Grupos & Descuentos'   },
    { id: 'reglas',       label: 'Reglas por Grupo'       },
    { id: 'asignaciones', label: 'Asignación de Clientes' },
];
const activeTab = ref('grupos');

const today = new Date().toLocaleDateString('es-MX', { day: '2-digit', month: 'short', year: 'numeric' });

// ── TAB 1: Porcentaje por grupo ───────────────────────────────────────────────
const porcentajeEdit = ref({});

// ── Modal: Crear nuevo grupo ──────────────────────────────────────────────────
const mostrarModalCrear = ref(false);
const nuevoGrupoForm = ref({ nombre: '', descripcion: '', promo_price: '', sublineas_inc: [] });

const cerrarModal = () => {
    mostrarModalCrear.value = false;
    nuevoGrupoForm.value = { nombre: '', descripcion: '', promo_price: '', sublineas_inc: [] };
};

const crearGrupo = () => {
    router.post(route('grupos-precio.crear'), {
        ...nuevoGrupoForm.value,
        nombre: nuevoGrupoForm.value.nombre.toUpperCase().trim(),
    }, {
        preserveScroll: true,
        onSuccess: cerrarModal,
    });
};

const guardarPorcentaje = (grupo) => {
    const val = porcentajeEdit.value[grupo.id];
    if (val === undefined || val === '') return;
    router.patch(route('grupos-precio.porcentaje', grupo.id), {
        promo_price: val,
        descripcion: grupo.descripcion,
    }, {
        preserveScroll: true,
        onSuccess: () => { porcentajeEdit.value[grupo.id] = ''; },
    });
};

// ── TAB 2: Reglas por grupo ───────────────────────────────────────────────────
const reglaActiva = ref(props.reglas?.[0]?.grupo ?? null);

const reglaSeleccionada = computed(() =>
    props.reglas.find(r => r.grupo === reglaActiva.value)
);

const reglaForm = ref({
    lineas:               [],
    sublineas_inc:        [],
    sublineas_exc:        [],
    skus_inc:             [],
    skus_exc:             [],
    sublineas_rates_lista: [],  // [{ sublinea: 'CLAMATO', rate: 5 }, ...]
    lineas_rates_lista:    [],  // [{ linea: '141-NISSIN', rate: 3 }, ...]
});

const ratesToSublineasLista = (rates) => {
    if (!rates || typeof rates !== 'object') return [];
    return Object.entries(rates).map(([sublinea, rate]) => ({ sublinea, rate }));
};

const ratesToLineasLista = (rates) => {
    if (!rates || typeof rates !== 'object') return [];
    return Object.entries(rates).map(([linea, rate]) => ({ linea, rate }));
};

watch(reglaSeleccionada, (r) => {
    if (!r) return;
    reglaForm.value = {
        lineas:                [...(r.lineas        ?? [])],
        sublineas_inc:         [...(r.sublineas_inc ?? [])],
        sublineas_exc:         [...(r.sublineas_exc ?? [])],
        skus_inc:              [...(r.skus_inc      ?? [])],
        skus_exc:              [...(r.skus_exc      ?? [])],
        sublineas_rates_lista: ratesToSublineasLista(r.sublineas_rates),
        lineas_rates_lista:    ratesToLineasLista(r.lineas_rates),
    };
}, { immediate: true });

const agregarTasa      = () => reglaForm.value.sublineas_rates_lista.push({ sublinea: '', rate: 0 });
const agregarTasaLinea = () => reglaForm.value.lineas_rates_lista.push({ linea: '', rate: 0 });

const guardarRegla = () => {
    // Convertir listas → objetos { KEY: % }
    const sublineas_rates = {};
    for (const item of reglaForm.value.sublineas_rates_lista) {
        if (item.sublinea?.trim()) sublineas_rates[item.sublinea.trim().toUpperCase()] = item.rate;
    }
    const lineas_rates = {};
    for (const item of reglaForm.value.lineas_rates_lista) {
        if (item.linea?.trim()) lineas_rates[item.linea.trim()] = item.rate;
    }
    router.put(route('grupos-precio.update-regla', reglaActiva.value), {
        lineas:          reglaForm.value.lineas,
        sublineas_inc:   reglaForm.value.sublineas_inc,
        sublineas_exc:   reglaForm.value.sublineas_exc,
        skus_inc:        reglaForm.value.skus_inc,
        skus_exc:        reglaForm.value.skus_exc,
        sublineas_rates,
        lineas_rates,
    }, { preserveScroll: true });
};

// ── TAB 3: Asignación de clientes ─────────────────────────────────────────────
const buscarCliente      = ref(props.filters?.buscar_cliente ?? '');
const clienteSeleccionado = ref(null);

const asignarForm = ref({
    id_cliente:   null,
    grupo:        '',
    fecha_inicio: new Date().toISOString().split('T')[0],
    fecha_fin:    '2030-12-31',
});

let debounceTimer = null;
const buscarDebounced = () => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        router.get(route('grupos-precio.index'),
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
    router.post(route('grupos-precio.asignar'), asignarForm.value, {
        preserveScroll: true,
        onSuccess: () => {
            clienteSeleccionado.value = null;
            asignarForm.value = {
                id_cliente:   null,
                grupo:        '',
                fecha_inicio: new Date().toISOString().split('T')[0],
                fecha_fin:    '2030-12-31',
            };
        },
    });
};

const remover = (idCliente) => {
    if (!confirm('¿Quitar el grupo de precio a este cliente?')) return;
    router.delete(route('grupos-precio.remover', idCliente), { preserveScroll: true });
};

const formatFecha = (f) => f ? new Date(f).toLocaleDateString('es-MX') : '—';
</script>
