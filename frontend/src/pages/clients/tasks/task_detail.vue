<template>
    <div class="admin__content bg-light p-3 m-0">
        <div v-if="loading.fetch" class="d-flex align-items-center justify-content-center" style="min-height: 75vh;">
            <loading_loader border="5px" size="50px" />
        </div>
        <div v-else class="row">
            <div class="col-12 col-lg-8">
                <div class="arrow-container">
                    <div @click="updateTask(task_status.id)" v-for="task_status in task_statuses" :class="[
                        'arrow',
                        task_status.id == task.task_status_id ? 'active-arrow' : '',
                        task_status.id === 1 ? 'start' : '',
                        task_status.id === 6 ? 'end' : ''
                    ]">
                        {{ $t('task.status.' + task_status.id) }}
                        <span class="subtext" v-if="statusDurations[task_status.id]">
                            <template v-if="statusDurations[task_status.id].days > 0">
                                {{ statusDurations[task_status.id].days }} {{ $t('common.day') }}
                            </template>
                            <template v-else-if="statusDurations[task_status.id].hours > 0">
                                {{ statusDurations[task_status.id].hours }} {{ $t('common.hour') }}
                            </template>
                            <template v-else-if="statusDurations[task_status.id].minutes > 0">
                                {{ statusDurations[task_status.id].minutes }} {{ $t('common.minute') }}
                            </template>
                        </span>
                    </div>
                </div>
                <div class="bg-white p-3 border mt-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <input type="text" class="fs-20 border-0 outline-none" v-model="task.title">
                    </div>
                    <div class="row my-3">
                        <div class="col-8">
                            <p class="fw-medium">
                                <strong>{{ $t('project.title') }}: </strong>
                                <router-link :to="{
                                    name: 'project-tasks',
                                    params: {
                                        id: project_id
                                    }
                                }">
                                    {{ task.name_project }}
                                </router-link>
                            </p>
                            <div class="form-group position-relative mb-3 d-flex align-items-center gap-2 mt-3">
                                <h3 class="fs-16 fw-medium">{{ $t('task.assignee') }}: </h3>
                                <div class="mb-2">
                                    <input type="text"
                                        class="bg-transparent radius-2 border-0 outline-none w-100 cursor-pointer fs-14"
                                        v-model="searchText" @focus="showDropdown = true" @blur="hideDropdownWithDelay"
                                        @input="filterUsers" :placeholder="$t('task.select_assignee')" />
                                    <ul v-show="showDropdown"
                                        class="list-group position-absolute w-100 z-3 radius-2 border"
                                        style="max-height: 200px; max-width: 250px; overflow-y: auto;">
                                        <li v-for="user in filteredUsers" :key="user.id"
                                            class="list-group-item list-group-item-actio d-flex align-items-center cursor-pointer border-0"
                                            @mousedown.prevent="selectUser(user)">
                                            <img :src="`${BACKEND_URL}/images/user-default.jpg`"
                                                class="rounded-circle me-2" width="24" height="24" alt="Avatar" />
                                            <span class="fs-14 fw-medium">{{ user.name }}</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label class="fs-14 fw-medium mb-1">{{ $t('task.start_date') }}:</label>
                                <input type="datetime-local" v-model="task.start_date" class="form-control radius-2" />
                            </div>
                            <div class="form-group mt-3">
                                <label class="fs-14 fw-medium mb-1">{{ $t('task.due_date') }}:</label>
                                <input type="datetime-local" v-model="task.due_date" class="form-control radius-2" />
                            </div>
                        </div>
                    </div>
                    <div class="form-group my-2">
                        <label for="comment" class="mb-1">{{ $t('task.description') }}</label>
                        <textarea class="form-control radius-2" v-model="task.description" rows="5" id="comment"
                            name="text"></textarea>
                    </div>
                    <button :disabled="loading.save" @click="updateTask()"
                        class="main-btn px-2 py-1 fs-14 d-flex align-items-center justify-content-center gap-2"
                        style="width: 130px;">
                        <loading_loader v-if="loading.save" size="16px" color="#fff" border="2px" />
                        {{ $t('action.update') }}
                    </button>
                </div>
            </div>
            <div class="col-12 col-lg-4">
                <button class="white-btn px-2 py-1 bg-light fs-14">{{ $t('comment.label') }}</button>
                <div class="mt-2 ">
                    <textarea class="form-control mb-2 radius-2" v-model="comment.content"
                        :placeholder="$t('comment.placeholder')"></textarea>
                    <button @click="addComment()" class="main-btn px-2 py-1 fs-14">{{ $t('comment.add') }}</button>
                </div>
                <div class="scrollable-comment-list">
                    <div v-for="group in groupedList" :key="group.date" class="mt-4">
                        <div class="divider-with-text">{{ group.date }}</div>
                        <div v-for="item in group.items" :key="item.id" class="mt-2">
                            <div class="d-flex gap-2">
                                <img v-if="item.user.avatar" :src="`${BACKEND_URL}/images/users/${item.user.avatar}`"
                                    class="radius-2" width="30" height="30" alt="Hình ảnh">
                                <img v-else :src="`${BACKEND_URL}/images/user-default.jpg`" class="radius-2" width="30"
                                    height="30" alt="Hình ảnh">
                                <div class="d-flex flex-column gap-0">
                                    <p class="fs-14 fw-semibold">
                                        {{ item.user.name }}
                                        <span class="fs-10 text-grey fw-medium">
                                            {{ formatDateTime(item.created_at) }}
                                        </span>
                                    </p>
                                    <template v-if="item.type === 'comment'">
                                        <span class="fs-14 fw-medium">{{ item.content }}</span>
                                    </template>
                                    <template v-else>
                                        <div v-if="!item.old_status">
                                            <p class="fs-14 fw-medium">{{ $t('task.created') }}</p>
                                        </div>
                                        <div v-else>
                                            <span class="fs-14">{{ $t('task.status_changed') }}</span>
                                            <div class="d-flex gap-2 fs-14 fw-medium">
                                                <span>{{ $t('task.status.' + item.old_status.id) ?? $t('task.status.1') }}</span>
                                                <i class="ri-arrow-right-long-fill"></i>
                                                <span class="fw-semibold text-primary">{{ $t('task.status.' + item.new_status.id) }}</span>
                                            </div>
                                        </div>
                                    </template>
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
import { onMounted, ref, watch } from 'vue'
import { useRoute } from 'vue-router'
import api from '@/configs/api'
import message from '@/utils/message_state'
import { formatDateTime, formatDate } from '@/utils/format_date'
import loading_loader from '@/components/loading/loading__loader-circle.vue'
import no_data from '@/components/no_data.vue'
import { BACKEND_URL } from '@/configs/env'
import { computed } from 'vue'

