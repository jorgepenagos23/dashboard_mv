<script setup>
import { ref, onMounted, onUnmounted } from 'vue';

// Datos reactivos para el contenido
const title = "Grupo MV";
const subtitle = "Líderes en Logística y Distribución Integral";
const isVisible = ref(false);
const isScrolled = ref(false);
const isMenuOpen = ref(false);

const handleScroll = () => {
    isScrolled.value = window.scrollY > 50;
};

const toggleMenu = () => {
    isMenuOpen.value = !isMenuOpen.value;
};

onMounted(() => {
    // Activamos la animación de entrada poco después de montar el componente
    setTimeout(() => {
        isVisible.value = true;
    }, 100);
    window.addEventListener('scroll', handleScroll);
    startCarousel();
});

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll);
    stopCarousel();
});

// Lógica del Carrusel
const slides = ref([
    'https://grupomv.mx/images/2025/login2.jpg',
    'https://grupomv.mx/images/2025/login3.jpg',
    'https://grupomv.mx/images/2025/login4.jpg'
]);

const currentSlide = ref(0);
let slideInterval = null;

const startCarousel = () => {
    slideInterval = setInterval(() => {
        nextSlide();
    }, 5000); // Cambiar cada 5 segundos
};

const stopCarousel = () => {
    if (slideInterval) clearInterval(slideInterval);
};

const nextSlide = () => {
    currentSlide.value = (currentSlide.value + 1) % slides.value.length;
};

const setSlide = (index) => {
    currentSlide.value = index;
    stopCarousel();
    startCarousel(); // Reiniciar el contador si apretan manualmente
};
</script>

<template>
    <section id="inicio" class="hero-container">
        <!-- Navegación / Menú de opciones -->
        <header class="top-nav" :class="{ 'scrolled': isScrolled }">
            <div class="logo">
                Grupo MV
            </div>
            <nav class="nav-links" :class="{ 'open': isMenuOpen }">
                <a href="#inicio" class="nav-item" @click="isMenuOpen = false">Inicio</a>
                <a href="#marcas" class="nav-item" @click="isMenuOpen = false">Marcas</a>
                <a href="#cobertura" class="nav-item" @click="isMenuOpen = false">Cobertura</a>
                <a href="#catalogo" class="nav-item" @click="isMenuOpen = false">Catálogo</a>
                <a href="#bolsa-trabajo" class="nav-item" @click="isMenuOpen = false">Bolsa de Trabajo</a>
                <a href="/login" class="nav-item login-link" @click="isMenuOpen = false">Acceder <span class="icon">🔒</span></a>
            </nav>
            <!-- Botón de menú hamburguesa para móviles -->
            <div class="menu-toggle" :class="{ 'active': isMenuOpen }" @click="toggleMenu">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </header>

        <!-- Carrusel de Imágenes de Fondo -->
        <div class="carousel-wrapper">
            <transition-group name="slide" tag="div" class="slide-group">
                <div 
                    v-for="(slide, index) in slides" 
                    :key="slide" 
                    class="carousel-slide" 
                    v-show="currentSlide === index"
                >
                    <img :src="slide" alt="Banner de Grupo MV" class="bg-img" />
                </div>
            </transition-group>
            
            <!-- Capa oscura para mejorar legibilidad del texto -->
            <div class="overlay"></div>

            <!-- Indicadores del Carrusel -->
            <div class="carousel-indicators">
                <span 
                    v-for="(slide, index) in slides" 
                    :key="'ind-'+index" 
                    class="indicator" 
                    :class="{ 'active': currentSlide === index }"
                    @click="setSlide(index)"
                ></span>
            </div>
        </div>

        <!-- Contenido Principal -->
        <div class="content" :class="{ 'fade-in': isVisible }">
            <h1 class="main-title">{{ title }}</h1>
            <p class="sub-title">{{ subtitle }}</p>
            
            <div class="actions">
                <button class="btn btn-primary">Nuestros Servicios</button>
                <button class="btn btn-outline">Contáctanos</button>
            </div>
        </div>
    </section>
</template>

<style scoped>
/* Estilos modernos con CSS puro */

.hero-container {
    position: relative;
    width: 100%;
    height: 80vh; /* Ajustado de 100vh a 80vh por solicitud del usuario */
    min-height: 500px; /* Asegura que siga viendose bien en pantallas muy anchas */
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
}

/* --- Menú de Opciones / Navegación --- */
.top-nav {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    padding: 25px 50px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    z-index: 50;
    transition: all 0.4s ease;
    background: transparent;
}

.top-nav.scrolled {
    background: rgba(10, 15, 25, 0.85);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    padding: 15px 50px;
    box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
}

.logo {
    font-size: 2rem;
    font-weight: 800;
    color: #ffffff;
    letter-spacing: 1px;
    text-shadow: 0 2px 4px rgba(0,0,0,0.5);
    z-index: 51;
}

.nav-links {
    display: flex;
    gap: 35px;
}

