<script setup>
defineProps({
    value: String,
    isRequired: {
        type: Boolean,
        default: false,
    }
});
</script>

<template>
    <label class="font-bold text-sm flex gap-1">
        <span v-if="value">{{ value }}</span>
        <span v-else><slot /></span>
        <span v-if="isRequired" class="text-red-500">*</span>
    </label>
</template>
