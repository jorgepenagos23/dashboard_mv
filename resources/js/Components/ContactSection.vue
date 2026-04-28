<script setup>
import { useForm, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const form = useForm({
    name: '',
    phone: '',
    email: '',
    service: '',
    message: ''
});

const page = usePage();

// Para mostrar mensajes de éxito o error que vienen del backend
const status = computed(() => page.props.status);
const errorMsg = computed(() => page.props.error);

const isVisible = ref(false);

const handleScroll = () => {
    const el = document.getElementById('contacto');
    if (el) {
        const rect = el.getBoundingClientRect();
        if (rect.top < window.innerHeight - 100) {
            isVisible.value = true;
        }
    }
};

import { onMounted } from 'vue';

onMounted(() => {
    window.addEventListener('scroll', handleScroll);
    handleScroll();
});

const submit = () => {
    form.post('/contact', {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            // Desaparecer el mensaje de éxito después de unos segundos
            setTimeout(() => {
                page.props.status = null;
            }, 5000);
        },
    });
};
</script>

<template>
    <section id="contacto" class="contact-section" :class="{ 'visible': isVisible }">
        <div class="container">
            <div class="contact-content">
                <div class="contact-info">
                    <span class="badge">Atención a Clientes</span>
                    <h2 class="title">Potencia tu Logística con <br><span>Nosotros</span></h2>
                    <p class="desc">
                        ¿Interesado en nuestros servicios de porteo, distribución o venta? Déjanos tus datos y un especialista se pondrá en contacto contigo de inmediato.
                    </p>
                    <div class="info-items">
                        <div class="info-item">
                            <div class="icon">📍</div>
                            <div>
                                <h4>Múltiples Puntos de Cobertura</h4>
                                <p>Presencia clave en el estado de Chiapas.</p>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="icon">📧</div>
                            <div>
                                <h4>Soporte Rápido</h4>
                                <p>Respuesta garantizada en menos de 24 horas.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="contact-form-wrapper">
                    <!-- Mensajes de estado -->
                    <div v-if="status" class="alert alert-success">
                        {{ status }}
                    </div>
                    <div v-if="errorMsg" class="alert alert-error">
                        {{ errorMsg }}
                    </div>

                    <form @submit.prevent="submit" class="contact-form">
                        <div class="form-group">
                            <label for="name">Nombre Completo</label>
                            <input 
                                id="name" 
                                v-model="form.name" 
                                type="text" 
                                placeholder="Tu nombre" 
                                :disabled="form.processing"
                                required
                            />
                            <span class="error-text" v-if="form.errors.name">{{ form.errors.name }}</span>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="phone">Teléfono</label>
                                <input 
                                    id="phone" 
                                    v-model="form.phone" 
                                    type="tel" 
                                    placeholder="Tu teléfono" 
                                    :disabled="form.processing"
                                    required
                                />
                                <span class="error-text" v-if="form.errors.phone">{{ form.errors.phone }}</span>
                            </div>
                            <div class="form-group">
                                <label for="email">Correo Electrónico</label>
                                <input 
                                    id="email" 
                                    v-model="form.email" 
                                    type="email" 
                                    placeholder="tu@correo.com" 
                                    :disabled="form.processing"
                                    required
                                />
                                <span class="error-text" v-if="form.errors.email">{{ form.errors.email }}</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="service">Servicio de Interés</label>
                            <div class="select-wrapper">
                                <select id="service" v-model="form.service" required :disabled="form.processing">
                                    <option value="" disabled selected>Selecciona una opción</option>
                                    <option value="Porteo">Servicio de Porteo</option>
                                    <option value="Distribucion">Distribución de Productos</option>
                                    <option value="Ventas">Atención a Ventas</option>
                                    <option value="Otro">Otro servicio</option>
                                </select>
                            </div>
                            <span class="error-text" v-if="form.errors.service">{{ form.errors.service }}</span>
                        </div>

                        <div class="form-group">
                            <label for="message">Mensaje Adicional</label>
                            <textarea 
                                id="message" 
                                v-model="form.message" 
                                rows="4" 
                                placeholder="Cuentanos un poco más sobre tus necesidades"
                                :disabled="form.processing"
                                required
                            ></textarea>
                            <span class="error-text" v-if="form.errors.message">{{ form.errors.message }}</span>
                        </div>

                        <button 
                            type="submit" 
                            class="submit-btn" 
                            :class="{ 'processing': form.processing }"
                            :disabled="form.processing"
                        >
                            {{ form.processing ? 'Enviando...' : 'Solicitar Información' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>
</template>

<style scoped>
.contact-section {
    padding: 100px 5%;
    background-color: #0f172a; /* Fondo oscuro */
    color: white;
    font-family: 'Inter', 'Segoe UI', sans-serif;
    opacity: 0;
    transform: translateY(40px);
    transition: opacity 1s ease-out, transform 1s ease-out;
    position: relative;
    overflow: hidden;
}

.contact-section::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -10%;
    width: 60%;
    height: 150%;
    background: radial-gradient(circle, rgba(50, 86, 220, 0.15) 0%, rgba(15, 23, 42, 0) 70%);
    z-index: 0;
}

.contact-section.visible {
    opacity: 1;
    transform: translateY(0);
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    position: relative;
    z-index: 1;
}

.contact-content {
    display: flex;
    flex-wrap: wrap;
    gap: 60px;
    align-items: center;
}

.contact-info {
    flex: 1;
    min-width: 300px;
}

.badge {
    display: inline-block;
    padding: 6px 14px;
    background: rgba(50, 86, 220, 0.2);
    color: #8da4ff;
    border: 1px solid rgba(50, 86, 220, 0.3);
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
    color: white;
    margin-bottom: 20px;
    line-height: 1.1;
}

.title span {
    color: #3b82f6; /* Accent color */
}

.desc {
    color: #cbd5e1;
    font-size: 1.1rem;
    line-height: 1.6;
    margin-bottom: 40px;
}

.info-items {
    display: flex;
    flex-direction: column;
    gap: 25px;
}

.info-item {
    display: flex;
    align-items: flex-start;
    gap: 15px;
}

.info-item .icon {
    font-size: 1.5rem;
    background: rgba(255, 255, 255, 0.1);
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
}

.info-item h4 {
    margin: 0 0 5px 0;
    font-size: 1.1rem;
    font-weight: 600;
}

.info-item p {
    margin: 0;
    color: #94a3b8;
    font-size: 0.95rem;
}

.contact-form-wrapper {
    flex: 1;
    min-width: 320px;
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.08);
    backdrop-filter: blur(10px);
    padding: 40px;
    border-radius: 20px;
    box-shadow: 0 25px 50px rgba(0,0,0,0.25);
}

