<template>
    <div class="admin__content">
        <div
            class="p-3 bg-white border-bottom text-grey fs-14 fw-medium d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <p class="m-0">
                    {{ $t('campaign.list_title') }}
                </p>
                <span>|</span>
                <button class="border-0 bg-white" @click="fetchTrashedCampaigns()" data-bs-toggle="modal"
                    data-bs-target="#modalShowTrashCampaign">
                    <i class="fs-16 cursor-pointer ri-delete-bin-5-line text-grey"></i>
                </button>
            </div>
            <div class="d-flex align-items-center gap-2">
                <div class="dropdown">
                    <button type="button" class="btn btn-light border dropdown-toggle radius-2 fw-medium"
                        data-bs-toggle="dropdown">
                        {{ $t("common.month") }}: {{ selectedMonth || 'Tất cả' }}
                    </button>
                    <ul class="dropdown-menu radius-2">
                        <li v-for="month in 12" :key="month" @click="setMonth(month)">
                            <a class="dropdown-item fw-medium cursor-pointer"
                                :class="{ active: selectedMonth === month }">
                                {{ $t("common.month") }} {{ month }}
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="dropdown">
                    <button type="button" class="btn btn-light border dropdown-toggle radius-2 fw-medium"
                        data-bs-toggle="dropdown">
                        {{ $t("common.year") }}: {{ selectedYear || 'Tất cả' }}
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
                        {{ $t("campaign.display") }}
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
                        {{ $t("campaign.status") }}
                    </button>
                    <ul class="dropdown-menu radius-2">
                        <li v-for="option in ['pending', 'scheduled', 'sending', 'sent', 'failed', 'canceled']"
                            :key="option" @click="setStatus(option)">
                            <a
                                :class="['dropdown-item fw-medium cursor-pointer', { active: selectedStatus == option }]">
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
                            <input type="text" v-model="searchValue" :placeholder="$t('campaign.search_placeholder')"
                                class="form-control radius-2" />
                            <button v-if="searchValue" type="button" @click="clearSearch"
                                class="btn position-absolute top-50 end-0 translate-middle-y me-2 p-0 border-0 bg-transparent">
                                <i class="ri-close-line"></i>
                            </button>
                        </div>
                    </form>
                </div>
                <button type="button" @click="showModalForm()" class="main-btn px-3" style="height: 38px;"
                    data-bs-toggle="modal" data-bs-target="#modalHandleCampaign">
                    <span class="d-none d-lg-block">{{ $t('campaign.add_campaign') }}</span>
                    <span class="d-block d-lg-none"><i class="ri-add-fill"></i></span>
                </button>
            </div>
        </div>
        <div class="p-10">
            <div v-if="loading.fetch" style="height: 60vh;" class="d-flex align-items-center justify-content-center">
                <loading_loader size="40px" border="4px" />
            </div>
            <div v-else class="table-responsive fade-in">
                <table v-if="campaigns.length > 0" class="table border">
                    <thead class="table-active">
                        <tr class="lh-lg align-middle">
                            <th scope="col" class="fs-14 fw-semibold min-w-150">{{ $t('campaign.name') }}</th>
                            <th scope="col" class="fs-14 fw-semibold min-w-150">{{ $t('campaign.description') }}</th>
                            <th scope="col" class="fs-14 fw-semibold min-w-150 text-center">{{ $t('campaign.date_range')
                                }}</th>
                            <th scope="col" class="fs-14 fw-semibold min-w-100">{{ $t('campaign.created_at') }}</th>
                            <th scope="col" class="fs-14 fw-semibold min-w-100">{{ $t('campaign.status') }}</th>
                            <th scope="col" class="fs-14 fw-semibold min-w-100">{{ $t('campaign.scheduled_at') }}</th>
                            <th scope="col" class="fs-14 fw-semibold min-w-100">{{ $t('campaign.created_by') }}</th>
                            <th scope="col" class="fs-14 fw-semibold min-w-100 text-center">{{ $t('campaign.actions') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="campaign in campaigns">
                            <td class="align-middle">
                                <p class="fw-semibold">{{ campaign.name }}</p>
                            </td>
                            <td class="align-middle">
                                <p>{{ campaign.description ?? '_' }}</p>
                            </td>
                            <td class="align-middle">
                                <div class="d-flex align-items-center gap-0 flex-column">
                                    <p>{{ formatDateTime(campaign.start_date) }}</p>
                                    <span><i class="ri-arrow-down-long-line"></i></span>
                                    <p>{{ formatDateTime(campaign.end_date) }}</p>
                                </div>
                            </td>
                            <td class="align-middle">
                                <p>{{ formatDate(campaign.created_at) }}</p>
                            </td>
                            <td class="align-middle">
                                <p class="fw-semibold">{{ campaign.status }}</p>
                            </td>
                            <td class="align-middle">
                                <p>{{ formatDateTime(campaign.scheduled_at) ?? 'Chưa hẹn lịch' }}</p>
                            </td>
                            <td class="align-middle">
                                <p>{{ campaign.creator_name }}</p>
                            </td>

                            <td class="align-middle text-center">
                                <div class="more-wrapper">
                                    <button class="more-btn">
                                        <i class="ri-more-fill"></i>
                                    </button>
                                    <div class="more-popup" style="top: -70px;">
                                        <button :disabled="campaign.status != 'pending'"
                                            @click="activateCampaign(campaign.id)" type="button" :class="{
                                                'disabled': campaign.status != 'pending'
                                            }" class="bg-info text-white">
                                            {{ $t('action.activate') }}
                                            <i class="ri-play-circle-line"></i>
                                        </button>
                                        <button @click="deleteCampaignConfirm(campaign.id)" type="button"
                                            class="bg-danger text-white">
                                            {{ $t('action.delete') }}
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                        <button :disabled="['sent', 'sending'].includes(campaign.status)"
                                            @click="showModalForm(campaign.id)" type="button" data-bs-toggle="modal"
                                            data-bs-target="#modalHandleCampaign" class="bg-warning text-dark" :class="{
                                                'disabled': ['sent', 'sending'].includes(campaign.status)
                                            }">
                                            {{ $t('action.edit') }}
                                            <i class="ri-edit-line"></i>
                                        </button>
                                        <button @click="fetchUsersAssign(campaign.id)" type="button"
                                            data-bs-toggle="modal" data-bs-target="#assignUsersToCampaignModal"
                                            class="bg-success text-white">
                                            {{ $t('action.select_recipient') }}
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div v-else>
                    <no_data />
                </div>
                <pagination :meta="meta" @changePage="onChangePage" />
            </div>
        </div>
    </div>
    <div class="modal" id="modalHandleCampaign">
        <div class="modal-dialog">
            <div class="modal-content radius-2">
                <div class="modal-header d-flex align-items-center justify-content-between">
                    <h4 class="modal-title fs-18">
                        {{ isEdit ? $t('campaign.edit_campaign') : $t('campaign.add_campaign') }}
                    </h4>
                    <span class="fs-18 fw-medium" data-bs-dismiss="modal">
                        <i class="ri-close-line cursor-pointer"></i>
                    </span>
                </div>

                <div class="modal-body">
                    <form @submit.prevent="submitCampaign">
                        <div class="form-group">
                            <label class="fs-14 fw-medium mb-1">
                                {{ $t('campaign.name') }} <span class="text-color">*</span>
                            </label>
                            <input type="text" v-model="campaignForm.name" class="form-control radius-2" />
                            <p v-if="errors.name" class="text-danger fs-14">{{ errors.name[0] }}</p>
                        </div>
                        <div class="form-group">
                            <label class="fs-14 fw-medium mb-1">
                                {{ $t('campaign.description') }}
                            </label>
                            <textarea v-model="campaignForm.description" class="form-control radius-2"
                                rows="3"></textarea>
                            <p v-if="errors.description" class="text-danger fs-14">{{ errors.description[0] }}</p>
                        </div>
                        <div class="form-group">
                            <label class="fs-14 fw-medium mb-1">
                                {{ $t('campaign.start_date') }}
                            </label>
                            <input type="datetime-local" v-model="campaignForm.start_date"
                                class="form-control radius-2" />
                            <p v-if="errors.start_date" class="text-danger fs-14">{{ errors.start_date[0] }}</p>
                        </div>
                        <div class="form-group">
                            <label class="fs-14 fw-medium mb-1">
                                {{ $t('campaign.end_date') }}
                            </label>
                            <input type="datetime-local" v-model="campaignForm.end_date"
                                class="form-control radius-2" />
                            <p v-if="errors.end_date" class="text-danger fs-14">{{ errors.end_date[0] }}</p>
                        </div>
                        <div class="form-group">
                            <label class="fs-14 fw-medium mb-1">
                                {{ $t('campaign.scheduled_at') }}
                            </label>
                            <input type="datetime-local" v-model="campaignForm.scheduled_at"
                                class="form-control radius-2" />
                            <p v-if="errors.scheduled_at" class="text-danger fs-14">{{ errors.scheduled_at[0] }}</p>
                        </div>
                        <div class="form-group">
                            <label class="fs-14 fw-medium mb-1">
                                {{ $t('campaign.status') }}
                            </label>
                            <select v-model="campaignForm.status" class="form-control radius-2">
                                <option value="pending">Pending</option>
                                <option value="scheduled">Scheduled</option>
                            </select>
                            <p v-if="errors.status" class="text-danger fs-14">{{ errors.status[0] }}</p>
                        </div>
                        <div class="form-group">
                            <label class="fs-14 fw-medium mb-1">
                                {{ $t('campaign.template') }}
                            </label>
                            <select v-model="campaignForm.template_id" class="form-control radius-2"
                                @change="loadTemplatePreview">
                                <option value="" disabled>-- Chọn mẫu email --</option>
                                <option v-for="template in templates" :key="template.id" :value="template.id">
                                    {{ template.name }}
                                </option>
                            </select>
                            <p v-if="errors.template_id" class="text-danger fs-14">{{ errors.template_id[0] }}</p>
                        </div>
                        <div v-if="selectedTemplate" class="mt-3 p-3 border radius-2 bg-light">
                            <h6>Xem trước nội dung mẫu:</h6>
                            <div v-html="selectedTemplate.content"></div>
                        </div>
                        <div class="mt-3" v-if="!['sent', 'sending'].includes(campaignForm.status)">
                            <button type="submit" :disabled="loading.handleForm"
                                class="main-btn px-4 py-2 d-flex align-items-center justify-content-center gap-2">
                                <loading_loader v-if="loading.handleForm" size="20px" color="#fff" border="2px" />
                                <span>{{ isEdit ? $t('campaign.edit_campaign') : $t('action.add') }}</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- END HANDLE FORM -->
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
                    <p class="m-0">{{ $t('campaign.confirm_delete_message') }}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" :disabled="loading.delete" @click="deleteCampaign()" style="width: 170px;"
                        class="main-btn px-3 py-1 d-flex align-items-center radius-2 justify-content-center gap-3">
                        <loading_loader v-if="loading.delete" size="20px" color="#fff" border="2px" />
                        <span v-else>{{ $t('action.confirm_delete') }}</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- END DELETE -->
    <div class="modal" id="assignUsersToCampaignModal">
        <div class="modal-dialog">
            <div class="modal-content radius-2">
                <div class="modal-header d-flex align-items-center justify-content-between">
                    <h4 class="modal-title fs-18">{{ $t('campaign.assign_user_title') }}</h4>
                    <span class="fs-18 fw-medium" data-bs-dismiss="modal">
                        <i class="ri-close-line cursor-pointer"></i>
                    </span>
                </div>

                <div class="modal-body">
                    <form @submit.prevent="submitAssignedUsers()">
                        <!-- Tìm kiếm người dùng -->
                        <div class="form-group position-relative">
                            <label class="fs-14 fw-medium mb-1">{{ $t('campaign.add_participant') }}</label>
                            <input type="text" class="form-control radius-2 cursor-pointer" v-model="searchUser"
                                @focus="showDropdown = true" @blur="showDropdown = false" @input="onSearchUser"
                                placeholder="Tìm kiếm người dùng" />
                            <!-- Dropdown chọn user -->
                            <ul v-show="showDropdown" class="list-group position-absolute w-100 z-3 radius-2"
                                style="max-height: 200px; overflow-y: auto;">
                                <li v-if="loading.fetchUser"
                                    class="list-group-item list-group-item-action d-flex align-items-center justify-content-center cursor-pointer">
                                    <loading_loader border="2px" size="22px" />
                                </li>
                                <template v-else>
                                    <li v-if="filteredUsers.length == 0" class="list-group-item list-group-item-action">
                                        Không có người dùng nào!
                                    </li>
                                    <li v-else v-for="user in filteredUsers" :key="user.id"
                                        class="list-group-item list-group-item-action d-flex align-items-center cursor-pointer"
                                        @mousedown.prevent="addUserToCampaign(user)">
                                        <img v-if="user.avatar" :src="`${BACKEND_URL}/images/users/${user.avatar}`"
                                            class="rounded-circle me-2" width="24" height="24" alt="Avatar" />
                                        <img v-else :src="`${BACKEND_URL}/images/user-default.jpg`"
                                            class="rounded-circle me-2" width="24" height="24" alt="Avatar" />
                                        <span>{{ user.name }}</span>
                                    </li>
                                </template>

                            </ul>
                        </div>
                        <div v-if="loading.fetchAssign" class="mt-3">
                            <label class="fs-14 fw-medium mb-1">
                                {{ $t('campaign.assigned') }}
                                <loading_loader size="20px" border="2px" />
                            </label>
                        </div>
                        <div v-else class="form-group mt-3">
                            <label class="fs-14 fw-medium mb-1">{{ $t('campaign.assigned') }}</label>
                            <div v-if="assignedUsers.length === 0" class="text-muted fs-14">{{
                                $t('campaign.no_participant') }}</div>
                            <div class="selected-tags mb-2">
                                <span v-for="user in assignedUsers" :key="user.id" class="badge bg-info me-1 radius-2">
                                    {{ user.name }}
                                    <i class="ri-close-line ms-1 cursor-pointer" @click="removeUserAssigned(user)"></i>
                                </span>
                            </div>
                        </div>
                        <button :disabled="loading.submitAssign" type="submit" style="width: 200px;"
                            class="main-btn px-3 py-1 mt-3 d-flex align-items-center justify-content-center gap-3">
                            <loading_loader v-if="loading.submitAssign" size="20px" color="#fff" border="2px" />
                            <span>{{ $t('campaign.confirm_assign') }}</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- END ASSIGN -->
    <div class="modal" id="modalShowTrashCampaign">
        <div class="modal-dialog">
            <div class="modal-content radius-2">
                <div class="modal-header d-flex align-items-center justify-content-between">
                    <h4 class="modal-title fs-18">{{ $t('campaign.trashed_list_title') }}</h4>
                    <span class="fs-18 fw-medium " data-bs-dismiss="modal">
                        <i class="ri-close-line cursor-pointer"></i>
                    </span>
                </div>
                <div v-if="loading.fetchTrashed" style="height: 60vh;"
                    class="d-flex align-items-center justify-content-center">
                    <loading_loader size="40px" border="4px" />
                </div>
                <div v-else class="modal-body">
                    <div v-if="trashedCampaigns.length == 0">
                        <no_data />
                    </div>
                    <div v-else class="row g-3">
                        <div v-for="trashed_campaign in trashedCampaigns" class="col-12">
                            <div
                                class="border radius-2 p-3 d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
                                <div class="mb-2 mb-md-0">
                                    <p class="mb-1 fw-bold text-primary">{{ trashed_campaign.name }}</p>
                                    <p class="mb-1 text-muted" v-if="trashed_campaign.phone">
                                        <i class="ri-phone-line me-1"></i>
                                        {{ trashed_campaign.phone }}
                                    </p>
                                </div>
                                <div class="d-flex flex-column flex-md-row align-items-md-center gap-2">
                                    <p class="mb-0 text-danger fs-14">🗑️ {{ $t('user.date_deleted') }}: {{
                                        formatDate(trashed_campaign.deleted_at) }}</p>
                                    <button type="button" class="btn btn-outline-success btn-sm radius-2"
                                        @click="restoreCampaign(trashed_campaign.id)">
                                        <i class="ri-refresh-line me-1"></i>
                                        {{ $t('action.restore') }}
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
</template>
<script setup>
import { ref, onMounted, watch } from 'vue'
import api from '@/configs/api'
import message from '@/utils/message_state'
import loading_loader from '@/components/loading/loading__loader-circle.vue'
import pagination from '@/components/pagination.vue'
import no_data from '@/components/no_data.vue'
import { formatDate, formatDateTime } from '@/utils/format_date'
import { BACKEND_URL } from '@/configs/env'

