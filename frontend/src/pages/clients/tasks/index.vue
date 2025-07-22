<template>
    <div class="admin__content p-0 m-0">
        <div
            class="p-3 bg-white border-bottom text-grey fs-14 fw-medium d-flex align-items-center justify-content-between">
            <p class="m-0">
                {{ $t('task.title') }}
            </p>
            <div class="admin__search">
                <button type="button" class="search__button">
                    <i class="ri-search-2-line"></i>
                </button>
                <form @submit.prevent="onSearch">
                    <div class="d-flex align-items-center gap-1 position-relative">
                        <button type="submit" style="height: 38px" class="main-btn py-1 px-3 ">
                            <i class="ri-search-2-line"></i>
                        </button>
                        <input type="text" v-model="searchValue" :placeholder="$t('task.search_placeholder')"
                            class="form-control radius-2" />
                        <button v-if="searchValue" type="button" @click="clearSearch"
                            class="btn position-absolute top-50 end-0 translate-middle-y me-2 p-0 border-0 bg-transparent">
                            <i class="ri-close-line"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <div v-if="loading.fetch" style="height: 60vh;" class="d-flex align-items-center justify-content-center">
            <loading_loader size="40px" border="4px" />
        </div>
        <div v-else class="p-1 bg-light fade-in">
            <div class="d-flex flex-nowrap overflow-auto gap-2" style="min-height: 100vh;">
                <div v-for="task_status in task_statuses" class="p-2" style="min-width: 300px;">
                    <div class="position-sticky top-0 py-2 mb-3">
                        <div class="d-flex gap-2 justify-content-between">
                            <h3 class="fs-16 fw-medium">
                                {{ $t('task.status.' + task_status.id) }}
                            </h3>
                            <i @click="toggleForm(task_status.id)"
                                class="cursor-pointer text-grey fw-bold ri-add-line"></i>
                        </div>
                        <div class="d-flex gap-2 fs-14 align-items-center justify-content-between">
                            <div :class="{
                                'o_column_progress': tasks.filter(task => task.task_status_id === task_status.id).length == 0
                            }" class="w-75 progressbar" style="height: 13px;"></div>
                            <span class="fw-bold">
                                {{
                                    tasks.filter(task => task.task_status_id === task_status.id).length
                                }}
                            </span>
                        </div>
                    </div>
                    <div v-if="activeFormStatusId == task_status.id" class="border p-2 my-3">
                        <div class="form-group mb-3">
                            <label class="fs-14 fw-medium mb-1">{{ $t('task.name') }}</label>
                            <input type="text" v-model="task.title" placeholder="Task Name"
                                class="bg-transparent radius-2 border-0 outline-none w-100 fs-14" />
                        </div>
                        <div class="form-group position-relative mb-3">
                            <label class="fs-14 fw-medium mb-1">{{ $t('task.assignee') }}</label>
                            <input type="text"
                                class="bg-transparent radius-2 border-0 outline-none w-100 cursor-pointer fs-14"
                                v-model="searchText" @focus="showDropdown = true" @blur="hideDropdownWithDelay"
                                @input="filterUsers" :placeholder="$t('task.select_assignee')" />
                            <ul v-show="showDropdown" class="list-group position-absolute w-100 z-3 radius-2"
                                style="max-height: 200px; overflow-y: auto;">
                                <li v-for="user in filteredUsers" :key="user.id"
                                    class="list-group-item list-group-item-actio d-flex align-items-center cursor-pointer border-0"
                                    @mousedown.prevent="selectUser(user)">
                                    <img v-if="user.avatar" :src="`${BACKEND_URL}/images/users/${user.avatar}`"
                                        class="rounded-circle me-2" width="24" height="24" alt="Avatar" />
                                    <img v-else :src="`${BACKEND_URL}/images/user-default.jpg`"
                                        class="rounded-circle me-2" width="24" height="24" alt="Avatar" />
                                    <span class="fs-14 fw-medium">{{ user.name }}</span>
                                </li>
                            </ul>
                        </div>
                        <div class="d-flex shadown-sm justify-content-between border-top pt-2">
                            <button type="button" @click="addTask()"
                                class="main-btn radius-2 px-2 py-1 d-flex align-items-center gap-2">
                                <loading_loader v-if="loading.add" size="16px" color="#fff" border="2px" />
                                {{ $t('task.add_task') }}
                            </button>
                            <button @click="toggleForm()" class="btn btn-sm btn-light border text-danger">
                                <i class="ri-delete-bin-line"></i>
                            </button>
                        </div>
                    </div>
                    <template v-for="task in tasks">
                        <div v-if="task.task_status_id == task_status.id" class="cursor-pointer">
                            <div class="position-relative d-flex flex-column gap-3 border p-2">
                                <router-link :to="{
                                    name: 'project-task-detail',
                                    params: {
                                        project_id: project_id,
                                        task_id: task.id
                                    }
                                }" class="fs-16 fw-medium">{{ task.title }}</router-link>
                                <div class="d-flex align-items-center justify-content-between">
                                    <p v-if="task.start_date && task.due_date" class="fs-14 fw-medium text-grey">
                                        {{ formatDate(task.start_date) }} - {{ formatDate(task.due_date) }}
                                    </p>
                                    <p v-else-if="task.due_date" class="fs-14 fw-medium text-grey">
                                        {{ task.due_date }}
                                    </p>
                                    <span v-else></span>
                                    <div v-if="task.assigned_to" class="avatar__hover">
                                        <img v-if="task.avatar" :src="`${BACKEND_URL}/images/users/${task.avatar}`"
                                            class="radius-2" style="width: 24px;" alt="Hình ảnh">
                                        <img v-else :src="`${BACKEND_URL}/images/user-default.jpg`" class="radius-2"
                                            style="width: 24px;" alt="Hình ảnh">
                                        <span>{{ task.assigned_to }}</span>
                                    </div>
                                    <div v-else class="avatar__hover">
                                        <i @click="toggleAssignedTo(task.id)"
                                            class="fs-18 fw-medium ri-user-add-line"></i>
                                        <span>{{ $t('task.assign') }}</span>
                                    </div>
                                    <div v-if="task.id == activeAssignedTo"
                                        class="position-absolute hover__selected-user">
                                        <div class="form-group mb-3">
                                            <ul class="list-group position-absolute w-100 z-3 radius-2"
                                                style="max-height: 200px; overflow-y: auto;">
                                                <li v-for="user in filteredUsers" :key="user.id"
                                                    class="list-group-item list-group-item-actio d-flex align-items-center cursor-pointer border-0"
                                                    @mousedown.prevent="updateTask(user, task.id)">
                                                    <img :src="`${BACKEND_URL}/images/user-default.jpg`"
                                                        class="rounded-circle me-2" width="24" height="24"
                                                        alt="Avatar" />
                                                    <p class="fs-14 fw-medium">{{ user.name }}</p>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</template>
