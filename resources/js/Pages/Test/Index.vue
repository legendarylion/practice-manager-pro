<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'; // Import the layout
import { Head } from '@inertiajs/vue3'; // Import Inertia's Head
import ChildComponent from '@/Components/ChildComponent.vue'; // Import the child component

const message = 'Hello from Parent!'; // A basic prop value
</script>

<template>
    <AppLayout>
        <Head title="Parent View" />
        <div>
            <h1>Parent View</h1>
            <!-- Pass the `message` prop to the child -->
            <ChildComponent :message="message" />
        </div>
    </AppLayout>
</template>