// ============ State =============

onMounted(() => {
    const now = new Date()
    const month = now.getMonth() + 1
    const year = now.getFullYear()

    const startYear = year - 5
    const endYear = year + 5

    years.value = Array.from({ length: endYear - startYear + 1 }, (_, i) => startYear + i)
    selectedMonth.value = month
    selectedYear.value = year
    fetchCampaigns()
    fetchUsers()
    fetchTemplates()
})
const campaigns = ref([])
const trashedCampaigns = ref([])
const id_deleted = ref(null)
const loading = ref({
    handleForm: false,
    fetch: false,
    delete: false,
    fetchTrashed: false,
    restore: false,
    fetchUser: false,
    fetchAssign: false,
    submitAssign: false
})
const meta = ref({})
const currentPage = ref(1)
const selectedStatus = ref(null)
const searchValue = ref('')
const per_page = ref(10)
const selectedMonth = ref(null)
const selectedYear = ref(null)
const years = ref([])
const errors = ref({})
const isDeleteModalVisible = ref(false)
const templates = ref([])

// =================================

// ============ User CRUD =============
const fetchCampaigns = async () => {
    loading.value.fetch = true
    try {
        const res = await api.get('/campaigns', {
            params: {
                month: selectedMonth.value,
                year: selectedYear.value,
                page: currentPage.value,
                per_page: per_page.value,
                search: searchValue.value,
                status: selectedStatus.value
            }
        })
        campaigns.value = res.data.data
        meta.value = res.data.meta
    } catch {
        message.emit('show-message', { title: "Error", text: "Failed to fetch campaigns.", type: "error" })
    } finally {
        loading.value.fetch = false
    }
}

