<template>
    <div class="container my-5">
        <div style="height: 100vh;" class="d-flex align-items-center justify-content-center">
            <div class="auth__content border p-4 rounded d-flex flex-column justify-content-center text-center w-100">
                <form @submit.prevent="password_reset()">
                    <h3 class="fs-24 text-color text-uppercase mb-4">Đặt Lại Mật Khẩu</h3>
                    <!-- password -->
                    <div class="mb-4 position-relative">
                        <div class="input-group auth__input">
                            <span class="input-group-text">
                                <i class="ri-lock-fill text-muted"></i>
                            </span>

                            <div class="form-floating flex-grow-1">
                                <input :type="showPassword ? 'text' : 'password'" id="password" class="form-control"
                                    placeholder="Nhập mật khẩu mới..." v-model="password" />

                                <label for="password">Mật Khẩu Mới</label>
                            </div>
                        </div>
                        <p class="m-0 text-danger text-start" v-if="errors.password">
                            {{
                                Array.isArray(errors.password)
                                    ? errors.password[0]
                                    : errors.password
                            }}
                        </p>
                    </div>
                    <!-- password Confirmation-->
                    <div class="mb-4 position-relative">
                        <div class="input-group auth__input">
                            <span class="input-group-text">
                                <i class="ri-lock-fill text-muted"></i>
                            </span>

                            <div class="form-floating flex-grow-1">
                                <input :type="showPassword ? 'text' : 'password'" id="password_confirmation"
                                    class="form-control" placeholder="Nhập lại mật khẩu..."
                                    v-model="password_confirmation" />

                                <label for="password_confirmation">Nhập Lại Mật Khẩu Mới</label>
                            </div>
                        </div>
                        <p class="m-0 text-danger text-start" v-if="errors.password_confirmation">
                            {{
                                Array.isArray(errors.password_confirmation)
                                    ? errors.password_confirmation[0]
                                    : errors.password_confirmation
                            }}
                        </p>
                    </div>
                    <div class="d-flex align-items-center justify-content-start my-4 gap-2">
                        <label for="show_pass" class="cursor-pointer">Hiện mật khẩu</label>
                        <div class="checkbox">
                            <label class="checkbox-label">
                                <input type="checkbox" id="show_pass" @change="showPassword = !showPassword" />
                                <div class="checkbox-wrapper">
                                    <div class="checkbox-bg"></div>
                                    <svg fill="none" viewBox="0 0 24 24" class="checkbox-icon">
                                        <path stroke-linejoin="round" stroke-linecap="round" stroke-width="3"
                                            stroke="currentColor" d="M4 12L10 18L20 6" class="check-path"></path>
                                    </svg>
                                </div>
                            </label>
                        </div>
                    </div>
                    <button type="submit" :disabled="is_loading"
                        class="main-btn w-100 radius-25 py-2 fs-18 d-flex align-items-center justify-content-center gap-3">
                        <loading__loader v-if="is_loading" size="20px" color="#fff" border="2px" />
                        <span v-else>Cập Nhật Mật Khẩu</span>
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
import { useRouter, useRoute } from 'vue-router'
import api from "@/configs/api"
import message from "@/utils/message_state"
import loading__loader from '@/components/loading/loading__loader-circle.vue'

const route = useRoute()
const router = useRouter()

const is_loading = ref(false)
const password = ref(null)
const password_confirmation = ref(null)
const token = route.params.token
const email = route.query.email
const errors = ref({})
const showPassword = ref(false)
const validate = () => {
    errors.value = {}

    if (!password.value) {
        errors.value.password = "Mật khẩu mới không được để trống."
    } else if (password.value.trim().length < 8) {
        errors.value.password = "Mật khẩu mới phải có ít nhất 8 ký tự."
    } else if (!/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/.test(password.value)) {
        errors.value.password = "Mật khẩu phải có ít nhất 1 chữ hoa, 1 chữ thường và 1 ký tự đặc biệt."
    }
    if (!password_confirmation.value) {
        errors.value.password_confirmation = "Mật khẩu xác nhận không được để trống."
    } else if (password.value !== password_confirmation.value) {
        errors.value.password_confirmation = "Mật khẩu xác nhận không khớp."
    }
    if (Object.keys(errors.value).length > 0) {
        return false
    }
    return true
}

const password_reset = async () => {
    if (!validate()) return
    try {
        is_loading.value = true
        const payload = {
            token: token,
            email: email,
            password: password.value,
            password_confirmation: password_confirmation.value
        }

        const res = await api.post("/reset-password", payload)
        if (res.status == 200) {
            message.emit("show-message", { title: "Thông báo", text: res.data.status, type: "success" })
            router.push('/login')
        }
    } catch (err) {
        errors.value = err.response.data.errors
    } finally {
        is_loading.value = false
    }
}
</script>