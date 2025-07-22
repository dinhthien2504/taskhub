<template>
    <div class="container my-5">
        <div class="row">
            <div class="col-lg-7 d-none d-lg-inline">
                <div class="d-flex align-items-center justify-content-center">
                    <img src="/images/image-user.svg" alt="Hình ảnh user" class="img-fluid auth__img" />
                </div>
            </div>
            <div class="col-lg-5 col-12 d-flex align-items-center justify-content-center">
                <div
                    class="auth__content border p-4 rounded d-flex flex-column justify-content-center text-center w-100">
                    <form @submit.prevent="login()">
                        <h3 class="fs-24 text-color text-uppercase mb-4">{{ $t('login.title') }}</h3>

                        <!-- Email -->
                        <div class="mb-4 position-relative">
                            <div class="input-group auth__input">
                                <span class="input-group-text">
                                    <i class="ri-mail-fill text-muted"></i>
                                </span>

                                <div class="form-floating flex-grow-1">
                                    <input type="email" id="email" class="form-control"
                                        :placeholder="$t('login.email_placeholder')" v-model="user.email" />
                                    <label for="email">{{ $t('login.email_label') }}</label>
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
                        <div class="mb-4 position-relative">
                            <div class="input-group auth__input">
                                <span class="input-group-text">
                                    <i class="ri-lock-fill text-muted"></i>
                                </span>
                                <div class="form-floating flex-grow-1">
                                    <input :type="showPassword ? 'text' : 'password'" id="password" class="form-control"
                                        :placeholder="$t('login.password_placeholder')" v-model="user.password"
                                        autocomplete="password" />
                                    <label>{{ $t('login.password_label') }}</label>
                                </div>
                                <i :class="showPassword ? 'ri-eye-off-line' : 'ri-eye-line'" class="toggle-password"
                                    @click="showPassword = !showPassword"></i>
                            </div>
                            <p class="m-0 text-danger text-start" v-if="errors.password">
                                {{
                                    Array.isArray(errors.password)
                                        ? errors.password[0]
                                        : errors.password
                                }}
                            </p>
                        </div>

                        <div class="d-flex align-items-center gap-2 justify-content-end mb-3">
                            <label for="remember" class="cursor-pointer">{{ $t('login.remember') }}</label>
                            <div class="checkbox">
                                <label class="checkbox-label">
                                    <input type="checkbox" id="remember" @change="user.remember = !user.remember" />
                                    <div class="checkbox-wrapper">
                                        <div class="checkbox-bg"></div>
                                        <svg fill="none" viewBox="0 0 24 24" class="checkbox-icon">
                                            <path stroke-linejoin="round" stroke-linecap="round" stroke-width="3"
                                                stroke="currentColor" d="M4 12L10 18L20 6" class="check-path">
                                            </path>
                                        </svg>
                                    </div>
                                </label>
                            </div>
                        </div>
                        <button type="submit" :disabled="is_loading"
                            class="main-btn w-100 radius-25 py-2 fs-18 d-flex align-items-center justify-content-center gap-3">
                            <loading__loader v-if="is_loading" size="20px" color="#fff" border="2px" />
                            <span v-else>{{ $t('login.submit') }}</span>
                        </button>

                        <div class="my-3 text-uppercase d-flex align-items-center">
                            <hr class="flex-grow-1 me-2" />
                            <span class="fs-12 fw-bold text-color">{{ $t('login.or') }}</span>
                            <hr class="flex-grow-1 ms-2" />
                        </div>

                        <div class="d-flex justify-content-center mb-3">
                            <a href="#" class="auth__icon"><i class="ri-google-fill"></i></a>
                            <a href="#" class="auth__icon"><i class="ri-facebook-fill"></i></a>
                            <a href="#" class="auth__icon"><i class="ri-github-fill"></i></a>
                            <a href="#" class="auth__icon"><i class="ri-linkedin-fill"></i></a>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onBeforeMount } from "vue"
import { useRouter } from 'vue-router'
import api from "@/configs/api"
import message from "@/utils/message_state"
import loading__loader from '@/components/loading/loading__loader-circle.vue'
import { set_user, check_login } from '@/utils/auth_state'
import { useI18n } from 'vue-i18n'
const { t } = useI18n()
const router = useRouter()

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
const is_loading = ref(false)
const user = ref({ email: "", password: "", remember: false })
const showPassword = ref(false)
const errors = ref({})

const validate = () => {
    errors.value = {}

    if (!user.value.email) {
        errors.value.email = t('login.email_required')
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(user.value.email)) {
        errors.value.email = t('login.email_invalid')
    }

    if (!user.value.password.trim()) {
        errors.value.password = t('login.password_required')
    } else if (user.value.password.length < 8) {
        errors.value.password = t('login.password_min')
    }

    if (Object.keys(errors.value).length > 0) {
        return false
    }
    return true
}

const login = async () => {
    if (!validate()) return

    try {
        is_loading.value = true
        const payload = user.value
        const res = await api.post("/login", payload)
        if (res.status == 200) {
            message.emit("show-message", { title: "Thông báo", text: res.data.message, type: "success" })
            set_user(res.data.user_name)
            const user = {
                user_name: res.data.user_name,
                token: res.data.token,
                expires_at: res.data.expires_at
            }
            localStorage.setItem('user', JSON.stringify(user))
            router.push('/')
        }
    } catch (err) {
        const res = err.response.data.message
        message.emit("show-message", { title: "Thông báo", text: res, type: "error" })
    } finally {
        is_loading.value = false
    }
}
</script>
