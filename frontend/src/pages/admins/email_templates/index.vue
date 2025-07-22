<template>
    <div class="admin__content">
        <!-- Header Controls -->
        <div
            class="p-3 bg-white border-bottom text-grey fs-14 fw-medium d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <p class="m-0">
                    {{ $t('email_template.list_title') }}
                </p>
                <span>|</span>
                <button class="border-0 bg-white" @click="fetchTrashedTemplates()" data-bs-toggle="modal"
                    data-bs-target="#modalShowTrashTemplate">
                    <i class="fs-16 cursor-pointer ri-delete-bin-5-line text-grey"></i>
                </button>
            </div>
            <div class="d-flex align-items-center gap-2">
                <div class="dropdown">
                    <button type="button" class="btn btn-light border dropdown-toggle radius-2 fw-medium"
                        data-bs-toggle="dropdown">
                        {{ $t("email_template.display") }}
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
                        {{ $t("email_template.status") }}
                    </button>
                    <ul class="dropdown-menu radius-2">
                        <li v-for="option in ['all', 'active', 'inactive']" :key="option.label"
                            @click="setStatus(option)">
                            <a
                                :class="['dropdown-item fw-medium cursor-pointer', { active: selectedStatus === option }]">
                                {{ $t('email_template.' + option) }}
                            </a>
                        </li>
                    </ul>
                </div>
                <!-- Search Box -->
                <div class="admin__search">
                    <button type="button" class="search__button">
                        <i class="ri-search-2-line"></i>
                    </button>
                    <form @submit.prevent="onSearch">
                        <div class="d-flex align-items-center gap-1 position-relative">
                            <button type="submit" style="height: 38px" class="main-btn py-1 px-3 ">
                                <i class="ri-search-2-line"></i>
                            </button>
                            <input type="text" v-model="searchValue"
                                :placeholder="$t('email_template.search_placeholder')" class="form-control radius-2" />
                            <button v-if="searchValue" type="button" @click="clearSearch"
                                class="btn position-absolute top-50 end-0 translate-middle-y me-2 p-0 border-0 bg-transparent">
                                <i class="ri-close-line"></i>
                            </button>
                        </div>
                    </form>
                </div>
                <button type="button" @click="showModalForm()" class="main-btn px-3" style="height: 38px;"
                    data-bs-toggle="modal" data-bs-target="#modalHandleTemplate">
                    <span class="d-none d-lg-block">{{ $t('email_template.add_template') }}</span>
                    <span class="d-block d-lg-none"><i class="ri-add-fill"></i></span>
                </button>
            </div>
        </div>
        <!-- Main Table -->
        <div class="p-10">
            <div v-if="loading.fetch" style="height: 60vh;" class="d-flex align-items-center justify-content-center">
                <loading_loader size="40px" border="4px" />
            </div>
            <div v-else class="table-responsive fade-in">
                <table v-if="templates.length > 0" class="table border">
                    <thead class="table-active">
                        <tr class="lh-lg align-middle">
                            <th scope="col" class="fs-14 fw-semibold min-w-150">{{ $t('email_template.name') }}</th>
                            <th scope="col" class="fs-14 fw-semibold min-w-150">{{ $t('email_template.description') }}
                            </th>
                            <th scope="col" class="fs-14 fw-semibold min-w-150">{{ $t('email_template.created_at') }}
                            </th>
                            <th scope="col" class="fs-14 fw-semibold min-w-100">{{ $t('email_template.status') }}</th>
                            <th scope="col" class="fs-14 fw-semibold min-w-100">{{ $t('email_template.created_by') }}
                            </th>
                            <th scope="col" class="fs-14 fw-semibold min-w-100 text-center">{{
                                $t('email_template.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="template in templates" :key="template.id">
                            <td class="align-middle">
                                <p class="fw-semibold my-4">{{ template.name }}</p>
                            </td>
                            <td class="align-middle">
                                <p>{{ template.description ?? '_' }}</p>
                            </td>
                            <td class="align-middle">
                                <p>{{ formatDate(template.created_at) }}</p>
                            </td>
                            <td class="align-middle">
                                <p class="fw-semibold">{{ template.is_active ? $t('email_template.active') :
                                    $t('email_template.inactive') }}</p>
                            </td>
                            <td class="align-middle">
                                <p>{{ template.created_by }}</p>
                            </td>
                            <td class="align-middle text-center">
                                <div class="more-wrapper">
                                    <button class="more-btn">
                                        <i class="ri-more-fill"></i>
                                    </button>
                                    <div class="more-popup" style="top: -75px;">
                                        <button @click="editTemplate(template.id)" type="button" data-bs-toggle="modal"
                                            data-bs-target="#modalHandleTemplate" class="bg-warning text-dark">
                                            {{ $t('action.edit') }}
                                            <i class="ri-edit-line"></i>
                                        </button>
                                        <button @click="deleteTemplateConfirm(template.id)" type="button"
                                            class="bg-danger text-white">
                                            {{ $t('action.delete') }}
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                        <button type="button" @click="showModalPreview(template)"
                                            data-bs-target="#modalShowPreviewTemplate" data-bs-toggle="modal"
                                            class="bg-success text-white">
                                            {{ $t('email_template.display') }}
                                            <i class="ri-eye-2-line"></i>
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
    <!-- Modal Handle Template -->
    <div class="modal" id="modalHandleTemplate">
        <div class="modal-dialog">
            <div class="modal-content radius-2">
                <div class="modal-header d-flex align-items-center justify-content-between">
                    <h4 class="modal-title fs-18">
                        {{ isEdit ? $t('email_template.edit_template') : $t('email_template.add_template') }}
                    </h4>
                    <span class="fs-18 fw-medium" data-bs-dismiss="modal">
                        <i class="ri-close-line cursor-pointer"></i>
                    </span>
                </div>
                <div class="modal-body">
                    <form @submit.prevent="submitTemplate">
                        <!-- Name -->
                        <div class="form-group">
                            <label class="fs-14 fw-medium mb-1">
                                {{ $t('email_template.name') }} <span class="text-color">*</span>
                            </label>
                            <input type="text" v-model="templateForm.name" class="form-control radius-2" />
                            <p v-if="errors.name" class="text-danger fs-14">{{ errors.name[0] }}</p>
                        </div>

                        <!-- Subject -->
                        <div class="form-group">
                            <label class="fs-14 fw-medium mb-1">
                                {{ $t('email_template.subject') }} <span class="text-color">*</span>
                            </label>
                            <input type="text" v-model="templateForm.subject" class="form-control radius-2" />
                            <p v-if="errors.subject" class="text-danger fs-14">{{ errors.subject[0] }}</p>
                        </div>

                        <!-- Description -->
                        <div class="form-group">
                            <label class="fs-14 fw-medium mb-1">
                                {{ $t('email_template.description') }}
                            </label>
                            <textarea v-model="templateForm.description" class="form-control radius-2"
                                rows="3"></textarea>
                            <p v-if="errors.description" class="text-danger fs-14">{{ errors.description[0] }}</p>
                        </div>

                        <!-- HTML Content -->
                        <div class="form-group">
                            <label class="fs-14 fw-medium mb-1">
                                {{ $t('email_template.content') }} <span class="text-color">*</span>
                            </label>
                            <QuillEditor v-model:content="templateForm.content" content-type="html" theme="snow"
                                style="min-height: 200px;" />
                            <p v-if="errors.content" class="text-danger fs-14">{{ errors.content[0] }}</p>
                        </div>

                        <!-- Type -->
                        <div class="form-group">
                            <label class="fs-14 fw-medium mb-1">
                                {{ $t('email_template.type') }}
                            </label>
                            <select v-model="templateForm.type" class="form-control radius-2">
                                <option value="campaign">{{ $t('email_template.type_campaign') }}</option>
                                <option value="welcome">{{ $t('email_template.type_welcome') }}</option>
                                <option value="promotion">{{ $t('email_template.type_promotion') }}</option>
                                <option value="notification">{{ $t('email_template.type_notification') }}</option>
                                <option value="custom">{{ $t('email_template.type_custom') }}</option>
                            </select>
                            <p v-if="errors.type" class="text-danger fs-14">{{ errors.type[0] }}</p>
                        </div>
                        <div class="mt-3">
                            <button type="submit" :disabled="loading.handleForm"
                                class="main-btn px-4 py-2 d-flex align-items-center justify-content-center gap-2">
                                <loading_loader v-if="loading.handleForm" size="20px" color="#fff" border="2px" />
                                <span>{{ isEdit ? $t('email_template.edit_template') : $t('action.add') }}</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Delete Modal -->
    <div v-if="isDeleteModalVisible" class="modal fade show d-block" style="background: rgba(0,0,0,0.5);">
        <div class="modal-dialog">
            <div class="modal-content radius-2">
                <div class="modal-header d-flex align-items-center justify-content-between">
                    <h4 class="modal-title fs-18">{{ $t('action.confirm_delete') }}</h4>
                    <span class="fs-18 fw-medium" @click="isDeleteModalVisible = false">
                        <i class="ri-close-line cursor-pointer"></i>
                    </span>
                </div>
                <div class="modal-body">
                    <p class="m-0">{{ $t('email_template.confirm_delete_message') }}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" :disabled="loading.delete" @click="deleteTemplate()" style="width: 170px;"
                        class="main-btn px-3 py-1 d-flex align-items-center radius-2 justify-content-center gap-3">
                        <loading_loader v-if="loading.delete" size="20px" color="#fff" border="2px" />
                        <span v-else>{{ $t('action.confirm_delete') }}</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- Trashed Templates Modal -->
    <div class="modal" id="modalShowTrashTemplate">
        <div class="modal-dialog">
            <div class="modal-content radius-2">
                <div class="modal-header d-flex align-items-center justify-content-between">
                    <h4 class="modal-title fs-18">{{ $t('email_template.trashed_list_title') }}</h4>
                    <span class="fs-18 fw-medium" data-bs-dismiss="modal">
                        <i class="ri-close-line cursor-pointer"></i>
                    </span>
                </div>
                <div v-if="loading.fetchTrashed" style="height: 60vh;"
                    class="d-flex align-items-center justify-content-center">
                    <loading_loader size="40px" border="4px" />
                </div>
                <div v-else class="modal-body">
                    <div v-if="trashedTemplates.length == 0">
                        <no_data />
                    </div>
                    <div v-else class="row g-3">
                        <div v-for="trashed_template in trashedTemplates" class="col-12">
                            <div
                                class="border radius-2 p-3 d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
                                <div class="mb-2 mb-md-0">
                                    <p class="mb-1 fw-bold text-primary">{{ trashed_template.name }}</p>
                                </div>
                                <div class="d-flex flex-column flex-md-row align-items-md-center gap-2">
                                    <p class="mb-0 text-danger fs-14">🗑️ {{ $t('user.date_deleted') }}: {{
                                        formatDate(trashed_template.deleted_at) }}</p>
                                    <button type="button" class="btn btn-outline-success btn-sm radius-2"
                                        @click="restoreTemplate(trashed_template.id)">
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

    <!-- Template Preview Modal-->
    <div id="modalShowPreviewTemplate" class="modal" style="background: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-lg">
            <div class="modal-content radius-2">
                <div class="modal-header d-flex align-items-center justify-content-between">
                    <h5 class="modal-title">{{ previewData.subject }}</h5>
                    <span class="fs-18 fw-medium" data-bs-dismiss="modal">
                        <i class="ri-close-line cursor-pointer"></i>
                    </span>
                </div>
                <div class="modal-body">
                    <div v-html="previewData.content"></div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/configs/api'
import message from '@/utils/message_state'
import loading_loader from '@/components/loading/loading__loader-circle.vue'
import pagination from '@/components/pagination.vue'
import no_data from '@/components/no_data.vue'
import { formatDate } from '@/utils/format_date'
import { QuillEditor } from '@vueup/vue-quill';
// ============ State =============
const templates = ref([])
const trashedTemplates = ref([])
const id_deleted = ref(null)
const loading = ref({
    handleForm: false,
    fetch: false,
    delete: false,
    fetchTrashed: false,
    restore: false,
})
const meta = ref({})
const currentPage = ref(1)
const selectedStatus = ref(null)
const searchValue = ref('')
const per_page = ref(10)
const errors = ref({})
const isDeleteModalVisible = ref(false)
const isEdit = ref(null)
const templateFormDefault = {
    name: '',
    subject: '',
    description: '',
    content: '',
    status: 'draft',
    type: 'campaign',
    is_active: true
}
const templateForm = ref({ ...templateFormDefault })

// ============ Lifecycle ============
onMounted(() => {
    fetchTemplates()
})

// ============ CRUD =============
const fetchTemplates = async () => {
    loading.value.fetch = true
    try {
        const res = await api.get('/email-templates', {
            params: {
                page: currentPage.value,
                per_page: per_page.value,
                search: searchValue.value,
                status: selectedStatus.value
            }
        })
        templates.value = res.data.data
        meta.value = res.data.meta
    } catch {
        message.emit('show-message', { title: "Lỗi", text: "Không lấy được danh sách template", type: "error" })
    } finally {
        loading.value.fetch = false
    }
}

const submitTemplate = async () => {
    if (!validateTemplateForm()) return
    const payload = templateForm.value
    try {
        loading.value.handleForm = true
        if (isEdit.value) {
            const res = await api.put(`/email-templates/${payload.id}`, payload)
            if (res.status === 200) {
                const idx = templates.value.findIndex(t => t.id === payload.id)
                if (idx !== -1) templates.value[idx] = res.data.data
                message.emit('show-message', { title: "Thông báo", text: "Cập nhật template thành công.", type: "success" })
            }
        } else {
            const res = await api.post('/email-templates', payload)
            if (res.status === 201) {
                templates.value.unshift(res.data.data)
                message.emit('show-message', { title: "Thông báo", text: "Thêm template thành công.", type: "success" })
                templateForm.value = { ...templateFormDefault }
            }
        }
    } catch (error) {
        if (error.response?.status == 422) {
            errors.value = error.response.data.errors
        }
    } finally {
        loading.value.handleForm = false
    }
}

const showModalForm = (template_id) => {
    if (template_id) {
        const template = templates.value.find(t => t.id === template_id)
        isEdit.value = template_id
        if (template) templateForm.value = { ...template }
        else message.emit('show-message', { title: "Warning", text: "Template không tồn tại.", type: "warning" })
    } else {
        isEdit.value = null
        templateForm.value = { ...templateFormDefault }
    }
}

const editTemplate = (template_id) => {
    showModalForm(template_id)
}

const deleteTemplateConfirm = (id = null) => {
    if (id) id_deleted.value = id
    if (!id_deleted.value) {
        message.emit('show-message', { title: 'Thông báo', text: 'Chọn template để xóa', type: 'info' })
        return
    }
    isDeleteModalVisible.value = true
}

const deleteTemplate = async () => {
    loading.value.delete = true
    try {
        const res = await api.delete(`/email-templates/${id_deleted.value}`)
        if (res.status === 200) {
            templates.value = templates.value.filter(t => t.id !== id_deleted.value)
            message.emit('show-message', {
                title: "Thông báo",
                text: res.data.message,
                type: "success"
            })
        }
    } catch (error) {
        message.emit('show-message', { title: "Lỗi", text: "Xóa template thất bại", type: "error" })
    } finally {
        loading.value.delete = false
        isDeleteModalVisible.value = false
        id_deleted.value = null
    }
}

const previewData = ref({})
const showModalPreview = (template) => {
    previewData.value = { ...template }
    console.log(previewData.value);

}
// ============ Search & Pagination & Filter =============
const onSearch = () => {
    currentPage.value = 1
    fetchTemplates()
}
const clearSearch = () => {
    searchValue.value = ''
    fetchTemplates()
}
const onChangePage = (page) => {
    currentPage.value = page
    fetchTemplates()
}
const setPerPage = (perPage) => {
    per_page.value = perPage
    fetchTemplates()
}
const setMonth = (month) => {
    selectedMonth.value = month
    fetchTemplates()
}
const setYear = (year) => {
    selectedYear.value = year
    fetchTemplates()
}
const setStatus = (status) => {
    selectedStatus.value = status
    fetchTemplates()
}

// ============ Trashed Templates =============
const fetchTrashedTemplates = async () => {
    loading.value.fetchTrashed = true
    try {
        const res = await api.get('/email-templates/trashed')
        trashedTemplates.value = res.data
    } catch {
        message.emit('show-message', { title: "Lỗi", text: "Không lấy được template đã xóa", type: "error" })
    } finally {
        loading.value.fetchTrashed = false
    }
}
const restoreTemplate = async (templateId) => {
    if (!confirm('Bạn chắc chắn muốn khôi phục?')) return
    loading.value.restore = true
    try {
        const res = await api.put(`/email-templates/${templateId}/restore`)
        const idx = trashedTemplates.value.findIndex(t => t.id === templateId)
        if (idx !== -1) {
            templates.value.push(trashedTemplates.value[idx])
            trashedTemplates.value.splice(idx, 1)
        }
        message.emit('show-message', { title: "Thông báo", text: res.data.message, type: "success" })
    } catch (err) {
        message.emit('show-message', { title: "Thông báo", text: err?.response?.data?.message || "Restore failed", type: "error" })
    } finally {
        loading.value.restore = false
    }
}

// ============ Validate & Error =============
const validateTemplateForm = () => {
    errors.value = {}

    if (!templateForm.value.name) {
        errors.value.name = ['Tên template là bắt buộc.']
    }

    if (!templateForm.value.subject) {
        errors.value.subject = ['Tiêu đề email là bắt buộc.']
    }

    if (!templateForm.value.content || templateForm.value.content.trim() === '<p><br></p>') {
        errors.value.content = ['Nội dung HTML là bắt buộc.']
    }
    return Object.keys(errors.value).length === 0
}


</script>