<template>
    <div v-if="meta && meta.last_page > 1" class="d-flex align-items-center gap-2 flex-wrap">
        <!-- First -->
        <!-- <button type="button" class="sub-btn px-3 py-1" :disabled="meta.current_page === 1" @click="$emit('changePage', 1)">
            First
        </button> -->

        <!-- Prev -->
        <button type="button" :class="{
            'sub-btn px-3 py-1': true,
            'disabled': meta.current_page === 1
        }" :disabled="meta.current_page === 1" @click="$emit('changePage', meta.current_page - 1)">
            <i class="ri-arrow-left-wide-fill"></i>
        </button>

        <!-- Page numbers -->
        <template v-for="page in pages" :key="page">
            <span v-if="page === '...'" class="sub-btn px-3 py-1 select-none cursor-default">...</span>

            <button type="button" v-else @click="$emit('changePage', page)" :class="[
                'sub-btn px-3 py-1',
                page === meta.current_page ? 'bg-main text-white' : ''
            ]">
                {{ page }}
            </button>
        </template>

        <!-- Next -->
        <button type="button" :class="{
            'sub-btn px-3 py-1': true,
            'disabled': meta.current_page === meta.last_page
        }" :disabled="meta.current_page === meta.last_page" @click="$emit('changePage', meta.current_page + 1)">
            <i class="ri-arrow-right-wide-fill"></i>
        </button>

        <!-- Last -->
        <!-- <button type="button" class="sub-btn px-3 py-1" :disabled="meta.current_page === meta.last_page"
            @click="$emit('changePage', meta.last_page)">
            Last
        </button> -->
    </div>
</template>

<script>
export default {
    props: {
        meta: {
            type: Object,
            required: true,
        },
    },
    computed: {
        pages() {
            const current = this.meta.current_page
            const last = this.meta.last_page
            const range = []

            if (last <= 7) {
                for (let i = 1; i <= last; i++) range.push(i)
            } else {
                range.push(1)

                if (current > 4) range.push('...')

                const start = Math.max(2, current - 1)
                const end = Math.min(last - 1, current + 1)

                for (let i = start; i <= end; i++) range.push(i)

                if (current < last - 3) range.push('...')

                range.push(last)
            }

            return range
        },
    },
}
</script>