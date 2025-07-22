<template>
    <div class="d-flex justify-content-center align-items-center min-vh-100 bg-light px-3">
        <div class="card shadow-lg w-100" style="max-width: 500px">
            <div class="card-body text-center p-4">
                <div class="display-4 mb-3">😥</div>
                <h2 class="card-title mb-3">Hủy đăng ký nhận email thông báo sinh nhật.</h2>
                <p class="card-text text-muted mb-4">
                    Bạn có chắc chắn muốn hủy nhận các email sinh nhật từ chúng tôi?
                    Bạn sẽ không còn nhận được lời chúc mừng và ưu đãi đặc biệt vào dịp sinh nhật.
                </p>

                <button @click="unsubscribe" :disabled="is_loading" class="main-btn py-2 w-100 d-flex align-items-center justify-content-center">
                    <loading__loader v-if="is_loading" size="20px" color="#fff" border="2px" />
                    <span v-else>Hủy đăng ký </span>
                </button>

                <div v-if="message" class="alert alert-success mt-4" role="alert">
                    {{ message }}
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { useRoute } from 'vue-router'
import api from '@/configs/api'
import loading__loader from '@/components/loading/loading__loader-circle.vue'


const message = ref('')
const userId = ref(null)
const is_loading = ref(false)
const route = useRoute()

onMounted(() => {
    userId.value = route.query.user_id || null
})

const unsubscribe = async () => {
    if (!userId.value) {
        message.value = 'Không tìm thấy thông tin người dùng.'
        return
    }

    try {
        is_loading.value = true
        await api.post('/unsubscribe-birthdate', { user_id: userId.value })
        message.value = '🎉 Bạn đã hủy đăng ký email sinh nhật thành công.'
    } catch (error) {
        message.value = '❌ Có lỗi xảy ra. Vui lòng thử lại.'
    } finally {
        is_loading.value = false
    }
}
</script>
