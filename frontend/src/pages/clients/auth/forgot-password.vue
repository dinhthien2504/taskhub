<template>
    <div class="container my-5">
        <div style="height: 100vh;" class="d-flex align-items-center justify-content-center">
            <div class="auth__content border p-4 rounded d-flex flex-column justify-content-center text-center w-100">
                <form @submit.prevent="forgot_password()">
                    <h3 class="fs-24 text-color text-uppercase mb-4">Lấy Lại Mật Khẩu</h3>
                    <p>Quên mật khẩu? Không vấn đề gì. Chỉ cần cho chúng tôi biết địa chỉ email của bạn và chúng
                        tôi sẽ gửi cho bạn
                        liên kết đặt lại mật khẩu cho phép bạn chọn mật khẩu mới.</p>
                    <!-- Email -->
                    <div class="mb-4 position-relative">
                        <div class="input-group auth__input">
                            <span class="input-group-text">
                                <i class="ri-mail-fill text-muted"></i>
                            </span>

                            <div class="form-floating flex-grow-1">
                                <input type="email" id="email" class="form-control" placeholder="Nhập email..."
                                    v-model="email" />

                                <label for="email">Email</label>
                            </div>
                        </div>
                        <p class="m-0 text-danger text-start" v-if="errors.email">
                            {{
                                Array.isArray(errors.email)
                                    ? errors.email[0]
                                    : errors.email
                            }}
                        </p>
                    </div>

                    <button type="submit" :disabled="is_loading"
                            class="main-btn w-100 radius-25 py-2 fs-18 d-flex align-items-center justify-content-center gap-3">
                            <loading__loader v-if="is_loading" size="20px" color="#fff" border="2px" />
                            <span v-else>Liên Kết Đặt Lại Mật Khẩu</span>
                        </button>
                    <p class="my-3">
                        Quay lại
                        <router-link :to="{ name: 'login' }">
                            <span class="text-color">Đăng nhập</span>
                        </router-link>
                    </p>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from "vue"
import api from "@/configs/api"
import message from "@/utils/message_state"
import loading__loader from '@/components/loading/loading__loader-circle.vue'


const is_loading = ref(false)
const email = ref('')
const errors = ref({})

const validate = () => {
    errors.value = {}

    if (!email.value) {
        errors.value.email = "Email không được để trống."
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) {
        errors.value.email = "Email không hợp lệ."
    }

    if (Object.keys(errors.value).length > 0) {
        return false
    }
    return true
}

const forgot_password = async () => {
    if (!validate()) return

    try {
        is_loading.value = true
        const res = await api.post("/forgot-password", {
            email: email.value
        })

        if (res.status == 200) {
            message.emit("show-message", { title: "Thông báo", text: res.data.status, type: "success" })
            email.value = ''
        }
    } catch (err) {
        const res = err.response.data.message
        message.emit("show-message", { title: "Thông báo", text: res, type: "error" })
    } finally {
        is_loading.value = false
    }
}
</script>