<script setup>
import { onMounted, ref, watch } from 'vue'
import { useRoute } from 'vue-router'
import api from '@/configs/api'
import message from '@/utils/message_state'
import loading_loader from '@/components/loading/loading__loader-circle.vue'
import no_data from '@/components/no_data.vue'
import { BACKEND_URL } from '@/configs/env'
import { formatDate } from '@/utils/format_date'
import { useI18n } from 'vue-i18n'
const { t } = useI18n()
const route = useRoute();
const project_id = route.params.id;
const currentPage = ref(1)
const searchValue = ref('')
const per_page = ref(10)
const tasks = ref([])
/*
    Search & Pagination 
*/
const onSearch = () => {
    currentPage.value = 1
    fetchTasks()
}
const clearSearch = () => {
    searchValue.value = ''
    fetchTasks()
}
/*
    LOADING
*/
const loading = ref({
    fetch: false,
    handleForm: false,
    canceled: false
})
/*
    GET STATUSES
*/
const task_statuses = ref([])
const fetchStatuses = async () => {
    try {
        const res = await api.get('/task_statuses')
        task_statuses.value = res.data
    } catch {
        message.emit('show-message', { title: "Error", text: "Failed to fetch statuses.", type: "error" })
    }
}
/*
    GET TASKS
*/
const fetchTasks = async () => {
    try {
        const res = await api.get(`/projects/${project_id}/tasks`, {
            params: {
                search: searchValue.value,
            }
        })
        tasks.value = res.data
    } catch {
        message.emit('show-message', { title: "Error", text: "Failed to fetch tasks.", type: "error" })
    }
}

