<template>
    <div class="container-fuild position-relative">
        <headerAdmin :toggleMenu="toggleMenu" />
        <div class="d-flex gap-0">
            <sidebarAdmin :showMenu="showMenu" :toggleMenu="toggleMenu" />
            <router-view></router-view>
        </div>
        <footerAdmin />
    </div>
</template>
<script setup>
import headerAdmin from '@/layouts/headerAdmin.vue'
import footerAdmin from '@/layouts/footerAdmin.vue'
import sidebarAdmin from '@/layouts/sidebarAdmin.vue'
import { ref, onMounted, onBeforeUnmount } from 'vue';

const navbarLeft = ref(null);
const showMenu = ref(false);

const toggleMenu = () => {
    showMenu.value = !showMenu.value;
};

const handleClickOutside = (e) => {
    if (
        navbarLeft.value &&
        !navbarLeft.value.contains(e.target) &&
        e.target.id !== 'menu-toggle'
    ) {
        showMenu.value = false;
    }
};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});
onBeforeUnmount(() => {
    document.removeEventListener('click', handleClickOutside);
});
</script>