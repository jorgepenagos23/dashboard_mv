<script setup>
import { usePage } from '@inertiajs/vue3';
import { computed, ref, onMounted, onUnmounted } from 'vue';

const page = usePage();
const jobs = computed(() => page.props.jobs || []);

const isVisible = ref(false);
const showModal = ref(false);
const activeJob = ref(null);

const handleScroll = () => {
    const el = document.getElementById('bolsa-trabajo');
    if (el) {
        const rect = el.getBoundingClientRect();
        if (rect.top < window.innerHeight - 100) {
            isVisible.value = true;
        }
    }
};

onMounted(() => {
    window.addEventListener('scroll', handleScroll);
    handleScroll();
});

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll);
});

// Ayuda para formato de nueva línea
const formatText = (text) => {
    if (!text) return '';
    return text.replace(/\n/g, '<br />');
};

const openJobDetails = (job) => {
    activeJob.value = job;
    showModal.value = true;
    document.body.style.overflow = 'hidden'; // disable page scrolling
};

const closeJobDetails = () => {
    showModal.value = false;
    setTimeout(() => {
        activeJob.value = null;
    }, 300); // match transition duration
    document.body.style.overflow = ''; // restore scrolling
};

// Formato de fechas
const formatDate = (dateStr) => {
    const date = new Date(dateStr);
    return date.toLocaleDateString('es-MX', { year: 'numeric', month: 'long', day: 'numeric' });
};
</script>

<template>
    <section id="bolsa-trabajo" class="jobs-section" :class="{ 'visible': isVisible }">
        <div class="container">
            <div class="header-center">
                <span class="badge">Bolsa de Trabajo</span>
                <h2 class="title">Únete a Nuestro <span>Equipo</span></h2>
                <div class="divider"></div>
                <p class="desc">Descubre nuestras oportunidades laborales y forma parte de una empresa en constante crecimiento. Estamos buscando talento como el tuyo.</p>
            </div>

            <div v-if="jobs.length > 0" class="jobs-grid">
                <div v-for="job in jobs" :key="job.id" class="job-card" @click="openJobDetails(job)">
                    <div class="job-image" v-if="job.image_path">
                        <img :src="`/storage/${job.image_path}`" alt="Póster de la Vacante" loading="lazy" />
                    </div>
                    <div class="job-image placeholder" v-else>
                        <div class="icon">💼</div>
                    </div>
                    
                    <div class="job-content">
                        <h3>{{ job.title }}</h3>
                        <div class="job-meta">
                            <span v-if="job.location" class="location"><span class="icon">📍</span> {{ job.location }}</span>
                            <span class="date">Publicado: {{ formatDate(job.created_at) }}</span>
                        </div>
                        <p class="excerpt">{{ job.description ? job.description.substring(0, 100) + '...' : '' }}</p>
                        
                        <button class="view-btn">
                            Ver Detalles <span class="arrow">→</span>
                        </button>
                    </div>
                </div>
            </div>

            <div v-else class="empty-state">
                <div class="icon">🔍</div>
                <h3>Por el momento no hay vacantes disponibles</h3>
                <p>Te invitamos a dejar tus datos en el formulario de contacto para futuras oportunidades.</p>
            </div>
        </div>

        <!-- Teleport The Modal Outside the flow to avoid overlap issues -->
        <Teleport to="body">
            <transition name="modal">
                <div v-if="showModal" class="modal-backdrop" @click.self="closeJobDetails">
                    <div class="modal-content">
                        <button class="close-btn" @click="closeJobDetails">×</button>
                        
                        <div class="modal-scroll-area" v-if="activeJob">
                            <div class="modal-header">
                                <div class="modal-image-container" v-if="activeJob.image_path">
                                    <img :src="`/storage/${activeJob.image_path}`" alt="Póster de la Vacante" />
                                </div>
                                <div :class="{'px-content': activeJob.image_path}">
                                    <h3 class="modal-title">{{ activeJob.title }}</h3>
                                    <div class="modal-meta">
                                        <span v-if="activeJob.location"><span class="icon">📍</span> {{ activeJob.location }}</span>
                                        <span>📅 Publicado: {{ formatDate(activeJob.created_at) }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="modal-body px-content">
                                <div class="detail-section">
                                    <h4>Descripción del Puesto</h4>
                                    <p v-html="formatText(activeJob.description)"></p>
                                </div>
                                
                                <div class="detail-section" v-if="activeJob.requirements">
                                    <h4>Requisitos</h4>
                                    <p v-html="formatText(activeJob.requirements)"></p>
                                </div>
                            </div>
                            
                            <div class="modal-footer px-content">
                                <a href="#contacto" class="apply-btn" @click="closeJobDetails">Postularme ahora</a>
                            </div>
                        </div>
                    </div>
                </div>
            </transition>
        </Teleport>
    </section>
</template>

<style scoped>
.jobs-section {
    padding: 100px 5%;
    background-color: #f8fafc; /* Color mas suave que el gris oscuro para resaltar imágenes */
    color: #1e293b;
    font-family: 'Inter', 'Segoe UI', sans-serif;
    opacity: 0;
    transform: translateY(40px);
    transition: opacity 1s ease-out, transform 1s ease-out;
}

.jobs-section.visible {
    opacity: 1;
    transform: translateY(0);
}

.container {
    max-width: 1200px;
    margin: 0 auto;
}

.header-center {
    text-align: center;
    margin-bottom: 50px;
}

.badge {
    display: inline-block;
    padding: 6px 14px;
    background: rgba(50, 86, 220, 0.1);
    color: #3256dc;
    border: 1px solid rgba(50, 86, 220, 0.2);
    border-radius: 50px;
    font-size: 0.85rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 20px;
}

.title {
    font-size: 3rem;
    font-weight: 800;
    color: #0f172a;
    margin-bottom: 15px;
}

.title span {
    color: #3256dc;
}

.divider {
    height: 3px;
    width: 50px;
    background: #3256dc;
    margin: 0 auto 20px auto;
    border-radius: 2px;
}

.desc {
    color: #64748b;
    font-size: 1.1rem;
    max-width: 600px;
    margin: 0 auto;
    line-height: 1.6;
}

/* Grid View Cards */
.jobs-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 30px;
}

