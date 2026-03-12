<script setup lang="ts">
import axios from 'axios';
import PvButton from 'primevue/button';
import Dialog from 'primevue/dialog';
import { useToast } from 'primevue/usetoast';
import { ref, onMounted } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';

interface Department {
    id: number;
    department: string;
}

const toast = useToast();

const departments = ref<Department[]>([]);
const departmentName = ref<string>('');
const editingId = ref<number | null>(null);

const showConfirm = ref(false);
const confirmMessage = ref('');
const confirmTitle = ref('');
const confirmAction = ref<(() => void) | null>(null);

const loadDepartments = async () => {
    try {
        const response = await axios.get('/api/department');
        departments.value = response.data.data;
    } catch (error) {
        console.error('Failed to load departments:', error);
        toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to load departments', life: 3000 });
    }
};

const editDepartment = (dept: Department) => {
    departmentName.value = dept.department;
    editingId.value = dept.id;
};

const saveDepartment = async () => {
    if (!departmentName.value.trim()) {
        toast.add({ severity: 'warn', summary: 'Warning', detail: 'Department name cannot be empty', life: 3000 });
        return;
    }

    try {
        if (editingId.value) {
            await axios.put(`/api/department/${editingId.value}`, { department: departmentName.value });
            toast.add({ severity: 'success', summary: 'Updated', detail: `Department "${departmentName.value}" updated successfully`, life: 3000 });
        } else {
            await axios.post('/api/department', { department: departmentName.value });
            toast.add({ severity: 'success', summary: 'Created', detail: `Department "${departmentName.value}" created successfully`, life: 3000 });
        }

        departmentName.value = '';
        editingId.value = null;
        loadDepartments();
    } catch {
        toast.add({ severity: 'error', summary: 'Error', detail: 'Save failed', life: 3000 });
    }
};

const deleteDepartment = (dept: Department) => {
    confirmTitle.value = 'Delete Department';
    confirmMessage.value = `Are you sure you want to delete "${dept.department}"? This action cannot be undone.`;
    confirmAction.value = async () => {
        try {
            await axios.delete(`/api/department/${dept.id}`);
            toast.add({ severity: 'success', summary: 'Deleted', detail: `"${dept.department}" deleted successfully`, life: 3000 });
            loadDepartments();
        } catch {
            toast.add({ severity: 'error', summary: 'Error', detail: 'Delete failed', life: 3000 });
        }
    };
    showConfirm.value = true;
};

const acceptConfirm = () => {
    showConfirm.value = false;
    confirmAction.value?.();
};

const rejectConfirm = () => {
    showConfirm.value = false;
};

onMounted(() => {
    loadDepartments();
});
</script>

<template>
    <AppLayout>

        <Toast position="top-right" />

        <Dialog
            v-model:visible="showConfirm"
            :header="confirmTitle"
            :modal="true"
            :closable="false"
            :dismissable-mask="true"
            class="w-96"
        >
            <div class="p-4 border-2 border-yellow-400 rounded-lg bg-yellow-50 flex items-center gap-3">
                <i class="pi pi-exclamation-triangle text-yellow-600 text-3xl"></i>
                <span class="text-gray-800">{{ confirmMessage }}</span>
            </div>

            <template #footer>
                <PvButton label="Delete" severity="danger" @click="acceptConfirm" />
                <PvButton label="Cancel" severity="secondary" @click="rejectConfirm" />
            </template>
        </Dialog>

        <div class="p-6">
            <h1 class="text-2xl font-bold mb-6">Department Management</h1>

            <div class="flex gap-2 mb-6">
                <input
                    v-model="departmentName"
                    type="text"
                    placeholder="Enter Department"
                    class="border rounded p-2 bg-white text-black "
                />
                <button
                    @click="saveDepartment"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
                >
                    {{ editingId ? 'Update' : 'Save' }}
                </button>
                <button
                    v-if="editingId"
                    @click="editingId = null; departmentName=''"
                    class="bg-gray-500 text-white px-4 py-2 rounded"
                >
                    Cancel
                </button>
            </div>

            <table class="w-full border-collapse border border-gray-300">
                <thead class="bg-gray-100">
                <tr>
                    <th class="border p-2">ID</th>
                    <th class="border p-2">Department</th>
                    <th class="border p-2">Actions</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="dept in departments" :key="dept.id" class="text-gray-800">
                    <td class="border p-2 text-center">{{ dept.id }}</td>
                    <td class="border p-2">{{ dept.department }}</td>
                    <td class="border p-2 text-center space-x-2">
                        <button @click="editDepartment(dept)" class="bg-yellow-500 text-white px-3 py-1 rounded">Edit</button>
                        <button @click="deleteDepartment(dept)" class="bg-red-600 text-white px-3 py-1 rounded">Delete</button>
                    </td>
                </tr>
                <tr v-if="departments.length === 0">
                    <td colspan="3" class="p-4 text-center text-gray-500">No departments found.</td>
                </tr>
                </tbody>
            </table>
        </div>
    </AppLayout>
</template>
