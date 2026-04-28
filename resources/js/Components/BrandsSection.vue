<script setup>
// BrandsSection.vue
import { ref, onMounted } from 'vue';

const brands = ref([
    {
        name: 'Peñafiel',
        description: 'Agua mineral originaria de México, reconocida por su pureza.',
        logo: 'https://images.unsplash.com/photo-1596547610015-84080dc8fc5a?q=80&w=600&auto=format&fit=crop', // Placeholder for a bottle/drink
        color: '#0055A5'
    },
    {
        name: 'Red Bull',
        description: 'Bebida energética líder a nivel mundial que revitaliza cuerpo y mente.',
        logo: 'https://distribucionesizquierdo.es/wp-content/uploads/2022/07/Captura-de-pantalla-2022-07-19-a-las-8.53.12.jpg', // Can of energy drink
        color: '#1a3962'
    },
    {
        name: 'Donde',
        description: 'Productos de calidad que acompañan a las familias mexicanas.',
        logo: 'https://newspack-yucatan.s3.amazonaws.com/uploads/2021/05/DON-DY-Arte-1-CD-26.90x26.19cm.jpg', // Generic products
        color: '#CD2027'
    }
]);

const isVisible = ref(false);

const handleScroll = () => {
    const el = document.getElementById('marcas');
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
    <section id="marcas" class="brands-section" :class="{ 'visible': isVisible }">
        <div class="container">
            <div class="header-content">
                <span class="badge">Nuestros Socios</span>
                <h2 class="section-title">Marcas con <span>Valor</span></h2>
                <div class="divider"></div>
                <p class="section-desc">
                    Grupo MV es Distribuidor Exclusivo, está unido con socios comerciales como <strong>Peñafiel</strong>, <strong>Red Bull</strong>, <strong>La Suprema</strong> entre otros, con la finalidad de desarrollar más mercado con cada uno de sus productos.
                </p>
            </div>
            
            <div class="brands-grid">
                <div v-for="(brand, index) in brands" :key="brand.name" class="brand-card" :style="{ animationDelay: `${index * 0.2}s` }">
                    <div class="card-image-wrapper">
                        <img :src="brand.logo" :alt="brand.name" class="brand-img" />
                        <div class="overlay" :style="{ background: `linear-gradient(to top, ${brand.color}e6, transparent)` }"></div>
                    </div>
                    <div class="card-content">
                        <h3>{{ brand.name }}</h3>
                        <p>{{ brand.description }}</p>
                        <button class="brand-btn" :style="{ color: brand.color, borderBottomColor: brand.color }">Ver Productos</button>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<style scoped>
.brands-section {
    padding: 100px 5%;
    background-color: #080c13;
    color: white;
    font-family: 'Inter', 'Segoe UI', sans-serif;
    position: relative;
    overflow: hidden;
    opacity: 0;
    transform: translateY(40px);
    transition: opacity 1s ease-out, transform 1s ease-out;
}

.brands-section.visible {
    opacity: 1;
    transform: translateY(0);
}

.brands-section::before {
    content: '';
    position: absolute;
    top: -20%;
    left: -10%;
    width: 50%;
    height: 60%;
    background: radial-gradient(circle, rgba(0, 86, 179, 0.1) 0%, transparent 60%);
    z-index: 0;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    position: relative;
    z-index: 1;
}

.header-content {
    text-align: center;
    margin-bottom: 80px;
    max-width: 800px;
    margin-left: auto;
    margin-right: auto;
}

.badge {
    display: inline-block;
    padding: 8px 16px;
    background: rgba(50, 86, 220, 0.1);
    color: #3256dc;
    border: 1px solid rgba(50, 86, 220, 0.2);
    border-radius: 50px;
    font-size: 0.9rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 2px;
    margin-bottom: 20px;
}

.section-title {
    font-size: 3.5rem;
    font-weight: 800;
    margin-bottom: 20px;
    color: #ffffff;
}

.section-title span {
    color: transparent;
    -webkit-text-stroke: 1px #3256dc;
    position: relative;
}

.section-title span::after {
    content: 'Valor';
    position: absolute;
    left: 0;
    top: 0;
    color: #3256dc;
    filter: blur(15px);
    opacity: 0.5;
    z-index: -1;
}

.divider {
    height: 4px;
    width: 60px;
    background: linear-gradient(90deg, #3256dc, transparent);
    margin: 0 auto 30px auto;
    border-radius: 2px;
}

.section-desc {
    font-size: 1.25rem;
    line-height: 1.7;
    color: #a0aabf;
    font-weight: 300;
}

.section-desc strong {
    color: #ffffff;
    font-weight: 600;
}

.brands-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 40px;
}

.brand-card {
    background: rgba(255, 255, 255, 0.03);
    border-radius: 20px;
    overflow: hidden;
    border: 1px solid rgba(255, 255, 255, 0.05);
    transition: all 0.4s ease;
    backdrop-filter: blur(10px);
    opacity: 0;
    transform: translateY(30px);
}

.brands-section.visible .brand-card {
    animation: fadeUpIn 0.8s ease forwards;
}

@keyframes fadeUpIn {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.brand-card:hover {
    transform: translateY(-10px) scale(1.02) !important;
    border-color: rgba(50, 86, 220, 0.3);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
}

.card-image-wrapper {
    position: relative;
    height: 250px;
    overflow: hidden;
}

.brand-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.6s ease;
}

.brand-card:hover .brand-img {
    transform: scale(1.1);
}

.overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 1;
}

.card-content {
    padding: 30px;
    position: relative;
    z-index: 2;
    background: linear-gradient(to bottom, rgba(16, 22, 34, 0.8), rgba(16, 22, 34, 1));
}

.card-content h3 {
    font-size: 1.8rem;
    font-weight: 700;
    margin-bottom: 15px;
    color: white;
}

.card-content p {
    color: #a0aabf;
    line-height: 1.6;
    margin-bottom: 25px;
    font-size: 1rem;
}

.brand-btn {
    background: transparent;
    border: none;
    border-bottom: 2px solid;
    padding-bottom: 5px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.brand-btn:hover {
    padding-left: 10px;
    padding-right: 10px;
    border-radius: 5px;
    background: rgba(255,255,255,0.05);
}

@media (max-width: 768px) {
    .section-title {
        font-size: 2.5rem;
    }
    .brands-section {
        padding: 60px 5%;
    }
}
</style>
