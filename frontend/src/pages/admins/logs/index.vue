<template>
    <div class="admin__content">
        <div
            class="p-3 bg-white border-bottom text-grey fs-14 fw-medium d-flex align-items-center justify-content-between">
            <p class="m-0">
                Lịch Sử Hoạt Động
            </p>
            <div class="d-flex align-items-center gap-2">
                <div class="dropdown">
                    <button type="button" class="btn btn-light border dropdown-toggle radius-2 fw-medium"
                        data-bs-toggle="dropdown">
                        Tháng: {{ selectedMonth || 'Tất cả' }}
                    </button>
                    <ul class="dropdown-menu radius-2">
                        <li v-for="month in 12" :key="month" @click="setMonth(month)">
                            <a class="dropdown-item fw-medium cursor-pointer"
                                :class="{ active: selectedMonth === month }">
                                Tháng {{ month }}
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="dropdown">
                    <button type="button" class="btn btn-light border dropdown-toggle radius-2 fw-medium"
                        data-bs-toggle="dropdown">
                        Năm: {{ selectedYear || 'Tất cả' }}
                    </button>
                    <ul class="dropdown-menu radius-2">
                        <li v-for="year in years" :key="year" @click="setYear(year)">
                            <a class="dropdown-item fw-medium cursor-pointer"
                                :class="{ active: selectedYear === year }">
                                {{ year }}
                            </a>
                        </li>
                    </ul>
                </div>
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
                <div class="admin__search">
                    <button type="button" class="search__button">
                        <i class="ri-search-2-line"></i>
                    </button>
                    <form @submit.prevent="onSearch">
                        <div class="d-flex align-items-center gap-1 position-relative">
                            <button type="submit" style="height: 38px" class="main-btn py-1 px-3 ">
                                <i class="ri-search-2-line"></i>
                            </button>
                            <input type="text" v-model="searchValue" placeholder="Tìm kiếm dự án"
                                class="form-control radius-2" />
                            <button v-if="searchValue" type="button" @click="clearSearch"
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
            <no_data v-if="user_action_logs.length == 0" />
            <table v-else class="table border">
                <thead class="table-active">
                    <tr class="lh-lg align-middle">
                        <th scope="col" class="fs-14 fw-semibold min-w-150">Thời Gian</th>
                        <th scope="col" class="fs-14 fw-semibold min-w-150">Tài Khoản</th>
                        <th scope="col" class="fs-14 fw-semibold min-w-100">Hành Động</th>
                        <th scope="col" class="fs-14 fw-semibold min-w-200">Mô Tả</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="log in user_action_logs">
                        <td class="align-middle fs-14">
                            {{ formatDateTime(log.created_at) }}
                        </td>
                        <td class="align-middle">
                            <p>{{ log.user_name }}</p>
                            <p>{{ log.email }}</p>
                        </td>
                        <td class="align-middle fs-14">
                            <p>{{ log.action }}</p>
                        </td>
                        <td class="align-middle fs-14">
                            <p>{{ log.description }}</p>
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
import { formatDateTime } from '@/utils/format_date'
import message from '@/utils/message_state'
import loading_loader from '@/components/loading/loading__loader-circle.vue'
import pagination from '@/components/pagination.vue'
import no_data from '@/components/no_data.vue'

onMounted(() => {
    const now = new Date()
    const month = now.getMonth() + 1
    const year = now.getFullYear()

    const startYear = year - 5
    const endYear = year + 5

    years.value = Array.from({ length: endYear - startYear + 1 }, (_, i) => startYear + i)
    selectedMonth.value = month
    selectedYear.value = year
    fetchLogs()
})
/*
    Search & Pagination & Filter
*/
const meta = ref({})
const currentPage = ref(1)
const searchValue = ref('')
const per_page = ref(10)
const selectedMonth = ref(null)
const selectedYear = ref(null)
const years = ref([])
const onSearch = () => {
    currentPage.value = 1
    fetchLogs()
}
const clearSearch = () => {
    searchValue.value = ''
    fetchLogs()
}
const setPerPage = (perPage) => {
    per_page.value = perPage
    fetchLogs()
}
const setMonth = (month) => {
    selectedMonth.value = month
    fetchLogs()
}
const setYear = (year) => {
    selectedYear.value = year
    fetchLogs()
}
const onChangePage = (page) => {
    currentPage.value = page
    fetchLogs()
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
    FETCH ACTION LOGS
*/
const user_action_logs = ref([])
const fetchLogs = async () => {
    try {
        loading.value.fetch = true
        const res = await api.get('/users/activity-logs', {
            params: {
                month: selectedMonth.value,
                year: selectedYear.value,
                page: currentPage.value,
                per_page: per_page.value,
                search: searchValue.value
            }
        })
        user_action_logs.value = res.data.data
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

</script>