<template>
    <div class="list-notification flex flex-col h-full">
<!--        <div class="list-notification__header mb-5">-->
<!--            <span class="ml-1">新しい通知{{ unread }}件</span>-->
<!--        </div>-->
        <div ref="listNotification" class="list-notification__content h-full overflow-auto">
            <slot />
            <div
                v-if="isLoadMore"
                class="list-notification__loadmore mt-2 text-center cursor-pointer"
                @click.prevent="handleScrollNotification"
            >
                <span>もっと見る</span>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        unread: {
            type: Number,
            default: 0,
        },
        isLoadMore: {
            type: Boolean,
            default: false,
        },
    },
    emits: ['load-more'],
    methods: {
        handleScrollNotification() {
            this.$emit('load-more')
        },
    },
}
</script>
