<template>
    Đây là trang admin nha
</template>
<script setup>
import api from '@/configs/api'
import { onMounted } from 'vue'
const test = async () => {
    try {
        const res = await api.get('/test')
        console.log(res)

    } catch (error) {

    }
}
onMounted(() => {
    test()
})
</script>