<script lang="ts">
import axios from 'axios';
import PvButton from 'primevue/button';
import Column from 'primevue/column';
import DataTable from 'primevue/datatable';
import Dialog from 'primevue/dialog';
import FloatLabel from 'primevue/floatlabel';
import InputText from 'primevue/inputtext';
import Toast from 'primevue/toast';
import { defineComponent } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import 'primeicons/primeicons.css';

interface Department {
    id: number;
    department: string;
}

export default defineComponent({
    name: 'DepartmentManagement',
    components: {
        AppLayout,
        InputText,
        PvButton,
        FloatLabel,
        DataTable,
        Column,
        PvDialog: Dialog,
        PvToast: Toast
    },

    data() {
        return {
            departments: [] as Department[],
            departmentName: '',
            editingId: null as number | null,
            showConfirm: false,
            confirmMessage: '',
            confirmTitle: '',
            confirmAction: null as (() => void) | null,
            search: '',
            first: 0,
            rows: 10,
            totalRecords: 0,
        };
    },

    mounted() {
        this.loadDepartments();
    },

    methods: {
        async loadDepartments() {
            try {
                const response = await axios.get('/api/department');
                this.departments = response.data.data;

                this.totalRecords = this.departments.length;
            } catch (error) {
                console.error('Failed to load departments:', error);
                this.$toast.add({
                    severity: 'error',
                    summary: 'Error',
                    detail: 'Failed to load departments',
                    life: 3000
                });
            }
        },

        editDepartment(dept: Department) {
            this.departmentName = dept.department;
            this.editingId = dept.id;
        },

        async saveDepartment() {
            if (!this.departmentName.trim()) {
                this.$toast.add({
                    severity: 'warn',
                    summary: 'Warning',
                    detail: 'Department name cannot be empty',
                    life: 3000
                });
                return;
            }

            try {
                if (this.editingId) {
                    await axios.put(`/api/department/${this.editingId}`, {
                        department: this.departmentName
                    });
                    this.$toast.add({
                        severity: 'success',
                        summary: 'Updated',
                        detail: `Department "${this.departmentName}" updated successfully`,
                        life: 3000
                    });
                } else {
                    await axios.post('/api/department', {
                        department: this.departmentName
                    });
                    this.$toast.add({
                        severity: 'success',
                        summary: 'Created',
                        detail: `Department "${this.departmentName}" created successfully`,
                        life: 3000
                    });
                }

                this.departmentName = '';
                this.editingId = null;
                this.loadDepartments();
            } catch (error: any) {
                if (error.response && error.response.status === 422) {
                    const validationErrors = error.response.data.errors;
                    const errorMessage = validationErrors.department ? validationErrors.department[0] : 'Validation failed';

                this.$toast.add({
                    severity: 'error',
                    summary: 'Validation Error',
                    detail: errorMessage,
                    life: 4000
                });
                } else {
                    this.$toast.add({
                        severity: 'error',
                        summary: 'Error',
                        detail: 'Save failed. Please try again.',
                        life: 3000
                    });
                }
            }
        },

        deleteDepartment(dept: Department) {
            this.confirmTitle = 'Delete Department';
            this.confirmMessage = `Are you sure you want to delete "${dept.department}"? This action cannot be undone.`;
            this.confirmAction = async () => {
                try {
                    await axios.delete(`/api/department/${dept.id}`);
                    this.$toast.add({
                        severity: 'success',
                        summary: 'Deleted',
                        detail: `"${dept.department}" deleted successfully`,
                        life: 3000
                    });
                    this.loadDepartments();
                } catch {
                    this.$toast.add({
                        severity: 'error',
                        summary: 'Error',
                        detail: 'Delete failed',
                        life: 3000
                    });
                }
            };
            this.showConfirm = true;
        },

        acceptConfirm() {
            this.showConfirm = false;
            if (this.confirmAction) this.confirmAction();
        },

        rejectConfirm() {
            this.showConfirm = false;
        }
    }
});
</script>

<template>
    <AppLayout>
        <PvToast position="top-right" />

        <PvDialog
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
        </PvDialog>

        <div class="p-6">
            <h1 class="text-2xl font-bold mb-6">Department Management</h1>

            <div class="flex flex-wrap items-center gap-4 mb-8 max-w-2xl">
                <div class="flex-grow">
                    <FloatLabel variant="on">
                        <InputText id="deptName" v-model="departmentName" class="w-full" />
                        <label for="deptName">Department Name</label>
                    </FloatLabel>
                </div>

                <div class="flex gap-2">
                    <PvButton
                        :label="editingId ? 'Update' : 'Save'"
                        icon="pi pi-check"
                        class="bg-emerald-500 border-none px-6 text-white"
                        @click="saveDepartment"
                    />
                    <PvButton
                        v-if="editingId"
                        label="Cancel"
                        severity="secondary"
                        variant="text"
                        @click="editingId = null; departmentName = ''"
                    />
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="mb-4 flex justify-end">
                    <span class="relative">
                        <InputText
                            v-model="search"
                            placeholder="Search departments..."
                            class="w-64 pl-10"
                        />
                    </span>
                </div>

                <DataTable
                    :value="departments.filter(d => d.department.toLowerCase().includes(search.toLowerCase()))"
                    :paginator="true"
                    :rows="rows"
                    :first="first"
                    :totalRecords="totalRecords"
                    @page="e => { first = e.first; rows = e.rows; }"
                    responsiveLayout="scroll"
                >
                    <Column
                        field="id"
                        header="ID"
                        class="w-16"
                        headerClass="justify-center"
                        bodyClass="text-center"
                    ></Column>

                    <Column
                        field="department"
                        header="Department"
                        class="pl-4"
                    ></Column>

                    <Column
                        header="Actions"
                        class="w-32"
                        headerClass="justify-center"
                        bodyClass="text-center"
                    >
                        <template #body="{ data }">
                            <div class="flex justify-center gap-1">
                                <PvButton
                                    icon="pi pi-pencil"
                                    variant="text"
                                    rounded
                                    class="!text-emerald-500 hover:!bg-emerald-50"
                                    @click="editDepartment(data)"
                                />

                                <PvButton
                                    icon="pi pi-trash"
                                    variant="text"
                                    rounded
                                    class="!text-red-500 hover:!bg-red-50"
                                    @click="deleteDepartment(data)"
                                />
                            </div>
                        </template>
                    </Column>
                </DataTable>
            </div>
        </div>
    </AppLayout>
</template>
