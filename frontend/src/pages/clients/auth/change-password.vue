<template>
    <div class="main-content">
        <div class="container-fluid">
            <div class="row">
                <!-- Sidebar -->
                <div class="col-lg-3 col-md-4">
                    <div class="sidebar fade-in">
                        <div class="user-info">
                            <div class="user-avatar">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="user-details">
                                <h6>nhthin346</h6>
                                <a href="#" class="edit-profile">
                                    <i class="fas fa-pencil-alt"></i>
                                    Sửa Hồ Sơ
                                </a>
                            </div>
                        </div>

                        <ul class="sidebar-menu">
                            <li>
                                <a href="#" class="fw-bold">
                                    <i class="ri-user-line"></i>
                                    Tài Khoản Của Tôi
                                </a>
                                <ul class="submenu">
                                    <li>
                                        <router-link class="fw-medium" :to="{ name: 'profile' }">Hồ Sơ</router-link>
                                    </li>
                                </ul>
                                <ul class="submenu">
                                    <li>
                                        <router-link :to="{ name: 'change-password' }" class="fw-bold">Đổi Mật
                                            Khẩu</router-link>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Main Profile Form -->
                <div class="col-lg-9 col-md-8">
                    <div class="profile-section fade-in">
                        <h2 class="section-title">Đổi Mật Khẩu</h2>
                        <p class="section-subtitle">Quản lý thông tin hồ sơ để bảo mật tài khoản</p>

                        <div class="d-flex align-items-center justify-content-center">
                            <div
                                class="auth__content border-0 p-4 rounded d-flex flex-column justify-content-center text-center w-100">
                                <form @submit.prevent="change_password()">
                                    <!-- password old-->
                                    <div class="mb-4 position-relative">
                                        <div class="input-group auth__input">
                                            <span class="input-group-text">
                                                <i class="ri-lock-fill text-muted"></i>
                                            </span>

                                            <div class="form-floating flex-grow-1">
                                                <input :type="showPassword ? 'text' : 'password'" id="current_password"
                                                    class="form-control" placeholder="Nhập mật khẩu hiện tại..."
                                                    v-model="current_password" />

                                                <label for="current_password">Mật Khẩu Cũ</label>
                                            </div>
                                        </div>
                                        <p class="m-0 text-danger text-start" v-if="errors.current_password">
                                            {{
                                                Array.isArray(errors.current_password)
                                                    ? errors.current_password[0]
                                                    : errors.current_password
                                            }}
                                        </p>
                                    </div>
                                    <!-- password new-->
                                    <div class="mb-4 position-relative">
                                        <div class="input-group auth__input">
                                            <span class="input-group-text">
                                                <i class="ri-lock-fill text-muted"></i>
                                            </span>

                                            <div class="form-floating flex-grow-1">
                                                <input :type="showPassword ? 'text' : 'password'" id="password"
                                                    class="form-control" placeholder="Nhập mật khẩu mới..."
                                                    v-model="password" />

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
                                                <input :type="showPassword ? 'text' : 'password'"
                                                    id="password_confirmation" class="form-control"
                                                    placeholder="Nhập lại mật khẩu..."
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
                                                <input type="checkbox" id="show_pass"
                                                    @change="showPassword = !showPassword" />
                                                <div class="checkbox-wrapper">
                                                    <div class="checkbox-bg"></div>
                                                    <svg fill="none" viewBox="0 0 24 24" class="checkbox-icon">
                                                        <path stroke-linejoin="round" stroke-linecap="round"
                                                            stroke-width="3" stroke="currentColor" d="M4 12L10 18L20 6"
                                                            class="check-path"></path>
                                                    </svg>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                    <button type="submit" :disabled="is_loading"
                                        class="main-btn w-100 radius-25 py-2 fs-18 d-flex align-items-center justify-content-center gap-3">
                                        <loading__loader v-if="is_loading" size="20px" color="#fff" border="2px" />
                                        <span v-else>Đổi Mật Khẩu</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { onBeforeMount, ref } from "vue"
import { useRouter } from 'vue-router'
import api from "@/configs/api"
import message from "@/utils/message_state"
import loading__loader from '@/components/loading/loading__loader-circle.vue'
import { clear_user, check_login } from '@/utils/auth_state'

const router = useRouter()
onBeforeMount(() => {
    const token = check_login()
    if (!token) {
        message.emit('show-message', {
            title: 'Thông báo',
            text: 'Vui lòng đăng nhập để thực hiện chức năng này.',
            type: 'warning'
        })
        router.push('/')
    }
})
const is_loading = ref(false)
const current_password = ref(null)
const password = ref(null)
const password_confirmation = ref(null)
const errors = ref({})
const showPassword = ref(false)
const validate = () => {
    errors.value = {}

    if (!current_password.value) {
        errors.value.current_password = "Mật khẩu hiện tại không được để trống."
    }

    if (!password.value) {
        errors.value.password = "Mật khẩu mới không được để trống."
    } else if (password.value.trim().length < 8) {
        errors.value.password = "Mật khẩu mới phải có ít nhất 8 ký tự."
    } else if (!/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/.test(password.value)) {
        errors.value.password = "Mật khẩu phải có ít nhất 1 chữ hoa, 1 chữ thường và 1 ký tự đặc biệt."
    }

    if (password.value === current_password.value) {
        errors.value.password = "Mật khẩu mới không được trùng với mật khẩu cũ."
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

const change_password = async () => {
    if (!validate()) return
    try {
        is_loading.value = true
        const payload = {
            current_password: current_password.value,
            password: password.value,
            password_confirmation: password_confirmation.value
        }

        const res = await api.put("/change-password", payload)

        if (res.status == 200) {
            message.emit("show-message", { title: "Thông báo", text: res.data.message, type: "success" })
            clear_user()
            router.push('/login')
        }
    } catch (error) {
        if (error.response && error.response.status === 422) {
            errors.value = error.response.data.errors
        } else {
            console.warn('Lỗi validation:', error)
        }
    } finally {
        is_loading.value = false
    }
}

</script>