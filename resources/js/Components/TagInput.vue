<template>
    <div class="space-y-2">
        <!-- Tags actuales -->
        <div class="flex flex-wrap gap-2 min-h-[36px]">
            <span v-for="tag in modelValue" :key="tag"
                  :class="['inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold border', colorClass.tag]">
                {{ tag }}
                <button type="button" @click="remove(tag)"
                        :class="['ml-1 rounded-full w-4 h-4 flex items-center justify-center transition', colorClass.btn]">
                    &times;
                </button>
            </span>
        </div>

        <!-- Input para agregar -->
        <div class="flex gap-2">
            <input v-model="inputVal"
                   @keydown.enter.prevent="add"
                   :placeholder="placeholder"
                   class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-violet-400 focus:border-transparent outline-none" />
            <button type="button" @click="add"
                    class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-bold rounded-lg border border-gray-300 transition">
                + Agregar
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
    modelValue: { type: Array, default: () => [] },
    placeholder: { type: String, default: '' },
    color: { type: String, default: 'violet' },
});

const emit = defineEmits(['update:modelValue']);

const inputVal = ref('');

const colors = {
    violet: { tag: 'bg-violet-100 text-violet-700 border-violet-200', btn: 'hover:bg-violet-200' },
    blue:   { tag: 'bg-blue-100 text-blue-700 border-blue-200',       btn: 'hover:bg-blue-200'   },
    orange: { tag: 'bg-orange-100 text-orange-700 border-orange-200', btn: 'hover:bg-orange-200' },
    green:  { tag: 'bg-green-100 text-green-700 border-green-200',    btn: 'hover:bg-green-200'  },
    red:    { tag: 'bg-red-100 text-red-700 border-red-200',          btn: 'hover:bg-red-200'    },
};

const colorClass = computed(() => colors[props.color] ?? colors.violet);

const add = () => {
    const val = inputVal.value.trim().toUpperCase();
    if (val && !props.modelValue.includes(val)) {
        emit('update:modelValue', [...props.modelValue, val]);
    }
    inputVal.value = '';
};

const remove = (tag) => {
    emit('update:modelValue', props.modelValue.filter(t => t !== tag));
};
</script>
