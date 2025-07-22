<template>
    <div class="w-100 d-flex align-items-center justify-content-center" style="height: 100vh;">
        <div class="mx-auto mt-16 p-4 max-w-md text-center">
            <h2 class="text-2xl font-bold mb-4">Xác thực Email</h2>

            <div v-if="loading">
                <div class="d-flex align-items-center gap-2">
                    <loading__loader size="30px" border="3px" />
                    <span>Đang xác thực, vui lòng chờ…</span>
                </div>
            </div>

            <div v-else>
                <p v-if="status === 'success'" class="text-success fw-medium">
                    {{ message }}
                </p>
                <p v-else-if="status == 'already'" class="text-primary fw-medium">
                    {{ message }}
                </p>
                <p v-else class="text-danger fw-medium">
                    {{ message }}
                </p>

                <div class="mt-6">
                    <router-link class="main-btn text-white py-1 px-2" :to="{ name: 'home' }">
                        Quay về Trang chủ
                    </router-link>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import loading__loader from '@/components/loading/loading__loader-circle.vue'
import api from '@/configs/api'

const loading = ref(true)
const status = ref(null)
const message = ref('')

const route = useRoute()

onMounted(async () => {
    const { id, hash, expires, signature } = route.query

    if (!id || !hash || !expires || !signature) {
        loading.value = false
        status.value = 'error'
        message.value = 'Link xác thực không hợp lệ (thiếu tham số).'
        return
    }

    try {
        const res = await api.get(`verify-email/${id}/${hash}`, {
            params: {
                expires, signature
            }
        })
        console.log(res);

        if (res.data.message.includes('Email đã được xác thực')) {
            status.value = 'already'
        } else {
            status.value = 'success'
        }
        message.value = res.data.message
    } catch (err) {
        if (err.response?.status === 403) {
            status.value = 'error'
            message.value = err.response.data.message || 'Link đã hết hạn hoặc không hợp lệ.'
        } else if (err.response?.status === 404) {
            status.value = 'error'
            message.value = 'Người dùng không tìm thấy.'
        } else {
            status.value = 'error'
            message.value = err.response?.data.message || 'Lỗi khi xác thực email.'
        }
    } finally {
        loading.value = false
    }
})
</script>
