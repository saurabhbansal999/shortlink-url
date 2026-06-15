<script setup>
</script>

<template>
    <div class="flex min-h-screen items-center justify-center bg-gray-50">
        <div class="w-full max-w-sm px-4">
            <div class="mb-7 text-center">
                <span class="text-2xl font-bold tracking-tight text-gray-900">Sembark</span>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white px-8 py-8 shadow-sm">
                <slot />
            </div>
        </div>
    </div>
</template>