const route = useRoute()

const project_id = route.params.project_id
const task_id = route.params.task_id

/*
  FETCH TASK
*/
const task = ref({})
const fetchTask = async () => {
    try {
        const res = await api.get(`/projects/${project_id}/tasks/${task_id}`)
        task.value = res.data
        searchText.value = task.value.assigned_name
    } catch (error) {
        console.log(error);
        message.emit('show-message', { title: "Error", text: "Failed to fetch task.", type: "error" })
    }
}

/*
  FETCH STATUSES
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
  HANDLE
*/
const loading = ref({
    fetch: false,
    save: false
})
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
    return users.value.find(user => user.name === newVal)
})

const groupedList = computed(() => {
    const task_comments = task.value?.comments || []
    const task_logs = task.value?.logs || []

    const items = [
        ...task_comments.map(item => ({
            ...item,
            type: 'comment',
            created_at: new Date(item.created_at),
        })),
        ...task_logs.map(item => ({
            ...item,
            type: 'log',
            created_at: new Date(item.created_at),
        }))
    ].sort((a, b) => b.created_at - a.created_at)

    const groups = {}

    items.forEach(item => {
        const dateKey = formatDate(item.created_at)
        if (!groups[dateKey]) {
            groups[dateKey] = []
        }
        groups[dateKey].push(item)
    })

    return Object.entries(groups).map(([date, items]) => ({
        date,
        items
    }))
})

const statusDurations = computed(() => {
    if (!task.value || !task.value.logs) return {}

    const logs = [...task.value.logs].sort((a, b) => new Date(a.created_at) - new Date(b.created_at))
    const result = {}

    for (let i = 0; i < logs.length; i++) {
        const current = logs[i]
        const next = logs[i + 1]
        const fromDate = new Date(current.created_at)
        const toDate = next ? new Date(next.created_at) : new Date()

        const statusId = current.new_status?.id
        if (!statusId) continue

        const durationMs = toDate - fromDate
        const days = Math.floor(durationMs / (1000 * 60 * 60 * 24))
        const hours = Math.floor((durationMs / (1000 * 60 * 60)) % 24)
        const minutes = Math.floor((durationMs / (1000 * 60)) % 60)

        if (!result[statusId]) {
            result[statusId] = { days: 0, hours: 0, minutes: 0 }
        }

        result[statusId].days += days
        result[statusId].hours += hours
        result[statusId].minutes += minutes
    }

    return result
})

