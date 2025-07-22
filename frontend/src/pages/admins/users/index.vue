<template>
    <div class="admin__content">
        <div class="d-flex flex-wrap justify-content-end gap-2 pe-3">
            <button @click="downloadTemplate()" class="btn btn-secondary btn-sm radius-2">
                📄 Tải mẫu CSV
            </button>
            <button @click="importUsers()" class="btn btn-primary btn-sm radius-2">
                📤 Nhập người dùng
            </button>
            <button @click="exportUsers()" class="btn btn-success btn-sm radius-2">
                📥 Xuất người dùng
            </button>
        </div>


        <div
            class="p-3 bg-white border-bottom text-grey fs-14 fw-medium d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <p class="m-0">
                    {{ $t('user.list_title') }}
                </p>
                <span>|</span>
                <button class="border-0 bg-white" @click="fetchTrashedUsers()" data-bs-toggle="modal"
                    data-bs-target="#modalShowTrashUser">
                    <i class="fs-16 cursor-pointer ri-delete-bin-5-line text-grey"></i>
                </button>
            </div>
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
                            <input type="text" v-model="searchValue" :placeholder="$t('user.search_placeholder')"
                                class="form-control radius-2" />
                            <button v-if="searchValue" type="button" @click="clearSearch"
                                class="btn position-absolute top-50 end-0 translate-middle-y me-2 p-0 border-0 bg-transparent">
                                <i class="ri-close-line"></i>
                            </button>
                        </div>
                    </form>
                </div>
                <button type="button" class="main-btn px-3" style="height: 38px;" data-bs-toggle="modal"
                    data-bs-target="#modalAddUser">
                    <span class="d-none d-lg-block">{{ $t('user.add_user') }}</span>
                    <span class="d-block d-lg-none"><i class="ri-add-fill"></i></span>
                </button>
            </div>
        </div>
        <div class="p-10">
            <div v-if="loading.fetch" style="height: 60vh;" class="d-flex align-items-center justify-content-center">
                <loading_loader size="40px" border="4px" />
            </div>
            <div v-else class="table-responsive fade-in">
                <table v-if="users.length > 0" class="table border">
                    <thead class="table-active">
                        <tr class="lh-lg align-middle">
                            <th scope="col" class="min-w-50">
                                <div class="checkbox">
                                    <label class="checkbox-label">
                                        <input type="checkbox" name="check-item" @change="toggleSelectAll($event)"
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
                            <th scope="col" class="fs-14 fw-semibold min-w-150">{{ $t('user.personal_info') }}</th>
                            <th scope="col" class="fs-14 fw-semibold min-w-150">{{ $t('user.email') }}</th>
                            <th scope="col" class="fs-14 fw-semibold min-w-100">{{ $t('user.role') }}</th>
                            <th scope="col" class="fs-14 fw-semibold min-w-150">{{ $t('user.verified_at') }}</th>
                            <th scope="col" class="fs-14 fw-semibold min-w-150">{{ $t('user.created_at') }}</th>
                            <th scope="col" class="fs-14 fw-semibold min-w-100">{{ $t('user.status') }}</th>
                            <th scope="col" class="fs-14 fw-semibold min-w-100 text-center">{{ $t('user.actions') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="user in users">
                            <td scope="row" class="align-middle">
                                <div class="checkbox me-2">
                                    <label class="checkbox-label">
                                        <input type="checkbox" name="check-item" @change="toggleSelection(user.id)"
                                            :value="user.id" :checked="selectedUserIds.includes(user.id)" />
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
                            <td class="align-middle fs-14">
                                <div class="d-flex flex-column gap-2">
                                    <img v-if="user.avatar" style="width: 50px; height: 50px;"
                                        :src="`${BACKEND_URL}/images/users/${user.avatar}`" :alt="user.name">
                                    <img v-else style="width: 50px; height: 50px;"
                                        :src="`${BACKEND_URL}/images/user-default.jpg`" :alt="user.name">
                                    <p class="fs-14">
                                        {{ user.name }}
                                    </p>
                                    <p class="fs-14">
                                        {{ user.phone ?? '_' }}
                                    </p>
                                </div>
                            </td>
                            <td class="align-middle">
                                <p class="m-0">{{ user.email }}</p>
                            </td>
                            <td class="align-middle fs-14">
                                {{ user.roles.length > 0 ? user.roles.join(', ') : '_' }}
                            </td>
                            <td class="align-middle fs-14">{{ user.email_verified_at ?? '_' }}</td>
                            <td class="align-middle fs-14">{{ user.created_at }}</td>
                            <td class="align-middle fs-14">
                                <p class="text-white text-center py-1 radius-2 fw-medium" :class="{
                                    'bg-success': user.active == 1,
                                    'bg-danger': user.active == 0,
                                }">
                                    {{ user.active == 1 ? $t('user.active') : $t('user.inactive') }}
                                </p>
                            </td>
                            <td class="align-middle text-center">
                                <div class="more-wrapper">
                                    <button class="more-btn">
                                        <i class="ri-more-fill"></i>
                                    </button>
                                    <div class="more-popup" style="top: -70px;">
                                        <button v-if="user.active == 1" type="button"
                                            @click="updateUserStatus(user.id, false)" class="bg-danger text-white">
                                            {{ $t('user.inactive') }}
                                            <i class="ri-lock-2-line"></i>
                                        </button>
                                        <button v-if="user.active == 0" type="button"
                                            @click="updateUserStatus(user.id, true)" class="bg-success text-white">
                                            {{ $t('user.active') }}
                                            <i class="ri-lock-2-line"></i>
                                        </button>
                                        <button type="button" @click="deleteUserConfirm(user.id)"
                                            class="bg-danger text-white">
                                            {{ $t('action.delete') }}
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                        <button type="button" @click="showEditModal(user.id)" data-bs-toggle="modal"
                                            data-bs-target="#modalEditUser" class="bg-warning text-dark">
                                            {{ $t('action.edit') }}
                                            <i class="ri-edit-line"></i>
                                        </button>
                                        <button type="button" @click="openAssignRolesModal(user.id)"
                                            class="bg-success text-white">
                                            {{ $t('action.assign_role') }}
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
                                        <input type="checkbox" name="check-item" @change="toggleSelectAll($event)"
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
                            <th scope="col"></th>
                            <td class="align-middle text-center">
                                <div class="more-wrapper">
                                    <button class="more-btn p-0">
                                        <i class="ri-more-fill"></i>
                                    </button>
                                    <div class="more-popup">
                                        <button type="button" @click="deleteUserConfirm()"
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
    <div class="modal" id="modalAddUser">
        <div class="modal-dialog">
            <div class="modal-content radius-2">
                <div class="modal-header d-flex align-items-center justify-content-between">
                    <h4 class="modal-title fs-18">{{ $t('user.add_user') }}</h4>
                    <span class="fs-18 fw-medium " data-bs-dismiss="modal">
                        <i class="ri-close-line cursor-pointer"></i>
                    </span>
                </div>
                <div class="modal-body">
                    <form @submit.prevent="addUser()">
                        <div class="form-group">
                            <label class="fs-14 fw-medium mb-1">{{ $t('user.name_user') }}<span
                                    class="text-color">*</span></label>
                            <input type="text" v-model="userForm.name" class="form-control radius-2" />
                        </div>
                        <p v-if="errors.name" class="text-danger fs-14">
                            {{
                                Array.isArray(errors.name)
                                    ? errors.name[0]
                                    : errors.name
                            }}
                        </p>
                        <div class="form-group">
                            <label class="fs-14 fw-medium mb-1">Email<span class="text-color">*</span></label>
                            <input type="email" v-model="userForm.email" class="form-control radius-2" />
                        </div>
                        <p v-if="errors.email" class="text-danger fs-14">
                            {{
                                Array.isArray(errors.email)
                                    ? errors.email[0]
                                    : errors.email
                            }}
                        </p>
                        <div class="form-group">
                            <label class="fs-14 fw-medium mb-1">
                                {{ $t('user.password') }}
                                <span class="text-color">*</span>
                            </label>
                            <input :type="showPassword ? 'text' : 'password'" v-model="userForm.password"
                                class="form-control radius-2" />
                        </div>
                        <p v-if="errors.password" class="text-danger fs-14">
                            {{
                                Array.isArray(errors.password)
                                    ? errors.password[0]
                                    : errors.password
                            }}
                        </p>
                        <div class="form-group">
                            <label class="fs-14 fw-medium mb-1">
                                {{ $t('user.password_confirmation') }}
                                <span class="text-color">*</span>
                            </label>
                            <input :type="showPassword ? 'text' : 'password'" v-model="userForm.password_confirmation"
                                class="form-control radius-2" />
                        </div>
                        <p v-if="errors.password_confirmation" class="text-danger fs-14">
                            {{
                                Array.isArray(errors.password_confirmation)
                                    ? errors.password_confirmation[0]
                                    : errors.password_confirmation
                            }}
                        </p>
                        <div class="d-flex align-items-center justify-content-start my-2 gap-2">
                            <label for="show_pass" class="cursor-pointer">
                                {{ $t('user.show_password') }}
                            </label>
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
                        <button type="submit" :disabled="loading.add" style="width: 200px;"
                            class="main-btn px-3 py-1 d-flex align-items-center justify-content-center gap-3">
                            <loading_loader v-if="loading.add" size="20px" color="#fff" border="2px" />
                            <span v-else>{{ $t('action.add') }}</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- END ADD -->
    <div class="modal" id="modalEditUser">
        <div class="modal-dialog">
            <div class="modal-content radius-2">
                <div class="modal-header d-flex align-items-center justify-content-between">
                    <h4 class="modal-title fs-18">{{ $t('user.edit_user') }}</h4>
                    <span class="fs-18 fw-medium " data-bs-dismiss="modal">
                        <i class="ri-close-line cursor-pointer"></i>
                    </span>
                </div>
                <div class="modal-body">
                    <form @submit.prevent="editUser()">
                        <div class="form-group">
                            <label class="fs-14 fw-medium mb-1">
                                {{ $t('user.name_user') }}
                                <span class="text-color">*</span>
                            </label>
                            <input type="text" v-model="editForm.name" class="form-control radius-2" />
                        </div>
                        <p v-if="errors.name" class="text-danger fs-14">
                            {{
                                Array.isArray(errors.name)
                                    ? errors.name[0]
                                    : errors.name
                            }}
                        </p>
                        <div class="form-group">
                            <label class="fs-14 fw-medium mb-1">Email<span class="text-color">*</span></label>
                            <input type="email" disabled v-model="editForm.email" class="form-control radius-2" />
                        </div>
                        <p v-if="errors.email" class="text-danger fs-14">
                            {{
                                Array.isArray(errors.email)
                                    ? errors.email[0]
                                    : errors.email
                            }}
                        </p>
                        <button type="submit" :disabled="loading.edit" style="width: 150px;"
                            class="main-btn px-3 py-1 d-flex align-items-center justify-content-center gap-3 mt-2">
                            <loading_loader v-if="loading.edit" size="20px" color="#fff" border="2px" />
                            <span v-else>{{ $t('action.update') }}</span>
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
                    <h4 class="modal-title fs-18">{{ $t('action.confirm_delete') }}</h4>
                    <span class="fs-18 fw-medium" @click="isDeleteModalVisible = false">
                        <i class="ri-close-line cursor-pointer"></i>
                    </span>
                </div>
                <div class="modal-body">
                    <p class="m-0">{{ $t('user.confirm_delete_message') }}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" :disabled="loading.delete" @click="deleteUsers()" style="width: 150px;"
                        class="main-btn px-3 py-1 d-flex align-items-center radius-2 justify-content-center gap-3">
                        <loading_loader v-if="loading.delete" size="20px" color="#fff" border="2px" />
                        <span v-else>{{ $t('user.confirm_delete_title') }}</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- END DELETE -->
    <div v-if="isAssignRolesModalVisible" class="modal fade show d-block" style="background: rgba(0,0,0,0.5);">
        <div class=" modal-dialog">
            <div class="modal-content radius-2">
                <div class="modal-header d-flex align-items-center justify-content-between">
                    <h4 class="modal-title fs-18">{{ $t('user.role') }}</h4>
                    <span class="fs-18 fw-medium" @click="isAssignRolesModalVisible = false">
                        <i class="ri-close-line cursor-pointer"></i>
                    </span>
                </div>
                <div class="modal-body">
                    <div v-if="loading.fetchRole" class="d-flex align-items-center justify-content-center"
                        style="min-height: 350px;">
                        <loading_loader size="20px" border="2px" />
                    </div>
                    <form v-else class="fade-in">
                        <div class="row g-3" v-if="roles.length > 0">
                            <div v-for="role in roles" class="col-6">
                                <div class="d-flex align-items-center gap-2">
                                    <label class="switch">
                                        <input type="checkbox" :id="`checkbox${role}`"
                                            :checked="assignedRoles.active.includes(role)" @change="toggleRole(role)" />
                                        <span class="slider"></span>
                                    </label>
                                    <label :for="`checkbox${role}`">{{ role }}</label>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" @click="resetRoles()"
                        class="white-btn px-3 py-1 d-flex align-items-center radius-2 justify-content-center gap-3">
                        {{ $t('action.reset') }}
                    </button>
                    <button type="button" @click="unselectAllRoles()"
                        class="white-btn px-3 py-1 d-flex align-items-center radius-2 justify-content-center gap-3">
                        {{ $t('action.unselect_all') }}
                    </button>
                    <button type="button" @click="selectAllRoles()"
                        class="white-btn px-3 py-1 d-flex align-items-center radius-2 justify-content-center gap-3">
                        {{ $t('action.select_all') }}
                    </button>
                    <button type="button" :class="{
                        'disabled': !hasRoleChanged
                    }" @click="saveUserRoles()" :disabled="!hasRoleChanged" style="width: 70px;"
                        class="main-btn px-3 py-1 d-flex align-items-center radius-2 justify-content-center gap-3">
                        <loading_loader v-if="loading.saveRole" size="20px" color="#fff" border="2px" />
                        <span v-else>{{ $t('action.save') }}</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- END ASSIGN ROLES -->
    <div class="modal" id="modalShowTrashUser">
        <div class="modal-dialog">
            <div class="modal-content radius-2">
                <div class="modal-header d-flex align-items-center justify-content-between">
                    <h4 class="modal-title fs-18">{{ $t('user.trashed_list_title') }}</h4>
                    <span class="fs-18 fw-medium " data-bs-dismiss="modal">
                        <i class="ri-close-line cursor-pointer"></i>
                    </span>
                </div>
                <div v-if="loading.fetchTrashed" style="height: 60vh;"
                    class="d-flex align-items-center justify-content-center">
                    <loading_loader size="40px" border="4px" />
                </div>
                <div v-else class="modal-body">
                    <div v-if="trashedUsers.length == 0">
                        <no_data />
                    </div>
                    <div v-else class="row g-3">
                        <div v-for="trashed_user in trashedUsers" class="col-12">
                            <div
                                class="border rounded-3 p-3 d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
                                <div class="mb-2 mb-md-0">
                                    <p class="mb-1 fw-bold text-primary">{{ trashed_user.name }}</p>
                                    <p class="mb-1 text-muted"><i class="ri-mail-line me-1"></i>{{ trashed_user.email }}
                                    </p>
                                    <p class="mb-1 text-muted" v-if="trashed_user.phone">
                                        <i class="ri-phone-line me-1"></i>
                                        {{ trashed_user.phone }}
                                    </p>
                                </div>
                                <div class="d-flex flex-column flex-md-row align-items-md-center gap-2">
                                    <p class="mb-0 text-danger fs-14">🗑️ {{ $t('user.date_deleted') }}: {{
                                        trashed_user.deleted_at }}</p>
                                    <button type="button" class="btn btn-outline-success btn-sm"
                                        @click="restoreUser(trashed_user.id)">
                                        <i class="ri-refresh-line me-1"></i>
                                        {{ $t('user.restore') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END SHOW USER TRASH -->
    <div v-if="loading.import" class="modal fade show d-block" style="background: rgba(0,0,0,0.5);">
        <div class=" modal-dialog">
            <div class="modal-content radius-2">
                <div class="modal-body d-flex align-items-center justify-content-center flex-column gap-3"
                    style="min-height: 300px;">
                    <p class="m-0">Đang thực hiện import người dùng...</p>
                    <loading_loader size="25px" border="3px" />
                </div>
            </div>
        </div>
    </div>
    <!-- END SHOW LOADING IMPORT -->
</template>
<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '@/configs/api'
import message from '@/utils/message_state'
import loading_loader from '@/components/loading/loading__loader-circle.vue'
import pagination from '@/components/pagination.vue'
import no_data from '@/components/no_data.vue'
import { BACKEND_URL } from '@/configs/env'
import { saveAs } from 'file-saver'

// ============ State =============
const users = ref([])
const trashedUsers = ref([])
const roles = ref([])
const assignedRoles = ref({ active: [], temp: [] })
const selectedUserIds = ref([])
const selectedUserId = ref(null)
const showPassword = ref(false)
const loading = ref({
    add: false,
    edit: false,
    fetch: false,
    delete: false,
    fetchRole: false,
    saveRole: false,
    fetchTrashed: false,
    restore: false,
    import: false
})
const errors = ref({})
const meta = ref({})
const currentPage = ref(1)
const searchValue = ref('')
const isDeleteModalVisible = ref(false)
const isAssignRolesModalVisible = ref(false)
const userForm = ref({ name: '', email: '', password: '', password_confirmation: '' })
const editForm = ref({})
// =================================

// ============ User CRUD =============
const fetchUsers = async () => {
    loading.value.fetch = true
    try {
        const res = await api.get('/users', {
            params: { page: currentPage.value, search: searchValue.value }
        })
        users.value = res.data.data
        meta.value = res.data.meta
    } catch {
        message.emit('show-message', { title: "Error", text: "Failed to fetch users.", type: "error" })
    } finally {
        loading.value.fetch = false
    }
}

const addUser = async () => {
    if (!validateUserForm(userForm.value)) return
    loading.value.add = true
    try {
        const res = await api.post('/users', userForm.value)
        if (res.status === 201) {
            users.value.unshift(res.data)
            Object.assign(userForm.value, { name: '', email: '', password: '', password_confirmation: '' })
            message.emit('show-message', { title: "Success", text: "Thêm người dùng thành công.", type: "success" })
        }
    } catch (err) {
        handleFormError(err)
    } finally {
        loading.value.add = false
    }
}

const showEditModal = (userId) => {
    const user = users.value.find(u => u.id === userId)
    if (user) editForm.value = { ...user }
    else message.emit('show-message', { title: "Warning", text: "Người dùng không tồn tại.", type: "warning" })
}

const editUser = async () => {
    if (!validateEditForm(editForm.value)) return
    loading.value.edit = true
    try {
        const res = await api.put(`/users/${editForm.value.id}`, { name: editForm.value.name })
        if (res.status === 200) {
            const idx = users.value.findIndex(u => u.id === editForm.value.id)
            if (idx !== -1) users.value[idx].name = editForm.value.name
            message.emit('show-message', { title: "Success", text: "Cập nhật người dùng thành công.", type: "success" })
        }
    } catch (err) {
        handleFormError(err)
    } finally {
        loading.value.edit = false
    }
}

const updateUserStatus = async (id, isActive) => {
    try {
        const res = await api.patch(`/users/${id}/status`, { active: isActive })
        if (res.status === 200) {
            const idx = users.value.findIndex(u => u.id === id)
            if (idx !== -1) users.value[idx].active = isActive
            message.emit('show-message', { title: "Success", text: res.data.message, type: "success" })
        }
    } catch (err) {
        handleFormError(err)
    }
}

const deleteUserConfirm = (id = null) => {
    if (id) selectedUserIds.value = [id]
    if (!selectedUserIds.value.length) {
        message.emit('show-message', { title: 'Info', text: 'Chọn người dùng để xóa', type: 'info' })
        return
    }
    isDeleteModalVisible.value = true
}

const deleteUsers = async () => {
    loading.value.delete = true
    try {
        const res = await api.delete('/users', { data: { ids: selectedUserIds.value } })
        if (res.status === 200) {
            users.value = users.value.filter(u => !res.data.deleted_ids.includes(u.id))
            message.emit('show-message', {
                title: "Info",
                text: res.data.not_deleted_names ? `Không thể xóa user: ${res.data.not_deleted_names}` : "Đã xóa user.",
                type: "info"
            })
        }
    } catch (err) {
        handleFormError(err)
    } finally {
        loading.value.delete = false
        isDeleteModalVisible.value = false
        selectedUserIds.value = []
    }
}
// =================================

// ============ Search & Pagination =============
const onSearch = () => {
    currentPage.value = 1
    fetchUsers()
}
const clearSearch = () => {
    searchValue.value = ''
    fetchUsers()
}
const onChangePage = (page) => {
    currentPage.value = page
    fetchUsers()
}
// =================================

// ============ Selection =============
const isAllSelected = computed(() => {
    const allIds = users.value.map(u => u.id)
    return selectedUserIds.value.length === allIds.length && allIds.every(id => selectedUserIds.value.includes(id))
})
const toggleSelection = (id) => {
    const idx = selectedUserIds.value.indexOf(id)
    if (idx !== -1) selectedUserIds.value.splice(idx, 1)
    else selectedUserIds.value.push(id)
}
const toggleSelectAll = (e) => {
    const checked = e.target.checked
    selectedUserIds.value = checked ? users.value.map(u => u.id) : []
}
// =================================

// ============ Role Assignment =============
const openAssignRolesModal = async (userId) => {
    selectedUserId.value = userId
    isAssignRolesModalVisible.value = true
    await fetchUserRoles(userId)
}
const fetchUserRoles = async (userId) => {
    loading.value.fetchRole = true
    try {
        const res = await api.get(`/users/${userId}/roles`)
        roles.value = res.data.all_roles
        assignedRoles.value = {
            active: [...res.data.assigned_roles],
            temp: [...res.data.assigned_roles]
        }
    } catch {
        message.emit('show-message', { title: "Error", text: "Failed to fetch roles.", type: "error" })
    } finally {
        loading.value.fetchRole = false
    }
}
const toggleRole = (role) => {
    const idx = assignedRoles.value.active.indexOf(role)
    idx === -1 ? assignedRoles.value.active.push(role) : assignedRoles.value.active.splice(idx, 1)
}
const hasRoleChanged = computed(() =>
    JSON.stringify([...assignedRoles.value.active].sort()) !== JSON.stringify([...assignedRoles.value.temp].sort())
)
const saveUserRoles = async () => {
    if (!assignedRoles.value.active.length) {
        message.emit('show-message', { title: "Warning", text: "Chọn ít nhất 1 vai trò", type: "warning" })
        return
    }
    loading.value.saveRole = true
    try {
        const res = await api.put(`/users/${selectedUserId.value}/roles`, { roles: assignedRoles.value.active })
        const idx = users.value.findIndex(u => u.id === selectedUserId.value)
        if (idx !== -1) users.value[idx].roles = [...assignedRoles.value.active]
        assignedRoles.value.temp = [...assignedRoles.value.active]
        message.emit('show-message', { title: "Success", text: res.data.message, type: "success" })
    } finally {
        loading.value.saveRole = false
    }
}
const selectAllRoles = () => assignedRoles.value.active = [...roles.value]
const unselectAllRoles = () => assignedRoles.value.active = []
const resetRoles = () => assignedRoles.value.active = [...assignedRoles.value.temp]
// =================================

// ============ Trashed User =============
const fetchTrashedUsers = async () => {
    loading.value.fetchTrashed = true
    try {
        const res = await api.get('/users/trashed')
        trashedUsers.value = res.data
    } catch {
        message.emit('show-message', { title: "Error", text: "Không lấy được user đã xóa", type: "error" })
    } finally {
        loading.value.fetchTrashed = false
    }
}
const restoreUser = async (userId) => {
    if (!confirm('Bạn chắc chắn muốn khôi phục?')) return
    loading.value.restore = true
    try {
        const res = await api.put(`/users/${userId}/restore`)
        // Di chuyển user từ trash về users
        const idx = trashedUsers.value.findIndex(u => u.id === userId)
        if (idx !== -1) {
            users.value.push(trashedUsers.value[idx])
            trashedUsers.value.splice(idx, 1)
        }
        message.emit('show-message', { title: "Success", text: res.data.message, type: "success" })
    } catch (err) {
        message.emit('show-message', { title: "Error", text: err?.response?.data?.message || "Restore failed", type: "error" })
    } finally {
        loading.value.restore = false
    }
}
// =================================

// ============ Validate & Error =============
function validateUserForm(form) {
    errors.value = {}
    if (!form.name?.trim()) errors.value.name = "Tên không được để trống."
    else if (form.name.trim().length < 3) errors.value.name = "Tên tối thiểu 3 ký tự."
    if (!form.email) errors.value.email = "Email không được để trống."
    else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email)) errors.value.email = "Email không hợp lệ."
    if (!form.password) errors.value.password = "Mật khẩu không được để trống."
    else if (form.password.length < 8) errors.value.password = "Mật khẩu tối thiểu 8 ký tự."
    else if (!/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/.test(form.password))
        errors.value.password = "Mật khẩu phải có chữ hoa, thường, số, ký tự đặc biệt."
    if (!form.password_confirmation) errors.value.password_confirmation = "Xác nhận mật khẩu không được để trống."
    else if (form.password !== form.password_confirmation)
        errors.value.password_confirmation = "Mật khẩu xác nhận không khớp."
    return Object.keys(errors.value).length === 0
}
function validateEditForm(form) {
    errors.value = {}
    if (!form.name?.trim()) errors.value.name = "Tên không được để trống."
    else if (form.name.trim().length < 3) errors.value.name = "Tên tối thiểu 3 ký tự."
    return Object.keys(errors.value).length === 0
}
function handleFormError(error) {
    const err = error?.response
    if (err?.status === 422) errors.value = err.data.errors
    else message.emit('show-message', { title: "Error", text: err?.data?.message || "Unknown error", type: "error" })
}
// =================================
// ============ Import & Export & DownloadTemplate =============
const exportUsers = async () => {
    try {
        const res = await api.get('/users/export-csv', { responseType: 'blob' });
        saveAs(res.data, 'users.csv')
    } catch (err) {
        console.log(err);
    }
}