const campaignFormDefault = {
    name: '',
    description: '',
    start_date: '',
    end_date: '',
    scheduled_at: '',
    status: 'pending',
    template_id: null
}
const campaignForm = ref({ ...campaignFormDefault })
const isEdit = ref(null)
const submitCampaign = async () => {
    if (!validateCampaignForm()) return
    const payload = campaignForm.value
    try {
        loading.value.handleForm = true
        if (isEdit.value) {
            const res = await api.put(`/campaigns/${payload.id}`, payload)
            if (res.status === 200) {
                const inx = campaigns.value.findIndex(c => c.id === payload.id)
                campaigns.value[inx] = res.data
                message.emit('show-message', { title: "Thông báo", text: "Cập nhật chiến dịch thành công.", type: "success" })
            }
        } else {
            const res = await api.post('/campaigns', payload)
            if (res.status === 201) {
                campaigns.value.unshift(res.data)
                message.emit('show-message', { title: "Thông báo", text: "Thêm chiến dịch thành công.", type: "success" })
                campaignForm.value = { ...campaignFormDefault }
            }
        }
    } catch (error) {
        if (error.response.status == 422) {
            errors.value = error.response.data.errors
        }
    } finally {
        loading.value.handleForm = false
    }
}

const showModalForm = (campaign_id) => {
    if (campaign_id) {
        const campaign = campaigns.value.find(c => c.id === campaign_id)
        console.log(campaign);

        isEdit.value = campaign_id
        if (campaign) campaignForm.value = { ...campaign }
        else message.emit('show-message', { title: "Warning", text: "Người dùng không tồn tại.", type: "warning" })
    } else {
        isEdit.value = null
        campaignForm.value = { ...campaignFormDefault }
    }
}

