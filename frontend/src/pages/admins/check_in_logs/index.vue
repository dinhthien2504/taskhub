<template>
    <div class="admin__content">
        <div class="text-end pe-3">
            <button @click="exportCheckInLogs()" class="btn btn-success btn-sm radius-2">
                📥 Xuất lịch chấm công
            </button>
        </div>
        <div
            class="p-3 bg-white border-bottom text-grey fs-14 fw-medium d-flex align-items-center justify-content-between">
            <p class="m-0">
                Lịch Sử Chấm Công
            </p>
            <div class="d-flex align-items-center gap-2">
                <div class="dropdown">
                    <button type="button" class="btn btn-light border dropdown-toggle radius-2 fw-medium"
                        data-bs-toggle="dropdown">
                        Hiển Thị
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
                        Trạng Thái
                    </button>
                    <ul class="dropdown-menu radius-2">
                        <li v-for="option in statusOptions" :key="option" @click="setStatus(option.value)">
                            <a
                                :class="['dropdown-item fw-medium cursor-pointer', { active: statusSelected === option.value }]">
                                {{ option.label }}
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="form-group">
                    <input type="date" class="form-control radius-2" v-model="selectedDate" @change="onSearch" />
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
                            <input type="text" v-model="username" placeholder="Tìm kiếm theo tên..."
                                class="form-control radius-2" />
                            <button v-if="username" type="button" @click="clearSearch"
                                class="btn position-absolute top-50 end-0 translate-middle-y me-2 p-0 border-0 bg-transparent">
                                <i class="ri-close-line"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div v-if="loading.fetch" style="height: 60vh;" class="d-flex align-items-center justify-content-center">
            <loading_loader size="40px" border="4px" />
        </div>
        <div v-else class="p-3 table-responsive fade-in">
            <no_data v-if="check_in_logs.length == 0" />
            <table v-else class="table border">
                <thead class="table-active">
                    <tr class="lh-lg align-middle">
                        <th scope="col" class="fs-14 fw-semibold min-w-150">Ngày chấm công</th>
                        <th scope="col" class="fs-14 fw-semibold min-w-150">Tên nhân viên</th>
                        <th scope="col" class="fs-14 fw-semibold min-w-100">Email</th>
                        <th scope="col" class="fs-14 fw-semibold min-w-200">Check-in lúc</th>
                        <th scope="col" class="fs-14 fw-semibold min-w-200">Check-out lúc</th>
                        <th scope="col" class="fs-14 fw-semibold min-w-150">Đi trễ</th>
                        <th scope="col" class="fs-14 fw-semibold min-w-150">Thời gian làm</th>
                        <th scope="col" class="fs-14 fw-semibold min-w-150">Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="log in check_in_logs">
                        <td class="align-middle fs-14">
                            {{ formatDate(log.date) }}
                        </td>
                        <td class="align-middle">
                            <p>{{ log.user.name }}</p>
                            <p>{{ log.email }}</p>
                        </td>
                        <td class="align-middle fs-14">
                            <p>{{ log.user.email }}</p>
                        </td>
                        <td class="align-middle fs-14">
                            <p>{{ log.check_in_time }}</p>
                        </td>
                        <td class="align-middle fs-14">
                            <p>{{ log.check_out_time ?? 'Chưa check-out' }}</p>
                        </td>
                        <td class="align-middle fs-14">
                            <span v-if="log.is_late" class="text-danger fw-bold">
                                Có (trễ {{ formatLateTime(log.late_by_minutes) }})
                            </span>
                            <span v-else class="text-success fw-bold">Không</span>
                        </td>
                        <td class="align-middle fs-14">
                            <p>{{ calculateDuration(log.check_in_time, log.check_out_time) }}</p>
                        </td>
                        <td>
                            <span v-if="log.check_in_time && !log.check_out_time">Đang làm</span>
                            <span v-else-if="log.check_in_time && log.check_out_time">Đã hoàn tất</span>
                            <span v-else>Chưa check-in</span>
                        </td>
                    </tr>
                </tbody>
            </table>
            <pagination :meta="meta" @changePage="onChangePage" />
        </div>
    </div>
</template>
<script setup>
import { onMounted, ref } from 'vue'
import api from '@/configs/api'
import { formatDate } from '@/utils/format_date'
import message from '@/utils/message_state'
import loading_loader from '@/components/loading/loading__loader-circle.vue'
import pagination from '@/components/pagination.vue'
import no_data from '@/components/no_data.vue'
import { saveAs } from 'file-saver'

onMounted(() => {
    fetchCheckInLogs()
})
/*
    Search & Pagination & Filter
*/
const meta = ref({})
const currentPage = ref(1)
const username = ref('')
const per_page = ref(10)
const selectedDate = ref(null)
const statusSelected = ref(null)
const statusOptions = [
    { label: 'Tất cả', value: null },
    { label: 'Đang làm', value: 'checked_in' },
    { label: 'Đã hoàn tất', value: 'checked_out' }
]
const onSearch = () => {
    currentPage.value = 1
    fetchCheckInLogs()
}
const clearSearch = () => {
    username.value = ''
    fetchCheckInLogs()
}
const setPerPage = (perPage) => {
    per_page.value = perPage
    currentPage.value = 1
    fetchCheckInLogs()
}
const onChangePage = (page) => {
    currentPage.value = page
    fetchCheckInLogs()
}
const setStatus = (status) => {
    statusSelected.value = status
    fetchCheckInLogs()
}
/*
    LOADING
*/
const loading = ref({
    fetch: false
})

/*
    FETCH CHECK IN LOGS
*/
const check_in_logs = ref([])
const fetchCheckInLogs = async () => {
    try {
        loading.value.fetch = true
        const res = await api.get('check-in-logs', {
            params: {
                page: currentPage.value,
                per_page: per_page.value,
                date: selectedDate.value,
                status: statusSelected.value,
                username: username.value
            }
        })
        check_in_logs.value = res.data.data
        meta.value = res.data.meta
    } catch (error) {
        console.log(error);
        message.emit('show-message', {
            title: 'Thông báo',
            text: error.response.data.message,
            type: 'error'
        })
    } finally {
        loading.value.fetch = false
    }
}

const calculateDuration = (start, end) => {
    if (!start || !end) return '-'
    const diffMs = new Date(end) - new Date(start)
    const diffMins = Math.floor(diffMs / 60000)
    const hours = Math.floor(diffMins / 60)
    const mins = diffMins % 60
    return `${hours}h ${mins}m`
}

const exportCheckInLogs = async () => {
    try {
        const res = await api.get('check-in-logs/export', {
            params: {
                page: currentPage.value,
                per_page: per_page.value,
                date: selectedDate.value,
                status: statusSelected.value,
                username: username.value
            },
            responseType: 'blob'
        });
        saveAs(res.data, 'check_in_logs.csv')
    } catch (err) {
        console.log(err);
    }
}

const formatLateTime = (minutes) => {
    if (!minutes) return "";
    const hours = Math.floor(minutes / 60);
    const mins = minutes % 60;
    let result = '';
    if (hours > 0) result += `${hours}h `;
    if (mins > 0) result += `${mins} m`;
    return result.trim();
}

</script>