const importUsers = async () => {
    try {
        // Tạo input file ẩn để chọn file
        const input = document.createElement('input')
        input.type = 'file'
        input.accept = '.csv,text/csv'
        input.onchange = async (e) => {
            const file = e.target.files[0]
            if (!file) return
            const formData = new FormData()
            formData.append('file', file)
            try {
                loading.value.import = true
                const res = await api.post('/users/import-csv', formData, {
                    headers: { 'Content-Type': 'multipart/form-data' }
                })
                if (res.status == 200) {
                    message.emit('show-message', {
                        title: 'Thông báo',
                        text: res.data.message || 'Đang xử lý file. Vui lòng chờ trong giây lát.',
                        type: 'info'
                    })
                }
            } catch (err) {
                message.emit('show-message', {
                    title: 'Lỗi',
                    text: err?.response?.data?.message || 'Import thất bại',
                    type: 'error'
                })
            } finally {
                loading.value.import = false
            }
        };
        input.click()
    } catch (err) {
        console.log(err)
    }
}

const downloadTemplate = async () => {
    try {
        const res = await api.get('/users/csv-template', { responseType: 'blob' });
        saveAs(res.data, 'user_template.csv');
    } catch (error) {
        console.log(error);
    }
}
// =================================
onMounted(fetchUsers)
</script>