.nav-item {
    color: white;
    text-decoration: none;
    font-size: 1.1rem;
    font-weight: 500;
    transition: color 0.3s ease;
    position: relative;
    padding-bottom: 5px;
}

.nav-item::after {
    content: '';
    position: absolute;
    width: 0;
    height: 3px;
    bottom: 0;
    left: 0;
    background-color: #3256dc;
    transition: width 0.3s ease;
    border-radius: 2px;
}

.nav-item:hover {
    color: #8ba7ff;
}

.nav-item:hover::after {
    width: 100%;
}

.login-link {
    background: rgba(255, 255, 255, 0.15);
    padding: 8px 18px !important;
    border-radius: 50px;
    border: 1px solid rgba(255, 255, 255, 0.3);
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
}

.login-link::after {
    display: none;
}

.login-link:hover {
    background: rgba(255, 255, 255, 0.25);
    border-color: white;
    transform: translateY(-2px);
    color: white !important;
}

.login-link .icon {
    font-size: 0.9em;
}

.menu-toggle {
    display: none;
    flex-direction: column;
    gap: 6px;
    cursor: pointer;
    z-index: 51;
}

.menu-toggle span {
    width: 30px;
    height: 3px;
    background-color: white;
    border-radius: 2px;
    transition: all 0.3s ease;
}

.menu-toggle.active span:nth-child(1) {
    transform: translateY(9px) rotate(45deg);
}

.menu-toggle.active span:nth-child(2) {
    opacity: 0;
}

.menu-toggle.active span:nth-child(3) {
    transform: translateY(-9px) rotate(-45deg);
}

/* --- Carrusel y Video --- */
.carousel-wrapper {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 0;
    background-color: #080c13;
}

.carousel-slide {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    will-change: transform;
}

.bg-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center;
}

.slide-group {
    position: relative;
    width: 100%;
    height: 100%;
    overflow: hidden;
}

.slide-enter-active,
.slide-leave-active {
    transition: transform 1.2s cubic-bezier(0.25, 0.8, 0.25, 1);
}

.slide-enter-from {
    transform: translateX(100%);
}

.slide-leave-to {
    transform: translateX(-100%);
}

.overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(to bottom, rgba(10, 15, 25, 0.4) 0%, rgba(10, 15, 25, 0.8) 100%);
    z-index: 1;
}

.carousel-indicators {
    position: absolute;
    bottom: 30px;
    left: 0;
    right: 0;
    display: flex;
    justify-content: center;
    gap: 12px;
    z-index: 3;
}

.indicator {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background-color: rgba(255, 255, 255, 0.4);
    cursor: pointer;
    transition: all 0.3s ease;
}

.indicator.active {
    background-color: #3256dc;
    width: 25px;
    border-radius: 6px;
}

.content {
    position: relative;
    z-index: 2;
    text-align: center;
    color: white;
    opacity: 0;
    transform: translateY(30px);
    transition: opacity 1.2s ease-out, transform 1.2s ease-out;
    padding: 0 20px;
}

.content.fade-in {
    opacity: 1;
    transform: translateY(0);
}

.main-title {
    font-size: 4.5rem;
    font-weight: 800;
    margin-bottom: 1rem;
    text-shadow: 0 4px 10px rgba(0,0,0,0.4);
    letter-spacing: -1px;
}

.sub-title {
    font-size: 1.6rem;
    font-weight: 300;
    margin-bottom: 2.5rem;
    max-width: 750px;
    margin-left: auto;
    margin-right: auto;
    line-height: 1.6;
    text-shadow: 0 2px 4px rgba(0,0,0,0.5);
}

.actions {
    display: flex;
    gap: 1.5rem;
    justify-content: center;
}

.btn {
    padding: 16px 36px;
    border-radius: 50px;
    font-size: 1rem;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s ease;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    border: none;
}

.btn-primary {
    background-color: #3256dc;
    color: white;
    box-shadow: 0 6px 20px rgba(50, 86, 220, 0.5);
}

.btn-primary:hover {
    background-color: #2643a8;
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(50, 86, 220, 0.6);
}

.btn-outline {
    background-color: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(5px);
    border: 2px solid white;
    color: white;
}

.btn-outline:hover {
    background-color: white;
    color: #000;
    transform: translateY(-3px);
}

/* Ajustes para móviles */
@media (max-width: 768px) {
    .top-nav {
        padding: 20px;
    }
    
    .top-nav.scrolled {
        padding: 15px 20px;
    }

    .nav-links {
        position: fixed;
        top: 0;
        right: -100%;
        height: 100vh;
        width: 100%;
        background: rgba(10, 15, 25, 0.98);
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        gap: 40px;
        transition: right 0.4s ease-in-out;
        z-index: 50;
    }

    .nav-links.open {
        right: 0;
    }

    .nav-item {
        font-size: 1.8rem;
    }

    .menu-toggle {
        display: flex;
    }

    .main-title {
        font-size: 3rem;
    }
    
    .sub-title {
        font-size: 1.2rem;
    }
    
    .actions {
        flex-direction: column;
        gap: 1rem;
    }
}
</style>