const deleteCampaignConfirm = (id = null) => {
    if (id) id_deleted.value = id
    if (!id_deleted.value) {
        message.emit('show-message', { title: 'Thông báo', text: 'Chọn chiến dịch để xóa', type: 'info' })
        return
    }
    isDeleteModalVisible.value = true
}

const deleteCampaign = async () => {
    loading.value.delete = true
    try {
        const res = await api.delete(`/campaigns/${id_deleted.value}`)
        if (res.status === 200) {
            campaigns.value = campaigns.value.filter(c => c.id !== id_deleted.value)
            message.emit('show-message', {
                title: "Thông báo",
                text: res.data.message,
                type: "success"
            })
        }
    } catch (error) {
        console.log(error);
    } finally {
        loading.value.delete = false
        isDeleteModalVisible.value = false
        id_deleted.value = null
    }
}


// =================================

// ============ Assign =============
const searchUser = ref('')
const showDropdown = ref(false)
const users = ref([])

const filteredUsers = ref([])
const assignedUsers = ref([])
const campaign_id = ref(null)
let debounceTimer = null

const onSearchUser = () => {
    clearTimeout(debounceTimer)
    debounceTimer = setTimeout(fetchUsers, 200)
}

const fetchUsers = async () => {
    users.value = []
    filteredUsers.value = []
    if (!searchUser.value) return
    loading.value.fetchUser = true
    try {
        const res = await api.get('/users', {
            params:
            {
                search: searchUser.value
            }
        })
        users.value = res.data.data
        filteredUsers.value = users.value.filter(user =>
            !assignedUsers.value.some(assigned => assigned.id === user.id)
        )
    } catch {
        message.emit('show-message', { title: "Error", text: "Failed to fetch users.", type: "error" })
    } finally {
        loading.value.fetchUser = false
    }
}

