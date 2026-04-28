<script setup>
import { ref, onMounted } from 'vue';

const regions = ref([
    {
        name: 'CENTRO / FRAYLESCA',
        cities: ['Tuxtla Gutiérrez', 'Cintalapa', 'Villaflores', 'La Concordia'],
        icon: `<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>`,
        delay: 0
    },
    {
        name: 'ITSMO COSTA / SOCONUSCO',
        cities: ['Arriaga', 'Tapachula', 'Tonalá', 'Pijijiapan', 'Huixtla', 'Mapastepec'],
        icon: `<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"></path><line x1="4" y1="22" x2="4" y2="15"></line></svg>`,
        delay: 0.2
    },
    {
        name: 'ALTOS / SELVA',
        cities: ['San Cristóbal de Las Casas', 'Comitán', 'Ocosingo'],
        icon: `<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="14 2 18 6 7 17 3 17 3 13 14 2"></polygon><line x1="3" y1="22" x2="21" y2="22"></line></svg>`,
        delay: 0.4
    }
]);

const isVisible = ref(false);

const handleScroll = () => {
    const el = document.getElementById('cobertura');
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

</script>

<template>
    <section id="cobertura" class="coverage-section" :class="{ 'visible': isVisible }">
        <div class="map-bg"></div>
        
        <div class="container">
            <div class="content-wrapper">
                <div class="header-col">
                    <span class="badge">Alcance</span>
                    <h2 class="title">Distribuimos en todo el estado de <span class="highlight">Chiapas</span></h2>
                    <p class="desc">
                        Nuestra sólida red logística nos permite llegar a cada rincón del estado, garantizando que nuestros productos lleguen con rapidez, frescura y seguridad a todos nuestros clientes comerciales.
                    </p>
                    <div class="stats">
                        <div class="stat-item">
                            <span class="stat-number">3</span>
                            <span class="stat-label">Regiones Clave</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number">13+</span>
                            <span class="stat-label">Ciudades</span>
                        </div>
                    </div>
                </div>
                
                <div class="regions-col">
                    <div class="regions-grid">
                        <div v-for="region in regions" :key="region.name" class="region-card" :style="{ animationDelay: `${region.delay}s` }">
                            <div class="region-header">
                                <div class="icon-box" v-html="region.icon"></div>
                                <h3>{{ region.name }}</h3>
                            </div>
                            <ul class="city-list">
                                <li v-for="city in region.cities" :key="city">
                                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="3" fill="none" stroke-linecap="round" stroke-linejoin="round" class="check-icon"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                    {{ city }}
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Truck Animation Added to the Bottom -->
        <div class="truck-animation">
            <svg class="moving-truck" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" fill="#3256dc">
                <path d="M57.6,33.1l-5-7.5c-0.6-0.9-1.6-1.5-2.6-1.5H38V20c0-2.2-1.8-4-4-4H6c-2.2,0-4,1.8-4,4v20H0v4h2.4c0.5,3.4,3.4,6,6.9,6 s6.4-2.6,6.9-6H40c0.5,3.4,3.4,6,6.9,6s6.4-2.6,6.9-6h8.2V36C62,34.8,61.4,33.7,60.6,33.1z M9.3,47.3c-2,0-3.6-1.6-3.6-3.6 s1.6-3.6,3.6-3.6s3.6,1.6,3.6,3.6S11.3,47.3,9.3,47.3z M38,40H16.2c-0.5-3.4-3.4-6-6.9-6s-6.4,2.6-6.9,6H6V20h28V40z M46.9,47.3 c-2,0-3.6-1.6-3.6-3.6s1.6-3.6,3.6-3.6c2,0,3.6,1.6,3.6,3.6S48.9,47.3,46.9,47.3z M58,40h-4.2c-0.5-3.4-3.4-6-6.9-6s-6.4,2.6-6.9,6 V28h10l5.3,8H58V40z"/>
            </svg>
        </div>
    </section>
</template>

<style scoped>
.coverage-section {
    padding: 120px 5%;
    background: linear-gradient(135deg, #0a0f19 0%, #0d1627 100%);
    color: white;
    font-family: 'Inter', 'Segoe UI', sans-serif;
    position: relative;
    overflow: hidden;
}

.map-bg {
    position: absolute;
    top: 0;
    right: -10%;
    width: 60%;
    height: 100%;
    background-image: radial-gradient(#1e3a8a 1px, transparent 1px);
    background-size: 30px 30px;
    opacity: 0.1;
    z-index: 0;
    transform: perspective(1000px) rotateY(-45deg) scale(1.5);
}

.container {
    max-width: 1300px;
    margin: 0 auto;
    position: relative;
    z-index: 1;
}

.content-wrapper {
    display: flex;
    gap: 60px;
    align-items: center;
}

.header-col {
    flex: 1;
    opacity: 0;
    transform: translateX(-50px);
    transition: all 1s ease-out;
}

.coverage-section.visible .header-col {
    opacity: 1;
    transform: translateX(0);
}

.regions-col {
    flex: 1.2;
}

.badge {
    display: inline-block;
    padding: 6px 14px;
    background: rgba(34, 197, 94, 0.1);
    color: #4ade80;
    border: 1px solid rgba(34, 197, 94, 0.2);
    border-radius: 50px;
    font-size: 0.85rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    margin-bottom: 25px;
}

.title {
    font-size: 3.2rem;
    font-weight: 800;
    line-height: 1.1;
    margin-bottom: 25px;
}

.highlight {
    color: transparent;
    background: linear-gradient(90deg, #3256dc, #4ade80);
    -webkit-background-clip: text;
    background-clip: text;
}

.desc {
    font-size: 1.15rem;
    line-height: 1.7;
    color: #94a3b8;
    margin-bottom: 40px;
}

.stats {
    display: flex;
    gap: 30px;
}

.stat-item {
    display: flex;
    flex-direction: column;
}

.stat-number {
    font-size: 2.5rem;
    font-weight: 800;
    color: #ffffff;
    line-height: 1;
}

.stat-label {
    font-size: 0.9rem;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-top: 5px;
}

.regions-grid {
    display: grid;
    gap: 20px;
}

.region-card {
    background: rgba(255, 255, 255, 0.02);
    border: 1px solid rgba(255, 255, 255, 0.05);
    border-radius: 16px;
    padding: 30px;
    backdrop-filter: blur(10px);
    transition: all 0.4s ease;
    opacity: 0;
    transform: translateX(50px);
}

.coverage-section.visible .region-card {
    animation: slideLeftIn 0.8s ease forwards;
}

@keyframes slideLeftIn {
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.region-card:hover {
    background: rgba(255, 255, 255, 0.04);
    border-color: rgba(50, 86, 220, 0.3);
    transform: translateY(-5px) !important;
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
}

.region-header {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 20px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    padding-bottom: 15px;
}

.icon-box {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    background: rgba(50, 86, 220, 0.1);
    color: #3256dc;
    display: flex;
    align-items: center;
    justify-content: center;
}

.region-header h3 {
    font-size: 1.3rem;
    font-weight: 700;
    color: #f8fafc;
    letter-spacing: 0.5px;
}

.city-list {
    list-style: none;
    padding: 0;
    margin: 0;
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
}

.city-list li {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #cbd5e1;
    font-size: 1rem;
}

.check-icon {
    color: #4ade80;
}

@media (max-width: 1024px) {
    .content-wrapper {
        flex-direction: column;
    }
    .header-col, .regions-col {
        width: 100%;
    }
}

@media (max-width: 640px) {
    .title {
        font-size: 2.5rem;
    }
    .city-list {
        grid-template-columns: 1fr;
    }
}

.truck-animation {
    position: absolute;
    bottom: 10px;
    left: 0;
    width: 100%;
    height: 70px;
    pointer-events: none;
    overflow: hidden;
    z-index: 0;
    opacity: 0.3;
}

.moving-truck {
    width: 70px;
    height: 70px;
    position: absolute;
    bottom: 0;
    left: -100px;
    animation: driveTruck 15s linear infinite;
}

@keyframes driveTruck {
    0% { left: -100px; }
    100% { left: 100%; }
}
</style>
