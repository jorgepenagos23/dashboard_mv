<script setup>
import { ref, onMounted, computed } from 'vue';

const activeCategory = ref('Todos');
const categories = ref(['Todos']);
const products = ref([]);

// Filtramos basándonos en 'linea_prod_id'
const filteredProducts = computed(() => {
    if (activeCategory.value === 'Todos') {
        return products.value;
    }
    return products.value.filter(p => p.linea_prod_id === activeCategory.value);
});

const filterCategory = (cat) => {
    activeCategory.value = cat;
};

const isVisible = ref(false);

const handleScroll = () => {
    const el = document.getElementById('catalogo');
    if (el) {
        const rect = el.getBoundingClientRect();
        if (rect.top < window.innerHeight - 100) {
            isVisible.value = true;
        }
    }
};

const fetchProducts = async () => {
    try {
        const response = await fetch('/api/productos/100-ofertar');
        const data = await response.json();
        products.value = data;
        
        // Extraemos las líneas únicas (categorías)
        const uniqueCategories = new Set(data.map(p => p.linea_prod_id).filter(Boolean));
        categories.value = ['Todos', ...Array.from(uniqueCategories)];
    } catch (error) {
        console.error('Error al obtener los productos:', error);
    }
};

onMounted(() => {
    fetchProducts();
    window.addEventListener('scroll', handleScroll);
    handleScroll();
});

</script>

<template>
    <section id="catalogo" class="catalog-section" :class="{ 'visible': isVisible }">
        <div class="container">
            <div class="header-center">
                <span class="badge">Portafolio</span>
                <h2 class="title">Catálogo de <span>Productos</span></h2>
                <div class="divider"></div>
                <p class="desc">Explora la variedad de marcas y productos que distribuimos con los más altos estándares de calidad.</p>
            </div>
            
            <div class="filter-nav">
                <button 
                    v-for="cat in categories" 
                    :key="cat"
                    class="filter-btn" 
                    :class="{ 'active': activeCategory === cat }"
                    @click="filterCategory(cat)"
                >
                    {{ cat }}
                </button>
            </div>
            
            <div class="products-grid">
                <!-- Vueltas de animación simples con el index en filteredProducts -->
                <transition-group name="fade-list" tag="div" class="grid-wrapper">
                    <div v-for="(product) in filteredProducts" :key="product.producto_id" class="product-card">
                        <div class="product-badge">{{ product.sublinea }}</div>
                        <div class="product-img-box">
                            <img :src="product.foto" :alt="product.nombre_corto" loading="lazy" />
                        </div>
                        <div class="product-info">
                            <h4 class="product-title">{{ product.nombre_corto }}</h4>
                            <p class="product-desc">{{ product.nombre }}</p>
                            <span class="product-category">{{ product.linea_prod_id }}</span>
                        </div>
                    </div>
                </transition-group>
            </div>
        </div>
    </section>
</template>

<style scoped>
.catalog-section {
    padding: 100px 5%;
    background-color: #f8fafc;
    color: #1e293b;
    font-family: 'Inter', 'Segoe UI', sans-serif;
    opacity: 0;
    transform: translateY(40px);
    transition: opacity 1s ease-out, transform 1s ease-out;
}

.catalog-section.visible {
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

.filter-nav {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 15px;
    margin-bottom: 50px;
}

.filter-btn {
    background: white;
    border: 1px solid #e2e8f0;
    color: #475569;
    padding: 10px 24px;
    border-radius: 50px;
    font-size: 0.95rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 2px 10px rgba(0,0,0,0.02);
}

.filter-btn:hover {
    border-color: #cbd5e1;
    color: #0f172a;
    transform: translateY(-2px);
}

.filter-btn.active {
    background: #0f172a;
    color: white;
    border-color: #0f172a;
    box-shadow: 0 5px 15px rgba(15, 23, 42, 0.2);
}

.grid-wrapper {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 30px;
}

.product-card {
    background: white;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0,0,0,0.05);
    transition: all 0.4s ease;
    border: 1px solid #f1f5f9;
    position: relative;
    display: flex;
    flex-direction: column;
}

.product-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.08);
}

.product-badge {
    position: absolute;
    top: 15px;
    right: 15px;
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(5px);
    padding: 5px 12px;
    border-radius: 50px;
    font-size: 0.75rem;
    font-weight: 700;
    color: #0f172a;
    z-index: 10;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.product-img-box {
    height: 220px;
    overflow: hidden;
    position: relative;
    background: #f8fafc;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
}

.product-img-box img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.6s ease;
}

.product-card:hover .product-img-box img {
    transform: scale(1.08);
}

.product-info {
    padding: 25px;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.product-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 10px;
    line-height: 1.3;
}

.product-desc {
    color: #64748b;
    font-size: 0.95rem;
    line-height: 1.5;
    margin-bottom: 20px;
    flex: 1;
}

.product-category {
    font-size: 0.8rem;
    color: #94a3b8;
    text-transform: uppercase;
    font-weight: 600;
    letter-spacing: 0.5px;
}

/* Animaciones para la lista */
.fade-list-enter-active,
.fade-list-leave-active {
    transition: all 0.5s ease;
}
.fade-list-enter-from {
    opacity: 0;
    transform: translateY(30px);
}
.fade-list-leave-to {
    opacity: 0;
    transform: scale(0.9);
}
.fade-list-move {
    transition: transform 0.5s ease;
}

@media (max-width: 768px) {
    .title {
        font-size: 2.3rem;
    }
    .catalog-section {
        padding: 60px 5%;
    }
}
</style>
