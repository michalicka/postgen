<template>
    <label :for="input" class="block text-sm dark:text-gray-400 font-medium text-label truncate">
        <div class="flex justify-between">
            <slot></slot>
        </div>
    </label>
</template>

<script>
export default {
    name: 'BaseLabel'
}
</script>

<script setup>
const props = defineProps({
    input: {
        type: String,
        default: ''
    }
})
</script>