/*
  FETCH USSER BY PROJECT
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

/*
  UPDATE TASK
*/
const updateTask = async (taskStatusId = null) => {
    if (!validate(task.value)) return

    if (taskStatusId) {
        task.value.task_status_id = taskStatusId

    } else {
        loading.value.save = true
    }
    const payload = {
        assigned_to: task.value.assigned_to,
        due_date: task.value.due_date,
        start_date: task.value.start_date,
        task_status_id: task.value.task_status_id,
        title: task.value.title,
        description: task.value.description
    }
    try {
        const response = await api.put(`/projects/${project_id}/tasks/${task_id}`, payload)
        if (response.status == 200) {
            fetchTask()
        }
    } catch (error) {
        if (error.response.status === 422) {
            message.emit("show-message", {
                title: "Thông báo",
                text: error.response.data.message,
                type: "error"
            })
        } else {
            console.log(error);
        }
    } finally {
        loading.value.save = false
    }
}

/*
  ADD COMMENT
*/
const comment = ref({
    task_id: task_id,
    content: ""
})
const addComment = async () => {
    if (!comment.value.content) {
        message.emit("show-message", {
            title: "Thông báo",
            text: "Vui lòng nhập nội dung bình luận.",
            type: "error"
        })
        return
    }
    try {
        const response = await api.post(`/tasks/${task_id}/comments/`, comment.value)
        if (response.status == 201) {
            task.value.comments.push(response.data)
            comment.value.content = ""
        }
    } catch (error) {
        message.emit("show-message", {
            title: "Thông báo",
            text: error.response.data.message,
            type: "error"
        })
    }
}
/*
    VALIDATE
*/
const validate = (task) => {
    if (!task.title) {
        message.emit("show-message", {
            title: "Thông báo",
            text: "Tên nhiệm vụ không được để trống.",
            type: "error"
        })
        return false
    } else if (task.title.length < 3) {
        message.emit("show-message", {
            title: "Thông báo",
            text: "Tên nhiệm vụ không được quá ngắn, tối thiểu 3 ký tự.",
            type: "error"
        })
        return false
    } else if (task.start_date && task.due_date) {
        const start = new Date(task.start_date)
        const end = new Date(task.due_date)
        if (end < start) {
            message.emit("show-message", {
                title: "Thông báo",
                text: "Ngày kết thúc không được nhỏ hơn ngày bắt đầu.",
                type: "error"
            })
            return false
        }
    }

    return true
}

onMounted(async () => {
    loading.value.fetch = true
    await fetchStatuses()
    await fetchTask()
    await fetchUsersByProject()
    loading.value.fetch = false
})
</script>
<style scoped>
.arrow-container {
    display: flex;
    justify-content: end;
    font-size: 14px;
    gap: 0;
    overflow-x: auto;
    max-width: 100%;
    white-space: nowrap;
    scrollbar-width: none;
    -ms-overflow-style: none;
}

.arrow-container::-webkit-scrollbar {
    display: none;
}

.arrow {
    position: relative;
    padding: 5px 15px;
    font-weight: 500;
    cursor: pointer;
    background: #dee2e6;
    color: #333;
    border: 2px solid #e6f6f9;
    clip-path: polygon(0 0, calc(100% - 15px) 0, 100% 50%, calc(100% - 15px) 100%, 0 100%, 15px 50%);
    transition: all 0.3s ease;
    z-index: 1;
}

.arrow:not(:first-child) {
    margin-left: -10px;
}

.arrow.active-arrow {
    pointer-events: none;
    color: var(--text-color);
    background: #e6f6f9;
    border: 2px solid #e6f6f9;
    z-index: 2;
}

.arrow:hover {
    background: #bac0c7;
    border: 2px solid #e6f6f9;
}

.arrow.start {
    clip-path: polygon(0 0, calc(100% - 15px) 0, 100% 50%, calc(100% - 15px) 100%, 0 100%);
    margin-left: 0;
}

.arrow.end {
    clip-path: polygon(0 0, calc(100%) 0, 100% 0%, calc(100%) 100%, 0 100%, 15px 50%);
    margin-right: 0;
}

.subtext {
    margin-left: 8px;
    opacity: 0.6;
    font-weight: normal;
}

.divider-with-text {
    display: flex;
    align-items: center;
    text-align: center;
    color: #333;
    font-weight: 500;
    margin: 16px 0;
}

.divider-with-text::before,
.divider-with-text::after {
    content: "";
    flex: 1;
    border-bottom: 1px solid #ccc;
}

.divider-with-text::before {
    margin-right: 12px;
}

.divider-with-text::after {
    margin-left: 12px;
}

.scrollable-comment-list {
    max-height: 500px;
    overflow-y: auto;
    padding-right: 6px;
    scroll-behavior: smooth;
}

.scrollable-comment-list::-webkit-scrollbar {
    width: 6px;
}

.scrollable-comment-list::-webkit-scrollbar-track {
    background: transparent;
}

.scrollable-comment-list::-webkit-scrollbar-thumb {
    background-color: rgba(0, 0, 0, 0.2);
    border-radius: 3px;
}

.scrollable-comment-list {
    scrollbar-width: thin;
    scrollbar-color: rgba(0, 0, 0, 0.2) transparent;
}
</style>