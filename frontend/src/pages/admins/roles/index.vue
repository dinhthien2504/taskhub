<template>
    <div class="admin__content">
        <ul class="nav nav-tabs" id="managementTabs">
            <li class="nav-item">
                <router-link :to="{ name: 'roles' }" :class="{ 'nav-link': true }" active-class="active"
                    exact-active-class="exact-active">
                    <i class="ri-user-settings-line me-2 fs-12 fw-medium"></i>
                    Quản Lý Vai Trò
                </router-link>
            </li>
            <li class="nav-item">
                <router-link :to="{ name: 'permissions' }" :class="{ 'nav-link': true }" active-class="active"
                    exact-active-class="exact-active">
                    <i class="ri-key-line me-2 fs-12 fw-medium"></i>
                    Quản Lý Quyền
                </router-link>
            </li>
        </ul>
        <div
            class="p-3 bg-white border-bottom text-grey fs-14 fw-medium d-flex align-items-center justify-content-between">
            <p class="m-0">
                Danh Sách Vai Trò
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
                            <input type="text" v-model="searchValue" placeholder="Tìm kiếm vai trò"
                                class="form-control radius-2" />
                            <button v-if="searchValue" type="button" @click="clearSearch"
                                class="btn position-absolute top-50 end-0 translate-middle-y me-2 p-0 border-0 bg-transparent">
                                <i class="ri-close-line"></i>
                            </button>
                        </div>
                    </form>
                </div>
                <button type="button" class="main-btn px-3" style="height: 38px;" data-bs-toggle="modal"
                    data-bs-target="#modalAddRole">
                    <span class="d-none d-lg-block">Thêm Vai Trò</span>
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
                <table v-if="roles.length > 0" class="table border">
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
                            <th scope="col" class="fs-14 fw-semibold min-w-150">Tên Vai Trò</th>
                            <th scope="col" class="fs-14 fw-semibold min-w-100">Số Quyền</th>
                            <th scope="col" class="fs-14 fw-semibold min-w-150">Số Người Dùng</th>
                            <th scope="col" class="fs-14 fw-semibold min-w-400">Mô Tả</th>
                            <th scope="col" class="fs-14 fw-semibold min-w-200">Ngày Tạo</th>
                            <th scope="col" class="fs-14 fw-semibold min-w-100 text-center">Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="role in roles">
                            <td scope="row" class="align-middle">
                                <div class="checkbox me-2">
                                    <label class="checkbox-label">
                                        <input type="checkbox" name="check-item" @change="toggleSelection(role.id)"
                                            :value="role.id" :checked="selectedRoleIds.includes(role.id)" />
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
                            <td class="align-middle fs-14">{{ role.name }}</td>
                            <td class="align-middle">
                                <p class="m-0">{{ role.permissions_count }}</p>
                            </td>
                            <td class="align-middle">
                                <p class="m-0">{{ role.users_count }}</p>
                            </td>
                            <td class="align-middle">
                                <p class="fs-14 my-4">
                                    {{ role.description ?? '_' }}
                                </p>
                            </td>
                            <td class="align-middle fs-14">{{ role.created_at }}</td>
                            <td class="align-middle text-center">
                                <div class="more-wrapper">
                                    <button class="more-btn">
                                        <i class="ri-more-fill"></i>
                                    </button>
                                    <div class="more-popup two-row">
                                        <button class="bg-warning text-dark" data-bs-toggle="modal"
                                            data-bs-target="#modalEditRole" @click="showRoleEdit(role.id)">
                                            Sửa
                                            <i class="ri-edit-line"></i>
                                        </button>
                                        <button type="button" @click="openDeleteModal(role.id)"
                                            class="bg-danger text-white">
                                            Xóa
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                        <button type="button" @click="handleAssignPermissions(role.id)"
                                            class="bg-info text-dark">
                                            Gán Quyền
                                            <i class="ri-shield-fill"></i>
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot class="table-active">
                        <tr class="lh-lg align-middle">
                            <th scope="col" class="align-middle">
                                <div class="checkbox">
                                    <label class="checkbox-label align-items-center d-flex gap-1">
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
                                        <span class="fw-medium">All</span>
                                    </label>
                                </div>
                            </th>
                            <th scope="col"></th>
                            <th scope="col"></th>
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
    <div class="modal" id="modalAddRole">
        <div class="modal-dialog">
            <div class="modal-content radius-2">
                <div class="modal-header d-flex align-items-center justify-content-between">
                    <h4 class="modal-title fs-18">Thêm Vai Trò</h4>
                    <span class="fs-18 fw-medium " data-bs-dismiss="modal">
                        <i class="ri-close-line cursor-pointer"></i>
                    </span>
                </div>
                <div class="modal-body">
                    <form @submit.prevent="addRole()">
                        <div class="form-group">
                            <label class="fs-14 fw-medium mb-1">Tên Vai Trò<span class="text-color">*</span></label>
                            <input type="text" v-model="role.name" class="form-control radius-2" name="role_name" />
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
                            <textarea class="form-control radius-2" v-model="role.description" rows="3" id="comment"
                                name="text"></textarea>
                        </div>
                        <button type="submit" :disabled="loading.save_add" style="width: 150px;"
                            class="main-btn px-3 py-1 d-flex align-items-center justify-content-center gap-3">
                            <loading_loader v-if="loading.save_add" size="20px" color="#fff" border="2px" />
                            <span v-else>Thêm Vai Trò</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- END ADD -->
    <div class="modal" id="modalEditRole">
        <div class="modal-dialog">
            <div class="modal-content radius-2">
                <div class="modal-header d-flex align-items-center justify-content-between">
                    <h4 class="modal-title fs-18">Sửa Vai Trò</h4>
                    <span class="fs-18 fw-medium " data-bs-dismiss="modal">
                        <i class="ri-close-line cursor-pointer"></i>
                    </span>
                </div>
                <div class="modal-body">
                    <form @submit.prevent="editRole()">
                        <div class="form-group">
                            <label class="fs-14 fw-medium mb-1">Tên Vai Trò<span class="text-color">*</span></label>
                            <input type="text" v-model="role_edit.name" class="form-control radius-2"
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
                            <textarea class="form-control radius-2" v-model="role_edit.description" rows="3"
                                id="comment" name="text"></textarea>
                        </div>
                        <button type="submit" :disabled="loading.save_edit" style="width: 170px;"
                            class="main-btn px-3 py-1 d-flex align-items-center justify-content-center gap-3">
                            <loading_loader v-if="loading.save_edit" size="20px" color="#fff" border="2px" />
                            <span v-else>Cập Nhật Vai Trò</span>
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
                    <p class="m-0">Bạn có chắc muốn xóa vai trò.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" :disabled="loading.delete" @click="handleDeleteRoles()" style="width: 150px;"
                        class="main-btn px-3 py-1 d-flex align-items-center radius-2 justify-content-center gap-3">
                        <loading_loader v-if="loading.delete" size="20px" color="#fff" border="2px" />
                        <span v-else>Xác nhận xóa</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- END DELETE -->
    <div v-if="isAssignPermissionsModalVisible" class="modal fade show d-block" style="background: rgba(0,0,0,0.5);">
        <div class=" modal-dialog">
            <div class="modal-content radius-2">
                <div class="modal-header d-flex align-items-center justify-content-between">
                    <h4 class="modal-title fs-18">Gán Quyền Cho Vai Trò</h4>
                    <span class="fs-18 fw-medium" @click="isAssignPermissionsModalVisible = false">
                        <i class="ri-close-line cursor-pointer"></i>
                    </span>
                </div>
                <div class="modal-body">
                    <div v-if="loading.fetch_permission_assignment"
                        class="d-flex align-items-center justify-content-center" style="min-height: 350px;">
                        <loading_loader size="20px" border="2px" />
                    </div>
                    <form v-else class="fade-in">
                        <div class="row g-3" v-if="all_permissions.length > 0">
                            <div v-for="permission in all_permissions" class="col-6">
                                <div class="d-flex align-items-center gap-2">
                                    <label class="switch">
                                        <input type="checkbox" :id="`checkbox${permission.id}`"
                                            :checked="role_permissions.active.includes(permission.id)"
                                            @change="togglePermission(permission.id)" />
                                        <span class="slider"></span>
                                    </label>
                                    <label :for="`checkbox${permission.id}`">{{ permission.name }}</label>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" @click="resetSelectedPermissions()"
                        class="white-btn px-3 py-1 d-flex align-items-center radius-2 justify-content-center gap-3">
                        Đặt lại
                    </button>
                    <button type="button" @click="unselectAllPermissions()"
                        class="white-btn px-3 py-1 d-flex align-items-center radius-2 justify-content-center gap-3">
                        Bỏ chọn tất cả
                    </button>
                    <button type="button" @click="selectedPermissionAll()"
                        class="white-btn px-3 py-1 d-flex align-items-center radius-2 justify-content-center gap-3">
                        Chọn tất cả
                    </button>
                    <button type="button" :class="{
                        'disabled': !hasChanged
                    }" :disabled="!hasChanged" @click="savePermissionAssignment()" style="width: 70px;"
                        class="main-btn px-3 py-1 d-flex align-items-center radius-2 justify-content-center gap-3">
                        <loading_loader v-if="loading.save_permission_assignment" size="20px" color="#fff"
                            border="2px" />
                        <span v-else>Lưu</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- END ASSIGN PERMISSIONS -->
