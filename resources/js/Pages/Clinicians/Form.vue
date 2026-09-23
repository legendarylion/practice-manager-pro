<script setup>
import { useForm } from '@inertiajs/vue3';
import FormField from '@/Components/Dashboard/FormField.vue';
import Card from '@/Components/Dashboard/Card.vue';

const props = defineProps({
    clinician: { type: Object, default: null },
});

const isEdit = !!props.clinician;

const form = useForm({
    first_name: props.clinician?.first_name ?? '',
    last_name: props.clinician?.last_name ?? '',
    specialty: props.clinician?.specialty ?? '',
    status: props.clinician?.status ?? 'active',
    current_caseload: props.clinician?.current_caseload ?? 0,
    max_caseload: props.clinician?.max_caseload ?? 20,
    average_sessions_per_week: props.clinician?.average_sessions_per_week ?? 0,
    avg_billable_rate: props.clinician?.avg_billable_rate ?? 150,
});

const submit = () => {
    if (isEdit) {
        form.put(route('clinicians.update', props.clinician.id));
    } else {
        form.post(route('clinicians.store'));
    }
};

const inputClass = 'w-full rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 text-sm text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:border-sky-400 focus:ring-sky-400 transition';
</script>

<template>
    <Card>
        <form @submit.prevent="submit" class="space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <FormField label="First name" :error="form.errors.first_name">
                    <input v-model="form.first_name" type="text" :class="inputClass" required />
                </FormField>
                <FormField label="Last name" :error="form.errors.last_name">
                    <input v-model="form.last_name" type="text" :class="inputClass" required />
                </FormField>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <FormField label="Specialty" :error="form.errors.specialty" hint="e.g. Anxiety & CBT, Trauma / EMDR">
                    <input v-model="form.specialty" type="text" :class="inputClass" />
                </FormField>
                <FormField label="Status" :error="form.errors.status">
                    <select v-model="form.status" :class="inputClass">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </FormField>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <FormField label="Current caseload" :error="form.errors.current_caseload">
                    <input v-model.number="form.current_caseload" type="number" min="0" :class="inputClass" />
                </FormField>
                <FormField label="Max caseload" :error="form.errors.max_caseload" hint="The number of clients this clinician can hold at full capacity.">
                    <input v-model.number="form.max_caseload" type="number" min="0" :class="inputClass" />
                </FormField>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <FormField label="Avg sessions per week" :error="form.errors.average_sessions_per_week">
                    <input v-model.number="form.average_sessions_per_week" type="number" step="0.5" min="0" :class="inputClass" />
                </FormField>
                <FormField label="Avg billable rate ($)" :error="form.errors.avg_billable_rate">
                    <input v-model.number="form.avg_billable_rate" type="number" step="1" min="0" :class="inputClass" />
                </FormField>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2 border-t border-gray-100 dark:border-gray-700">
                <a :href="route('clinicians.index')" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">Cancel</a>
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="inline-flex items-center px-4 py-2 rounded-lg bg-sky-600 hover:bg-sky-500 text-white text-sm font-medium shadow-sm disabled:opacity-50"
                >
                    {{ isEdit ? 'Save changes' : 'Add clinician' }}
                </button>
            </div>
        </form>
    </Card>
</template>
