<template>
    <div class="admin__content">
        <ul class="nav nav-tabs" id="managementTabs">
            <li class="nav-item">
                <router-link :to="{ name: 'roles' }" :class="{
                    'nav-link': true,
                }" active-class="active" exact-active-class="exact-active">
                    <i class="ri-user-settings-line me-2 fs-12 fw-medium"></i>
                    Quản Lý Vai Trò
                </router-link>
            </li>
            <li class="nav-item">
                <router-link :to="{ name: 'permissions' }" :class="{
                    'nav-link': true,
                }" active-class="active" exact-active-class="exact-active">
                    <i class="ri-key-line me-2 fs-12 fw-medium"></i>
                    Quản Lý Quyền
                </router-link>
            </li>
        </ul>
        <div
            class="p-3 bg-white border-bottom text-grey fs-14 fw-medium d-flex align-items-center justify-content-between">
            <p class="m-0">
                Danh Sách Quyền
            </p>
            <div class="d-flex align-items-center gap-2">
                <div class="admin__search">
                    <button type="button" class="search__button">
                        <i class="ri-search-2-line"></i>
                    </button>
                    <form @submit.prevent="onSearch">
                        <div class="d-flex align-items-center gap-1 position-relative">
                            <button type="submit" style="height: 38px" class="main-btn py-1 px-3 ">
                                <i class="ri-search-2-line"></i>
                            </button>
                            <input type="text" v-model="searchValue" placeholder="Tìm kiếm quyền"
                                class="form-control radius-2" />
                            <button type="button" v-if="searchValue" @click="clearSearch"
                                class="btn position-absolute top-50 end-0 translate-middle-y me-2 p-0 border-0 bg-transparent">
                                <i class="ri-close-line"></i>
                            </button>
                        </div>
                    </form>
                </div>
                <button type="button" class="main-btn px-3" style="height: 38px;" data-bs-toggle="modal"
                    data-bs-target="#modalAddPermission">
                    <span class="d-none d-lg-block">Thêm Quyền</span>
                    <span class="d-block d-lg-none"><i class="ri-add-fill"></i></span>
                </button>
            </div>
        </div>
        <div class="p-10">
            <div v-if="loading.fetch_list" style="height: 60vh;"
                class="d-flex align-items-center justify-content-center">
                <loading_loader size="40px" border="4px" />
            </div>
            <div v-else class="table-responsive fade-in">
                <table v-if="permissions.length > 0" class="table border">
                    <thead class="table-active">
                        <tr class="lh-lg align-middle">
                            <th scope="col" class="min-w-50">
                                <div class="checkbox">
                                    <label class="checkbox-label">
                                        <input type="checkbox" name="check-item" @change="toggleSelectionAll($event)"
                                            :checked="isAllSelected" />
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
                            </th>
                            <th scope="col" class="fs-14 fw-semibold min-w-150">Tên Quyền</th>
                            <th scope="col" class="fs-14 fw-semibold min-w-200">Mô Tả</th>
                            <th scope="col" class="fs-14 fw-semibold min-w-150">Vai Trò Sử Dụng</th>
                            <th scope="col" class="fs-14 fw-semibold min-w-100 text-center">Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="permission in permissions">
                            <td scope="row" class="align-middle">
                                <div class="checkbox me-2">
                                    <label class="checkbox-label">
                                        <input type="checkbox" name="check-item"
                                            @change="toggleSelection(permission.id)" :value="permission.id"
                                            :checked="selectedPermissionIds.includes(permission.id)" />
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
                            </td>
                            <td class="align-middle fs-14">{{ permission.name }}</td>
                            <td class="align-middle">
                                <p>{{ permission.description ?? '_' }}</p>
                            </td>
                            <td class="align-middle">
                                <p class="fs-14 my-3">
                                    {{ permission.roles.length > 0 ? permission.roles.join(', ') : '_' }}
                                </p>
                            </td>
                            <td class="align-middle text-center">
                                <div class="more-wrapper">
                                    <button class="more-btn one-button">
                                        <i class="ri-more-fill"></i>
                                    </button>
                                    <div class="more-popup">
                                        <button type="button" data-bs-toggle="modal"
                                            data-bs-target="#modalEditPermission"
                                            @click="showPermissionEdit(permission.id)" class="bg-warning text-dark">
                                            Sửa
                                            <i class="ri-edit-line"></i>
                                        </button>
                                        <button type="button" @click="openDeleteModal(permission.id)"
                                            class="bg-danger text-white">
                                            Xóa
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot class="table-active">
                        <tr class="lh-lg">
                            <th scope="col" class="align-middle">
                                <div class="checkbox align-items-center d-flex gap-1">
                                    <label class="checkbox-label">
                                        <input type="checkbox" name="check-item" @change="toggleSelectionAll($event)"
                                            :checked="isAllSelected" />
                                        <div class="checkbox-wrapper">
                                            <div class="checkbox-bg"></div>
                                            <svg fill="none" viewBox="0 0 24 24" class="checkbox-icon">
                                                <path stroke-linejoin="round" stroke-linecap="round" stroke-width="3"
                                                    stroke="currentColor" d="M4 12L10 18L20 6" class="check-path">
                                                </path>
                                            </svg>
                                        </div>
                                    </label>
                                    <span class="fw-medium">All</span>
                                </div>
                            </th>
                            <th scope="col"></th>
                            <th scope="col"></th>
                            <th scope="col"></th>
                            <td class="align-middle text-center">
                                <div class="more-wrapper">
                                    <button class="more-btn p-0">
                                        <i class="ri-more-fill"></i>
                                    </button>
                                    <div class="more-popup">
                                        <button type="button" @click="openDeleteModal()"
                                            class="text-white bg-danger px-2 py-0">
                                            Xóa Tất Cả
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tfoot>
                </table>
                <div v-else>
                    <no_data />
                </div>
                <pagination :meta="meta" @changePage="onChangePage" />
            </div>
        </div>
    </div>
    <div class="modal" id="modalAddPermission">
        <div class="modal-dialog">
            <div class="modal-content radius-2">
                <div class="modal-header d-flex align-items-center justify-content-between">
                    <h4 class="modal-title fs-18">Thêm Quyền</h4>
                    <span class="fs-18 fw-medium " data-bs-dismiss="modal">
                        <i class="ri-close-line cursor-pointer"></i>
                    </span>
                </div>
                <div class="modal-body">
                    <form @submit.prevent="addPermission()">
                        <div class="form-group">
                            <label class="fs-14 fw-medium mb-1">Tên Quyền<span class="text-color">*</span></label>
                            <input type="text" v-model="permission.name" class="form-control radius-2" />
                        </div>
                        <p v-if="errors.name" class="text-danger fs-14">
                            {{
                                Array.isArray(errors.name)
                                    ? errors.name[0]
                                    : errors.name
                            }}
                        </p>
                        <div class="form-group my-2">
                            <label for="comment" class="mb-1">Mô tả</label>
                            <textarea class="form-control radius-2" v-model="permission.description" rows="3"
                                id="comment" name="text"></textarea>
                        </div>
                        <button type="submit" :disabled="loading.save_add" style="width: 150px;"
                            class="main-btn px-3 py-1 d-flex align-items-center justify-content-center gap-3">
                            <loading_loader v-if="loading.save_add" size="20px" color="#fff" border="2px" />
                            <span v-else>Thêm Quyền</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- END ADD -->
    <div class="modal" id="modalEditPermission">
        <div class="modal-dialog">
            <div class="modal-content radius-2">
                <div class="modal-header d-flex align-items-center justify-content-between">
                    <h4 class="modal-title fs-18">Sửa Quyền</h4>
                    <span class="fs-18 fw-medium" data-bs-dismiss="modal">
                        <i class="ri-close-line cursor-pointer"></i>
                    </span>
                </div>
                <div class="modal-body">
                    <form @submit.prevent="editPermission()">
                        <div class="form-group">
                            <label class="fs-14 fw-medium mb-1">Tên Quyền<span class="text-color">*</span></label>
                            <input type="text" v-model="permission_edit.name" class="form-control radius-2"
                                name="role_name" />
                        </div>
                        <p v-if="errors.name" class="text-danger fs-14">
                            {{
                                Array.isArray(errors.name)
                                    ? errors.name[0]
                                    : errors.name
                            }}
                        </p>
                        <div class="form-group my-2">
                            <label for="comment" class="mb-1">Mô tả</label>
                            <textarea class="form-control radius-2" v-model="permission_edit.description" rows="3"
                                id="comment" name="text"></textarea>
                        </div>
                        <button type="submit" :disabled="loading.save_edit" style="width: 170px;"
                            class="main-btn px-3 py-1 d-flex align-items-center justify-content-center gap-3">
                            <loading_loader v-if="loading.save_edit" size="20px" color="#fff" border="2px" />
                            <span v-else>Cập Nhật Quyền</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- END EDIT -->
    <div v-if="isDeleteModalVisible" class="modal fade show d-block" style="background: rgba(0,0,0,0.5);">
        <div class=" modal-dialog">
            <div class="modal-content radius-2">
                <div class="modal-header d-flex align-items-center justify-content-between">
                    <h4 class="modal-title fs-18">Xác Nhận Xóa</h4>
                    <span class="fs-18 fw-medium" @click="isDeleteModalVisible = false">
                        <i class="ri-close-line cursor-pointer"></i>
                    </span>
                </div>
                <div class="modal-body">
                    <p class="m-0">Bạn có chắc muốn xóa quyền.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" :disabled="loading.delete" @click="handleDeletePermissions()"
                        style="width: 150px;"
                        class="main-btn px-3 py-1 d-flex align-items-center radius-2 justify-content-center gap-3">
                        <loading_loader v-if="loading.delete" size="20px" color="#fff" border="2px" />
                        <span v-else>Xác nhận xóa</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- END DELETE -->

