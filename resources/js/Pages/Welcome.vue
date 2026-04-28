<script setup>
import { Head } from '@inertiajs/vue3';
import HeroBanner from '../../../HeroBanner.vue';
import BrandsSection from '../Components/BrandsSection.vue';
import CoverageSection from '../Components/CoverageSection.vue';
import CatalogSection from '../Components/CatalogSection.vue';
import ContactSection from '../Components/ContactSection.vue';
import JobBoardSection from '../Components/JobBoardSection.vue';
import FloatingWhatsApp from '../Components/FloatingWhatsApp.vue';
</script>

<template>
    <Head title="Inicio" />
    
    <main>
        <HeroBanner />
        <!-- Nueva sección de marcas con valor -->
        <BrandsSection />
        
        <!-- Sección de cobertura estatal en Chiapas -->
        <CoverageSection />

        <!-- Catálogo de productos y marcas -->
        <CatalogSection />

        <!-- Sección de Bolsa de Trabajo -->
        <JobBoardSection />

        <!-- Sección de Contacto -->
        <ContactSection />

        <!-- Botón de WhatsApp flotante -->
        <FloatingWhatsApp />
    </main>
</template>

<style>
/* Reseteo básico y scroll suave para la navegación de la página */
html {
    scroll-behavior: smooth;
    overflow-x: hidden;
}

body {
    margin: 0;
    padding: 0;
    background-color: #080c13; /* Fondo base oscuro que empalma con la primera sección */
}
</style>
