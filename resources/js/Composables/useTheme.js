import { ref, watch, onMounted } from 'vue';

const isDark = ref(true);
const primaryColor = ref('#000000');
const secondaryColor = ref('#111827');
const textColor = ref('#ffffff');

// Función para actualizar variables CSS globalmente
const updateCssVariables = () => {
    document.documentElement.style.setProperty('--app-bg', primaryColor.value);
    document.documentElement.style.setProperty('--app-card', secondaryColor.value);
    document.documentElement.style.setProperty('--app-text', textColor.value);
};

// Guardar al cambiar y actualizar CSS
watch([primaryColor, secondaryColor, textColor, isDark], () => {
    localStorage.setItem('theme_is_dark', JSON.stringify(isDark.value));
    localStorage.setItem('theme_primary', primaryColor.value);
    localStorage.setItem('theme_secondary', secondaryColor.value);
    localStorage.setItem('theme_text', textColor.value);
    updateCssVariables();
});

export const useTheme = () => {
    const initTheme = () => {
        const savedDark = localStorage.getItem('theme_is_dark');
        const savedPrimary = localStorage.getItem('theme_primary');
        const savedSecondary = localStorage.getItem('theme_secondary');
        const savedText = localStorage.getItem('theme_text');

        if (savedPrimary && savedSecondary && savedText) {
            if (savedDark !== null) isDark.value = JSON.parse(savedDark);
            primaryColor.value = savedPrimary;
            secondaryColor.value = savedSecondary;
            textColor.value = savedText;
        } else {
            // Predeterminado: Interfaz oscura original del proyecto MV
            isDark.value = true;
            primaryColor.value = '#111827'; // gray-900
            secondaryColor.value = '#1f2937'; // gray-800
            textColor.value = '#f3f4f6'; // gray-100
        }

        updateCssVariables();
    };

    const setTheme = (type) => {
        if (type === 'dark') {
            isDark.value = true;
            primaryColor.value = '#111827';
            secondaryColor.value = '#1f2937';
            textColor.value = '#f3f4f6';
        } else if (type === 'light') {
            isDark.value = false;
            primaryColor.value = '#f3f4f6'; // gray-100
            secondaryColor.value = '#ffffff'; // white
            textColor.value = '#1f2937'; // gray-800
        } else if (type === 'blue') {
            isDark.value = true;
            primaryColor.value = '#0f172a'; // slate-900
            secondaryColor.value = '#1e293b'; // slate-800
            textColor.value = '#f8fafc'; // slate-50
        }
    };

    return {
        isDark,
        primaryColor,
        secondaryColor,
        textColor,
        initTheme,
        setTheme,
        updateCssVariables
    };
};
