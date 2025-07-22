<template>
    <div class="loading__loader-circle" :style="styleLoader"></div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    size: {
        type: String,
        default: '5rem'
    },
    border: {
        type: String,
        default: '7px'
    },
    color: {
        type: String,
        default: 'var(--text-color)'
    },
    bgColor: {
        type: String,
        default: 'transparent'
    }
});

const styleLoader = computed(() => ({
    '--dim': props.size,
    '--border': props.border,
    '--borderColor': props.color,
    '--borderBg': props.bgColor
}));
</script>

<style scoped>
.loading__loader-circle {
    width: var(--dim);
    height: var(--dim);
    border: var(--border) solid var(--borderBg);
    border-top-color: var(--borderColor);
    border-bottom-color: var(--borderColor);
    border-radius: 50%;
    animation: spin 1.5s infinite linear;
}

@keyframes spin {
    from {
        transform: rotate(0);
    }

    to {
        transform: rotate(360deg);
    }
}
</style>