.alert {
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-weight: 500;
    text-align: center;
}

.alert-success {
    background: rgba(16, 185, 129, 0.2);
    color: #10b981;
    border: 1px solid rgba(16, 185, 129, 0.3);
}

.alert-error {
    background: rgba(239, 68, 68, 0.2);
    color: #ef4444;
    border: 1px solid rgba(239, 68, 68, 0.3);
}

.contact-form {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.form-row {
    display: flex;
    gap: 20px;
}

.form-row .form-group {
    flex: 1;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.form-group label {
    font-size: 0.9rem;
    font-weight: 600;
    color: #cbd5e1;
}

.form-group input,
.form-group select,
.form-group textarea {
    background: rgba(15, 23, 42, 0.5);
    border: 1px solid rgba(255, 255, 255, 0.1);
    color: white;
    padding: 14px 16px;
    border-radius: 10px;
    font-size: 1rem;
    font-family: inherit;
    transition: all 0.3s ease;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #3b82f6;
    background: rgba(15, 23, 42, 0.8);
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
}

.form-group select {
    appearance: none;
    width: 100%;
    cursor: pointer;
}

.select-wrapper {
    position: relative;
}

.select-wrapper::after {
    content: '▼';
    font-size: 0.8rem;
    color: #cbd5e1;
    position: absolute;
    right: 16px;
    top: 50%;
    transform: translateY(-50%);
    pointer-events: none;
}

.error-text {
    color: #ef4444;
    font-size: 0.8rem;
    margin-top: 4px;
}

.submit-btn {
    background: linear-gradient(135deg, #3256dc, #2563eb);
    color: white;
    border: none;
    padding: 16px;
    border-radius: 10px;
    font-size: 1.05rem;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-top: 10px;
    display: flex;
    justify-content: center;
    align-items: center;
}

.submit-btn:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(59, 130, 246, 0.4);
}

.submit-btn:active:not(:disabled) {
    transform: translateY(0);
}

.submit-btn:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}

@media (max-width: 768px) {
    .contact-content {
        flex-direction: column;
    }
    
    .title {
        font-size: 2.2rem;
    }
    
    .form-row {
        flex-direction: column;
        gap: 20px;
    }
    
    .contact-form-wrapper {
        padding: 25px;
    }
}
</style>
