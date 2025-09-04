<template>
    <div class="admin__content p-0 m-0">
        <div
            class="p-3 bg-white border-bottom text-grey fs-14 fw-medium d-flex align-items-center justify-content-between">
            <p class="m-0">
                {{ $t('project.title') }}
            </p>
            <div class="d-flex align-items-center gap-2">
                <div class="dropdown">
                    <button type="button" class="btn btn-light border dropdown-toggle radius-2 fw-medium"
                        data-bs-toggle="dropdown">
                        {{ $t('project.display') }}
                    </button>
                    <ul class="dropdown-menu radius-2">
                        <li v-for="option in [10, 25, 50, 100, 500]" :key="option" @click="setPerPage(option)">
                            <a :class="['dropdown-item fw-medium cursor-pointer', { active: per_page === option }]">
                                {{ option }}
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="dropdown">
                    <button type="button" class="btn btn-light border dropdown-toggle radius-2 fw-medium"
                        data-bs-toggle="dropdown">
                        {{ $t('project.status') }}
                    </button>
                    <ul class="dropdown-menu radius-2">
                        <li v-for="option in ['canceled', 'in_progress', 'completed']" :key="option"
                            @click="fetchProjectsByStatus(option)">
                            <a :class="['dropdown-item fw-medium cursor-pointer', { active: status === option }]">
                                {{ option }}
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="admin__search">
                    <button type="button" class="search__button">
                        <i class="ri-search-2-line"></i>
                    </button>
                    <form @submit.prevent="onSearch">
                        <div class="d-flex align-items-center gap-1 position-relative">
                            <button type="submit" style="height: 38px" class="main-btn py-1 px-3 ">
                                <i class="ri-search-2-line"></i>
                            </button>
                            <input type="text" v-model="searchValue" :placeholder="$t('project.search_placeholder')"
                                class="form-control radius-2" />
                            <button v-if="searchValue" type="button" @click="clearSearch"
                                class="btn position-absolute top-50 end-0 translate-middle-y me-2 p-0 border-0 bg-transparent">
                                <i class="ri-close-line"></i>
                            </button>
                        </div>
                    </form>
                </div>
                <button @click="openModalAddProject()" type="button" class="main-btn px-3" style="height: 38px;"
                    data-bs-toggle="modal" data-bs-target="#modalHandleFormProject">
                    <span class="d-none d-lg-block">{{ $t('project.add_project') }}</span>
                    <span class="d-block d-lg-none"><i class="ri-add-fill"></i></span>
                </button>
            </div>
        </div>
        <div v-if="loading.fetch" style="height: 60vh;" class="d-flex align-items-center justify-content-center">
            <loading_loader size="40px" border="4px" />
        </div>
        <div class="p-3" v-else>
            <no_data v-if="projects.length == 0" />
            <div v-else class="row g-1 fade-in">
                <div v-for="project in projects" class="col-12 col-sm-6 col-lg-4 col-xl-3 cursor-pointer">
                    <div class="position-relative shadow-sm d-flex flex-column gap-3 border p-2 project__action">
                        <div v-if="project.status == 'in_progress'"
                            class="position-absolute top-0 end-0 m-2 d-flex gap-1">
                            <button @click="fetchAssigns(project.id)" class="btn btn-sm btn-light border"
                                data-bs-toggle="modal" data-bs-target="#assignUsersToProjectModal">
                                <i class="ri-user-add-line" title="Phân công người phụ trách"></i>
                            </button>
                            <button @click="openModalEditProject(project.id)" class="btn btn-sm btn-light border"
                                data-bs-toggle="modal" data-bs-target="#modalHandleFormProject">
                                <i class="ri-edit-line" title="Chỉnh sửa dự án"></i>
                            </button>
                            <button @click="openModalConfirmUpdateStatus(project.id)"
                                class="btn btn-sm btn-light border text-danger">
                                <i class="ri-close-line" title="Hủy bỏ dự án"></i>
                            </button>
                        </div>
                        <router-link :to="{
                            name: 'project-tasks',
                            params: { id: project.id }
                        }" class="fs-16 fw-semibold">
                            {{ project.name }}
                        </router-link>
                        <div class="d-flex align-items-center justify-content-between">
                            <p class="fs-14 fw-bold">
                                {{ project.tasks_count ?? 0 }}
                                <span class="fw-medium">{{ $t('project.task_count') }}</span>
                            </p>
                            <div class="avatar__hover">
                                <img v-if="project.avatar" :src="`${BACKEND_URL}/images/users/${project.avatar}`"
                                    class="radius-2" style="width: 24px;" alt="Hình ảnh">
                                <img v-else :src="`${BACKEND_URL}/images/user-default.jpg`" class="radius-2"
                                    style="width: 24px;" alt="Hình ảnh">
                                <span>{{ project.owner_name }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-3">
                    <pagination :meta="meta" @changePage="onChangePage" />
                </div>
            </div>
        </div>
    </div>
    <div class="modal" id="modalHandleFormProject">
        <div class="modal-dialog">
            <div class="modal-content radius-2">
                <div class="modal-header d-flex align-items-center justify-content-between">
                    <h4 class="modal-title fs-18">
                        {{ isEditModalVisible ? $t('project.edit_project') : $t('project.add_project') }}
                    </h4>
                    <span class="fs-18 fw-medium " data-bs-dismiss="modal">
                        <i class="ri-close-line cursor-pointer"></i>
                    </span>
                </div>
                <div class="modal-body">
                    <form @submit.prevent="handleFormProject()">
                        <div class="form-group">
                            <label class="fs-14 fw-medium mb-1">
                                {{ $t('project.name') }}
                                <span class="text-primary">*</span></label>
                            <input type="text" v-model="project.name" class="form-control radius-2" />
                        </div>
                        <p v-if="errors.name" class="text-danger fs-14">
                            {{
                                Array.isArray(errors.name)
                                    ? errors.name[0]
                                    : errors.name
                            }}
                        </p>
                        <div class="form-group position-relative">
                            <label class="fs-14 fw-medium mb-1">
                                {{ $t('project.owner') }}
                                <span class="text-primary">*</span>
                            </label>
                            <input type="text" class="form-control radius-2 cursor-pointer" v-model="searchText"
                                @focus="showDropdown = true" @blur="hideDropdownWithDelay" @input="filterAvailableUsers"
                                placeholder="Chọn người sở hữu" />

                            <ul v-show="showDropdown" class="list-group position-absolute w-100 zindex-1000 radius-2"
                                style="max-height: 200px; overflow-y: auto;">
                                <li v-for="user in filteredUsers" :key="user.id"
                                    class="list-group-item list-group-item-action d-flex align-items-center cursor-pointer"
                                    @mousedown.prevent="selectUser(user)">
                                    <img :src="`${BACKEND_URL}/images/user-default.jpg`" class="rounded-circle me-2"
                                        width="24" height="24" alt="Avatar" />
                                    <span>{{ user.name }}</span>
                                </li>
                            </ul>

                            <!-- Lỗi -->
                            <p v-if="errors.owner_id" class="text-danger fs-14">
                                {{ Array.isArray(errors.owner_id) ? errors.owner_id[0] : errors.owner_id }}
                            </p>
                        </div>
                        <div class="form-group my-2">
                            <label for="comment" class="mb-1">{{ $t('project.description') }}</label>
                            <textarea v-model="project.description" class="form-control radius-2" rows="3" id="comment"
                                name="text"></textarea>
                        </div>
                        <button :disabled="loading.handleForm" type="submit" style="width: 180px;"
                            class="main-btn px-3 py-1 d-flex align-items-center justify-content-center gap-3">
                            <loading_loader v-if="loading.handleForm" size="20px" color="#fff" border="2px" />
                            <span>
                                {{ isEditModalVisible ? $t('action.update') : $t('project.add_project') }}
                            </span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- END FORM -->
    <div class="modal" id="assignUsersToProjectModal">
        <div class="modal-dialog">
            <div class="modal-content radius-2">
                <div class="modal-header d-flex align-items-center justify-content-between">
                    <h4 class="modal-title fs-18">{{ $t('project.assign_user_title') }}</h4>
                    <span class="fs-18 fw-medium" data-bs-dismiss="modal">
                        <i class="ri-close-line cursor-pointer"></i>
                    </span>
                </div>

                <div class="modal-body">
                    <form @submit.prevent="handleAssignUsersToProject()">
                        <!-- Tìm kiếm người dùng -->
                        <div class="form-group position-relative">
                            <label class="fs-14 fw-medium mb-1">{{ $t('project.add_participant') }}</label>
                            <div class="selected-tags mb-2">
                                <span v-for="user in selectedUsers" :key="user.id"
                                    class="badge bg-primary me-1 radius-2">
                                    {{ user.name }}
                                    <i class="ri-close-line ms-1 cursor-pointer" @click="removeSelectedUser(user)"></i>
                                </span>
                            </div>
                            <input type="text" class="form-control radius-2 cursor-pointer" v-model="searchText"
                                @focus="showDropdown = true" @blur="hideDropdownWithDelay" @input="filterAvailableUsers"
                                placeholder="Tìm kiếm người dùng..." />

                            <!-- Dropdown chọn user -->
                            <ul v-show="showDropdown" class="list-group position-absolute w-100 z-3 radius-2"
                                style="max-height: 200px; overflow-y: auto;">
                                <li v-for="user in filteredUsers" :key="user.id"
                                    class="list-group-item list-group-item-action d-flex align-items-center cursor-pointer"
                                    @mousedown.prevent="addUserToProject(user)">
                                    <img :src="`${BACKEND_URL}/images/user-default.jpg`" class="rounded-circle me-2"
                                        width="24" height="24" alt="Avatar" />
                                    <span>{{ user.name }}</span>
                                </li>
                            </ul>
                        </div>

                        <!-- Danh sách người đã được gán -->
                        <div v-if="loading.fetchAssign" class="mt-3">
                            <label class="fs-14 fw-medium mb-1">
                                {{ $t('project.assigned') }}
                                <loading_loader size="20px" border="2px" />
                            </label>
                        </div>
                        <div v-else class="form-group mt-3">
                            <label class="fs-14 fw-medium mb-1">{{ $t('project.assigned') }}</label>
                            <div v-if="assignedUsers.length === 0" class="text-muted fs-14">{{
                                $t('project.no_participant') }}</div>
                            <div class="selected-tags mb-2">
                                <span v-for="user in assignedUsers" :key="user.id" class="badge bg-info me-1 radius-2">
                                    {{ user.name }}
                                    <i class="ri-close-line ms-1 cursor-pointer" @click="removeUser(user)"></i>
                                </span>
                            </div>
                        </div>

                        <!-- Nút xác nhận -->
                        <button :disabled="loading.handleAssign" type="submit" style="width: 250px;"
                            class="main-btn px-3 py-1 mt-3 d-flex align-items-center justify-content-center gap-3">
                            <loading_loader v-if="loading.handleAssign" size="20px" color="#fff" border="2px" />
                            <span>{{ $t('project.confirm_assign') }}</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- END ASSIGN -->
    <div v-if="isDeleteModalVisible" class="modal fade show d-block" style="background: rgba(0,0,0,0.5);">
        <div class=" modal-dialog">
            <div class="modal-content radius-2">
                <div class="modal-header d-flex align-items-center justify-content-between">
                    <h4 class="modal-title fs-18">{{ $t('project.confrim_cancel_title') }}</h4>
                    <span class="fs-18 fw-medium" @click="isDeleteModalVisible = false">
                        <i class="ri-close-line cursor-pointer"></i>
                    </span>
                </div>
                <div class="modal-body">
                    <p class="m-0">{{ $t('project.confirm_cancel_message') }}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" :disabled="loading.canceled" @click="updateStatus()" style="width:220px;"
                        class="main-btn px-3 py-1 d-flex align-items-center radius-2 justify-content-center gap-3">
                        <loading_loader v-if="loading.canceled" size="20px" color="#fff" border="2px" />
                        <span>{{ $t('project.confrim_cancel_title') }}</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- END DELETE -->
</template>
<script setup>
import { onMounted, ref, watch } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/configs/api'
import message from '@/utils/message_state'
import loading_loader from '@/components/loading/loading__loader-circle.vue'
import pagination from '@/components/pagination.vue'
import no_data from '@/components/no_data.vue'
import { BACKEND_URL } from '@/configs/env'
import { check_login } from '@/utils/auth_state'

const router = useRouter()
onMounted(() => {
    const token = check_login()
    if (!token) {
        message.emit('show-message', {
            title: 'Thông báo',
            text: 'Vui lòng đăng nhập để có thể sử dụng hệ thống.',
            type: 'warning'
        })
        router.push('/login')
        return
    }
    fetchProjectsByUser()
    fetchUsers()
})
const meta = ref({})
const currentPage = ref(1)
const searchValue = ref('')
const per_page = ref(10)
const status = ref('in_progress')
const projects = ref([])
/*
    Search & Pagination 
*/
const onSearch = () => {
    currentPage.value = 1
    fetchProjectsByUser()
}
const clearSearch = () => {
    searchValue.value = ''
    fetchProjectsByUser()
}
const setPerPage = (perPage) => {
    per_page.value = perPage
    fetchProjectsByUser()
}
const onChangePage = (page) => {
    currentPage.value = page
    fetchProjectsByUser()
}
/*
    LOADING
*/
const loading = ref({
    fetch: false,
    handleForm: false,
    canceled: false,
    fetchAssign: false,
    handleAssign: false
})

/*
    GET PROJECTS BY USER
*/
const fetchProjectsByUser = async () => {
    loading.value.fetch = true
    try {
        const res = await api.get('/users/projects', {
            params: {
                page: currentPage.value,
                search: searchValue.value,
                per_page: per_page.value,
                status: status.value
            }
        })
        projects.value = res.data.data
        meta.value = res.data.meta
    } catch {
        message.emit('show-message', { title: "Error", text: "Failed to fetch projects.", type: "error" })
    } finally {
        loading.value.fetch = false
    }
}

const fetchProjectsByStatus = (status_active) => {
    status.value = status_active
    currentPage.value = 1
    searchValue.value = ''
    fetchProjectsByUser()
}

/*
    ADD & EDIT PROJECT
*/
const project = ref({
    name: '',
    owner_id: null,
    description: ''
})
const openModalAddProject = () => {
    isEditModalVisible.value = false
    project.value = {
        name: '',
        owner_id: null,
        description: ''
    }
    isEditModalVisible.value = false
}
const handleFormProject = async () => {
    if (!validate(project.value)) return
    try {
        loading.value.handleForm = true
        if (isEditModalVisible.value) {
            const response = await api.put(`/projects/${project.value.id}`, project.value)
            if (response.status == 200) {
                const inx = projects.value.findIndex((item) => item.id == project.value.id)
                if (inx !== -1) {
                    projects.value[inx] = response.data
                }
                message.emit("show-message", {
                    title: "Thông báo",
                    text: "Cập nhật dự án thành công.",
                    type: "success"
                })
            }
        } else {
            const response = await api.post('/projects', project.value)
            if (response.status == 201) {
                projects.value.unshift(response.data)
                project.value = { name: '', description: '', owner_id: null }
                searchText.value = ''
                message.emit("show-message", {
                    title: "Thông báo",
                    text: "Thêm dự án thành công.",
                    type: "success"
                })
            }
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
        loading.value.handleForm = false
    }
}
const searchText = ref('')
const showDropdown = ref(false)
const users = ref([])

const filteredUsers = ref()

const selectUser = (user) => {
    searchText.value = user.name
    project.value.owner_id = user.id
    showDropdown.value = false
}

const hideDropdownWithDelay = () => {
    setTimeout(() => (showDropdown.value = false), 200)
}

watch(searchText, (newVal) => {
    const matched = users.value.find(user => user.name === newVal)
    if (!matched) {
        project.value.owner_id = null
    }
})

const isEditModalVisible = ref(false)
const openModalEditProject = (projcet_id) => {
    isEditModalVisible.value = true
    project.value = projects.value.find(project => project.id === projcet_id)
    const user = {
        id: project.value.owner_id,
        name: project.value.owner_name
    }
    selectUser(user)
}
const isDeleteModalVisible = ref(false)

const openModalConfirmUpdateStatus = (id) => {
    isDeleteModalVisible.value = id
}
const updateStatus = async () => {
    const id = isDeleteModalVisible.value
    try {
        loading.value.canceled = true
        const response = await api.patch(`/projects/${id}`, {
            status: 'canceled'
        })
        if (response.status == 200) {
            projects.value = projects.value.filter((item) => item.id !== id)
            message.emit("show-message", {
                title: "Thông báo",
                text: response.data.message,
                type: "success"
            })
        }
    } catch (error) {
        console.log(error);
    } finally {
        isDeleteModalVisible.value = false
        loading.value.canceled = false
    }
}

/*
    VALIDATE
*/
const errors = ref({})
const validate = (project) => {
    errors.value = {}
    if (!project.name) {
        errors.value.name = 'Tên dự án không được để trống.'
    } else if (project.name.length < 6) {
        errors.value.name = 'Tên dự án không được quá ngắn, tối thiểu 6 ký tự.'
    }
    if (!project.owner_id) {
        errors.value.owner_id = 'Chủ sở hữu không được để trống.'
    }

    if (project.description && project.description.length > 255) {
        errors.value.description = 'Mô tả không được vượt quá 255 ký tự.'
    }
    return Object.keys(errors.value).length === 0
}

/*
    GET USERS
*/
const fetchUsers = async () => {
    try {
        const res = await api.get('/users/dropdown')
        users.value = res.data
        filteredUsers.value = [...users.value]
    } catch {
        message.emit('show-message', { title: "Error", text: "Failed to fetch projects.", type: "error" })
    }
}

/*
   GET ASSIGN USSER TO PROJECT
*/
const assignedUsers = ref([])
const selectedUsers = ref([])
const project_id = ref(null)

const filterAvailableUsers = () => {
    const text = searchText.value.toLowerCase();
    const assignedIds = assignedUsers.value.map(u => u.id);
    filteredUsers.value = users.value.filter(user =>
        !assignedIds.includes(user.id) &&
        user.name.toLowerCase().includes(text)
    );
};
const fetchAssigns = async (projectId) => {
    filteredUsers.value = [...users.value]
    project_id.value = projectId
    try {
        loading.value.fetchAssign = true
        const res = await api.get(`/projects/${projectId}/users`)
        if (res.status == 200) {
            assignedUsers.value = res.data
            filteredUsers.value = filteredUsers.value.filter(user => {
                return !assignedUsers.value.some(assigned => assigned.id == user.id);
            });
        }
    } catch {
        message.emit('show-message', { title: "Error", text: "Failed to fetch projects users.", type: "error" })
    } finally {
        loading.value.fetchAssign = false
    }
}

const handleAssignUsersToProject = async () => {
    if (selectedUsers.value.length === 0) {
        message.emit('show-message', { title: "Lỗi", text: "Vui lòng chọn ít nhất một người dùng", type: "warning" });
        return;
    }
    try {
        loading.value.handleAssign = true;

        const payload = {
            user_ids: selectedUsers.value.map(user => user.id)
        };

        const res = await api.post(`/projects/${project_id.value}/users`, payload);
        if (res.status == 201) {
            message.emit('show-message', { title: "Thành công", text: "Gán người dùng vào dự án thành công", type: "success" });
            assignedUsers.value.unshift(...selectedUsers.value);
            selectedUsers.value = [];
        }
    } catch (error) {
        console.error(error);
        message.emit('show-message', { title: "Lỗi", text: "Không thể gán người dùng", type: "error" });
    } finally {
        loading.value.handleAssign = false;
    }
}

const addUserToProject = (user) => {
    selectedUsers.value.push(user);
    filteredUsers.value = filteredUsers.value.filter(u => u.id !== user.id);
}

const removeSelectedUser = (user) => {
    selectedUsers.value = selectedUsers.value.filter(u => u.id !== user.id)
    filteredUsers.value.push(user)
    showDropdown.value = true
}

const removeUser = async (user) => {
    if (!user) {
        message.emit('show-message', { title: "Lỗi", text: "Người dùng không tồn tại.", type: "warning" });
        return;
    }
    const user_id = user.id
    assignedUsers.value = assignedUsers.value.filter(user => user.id != user_id)
    filteredUsers.value.unshift(user)
    try {
        await api.delete(`/projects/${project_id.value}/users/${user_id}`);
    } catch (error) {
        console.error(error);
        message.emit('show-message', { title: "Lỗi", text: "Không thể gán người dùng", type: "error" });
    }
}


</script>
<style>
.project__action>div>button {
    opacity: 0;
    transition: all 0.5 ease;
}

.project__action:hover button {
    opacity: 1;
}
</style>