/*
    ADD & EDIT TASK
*/
const activeFormStatusId = ref(null)
const activeAssignedTo = ref(null)
const toggleForm = (statusId = null) => {
    if (activeFormStatusId.value === statusId) {
        activeFormStatusId.value = null
        task.value = {
            title: '',
            assigned_to: null,
            task_status_id: null,
        }
    } else {
        activeFormStatusId.value = statusId
        task.value = {
            title: '',
            assigned_to: null,
            task_status_id: statusId
        }
    }
}
const toggleAssignedTo = (AssignedTo = null) => {
    activeAssignedTo.value = activeAssignedTo.value ? null : AssignedTo
}


const task = ref({
    title: '',
    assigned_to: null,
    task_status_id: null,
})

const addTask = async () => {
    if (!validate(task.value)) return
    const payload = task.value
    loading.value.add = true
    try {
        const response = await api.post(`/projects/${project_id}/tasks`, payload)
        if (response.status == 201) {
            tasks.value.unshift(response.data)
            message.emit("show-message", {
                title: t('message.notification'),
                text: t('task.add_success'),
                type: "success"
            })
            toggleForm()
        }
    } catch (error) {
        if (error.response.status === 422) {
            message.emit("show-message", {
                title: t('message.notification'),
                text: error.response.data.message,
                type: "error"
            })
        } else {
            console.log(error);
        }
    } finally {
        loading.value.add = false
    }
}

const updateTask = async (user, task_id) => {
    selectUser(user)
    toggleAssignedTo()
    try {
        const response = await api.put(`/projects/${project_id}/tasks/${task_id}`, {
            assigned_to: task.value.assigned_to
        })
        if (response.status == 200) {
            const inx = tasks.value.findIndex((item) => item.id == task_id)
            if (inx != -1) {
                tasks.value[inx] = response.data
                message.emit("show-message", {
                    title: t('message.notification'),
                    text: t('task.assign_success'),
                    type: "success"
                })
            }
        }
    } catch (error) {
        if (error.response.status === 422) {
            message.emit("show-message", {
                title: t('message.notification'),
                text: error.response.data.message,
                type: "error"
            })
        } else {
            console.log(error);
        }
    }
}

const searchText = ref('')
const showDropdown = ref(false)
const users = ref([])

const filteredUsers = ref()
const filterUsers = () => {
    const text = searchText.value.toLowerCase()
    filteredUsers.value = users.value.filter(user =>
        user.name.toLowerCase().includes(text)
    )
}

const selectUser = (user) => {
    searchText.value = user.name
    task.value.assigned_to = user.id
    showDropdown.value = false
}

const hideDropdownWithDelay = () => {
    setTimeout(() => (showDropdown.value = false), 100)
}

watch(searchText, (newVal) => {
    const matched = users.value.find(user => user.name === newVal)
    if (!matched) {
        task.value.assigned_to = null
    }
})
/*
    VALIDATE
*/
const validate = (task) => {
    if (!task.title) {
        message.emit("show-message", {
            title: t('message.notification'),
            text: t('task.required'),
            type: "error"
        })
        return false
    } else if (task.title.length < 3) {
        message.emit("show-message", {
            title: t('message.notification'),
            text: t('task.min'),
            type: "error"
        })
        return false
    }
    return true
}

/*
    GET USERS
*/
const fetchUsersByProject = async () => {
    try {
        const res = await api.get(`/projects/${project_id}/users`)
        users.value = res.data
        filteredUsers.value = { ...users.value }
    } catch {
        message.emit('show-message', { title: "Error", text: "Failed to fetch projects.", type: "error" })
    }
}
onMounted(async () => {
    loading.value.fetch = true
    await fetchStatuses(),
        await fetchTasks(),
        loading.value.fetch = false
    fetchUsersByProject()
})
</script>
<style scoped>
.progressbar {
    background-color: #d8dadd;
}

.o_column_progress {
    background-color: #d8dadd;
    opacity: 0.3;
}

.hover__selected-user {
    position: absolute;
    right: 0px;
    bottom: -13px;
    min-width: 250px;
    z-index: 100900;
    background: white;
    border-radius: 4px;
}

.hover__selected-user::before {
    content: "";
    position: absolute;
    top: -10px;
    right: 9px;
    width: 0;
    height: 0;
    border-left: 10px solid transparent;
    border-right: 10px solid transparent;
    border-bottom: 10px solid #ddd;
    z-index: 0;
}

.hover__selected-user::after {
    content: "";
    position: absolute;
    top: -9px;
    right: 10px;
    width: 0;
    height: 0;
    border-left: 9px solid transparent;
    border-right: 9px solid transparent;
    border-bottom: 9px solid white;
    z-index: 1;
}
</style>