</template>
<script setup>
import { onMounted, ref, computed } from 'vue'
import api from '@/configs/api'
import message from '@/utils/message_state'
import loading_loader from '@/components/loading/loading__loader-circle.vue'
import pagination from '@/components/pagination.vue'
import no_data from '@/components/no_data.vue'

/*
    SUPORT
*/
const loading = ref({
    save_add: false,
    fetch_list: false,
    save_edit: false,
    delete: false
})

const permissions = ref([])
const getPermissions = async () => {
    try {
        loading.value.fetch_list = true
        const resPermissions = await api.get('/permissions', {
            params: {
                page: currentPage.value,
                search: searchValue.value
            }
        })
        if (resPermissions.status == 200) {
            permissions.value = resPermissions.data.data
            meta.value = resPermissions.data.meta
        }
    } catch (error) {
        message.emit("show-message", {
            title: "Thông báo",
            text: "Đã xảy ra lỗi khi lấy dữ liệu quyền.",
            type: "error"
        })
    } finally {
        loading.value.fetch_list = false
    }

}

/*
    ADD
*/
const permission = ref({
    name: '',
    description: null
})
const addPermission = async () => {
    try {
        if (!validate(permission.value)) return
        loading.value.save_add = true
        const payload = permission.value
        const resAddPermission = await api.post('/permissions', payload)
        if (resAddPermission.status == 201) {
            permissions.value.unshift(resAddPermission.data)

            permission.value = {
                name: '',
                description: null
            }

            message.emit("show-message", {
                title: "Thông báo",
                text: "Tạo quyền thành công.",
                type: "success"
            })
        }
    } catch (error) {
        error = error.response
        if (error && error.status === 422) {
            errors.value = error.data.errors
        } else if (error.status === 500) {
            message.emit("show-message", {
                title: "Thông báo",
                text: error.data.message,
                type: "error"
            })
        } else {
            console.warn('Lỗi validation:', error)
        }
    } finally {
        loading.value.save_add = false
    }
}

