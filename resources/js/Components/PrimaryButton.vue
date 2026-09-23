<script setup>
defineProps({
    type: {
        type: String,
        default: 'submit',
    },
});
</script>

<template>
    <button
        :type="type"
        class="inline-flex items-center px-4 py-2 
               bg-gray-800 dark:bg-blue-500 
               border border-transparent 
               rounded-md font-semibold text-xs 
               text-white uppercase tracking-widest 
               hover:bg-gray-700 dark:hover:bg-blue-600 
               active:bg-gray-900 dark:active:bg-blue-700 
               focus:outline-none focus:ring-2 
               focus:ring-blue-500 focus:ring-offset-2 
               dark:focus:ring-offset-gray-900
               transition ease-in-out duration-150"
    >
        <slot />
    </button>
</template>