const fetchUsersAssign = async (campaignId) => {
    campaign_id.value = campaignId
    try {
        loading.value.fetchAssign = true
        const res = await api.get(`/campaigns/${campaignId}/users`)
        assignedUsers.value = res.data.data
    } catch {
        message.emit('show-message', { title: "Error", text: "Failed to fetch users assign.", type: "error" })
    } finally {
        loading.value.fetchAssign = false
    }
}

const addUserToCampaign = (user) => {
    filteredUsers.value = filteredUsers.value.filter(u => u.id !== user.id)
    assignedUsers.value.push(user)
}

const removeUserAssigned = (user) => {
    assignedUsers.value = assignedUsers.value.filter(u => u.id !== user.id)
}

const submitAssignedUsers = async () => {
    if (assignedUsers.value.length == 0) {
        message.emit('show-message', { title: "Thông báo", text: "Bạn chưa chọn người dùng.", type: "error" })
        return
    }
    try {
        const user_ids = assignedUsers.value.map(user => user.id)
        loading.value.submitAssign = true
        const res = await api.post(`/campaigns/${campaign_id.value}/users`, {
            user_ids: user_ids
        })
        message.emit('show-message', { title: "Thông báo", text: res.data.message, type: "success" })
    } catch (error) {
        message.emit('show-message', { title: "Thông báo", text: error.response.data.message, type: "error" })
    } finally {
        loading.value.submitAssign = false
    }
}

