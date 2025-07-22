<template>
    <nav>
        <div class="navbar__left d-flex bg-white shadow-sm " :class="{ show: showMenu }">
            <div class="my-3 px-2 w-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="mb-2 text-uppercase fs-14 fw-semibold">{{ $t('sidebar.menu') }}</div>
                    <i @click="toggleMenu" class="fs-22 ri-close-line cursor-pointer d-md-none d-block"></i>
                </div>
                <div class="d-flex flex-column gap-3">
                    <div class="menu">
                        <a href="#" data-bs-toggle="collapse" data-bs-target="#submenu">
                            <i class="fs-18 ri-home-line me-1"></i>
                            <span class="fs-14 fw-medium">{{ $t('sidebar.management') }}</span>
                            <i class="fs-18 me-1 ri-arrow-down-s-line"></i>
                        </a>
                        <div id="submenu" class="collapse">
                            <a href="#" class="mt-16 fs-14 fw-medium second-text d-block ps-23">
                                {{ $t('sidebar.dashboard') }}
                            </a>
                        </div>
                    </div>
                    <div class="menu">
                        <router-link :to="{ name: 'admin-projects' }" class="fs-14 fw-medium second-text d-block"
                            :class="{
                                'active_sidebar': $route.name == 'admin-projects',
                            }">
                            <i class="fs-18 ri-projector-line me-1"></i>
                            <span class="fs-14 fw-medium">{{ $t('sidebar.project') }}</span>
                        </router-link>
                    </div>
                    <div class="menu">
                        <a href="#" data-bs-toggle="collapse" data-bs-target="#submenu_task">
                            <i class="fs-18 ri-group-line me-1"></i>
                            <span class="fs-14 fw-medium">{{ $t('sidebar.account') }}</span>
                            <i class="fs-18 me-1 ri-arrow-down-s-line"></i>
                        </a>
                        <div id="submenu_task" class="collapse">
                            <router-link :to="{ name: 'users' }" class="mt-16 fs-14 fw-medium second-text d-block ps-23"
                                :class="{
                                    'active_sidebar': $route.name == 'users',
                                }">
                                {{ $t('sidebar.user') }}
                            </router-link>
                            <router-link :to="{ name: 'roles' }" class="mt-16 fs-14 fw-medium second-text d-block ps-23"
                                :class="{
                                    'active_sidebar': $route.name == 'roles' || $route.name == 'permissions',
                                }">
                                {{ $t('sidebar.role_permission') }}
                            </router-link>
                            <router-link to="#" class="mt-10 fs-14 fw-medium second-text d-block ps-23">
                                {{ $t('sidebar.profile') }}
                            </router-link>
                            <router-link :to="{ name: 'admin-logs' }"
                                class="mt-10 fs-14 fw-medium second-text d-block ps-23">
                                {{ $t('sidebar.recent_activity') }}
                            </router-link>
                            <router-link :to="{ name: 'admin-check-in-logs' }"
                                class="mt-10 fs-14 fw-medium second-text d-block ps-23">
                                Chấm Công
                            </router-link>
                            <router-link @click="getWorkingTime" to="#" data-bs-toggle="modal"
                                data-bs-target="#workingTimeModal"
                                class="mt-10 fs-14 fw-medium second-text d-block ps-23">
                                Giờ Làm Việc
                            </router-link>
                        </div>
                    </div>
                    <div class="menu">
                        <a href="#" data-bs-toggle="collapse" data-bs-target="#submenu_email">
                            <i class="fs-18 ri-mail-send-line me-1"></i>
                            <span class="fs-14 fw-medium">{{ $t('sidebar.email') }}</span>
                            <i class="fs-18 me-1 ri-arrow-down-s-line"></i>
                        </a>
                        <div id="submenu_email" class="collapse">
                            <router-link :to="{ name: 'admin-campaigns' }"
                                class="mt-16 fs-14 fw-medium second-text d-block ps-23"
                                :class="{ 'active_sidebar': $route.name == 'campaigns' }">
                                {{ $t('sidebar.campaign') }}
                            </router-link>
                            <router-link :to="{ name: 'admin-email-templates' }"
                                class="mt-10 fs-14 fw-medium second-text d-block ps-23"
                                :class="{ 'active_sidebar': $route.name == 'email-templates' }">
                                {{ $t('sidebar.email_template') }}
                            </router-link>
                            <!-- <router-link :to="{ name: 'email-log' }"
                                class="mt-10 fs-14 fw-medium second-text d-block ps-23"
                                :class="{ 'active_sidebar': $route.name == 'email-log' }">
                                Lịch sử gửi
                            </router-link> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>
    <div class="modal fade" id="workingTimeModal" tabindex="-1" aria-labelledby="workingTimeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg  radius-2">
                <div class="modal-header">
                    <h5 class="modal-title" id="workingTimeModalLabel">🕒 Cấu hình giờ làm việc</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>

                <div class="modal-body">
                    <form @submit.prevent="saveConfig()">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">🟢 Giờ bắt đầu làm</label>
                            <input type="time" v-model="form.start_time" class="form-control radius-2 cursor-pointer" />
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">🔴 Đi trễ nếu sau</label>
                            <input type="time" v-model="form.late_after" class="form-control radius-2 cursor-pointer" />
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">🔚 Giờ kết thúc (tuỳ chọn)</label>
                            <input type="time" v-model="form.end_time" class="form-control radius-2 cursor-pointer" />
                        </div>

                        <button type="submit" class="main-btn py-2 w-100" :disabled="isSaving">
                            {{ isSaving ? 'Đang lưu...' : '💾 Lưu cấu hình' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import message from '@/utils/message_state.js'
import api from '@/configs/api'

const form = ref({
    start_time: '',
    late_after: '',
    end_time: ''
})

const getWorkingTime = async () => {
    try {
        const res = await api.get('working-time')
        if (res.status == 200 && res.data) {
            form.value = res.data
        }
    } catch (error) {
        console.log(error);
    }
}

const isSaving = ref(false)
const saveConfig = async () => {
    if (!validateForm()) return
    isSaving.value = true
    try {
        const res = await api.put('working-time', form.value)
        if (res.status == 200) {
            form.value = res.data
            console.log("thành công");
        }
    } catch (error) {
        console.error(error)
    } finally {
        isSaving.value = false
    }
}

const validateForm = () => {
    if (!form.value.start_time) {
        message.emit('show-message', {
            title: 'Thông báo',
            text: 'Vui lòng chọn giờ bắt đầu làm.',
            type: 'error'
        })
        return false
    }

    if (!form.value.late_after) {
        message.emit('show-message', {
            title: 'Thông báo',
            text: 'Vui lòng chọn thời gian được coi là đi trễ.',
            type: 'error'
        })
        return false
    }

    if (form.value.end_time) {
        if (form.value.end_time <= form.value.start_time) {
            message.emit('show-message', {
                title: 'Thông báo',
                text: 'Giờ kết thúc phải sau giờ bắt đầu.',
                type: 'error'
            })
            return false
        }
    }

    if (form.value.late_after <= form.value.start_time) {
        message.emit('show-message', {
            title: 'Thông báo',
            text: 'Thời gian đi trễ phải sau giờ bắt đầu làm.',
            type: 'error'
        })
        return false
    }

    return true
}

const props = defineProps({
    showMenu: {
        type: Boolean,
        required: true
    },
    toggleMenu: {
        type: Function,
        required: true
    }
})
</script>