<script setup>
// FloatingWhatsApp.vue
</script>

<template>
    <a href="https://wa.me/5211234567890" target="_blank" class="floating-whatsapp" aria-label="Chat en WhatsApp">
        <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" viewBox="0 0 24 24" fill="white">
            <path d="M11.99 2C6.47 2 2 6.48 2 12c0 1.76.46 3.42 1.25 4.89L2 22l5.24-1.2A9.954 9.954 0 0011.99 22c5.52 0 10-4.48 10-10s-4.48-10-10-10zM12 20c-1.55 0-3.03-.39-4.33-1.07l-.31-.17-3.21.73.74-3.13-.18-.3A7.94 7.94 0 014 12c0-4.41 3.59-8 8-8s8 3.59 8 8-3.59 8-8 8zm4.27-5.59c-.23-.12-1.38-.68-1.59-.76-.21-.08-.36-.12-.51.12-.15.24-.61.76-.74.92-.14.15-.27.17-.5.06-.23-.12-1-.37-1.9-1.18-.7-.63-1.17-1.41-1.31-1.65-.13-.24-.01-.36.1-.48.1-.11.23-.27.35-.41.11-.14.15-.24.23-.39.08-.15.04-.29-.02-.41-.06-.12-.51-1.24-.71-1.71-.19-.46-.39-.5-.4-.11-.01-.24-.01-.36-.01-.13 0-.33.05-.5.24-.17.19-.66.65-.66 1.59 0 .94.68 1.84.77 1.97.1.13 1.34 2.11 3.28 2.94.46.2.82.32 1.1.41.46.15.89.13 1.23.08.38-.06 1.17-.48 1.33-.94.17-.46.17-.86.12-.94-.04-.09-.16-.14-.39-.26z" />
        </svg>
    </a>
</template>

<style scoped>
.floating-whatsapp {
    position: fixed;
    bottom: 30px;
    right: 30px;
    width: 65px;
    height: 65px;
    background-color: #25D366;
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
    box-shadow: 0 6px 20px rgba(37, 211, 102, 0.4);
    z-index: 1000;
    transition: all 0.3s ease;
    animation: bounce 2s infinite;
}

.floating-whatsapp:hover {
    transform: scale(1.1) translateY(-5px);
    box-shadow: 0 8px 25px rgba(37, 211, 102, 0.6);
    animation: none;
}

@keyframes bounce {
    0%, 20%, 50%, 80%, 100% {
        transform: translateY(0);
    }
    40% {
        transform: translateY(-15px);
    }
    60% {
        transform: translateY(-7px);
    }
}
</style>
