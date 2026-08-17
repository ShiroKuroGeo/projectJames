<template>
    <router-view></router-view>
</template>

<!-- <script setup>
import { onMounted, onUnmounted } from 'vue'

const handleBeforeUnload = (event) => {
    event.preventDefault()
    event.returnValue = ''
}

onMounted(() => {
    window.addEventListener('beforeunload', handleBeforeUnload)
})

onUnmounted(() => {
    window.removeEventListener('beforeunload', handleBeforeUnload)
})
</script> -->