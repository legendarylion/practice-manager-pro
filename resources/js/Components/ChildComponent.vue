<script setup>
defineProps({
    message: {
        type: String,
        required: true, // Ensure the prop is required
    },
});
</script>

<template>
    <div>
        <h2>Child Component</h2>
        <!-- Display the `message` prop passed from the parent -->
        <p>{{ message }}</p>
    </div>
</template>