/*
    EDIT
*/
const permission_edit = ref({})
const showPermissionEdit = (permission_id) => {
    errors.value = {}
    permission_edit.value = {}
    if (permission_id) {
        permission_edit.value = { ...permissions.value.find((r) => r.id == permission_id) }
    } else {
        message.emit("show-message", {
            title: "Thông báo",
            text: "Không tìm thấy quyền.",
            type: "warning"
        })
    }
}

const editPermission = async () => {
    try {
        if (!validate(permission_edit.value)) return
        loading.value.save_edit = true
        const payload = {
            name: permission_edit.value?.name ?? '',
            description: permission_edit.value?.description ?? ''
        }

        const resEditPermission = await api.put(`/permissions/${permission_edit.value.id}`, payload)
        if (resEditPermission.status == 200) {
            const index = permissions.value.findIndex(r => r.id === permission_edit.value.id)
            if (index !== -1) {
                permissions.value[index] = { ...permissions.value[index], ...payload }
            }

            message.emit("show-message", {
                title: "Thông báo",
                text: resEditPermission.data.message,
                type: "success"
            })
        }
    } catch (error) {
        error = error.response
        if (error && error.status === 422) {
            errors.value = error.data.errors
        } else if (error.status === 500) {
            message.emit("show-message", {
                title: "Thông báo",
                text: error.data.message,
                type: "error"
            })
        } else {
            console.warn('Lỗi validation:', error)
        }
    } finally {
        loading.value.save_edit = false
    }
}

