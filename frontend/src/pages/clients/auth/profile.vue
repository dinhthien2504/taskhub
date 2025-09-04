<template>
    <div class="main-content">
        <div class="container-fluid">
            <div class="row">
                <!-- Sidebar -->
                <div class="col-lg-3 col-md-4">
                    <div class="sidebar fade-in">
                        <div class="user-info">
                            <div class="user-avatar">
                                <img v-if="avatar.preview" :src="avatar.preview" alt="Hình ảnh preview" class="w-100">
                                <img v-else-if="user.avatar" class="w-100"
                                    :src="`${BACKEND_URL}/images/users/${user.avatar}`" alt="Hình ảnh">
                                <i v-else class="ri-user-line"></i>
                            </div>
                            <div class="user-details">
                                <h6>{{ user_name_auth ?? '' }}</h6>
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
                                        <router-link class="fw-bold" :to="{ name: 'profile' }">
                                            Hồ Sơ
                                        </router-link>
                                    </li>
                                </ul>
                                <ul class="submenu">
                                    <li>
                                        <router-link class="fw-medium" :to="{ name: 'change-password' }">
                                            Đổi Mật Khẩu
                                        </router-link>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Main Profile Form -->
                <div class="col-lg-9 col-md-8">
                    <div class="profile-section fade-in">
                        <h2 class="section-title">Hồ Sơ Của Tôi</h2>
                        <p class="section-subtitle">Quản lý thông tin hồ sơ để bảo mật tài khoản</p>
                        <div v-if="is_loading" class="d-flex align-items-center justify-content-center">
                            <loading__loader size="30px" border="2px" />
                        </div>
                        <div class="row" v-else>
                            <div class="col-lg-8">
                                <form>
                                    <div class="row mb-3">
                                        <label class="col-sm-3 col-form-label form-label">Email đăng
                                            nhập</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" :value="user.email" disabled>
                                        </div>
                                        <p class="m-0 text-danger text-start" v-if="errors.email">
                                            {{
                                                Array.isArray(errors.email)
                                                    ? errors.email[0]
                                                    : errors.email
                                            }}
                                        </p>
                                    </div>

                                    <div class="row mb-3">
                                        <label class="col-sm-3 col-form-label form-label">Tên</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" placeholder="" v-model="user.name">
                                        </div>
                                        <p class="m-0 text-danger text-start" v-if="errors.name">
                                            {{
                                                Array.isArray(errors.name)
                                                    ? errors.name[0]
                                                    : errors.name
                                            }}
                                        </p>
                                    </div>

                                    <div class="row mb-3">
                                        <label class="col-sm-3 col-form-label form-label">Số điện thoại</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" v-model="user.phone">
                                        </div>
                                        <p class="m-0 text-danger text-start" v-if="errors.phone">
                                            {{
                                                Array.isArray(errors.phone)
                                                    ? errors.phone[0]
                                                    : errors.phone
                                            }}
                                        </p>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-9 offset-sm-3">
                                            <button type="button" @click="updateProfile()" class="main-btn py-1 px-3">
                                                <loading__loader v-if="is_loading_save" size="20px" color="#fff"
                                                    border="2px" />
                                                <span v-else>Lưu</span>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <div class="col-lg-4">
                                <div class="profile-photo">
                                    <div class="photo-placeholder">
                                        <img v-if="avatar.preview" :src="avatar.preview" alt="Hình ảnh preview"
                                            class="w-100">
                                        <img v-else-if="user.avatar" class="w-100"
                                            :src="`${BACKEND_URL}/images/users/${user.avatar}`" alt="Hình ảnh">
                                        <i v-else class="ri-user-line"></i>
                                    </div>

                                    <div class="mb-3 mt-3">
                                        <label for="image" class="form-label fs-14 cursor-pointer fw-medium">
                                            Chọn ảnh
                                        </label>
                                        <br>
                                        <input type="file" hidden id="image" accept="image/*"
                                            @change="(e) => onFileChange(e)">
                                    </div>
                                    <div class="image-requirements">
                                        Dung lượng file tối đa 1 MB<br>
                                        Định dạng: .JPEG, .PNG
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script setup>
import { ref, onMounted, onBeforeMount } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/configs/api'
import message from '@/utils/message_state'
import { BACKEND_URL } from '@/configs/env'
import loading__loader from '@/components/loading/loading__loader-circle.vue'
import { check_login, user_name_auth, set_avatar, set_user } from '@/utils/auth_state'

const router = useRouter()

onMounted(() => {
    const token = check_login()
    if (!token) {
        message.emit('show-message', {
            title: 'Thông báo',
            text: 'Bạn cần đăng nhập để sử dụng chức năng này.',
            type: 'warning'
        })
        router.push('/')
        return
    }
    getUserProfile()
})
const user = ref({
    email: '',
    name: '',
    phone: '',
    avatar: null
})

const avatar = ref({
    preview: null,
    object: null
})

const errors = ref({})
const is_loading = ref(null)
const is_loading_save = ref(null)
const getUserProfile = async () => {
    try {
        is_loading.value = true
        const resProfile = await api.post('/profile');
        if (resProfile.status == 200) {
            user.value = resProfile.data
        }
    } catch (error) {
        message.emit("show-message", { title: "Lỗi", text: "Không lấy được thông tin tài khoản.", type: "error" })
        console.warn('Lỗi:', error)
    } finally {
        is_loading.value = false
    }
}

const validate = () => {
    errors.value = {}

    if (!user.value.name.trim()) {
        errors.value.name = "Tên không được để trống."
    } else if (user.value.name.trim().length < 3) {
        errors.value.name = "Tên người dùng tối thiểu 3 ký tự."
    }

    if (user.value.phone && !/^0\d{9}$/.test(user.value.phone)) {
        errors.value.phone = "Số điện thoại không hợp lệ."
    }

    if (Object.keys(errors.value).length > 0) {
        return false
    }
    return true
}

const onFileChange = (e) => {
    try {
        const file = e.target.files[0]
        if (file) {
            avatar.value.preview = URL.createObjectURL(file)
            avatar.value.object = file
        }
    } catch (err) {
        console.error('Lỗi trong uploadImage:', err)
    }
}

const updateProfile = async () => {
    if (!validate()) return
    const formData = new FormData()
    formData.append('_method', 'PUT')
    if (avatar.value.object) {
        formData.append('avatar', avatar.value.object)
    }
    for (let key in user.value) {
        if (key != 'avatar') {
            formData.append(key, user.value[key])
        }
    }
    try {
        is_loading_save.value = true
        const resUpdateProfile = await api.post('/profile', formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        });
        if (resUpdateProfile.status == 200) {
            set_avatar(resUpdateProfile.data.avatar);
            set_user(resUpdateProfile.data.name);
            message.emit("show-message", { title: "Thông báo", text: resUpdateProfile.data.message, type: "success" })
        }
    } catch (error) {
        if (error.response && error.response.status === 422) {
            errors.value = error.response.data.errors
        } else {
            console.warn('Lỗi validation:', error)
        }
    } finally {
        is_loading_save.value = false
    }
}
</script>