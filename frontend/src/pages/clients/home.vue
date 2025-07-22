<template>
    <div class="d-flex justify-content-center align-items-center" style="min-height: 100vh;">
        <div class="p-4 text-center" style="max-width: 500px; width: 100%;">
            <h1 class="mb-3 text-color">{{ $t('home.welcome') }}</h1>
            <p class="fs-5">{{ $t('home.intro') }}</p>
            <p class="text-muted fw-medium">{{ $t('home.guide') }}</p>
            <div class="d-flex justify-content-center gap-3 mt-4">
                <router-link :to="{ name: 'projects' }" class="main-btn text-white py-1 px-3">
                    {{ $t('home.view_projects') }}
                </router-link>
            </div>
        </div>
    </div>
</template>