/*
    DELETE
*/
const isDeleteModalVisible = ref(false)
const selectedPermissionIds = ref([])

const isAllSelected = computed(() => {
    const allIds = permissions.value.map(permission => permission.id)
    return (
        selectedPermissionIds.value.length === allIds.length &&
        selectedPermissionIds.value.every(id => allIds.includes(id))
    )
})

const toggleSelection = (id) => {
    if (selectedPermissionIds.value.includes(id)) {
        selectedPermissionIds.value = selectedPermissionIds.value.filter(permission_id => permission_id !== id)
    } else {
        selectedPermissionIds.value.push(id)
    }
}

const toggleSelectionAll = (e) => {
    const checked = e.target.checked
    const allIds = permissions.value.map(permission => permission.id)
    if (checked) {
        selectedPermissionIds.value = allIds
    } else {
        selectedPermissionIds.value = []
    }
}

const openDeleteModal = (id = null) => {
    isDeleteModalVisible.value = true
    if (id) {
        selectedPermissionIds.value = []
        selectedPermissionIds.value = [id]
    }
    if (selectedPermissionIds.value.length === 0) {
        message.emit('show-message', {
            title: 'Thông báo',
            text: 'Vui lòng chọn vai trò muốn xóa',
            type: 'info'
        })
        isDeleteModalVisible.value = false
        return
    }
}

const handleDeletePermissions = async () => {
    try {
        const ids = selectedPermissionIds.value
        loading.value.delete = true
        const resDelete = await api.delete('/permissions', {
            data: {
                ids: ids
            }
        })
        if (resDelete.status == 200) {
            permissions.value = permissions.value.filter((permission) => !resDelete.data.deleted_ids.includes(permission.id))
            const mess = resDelete.data.not_deleted_names
                ? `Quyền ${resDelete.data.not_deleted_names} không thể xóa vì có tài khoản đang sử dụng.`
                : 'Đã xóa quyền thành công.'
            message.emit("show-message", {
                title: "Thông báo",
                text: mess,
                type: "info"
            })
        }
    } catch (error) {
        error = error.response
        if (error && error.status === 422) {
            message.emit("show-message", {
                title: "Thông báo",
                text: error.data.errors.ids[0],
                type: "error"
            })
        } else if (error.status == 500) {
            message.emit("show-message", {
                title: "Thông báo",
                text: error.data.message,
                type: "error"
            })
        } else {
            console.warn('Lỗi validation:', error)
        }
    } finally {
        loading.value.delete = false
        isDeleteModalVisible.value = false
        selectedPermissionIds.value = []
    }
}
/*
    PAGINATION & SEARCH
*/
const meta = ref({})
const currentPage = ref(1)
const onChangePage = (page) => {
    currentPage.value = page
    getPermissions()
}
const searchValue = ref('')
const onSearch = () => {
    currentPage.value = 1
    getPermissions()
}
const clearSearch = () => {
    searchValue.value = ''
    getPermissions()
}

/*
    VALIDATE
*/
const errors = ref({})
const validate = (permission) => {
    errors.value = {}
    if (!permission.name) {
        errors.value.name = 'Tên quyền không được để trống.'
    } else if (permission.name.length < 3) {
        errors.value.name = 'Tên quyền không được quá ngắn, tối thiểu 3 ký tự.'
    }

    if (permission.description && permission.description.length > 255) {
        errors.value.description = 'Mô tả không được vượt quá 255 ký tự.'
    }
    return Object.keys(errors.value).length === 0

}
onMounted(() => {
    getPermissions()
})
</script>