</template>
<script setup>
import { computed, onMounted, ref } from 'vue'
import api from '@/configs/api'
import message from '@/utils/message_state'
import loading_loader from '@/components/loading/loading__loader-circle.vue'
import pagination from '@/components/pagination.vue'
import no_data from '@/components/no_data.vue'

/*
    ASSIGN PERMISSIONS
*/
const selectedRoleId = ref(null)
const all_permissions = ref([])
const role_permissions = ref({
    active: [],
    temp: []
})

const isAssignPermissionsModalVisible = ref(false)
const handleAssignPermissions = async (role_id) => {
    selectedRoleId.value = null
    isAssignPermissionsModalVisible.value = true
    getPermissionAssignment(role_id)
}
const getPermissionAssignment = async (role_id) => {
    try {
        loading.value.fetch_permission_assignment = true
        selectedRoleId.value = role_id
        const resPermissionAssignment = await api.get(`roles/${role_id}/permissions`)
        if (resPermissionAssignment.status == 200) {
            all_permissions.value = resPermissionAssignment.data.all_permissions
            role_permissions.value = {
                active: [...resPermissionAssignment.data.role_permissions],
                temp: [...resPermissionAssignment.data.role_permissions]
            };

        }
    } catch (error) {
        message.emit("show-message", {
            title: "Thông báo",
            text: "Đã xảy ra lỗi khi lấy dữ liệu phân quyền.",
            type: "error"
        })
    } finally {
        loading.value.fetch_permission_assignment = false
    }
}
const togglePermission = (permissionId) => {
    const index = role_permissions.value.active.indexOf(permissionId);
    if (index === -1) {
        role_permissions.value.active.push(permissionId);
    } else {
        role_permissions.value.active.splice(index, 1);
    }
}

