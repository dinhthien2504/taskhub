<template>
  <div
    class="d-flex flex-column align-items-center shawdow-sm justify-content-center border-0 border-secondary-subtle rounded p-4 bg-light text-secondary text-center"
    style="min-height:550px;"
  >
    <i class="fs-32 ri-folder-forbid-line"></i>

    <h5 class="mb-1">{{ title }}</h5>
    <p class="text-muted mb-3">{{ description }}</p>

    <button
      v-if="showReload"
      @click="reloadPage"
      class="main-btn py-1 px-3"
    >
      Tải Lại Trang
    </button>
  </div>
</template>

<script setup>
import { defineProps } from 'vue'

const props = defineProps({
  title: {
    type: String,
    default: 'Không có dữ liệu có sẳn.',
  },
  description: {
    type: String,
    default: 'Hiện tại không có dữ liệu để hiển thị.',
  },
  showReload: {
    type: Boolean,
    default: true,
  },
})

function reloadPage() {
  window.location.reload()
}
</script>
