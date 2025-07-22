<template>
    <header class="z-3 admin__header-top d-flex align-items-center justify-content-between px-2 shadow-sm">
        <button id="menu-toggle" @click.stop="toggleMenu" class="d-md-none">
            <i class="ri-menu-line fs-24"></i>
        </button>
        <div class="d-flex align-items-center gap-2 fs-22 logo">
            <router-link :to="{ name: 'admin-dashboard' }" class="navbar-brand text-white fw-bold">
                <img :src="`${BACKEND_URL}/images/logo-am-bang.png`" width="28" height="28" alt="LOGO">
            </router-link>
            <span class="text-dark fw-medium">ADMIN</span>
        </div>
        <div class="d-flex align-items-center gap-4">
            <div class="position-relative">
                <i class="fs-24 ri-notification-3-line"></i>
                <span class="badge bg-danger rounded-pill position-absolute translate-middle-x">3</span>
            </div>
            <div class="position-relative account">
                <a class="fw-medium cursor-pointer dropdown-toggle">
                    <img class="avatar"
                        src="https://img.lovepik.com/png/20231024/Shirt-men-s-half-length-cartoon-character-avatar-hand-draw_332492_wh860.png">
                    <span class="name">{{ user_name }}</span>
                </a>
                <div class="subaccount position-absolute bg-white">
                    <div class="d-flex justify-content-center align-items-center flex-column">
                        <img class="avatar mt-4"
                            src="https://img.lovepik.com/png/20231024/Shirt-men-s-half-length-cartoon-character-avatar-hand-draw_332492_wh860.png">
                        <span class="fs-16 fw-medium">{{ user_name }}</span>
                    </div>
                    <hr class="text-secondary">
                    <div class="d-flex flex-column gap-2">
                        <a class="fs-16 text-dark fw-medium" href="#">
                            <i class="ri-settings-2-line"></i>
                            Hồ sơ
                        </a>
                    </div>
                    <hr class="text-secondary">
                    <a class="fs-16 text-dark fw-medium cursor-pointer" @click="logout">
                        <i class="ri-logout-circle-r-line"></i>
                        Đăng xuất
                    </a>
                </div>
            </div>
            <!-- <LanguageSwitcher /> -->
        </div>
    </header>
</template>
<script setup>
import { useRouter } from 'vue-router'
import api from '@/configs/api'
import message from "@/utils/message_state"
import { user_name_auth, clear_user } from '@/utils/auth_state'
import { BACKEND_URL } from '@/configs/env'
import LanguageSwitcher from '@/components/Language_switcher.vue'
const user_name = user_name_auth
const router = useRouter()

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
const props = defineProps({
    toggleMenu: {
        type: Function,
        required: true
    }
})
</script>