const hasChanged = computed(() => {
    const a = [...role_permissions.value.active].sort((x, y) => x - y);
    const b = [...role_permissions.value.temp].sort((x, y) => x - y);

    return JSON.stringify(a) !== JSON.stringify(b);
});

const savePermissionAssignment = async () => {
    const role_id = selectedRoleId.value
    if (role_permissions.value.active.length === 0) {
        message.emit("show-message", {
            title: "Thông báo",
            text: "Vui lòng chọn ít nhất một quyền",
            type: "warning"
        })
        return;
    }
    try {
        loading.value.save_permission_assignment = true
        const resUpdatePermissionAssignment = await api.put(`roles/${role_id}/permissions`, {
            permission_ids: role_permissions.value.active
        })
        if (resUpdatePermissionAssignment.status == 200) {
            updatePermissionCount(role_id)
            message.emit("show-message", {
                title: "Thông báo",
                text: resUpdatePermissionAssignment.data.message,
                type: "success"
            })
            role_permissions.value.temp = [...role_permissions.value.active]
        }
    } catch (error) {
        error = error.response
        if (error.status === 500) {
            message.emit("show-message", {
                title: "Thông báo",
                text: error.data.message,
                type: "error"
            })
        } else {
            console.warn('Lỗi validation:', error)
        }
    } finally {
        loading.value.save_permission_assignment = false
    }
}
const updatePermissionCount = (role_id) => {
    const index = roles.value.findIndex((role) => role.id === role_id)
    if (index != -1) {
        roles.value[index].permissions_count = role_permissions.value.active.length
    }
}

const selectedPermissionAll = () => {
    role_permissions.value.active = [...all_permissions.value.map((permission) => permission.id)]
}

const unselectAllPermissions = () => {
    role_permissions.value.active = []
}

const resetSelectedPermissions = () => {
    role_permissions.value.active = [...role_permissions.value.temp]
}
/*
    SUPORT
*/
const loading = ref({
    save_add: false,
    fetch_list: false,
    save_edit: false,
    delete: false,
    fetch_permission_assignment: false,
    save_permission_assignment: false,
})

/*
    GET
*/
const roles = ref([])
const getRoles = async () => {
    try {
        loading.value.fetch_list = true
        const resRoles = await api.get('/roles', {
            params: {
                page: currentPage.value,
                search: searchValue.value
            }
        })
        if (resRoles.status == 200) {
            meta.value = resRoles.data.meta
            roles.value = resRoles.data.data
        }
    } catch (error) {
        console.log(error);

        message.emit("show-message", {
            title: "Thông báo",
            text: "Đã xảy ra lỗi khi lấy dữ liệu vai trò.",
            type: "error"
        })
    } finally {
        loading.value.fetch_list = false
    }

}

