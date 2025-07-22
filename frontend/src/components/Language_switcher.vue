<template>
    <div class="d-flex align-items-center gap-3">
        <div class="dropdown">
            <button class="btn btn-light border dropdown-toggle radius-2 fw-medium" type="button"
                data-bs-toggle="dropdown">
                🌐 {{ currentLocale === 'vi' ? 'Tiếng Việt' : 'English' }}
            </button>
            <ul class="dropdown-menu">
                <li>
                    <a class="dropdown-item fw-medium cursor-pointer" @click="changeLanguage('vi')"
                        :class="{ active: currentLocale === 'vi' }">
                        🇻🇳 Tiếng Việt
                    </a>
                </li>
                <li>
                    <a class="dropdown-item fw-medium cursor-pointer" @click="changeLanguage('en')"
                        :class="{ active: currentLocale === 'en' }">
                        🇺🇸 English
                    </a>
                </li>
            </ul>
        </div>
    </div>
</template>

<script setup>
import { useI18n } from 'vue-i18n'
import { ref } from 'vue'

// i18n
const { locale } = useI18n()
const currentLocale = ref(locale.value)

const changeLanguage = (lang) => {
    currentLocale.value = lang
    locale.value = lang
    localStorage.setItem('locale', lang)
}
</script>