const activateCampaign = async (campaign_id) => {
    await fetchUsersAssign(campaign_id)
    if (assignedUsers.value == 0) {
        message.emit('show-message', { title: "Thông báo", text: "Vui lòng chọn người dùng nhận chiến dịch", type: "error" })
        return
    }
    try {
        const res = await api.post(`/campaigns/${campaign_id}/send`)
        if (res.status == 200) {
            const inx = campaigns.value.findIndex(c => c.id == campaign_id)
            if (inx != -1) {
                campaigns.value[inx] = res.data
            }
            message.emit('show-message', { title: "Thông báo", text: "Chiến dịch đang được gửi (queue xử lý)", type: "success" })
        }
    } catch (error) {
        message.emit('show-message', { title: "Thông báo", text: error.response.data.message, type: "error" })
    }
}
// ============ Search & Pagination & Filter =============
const onSearch = () => {
    currentPage.value = 1
    fetchCampaigns()
}
const clearSearch = () => {
    searchValue.value = ''
    fetchCampaigns()
}
const onChangePage = (page) => {
    currentPage.value = page
    fetchCampaigns()
}
const setPerPage = (perPage) => {
    per_page.value = perPage
    fetchCampaigns()
}
const setMonth = (month) => {
    selectedMonth.value = month
    fetchCampaigns()
}
const setYear = (year) => {
    selectedYear.value = year
    fetchCampaigns()
}
const setStatus = (status) => {
    selectedStatus.value = status
    fetchCampaigns()
}
// =================================

