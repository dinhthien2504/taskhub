<template>
    <client_header />
    <div class="container-fuild">
        <router-view></router-view>
    </div>
    <client_footer />
</template>
<script setup>
import client_header from './header.vue'
import client_footer from './footer.vue'
</script>