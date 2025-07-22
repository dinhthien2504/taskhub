<template>
    <nav class="navbar navbar-expand-lg bg-main container-fuild py-2 px-4">
        <router-link :to="{ name: 'home' }" class="navbar-brand text-white fw-bold">
            <img :src="`${BACKEND_URL}/images/logo-nguyen-bang.png`" width="35" height="35" alt="LOGO">
        </router-link>
        <button class="navbar-toggler py-2" type="button" data-bs-toggle="collapse"
            data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
            aria-label="Toggle navigation">
            <i class="text-white ri-menu-line"></i>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <router-link :to="{ name: 'home' }" class="nav-link text-white">
                        {{ $t('home.title') }}
                    </router-link>
                </li>
            </ul>
            <div class="d-flex align-items-center justify-content-between gap-5">
                <div v-if="user_name">
                    <div class="position-relative account">
                        <a class="fw-medium cursor-pointer dropdown-toggle text-white">
                            <img class="avatar"
                                src="https://chiemtaimobile.vn/images/companies/1/%E1%BA%A2nh%20Blog/avatar-facebook-dep/Anh-avatar-hoat-hinh-de-thuong-doi-lot-soi.jpg?1704788224743">
                            {{ user_name }}
                        </a>
                        <div class="subaccount position-absolute bg-white">
                            <div class="d-flex justify-content-center align-items-center flex-column">
                                <img class="avatar mt-4"
                                    src="https://chiemtaimobile.vn/images/companies/1/%E1%BA%A2nh%20Blog/avatar-facebook-dep/Anh-avatar-hoat-hinh-de-thuong-doi-lot-soi.jpg?1704788224743">
                                <span class="fs-16 fw-medium mt-2">{{ user_name }}</span>
                            </div>
                            <div v-if="log && log.check_in_time" class="fs-12 text-dark fw-medium cursor-pointer ps-3">
                                <p v-if="log.check_in_time">Đã check-in lúc: {{ formatDateTime(log.check_in_time) }}</p>
                                <p>{{ log.check_out_time ? `Đã check-out lúc: ${formatDateTime(log.check_out_time)}` :
                                    'Chưa check-out'
                                }}
                                </p>
                            </div>
                            <hr class="text-secondary">
                            <div class="d-flex flex-column gap-1">
                                <router-link class="fs-16 text-dark fw-medium subaccount-item"
                                    :to="{ name: 'admin-dashboard' }">
                                    <i class="ri-tools-line"></i>
                                    {{ $t('header.admin_dashboard') }}
                                </router-link>
                                <router-link class="fs-16 text-dark fw-medium subaccount-item"
                                    :to="{ name: 'profile' }">
                                    <i class="ri-settings-2-line"></i>
                                    {{ $t('header.profile') }}
                                </router-link>
                                <router-link class="fs-16 text-dark fw-medium subaccount-item"
                                    :to="{ name: 'change-password' }">
                                    <i class="ri-lock-2-line"></i>
                                    {{ $t('header.change_password') }}
                                </router-link>
                                <div v-if="!log.check_in_time" @click="checkIn()"
                                    class="fs-16 text-dark fw-medium subaccount-item cursor-pointer">
                                    <i class="ri-login-box-line"></i>
                                    Check In
                                </div>
                                <div v-if="log.check_in_time && !log.check_out_time" @click="checkOut()"
                                    class="fs-16 text-dark fw-medium subaccount-item cursor-pointer">
                                    <i class="ri-logout-box-line"></i>
                                    Check Out
                                </div>
                            </div>
                            <hr class="text-secondary">
                            <a class="fs-16 text-dark fw-medium subaccount-item cursor-pointer" @click="logout">
                                <i class="ri-logout-circle-r-line"></i>
                                {{ $t('header.logout') }}
                            </a>
                        </div>
                    </div>
                </div>
                <div v-else class="d-flex align-items-center justify-content-between gap-2">
                    <router-link :to="{ name: 'login' }" class="nav-link text-white">{{ $t('header.login')
                        }}</router-link>
                    <span class="text-secondary">|</span>
                    <router-link :to="{ name: 'register' }" class="nav-link text-white">{{ $t('header.register')
                        }}</router-link>
                </div>
            </div>
            <!-- <div class="ms-2">
                <LanguageSwitcher />
            </div> -->
        </div>
    </nav>
</template>
<script setup>
import { useRouter } from 'vue-router'
import api from '@/configs/api'
import message from "@/utils/message_state"
import { user_name_auth, clear_user } from '@/utils/auth_state'
import { BACKEND_URL } from '@/configs/env'
import LanguageSwitcher from '@/components/Language_switcher.vue'
import { ref, onMounted } from 'vue'
import { formatDateTime } from '@/utils/format_date.js'
import { check_login } from '@/utils/auth_state'

onMounted(() => {
    const token = check_login()
    if (token) {
        fetchLogToday()
    }
})

const user_name = user_name_auth
const router = useRouter()
const log = ref({})
const logout = async () => {
    try {
        const res = await api.post('/logout')
        if (res.status == 200) {
            clear_user()
            message.emit("show-message", { title: "Thông báo", text: res.data.message, type: "success" })
            router.push('/')
        }
    } catch (error) {
        console.log(error);
    }
}

const fetchLogToday = async () => {
    try {
        const res = await api.get('/today-log')
        log.value = res.data
    } catch (e) {
        console.error(e)
    }
}

const checkIn = async () => {
    try {
        const res = await api.post('/check-in')
        if (res.status == 201) {
            message.emit("show-message", { title: "Thông báo", text: 'Check in thành công', type: "success" })
            log.value = res.data
        }
    } catch (e) {
        if (e.response.data.error) {
            message.emit("show-message", { title: "Thông báo", text: e.response.data.error, type: "error" })
        } else {
            console.log(e);
        }
    }
}

const checkOut = async () => {
    try {
        const res = await api.post('/check-out')
        console.log(res);

        if (res.status == 200) {
            message.emit("show-message", { title: "Thông báo", text: 'Check out thành công', type: "success" })
            log.value = res.data
        }
    } catch (e) {
        if (e.response.data.error) {
            message.emit("show-message", { title: "Thông báo", text: e.response.data.error, type: "error" })
        } else {
            console.log(e);
        }
    }
}
</script>