// ============ Trashed Campaign =============
const fetchTrashedCampaigns = async () => {
    loading.value.fetchTrashed = true
    try {
        const res = await api.get('/campaigns/trashed')
        trashedCampaigns.value = res.data
    } catch {
        message.emit('show-message', { title: "Lỗi", text: "Không lấy được chiến dịch đã xóa", type: "error" })
    } finally {
        loading.value.fetchTrashed = false
    }
}
const restoreCampaign = async (campaignId) => {
    if (!confirm('Bạn chắc chắn muốn khôi phục?')) return
    loading.value.restore = true
    try {
        const res = await api.put(`/campaigns/${campaignId}/restore`)
        const idx = trashedCampaigns.value.findIndex(u => u.id === campaignId)
        if (idx !== -1) {
            campaigns.value.push(trashedCampaigns.value[idx])
            trashedCampaigns.value.splice(idx, 1)
        }
        message.emit('show-message', { title: "Thông báo", text: res.data.message, type: "success" })
    } catch (err) {
        message.emit('show-message', { title: "Thông báo", text: err?.response?.data?.message || "Restore failed", type: "error" })
    } finally {
        loading.value.restore = false
    }
}
// =================================

// ============ FetchTemplate =============
const fetchTemplates = async () => {
    try {
        const res = await api.get('/email-templates')
        templates.value = res.data.data
    } catch {
        message.emit('show-message', { title: "Lỗi", text: "Không lấy được danh sách template", type: "error" })
    }
}
const selectedTemplate = ref(null)
watch(() => campaignForm.value.template_id, (newId) => {
    selectedTemplate.value = templates.value.find(t => t.id === newId) || null
})
// =================================
// ============ Validate & Error =============
const validateCampaignForm = () => {
    errors.value = {}

    if (!campaignForm.value.name) {
        errors.value.name = ['Tên chiến dịch là bắt buộc.']
    }

    if (!campaignForm.value.start_date) {
        errors.value.start_date = ['Ngày bắt đầu là bắt buộc.']
    }

    if (!campaignForm.value.end_date) {
        errors.value.end_date = ['Ngày kết thúc là bắt buộc.']
    }

    if (campaignForm.value.start_date && campaignForm.value.end_date) {
        const start = new Date(campaignForm.value.start_date)
        const end = new Date(campaignForm.value.end_date)
        if (start > end) {
            errors.value.end_date = ['Ngày kết thúc phải sau ngày bắt đầu.']
        }
    }

    if (campaignForm.value.status === 'scheduled') {
        if (!campaignForm.value.scheduled_at) {
            errors.value.scheduled_at = ['Vui lòng chọn thời gian gửi khi trạng thái là Scheduled.']
        } else {
            const now = new Date()
            const scheduled = new Date(campaignForm.value.scheduled_at)
            if (scheduled < now) {
                errors.value.scheduled_at = ['Thời gian gửi phải lớn hơn thời gian hiện tại.']
            }
        }
    }

    if (!campaignForm.value.template_id) {
        errors.value.template_id = ['Chọn mẫu chiến dịch.']
    }

    return Object.keys(errors.value).length === 0
}

</script>