/*
    ADD
*/
const role = ref({
    name: '',
    description: null
})
const addRole = async () => {
    try {
        if (!validate(role.value)) return
        loading.value.save_add = true
        const payload = role.value
        const resAddRole = await api.post('/roles', payload)
        if (resAddRole.status == 201) {
            roles.value.unshift(resAddRole.data)

            role.value = {
                name: '',
                description: null
            }

            message.emit("show-message", {
                title: "Thông báo",
                text: "Tạo vai trò thành công.",
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
const role_edit = ref({})
const showRoleEdit = (role_id) => {
    role_edit.value = {}
    if (role_id) {
        role_edit.value = { ...roles.value.find((r) => r.id == role_id) }
    } else {
        message.emit("show-message", {
            title: "Thông báo",
            text: "Không tìm thấy vai trò.",
            type: "warning"
        })
    }
}

const editRole = async () => {
    try {
        if (!validate(role_edit.value)) return
        loading.value.save_edit = true
        const payload = {
            name: role_edit.value?.name ?? '',
            description: role_edit.value?.description ?? ''
        }

        const resEditRole = await api.put(`/roles/${role_edit.value.id}`, payload)
        if (resEditRole.status == 200) {
            const index = roles.value.findIndex(r => r.id === role_edit.value.id)
            if (index !== -1) {
                roles.value[index] = { ...roles.value[index], ...payload }
            }

            message.emit("show-message", {
                title: "Thông báo",
                text: resEditRole.data.message,
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
const selectedRoleIds = ref([])

const isAllSelected = computed(() => {
    const allIds = roles.value.map(role => role.id)
    return (
        selectedRoleIds.value.length === allIds.length &&
        selectedRoleIds.value.every(id => allIds.includes(id))
    )
})

const toggleSelection = (id) => {
    if (selectedRoleIds.value.includes(id)) {
        selectedRoleIds.value = selectedRoleIds.value.filter(role_id => role_id !== id)
    } else {
        selectedRoleIds.value.push(id)
    }
}

const toggleSelectionAll = (e) => {
    const checked = e.target.checked
    const allIds = roles.value.map(role => role.id)
    if (checked) {
        selectedRoleIds.value = allIds
    } else {
        selectedRoleIds.value = []
    }
}

const openDeleteModal = (id = null) => {
    isDeleteModalVisible.value = true
    if (id) {
        selectedRoleIds.value = []
        selectedRoleIds.value = [id]
    }
    if (selectedRoleIds.value.length === 0) {
        message.emit('show-message', {
            title: 'Thông báo',
            text: 'Vui lòng chọn vai trò muốn xóa',
            type: 'info'
        })
        isDeleteModalVisible.value = false
        return
    }
}

const handleDeleteRoles = async () => {
    try {
        const ids = selectedRoleIds.value
        loading.value.delete = true
        const resDelete = await api.delete('/roles', {
            data: {
                ids: ids
            }
        })
        if (resDelete.status == 200) {
            roles.value = roles.value.filter((role) => !resDelete.data.deleted_ids.includes(role.id))
            const mess = resDelete.data.not_deleted_names
                ? `Vai trò ${resDelete.data.not_deleted_names} không thể xóa vì có tài khoản đang sử dụng.`
                : 'Đã xóa thành công.'
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
        selectedRoleIds.value = []
    }
}

/*
    PAGINATION & SEARCH
*/
const meta = ref({})
const currentPage = ref(1)
const onChangePage = (page) => {
    currentPage.value = page
    getRoles()
}
const searchValue = ref('')
const onSearch = () => {
    currentPage.value = 1
    getRoles()
}
const clearSearch = () => {
    searchValue.value = ''
    getRoles()
}

/*
    VALIDATE
*/
const errors = ref({})
const validate = (role) => {
    errors.value = {}
    if (!role.name) {
        errors.value.name = 'Tên vai trò không được để trống.'
    } else if (role.name.length < 3) {
        errors.value.name = 'Tên vai trò không được quá ngắn, tối thiểu 3 ký tự.'
    }

    if (role.description && role.description.length > 255) {
        errors.value.description = 'Mô tả không được vượt quá 255 ký tự.'
    }
    return Object.keys(errors.value).length === 0

}

onMounted(() => {
    getRoles()
})
</script>