<template>
    <div class="container my-5">
        <div class="row pb-5">
            <div class="col-lg-5 col-12 d-flex align-items-center justify-content-center">
                <div
                    class="auth__content border p-4 rounded d-flex flex-column justify-content-center text-center w-100">
                    <form @submit.prevent="register">
                        <h3 class="fs-24 text-color text-uppercase mb-4">Đăng Ký</h3>
                        <!-- Username -->
                        <div class="mb-3 position-relative">
                            <div class="input-group auth__input">
                                <span class="input-group-text">
                                    <i class="ri-user-fill text-muted"></i>
                                </span>
                                <div class="form-floating flex-grow-1">
                                    <input type="text" id="username" class="form-control" placeholder=""
                                        v-model="user.name" />
                                    <label for="username">Tên người dùng</label>
                                </div>
                            </div>
                            <p class="m-0 text-danger text-start" v-if="errors.name">
                                {{
                                    Array.isArray(errors.name)
                                        ? errors.name[0]
                                        : errors.name
                                }}
                            </p>
                        </div>
                        <!-- Email -->
                        <div class="mb-3 position-relative">
                            <div class="input-group auth__input">
                                <span class="input-group-text">
                                    <i class="ri-mail-fill text-muted"></i>
                                </span>
                                <div class="form-floating flex-grow-1">
                                    <input type="email" id="email" class="form-control" placeholder=""
                                        v-model="user.email" />
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
                        <!-- Password -->
                        <div class="mb-3 position-relative">
                            <div class="input-group auth__input">
                                <span class="input-group-text">
                                    <i class="ri-lock-fill text-muted"></i>
                                </span>
                                <div class="form-floating flex-grow-1">
                                    <input :type="showPassword ? 'text' : 'password'" id="password" class="form-control"
                                        placeholder="" autocomplete="password" v-model="user.password" />
                                    <label for="password">Mật khẩu</label>
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
                        <!-- Password Confirmation -->
                        <div class="mb-3 position-relative">
                            <div class="input-group auth__input">
                                <span class="input-group-text">
                                    <i class="ri-lock-fill text-muted"></i>
                                </span>
                                <div class="form-floating flex-grow-1">
                                    <input :type="showPassword ? 'text' : 'password'" id="password_confirm"
                                        class="form-control" placeholder="" autocomplete="password_confirmation"
                                        v-model="user.password_confirmation" />
                                    <label for="password_confirm">Xác nhận mật khẩu</label>
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
                            <span v-else>Đăng ký</span>
                        </button>
                        <div class="my-3 text-uppercase d-flex align-items-center">
                            <hr class="flex-grow-1 me-2" />
                            <span class="fs-12 fw-bold text-color">Hoặc</span>
                            <hr class="flex-grow-1 ms-2" />
                        </div>

                        <div class="d-flex justify-content-center mb-3">
                            <a href="#" class="auth__icon"><i class="ri-google-fill"></i></a>
                            <a href="#" class="auth__icon"><i class="ri-facebook-fill"></i></a>
                            <a href="#" class="auth__icon"><i class="ri-github-fill"></i></a>
                            <a href="#" class="auth__icon"><i class="ri-linkedin-fill"></i></a>
                        </div>
                        <p class="fs-12 m-0">Bằng việc đăng kí, bạn đã đồng ý với LOGO về <br>
                            <a href="#" class="register__register">Điều khoản dịch vụ</a> & <a href="#"
                                class="register__register">Chính sách bảo mật</a>
                        </p>
                        <p class="mb-0">
                            Bạn đã có tài khoản LOGO?
                            <router-link :to="{ name: 'login' }" class="text-color">Đăng nhập</router-link>
                        </p>
                    </form>
                </div>
            </div>
            <div class="col-lg-7 d-none d-lg-inline">
                <div class="d-flex align-items-center justify-content-center p-3">
                    <img src="/images/image-user.svg" alt="" class="img-fluid auth__img" />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onBeforeMount } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/configs/api'
import message from '@/utils/message_state'
import { set_user, check_login } from '@/utils/auth_state'
import loading__loader from '@/components/loading/loading__loader-circle.vue'

onBeforeMount(() => {
    const token = check_login()
    if (token) {
        message.emit('show-message', {
            title: 'Thông báo',
            text: 'Bạn đã đăng nhập.',
            type: 'warning'
        })
        router.push('/')
    }
})

/*
    SUPPORT
*/
const router = useRouter()
const is_loading = ref(false)
const showPassword = ref(false)
const errors = ref({})

/*
    REGISTER
*/
const user = ref({
    name: '',
    email: '',
    passwrod: '',
    password_confirmation: ''
})
const validate = () => {
    errors.value = {}

    if (!user.value.name.trim()) {
        errors.value.name = "Tên không được để trống."
    } else if (user.value.name.trim().length < 6) {
        errors.value.name = "Tên người dùng tối thiểu 6 ký tự."
    }

    if (!user.value.email) {
        errors.value.email = "Email không được để trống."
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(user.value.email)) {
        errors.value.email = "Email không hợp lệ."
    }

    if (!user.value.password) {
        errors.value.password = "Mật khẩu không được để trống."
    } else if (user.value.password.trim().length < 8) {
        errors.value.password = "Mật khẩu phải có ít nhất 8 ký tự."
    } else if (!/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/.test(user.value.password)) {
        errors.value.password = "Mật khẩu phải có ít nhất 1 chữ hoa, 1 chữ thường và 1 ký tự đặc biệt."
    }
    if (!user.value.password_confirmation) {
        errors.value.password_confirmation = "Mật khẩu xác nhận không được để trống."
    } else if (user.value.password !== user.value.password_confirmation) {
        errors.value.password_confirmation = "Mật khẩu xác nhận không khớp."
    }
    if (Object.keys(errors.value).length > 0) {
        return false
    }
    return true
}
const register = async () => {
    if (!validate()) return
    try {
        is_loading.value = true
        const payload = user.value
        const res = await api.post('/register', payload)

        if (res.status == 201) {
            message.emit('show-message', {
                title: 'Thông báo',
                text: res.data.message,
                type: 'success'
            })
            set_user(res.data.user_name)
            const user = {
                user_name: res.data.user_name,
                token: res.data.token,
                expires_at: res.data.expires_at,
            }
            localStorage.setItem('user', JSON.stringify(user))
            router.push('/')
            Object.assign(user.value = { name: '', email: '', password: '', password_confirmation: '' })
            showPassword.value = false
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