.job-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.05);
    border: 1px solid #f1f5f9;
    overflow: hidden;
    transition: all 0.4s ease;
    cursor: pointer;
    display: flex;
    flex-direction: column;
}

.job-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.1);
}

.job-image {
    width: 100%;
    height: 200px;
    overflow: hidden;
}

.job-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.6s ease;
}

.job-card:hover .job-image img {
    transform: scale(1.05);
}

.job-image.placeholder {
    background: linear-gradient(135deg, #e2e8f0, #f8fafc);
    display: flex;
    align-items: center;
    justify-content: center;
}

.job-image.placeholder .icon {
    font-size: 4rem;
    opacity: 0.3;
}

.job-content {
    padding: 25px;
    display: flex;
    flex-direction: column;
    flex: 1;
}

.job-content h3 {
    font-size: 1.25rem;
    font-weight: 700;
    color: #0f172a;
    margin: 0 0 15px 0;
    line-height: 1.3;
}

.job-meta {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-bottom: 15px;
}

.job-meta span {
    font-size: 0.85rem;
    color: #64748b;
}

.excerpt {
    color: #64748b;
    font-size: 0.95rem;
    line-height: 1.6;
    margin-bottom: 25px;
    flex: 1;
}

.view-btn {
    background: transparent;
    border: none;
    color: #3256dc;
    font-weight: 700;
    font-size: 0.95rem;
    padding: 0;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
}

.view-btn .arrow {
    transition: transform 0.3s ease;
}

.job-card:hover .view-btn .arrow {
    transform: translateX(5px);
}

/* Modal Estilos Transparentes / UI Avanzada */
.modal-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(15, 23, 42, 0.7);
    backdrop-filter: blur(8px);
    z-index: 9999; /* Sobre todo */
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.modal-content {
    background: white;
    border-radius: 20px;
    width: 100%;
    max-width: 800px;
    max-height: 90vh; /* Para prevenir que desborde pantalla */
    position: relative;
    display: flex;
    flex-direction: column;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
    overflow: hidden;
}

.close-btn {
    position: absolute;
    top: 15px;
    right: 15px;
    background: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(5px);
    color: white;
    border: none;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    font-size: 1.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    z-index: 10;
    transition: background 0.3s ease;
}

.close-btn:hover {
    background: #ef4444;
}

.modal-scroll-area {
    overflow-y: auto;
    flex: 1;
}

.modal-header {
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
}

.modal-image-container {
    width: 100%;
    max-height: 400px;
    background: #e2e8f0;
    overflow: hidden;
    display: flex;
    justify-content: center;
    align-items: center;
}

.modal-image-container img {
    width: 100%;
    height: auto;
    max-height: 400px;
    object-fit: contain; /* Para que la imagen no se recorte pero se vea entera en el modal */
    background: rgba(0,0,0,0.05);
}

.px-content {
    padding: 30px 40px;
}

/* Solo si no hay imagen usa padding especial al header */
.modal-header > div:not(.modal-image-container) {
    padding: 30px 40px;
}

.modal-title {
    font-size: 2rem;
    font-weight: 800;
    color: #0f172a;
    margin-bottom: 15px;
    line-height: 1.2;
}

.modal-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    color: #64748b;
    font-size: 0.95rem;
}

.modal-body {
    background: white;
}

.detail-section {
    margin-bottom: 30px;
}

.detail-section h4 {
    font-size: 1.2rem;
    font-weight: 700;
    color: #334155;
    margin-bottom: 12px;
}

.detail-section p {
    color: #475569;
    line-height: 1.7;
    font-size: 1.05rem;
}

.modal-footer {
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
    display: flex;
    justify-content: flex-end;
}

.apply-btn {
    display: inline-block;
    background: linear-gradient(135deg, #3256dc, #2563eb);
    color: white;
    text-decoration: none;
    padding: 14px 35px;
    border-radius: 50px;
    font-weight: 600;
    font-size: 1rem;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
}

.apply-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(59, 130, 246, 0.4);
}

/* Modal Transitions */
.modal-enter-active,
.modal-leave-active {
    transition: opacity 0.3s ease;
}

.modal-enter-active .modal-content,
.modal-leave-active .modal-content {
    transition: transform 0.3s ease, opacity 0.3s cubic-bezier(0.5, 0, 0, 1) !important;
}

.modal-enter-from,
.modal-leave-to {
    opacity: 0;
}

.modal-enter-from .modal-content,
.modal-leave-to .modal-content {
    transform: scale(0.95) translateY(20px);
    opacity: 0;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 60px 20px;
    background: white;
    border-radius: 20px;
    border: 1px dashed #cbd5e1;
}

.empty-state .icon {
    font-size: 3rem;
    margin-bottom: 15px;
}

.empty-state h3 {
    font-size: 1.5rem;
    font-weight: 700;
    color: #334155;
    margin-bottom: 10px;
}

.empty-state p {
    color: #64748b;
}

@media (max-width: 768px) {
    .title {
        font-size: 2.3rem;
    }
    
    .px-content, .modal-header > div:not(.modal-image-container) {
        padding: 20px;
    }
    
    .modal-title {
        font-size: 1.5rem;
    }
}
</style>
