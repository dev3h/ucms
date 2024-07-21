<template>
    <div class="flex justify-between items-center w-full">
        <h2 class="text-2xl font-bold">{{ title }}</h2>
        <img @click="$emit('toggleFullscreen')" class="mr-5 cursor-pointer" :src="isFullscreen ? '/images/svg/zoom-big.svg' : '/images/svg/zoom-small.svg'" alt="Toggle Fullscreen">
    </div>
</template>

<script>
export default {
    props: {
        title: String,
        isFullscreen: Boolean
    }
}
</script>
