<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import Card from 'primevue/card';
import AppLayout from '@/layouts/AppLayout.vue';

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' }
];

const stats = [
    { label: 'Admin', icon: 'pi pi-shield', colorClass: 'text-blue-500', bgClass: 'bg-blue-50' },
    { label: 'Users', icon: 'pi pi-users', colorClass: 'text-orange-500', bgClass: 'bg-orange-50' },
    { label: 'Roles', icon: 'pi pi-lock', colorClass: 'text-cyan-500', bgClass: 'bg-cyan-50' }
];
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                <Card class="shadow-sm border-none ring-1 ring-gray-200 !rounded-lg overflow-hidden">
                    <template #content>
                        <div class="flex items-center justify-between p-2">
                            <span class="text-gray-600 font-medium">{{ stats[0].label }}</span>
                            <div class="flex items-center justify-center size-10 rounded bg-blue-50 border border-blue-100">
                                <i :class="[stats[0].icon, 'text-blue-500 text-lg']"></i>
                            </div>
                        </div>
                    </template>
                </Card>

                <Card class="shadow-sm border-none ring-1 ring-gray-200 !rounded-lg overflow-hidden">
                    <template #content>
                        <div class="flex items-center justify-between p-2">
                            <span class="text-gray-600 font-medium">{{ stats[1].label }}</span>
                            <div class="flex items-center justify-center size-10 rounded bg-orange-50 border border-orange-100">
                                <i :class="[stats[1].icon, 'text-orange-500 text-lg']"></i>
                            </div>
                        </div>
                    </template>
                </Card>

                <Card class="shadow-sm border-none ring-1 ring-gray-200 !rounded-lg overflow-hidden">
                    <template #content>
                        <div class="flex items-center justify-between p-2">
                            <span class="text-gray-600 font-medium">Permission</span>
                            <div class="flex items-center justify-center size-10 rounded bg-cyan-50 border border-cyan-100">
                                <i class="pi pi-key text-cyan-500 text-lg"></i>
                            </div>
                        </div>
                    </template>
                </Card>

            </div>
        </div>
    </AppLayout>
</template>

<style scoped>

:deep(.p-card-body) {
    padding: 1rem !important;
}
:deep(.p-card-content) {
    padding: 0 !important;
}
</style>
