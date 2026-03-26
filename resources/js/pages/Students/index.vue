<script lang="ts">
import axios from 'axios';
import PvButton from 'primevue/button';
import Column from 'primevue/column';
import ConfirmDialog from 'primevue/confirmdialog';
import DataTable from 'primevue/datatable';
import DatePicker from 'primevue/datepicker';
import FloatLabel from 'primevue/floatlabel';
import InputText from 'primevue/inputtext';
import PvSelect from 'primevue/select';
import Toast from 'primevue/toast';
import { useConfirm } from 'primevue/useconfirm';
import { useToast } from 'primevue/usetoast';
import { defineComponent } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';

export default defineComponent({
    name: 'StudentIndex',
    components: {
        AppLayout,
        InputText,
        PvButton,
        FloatLabel,
        PvSelect,
        DatePicker,
        DataTable,
        Column,
        Toast,
        ConfirmDialog
    },
    setup() {
        const toast = useToast();
        const confirm = useConfirm();
        return { toast, confirm };
    },
    data() {
        return {
            departments: [] as string[],
            students: [] as any[],

            editingStudentId: null as number | null,
            selectedDepartment: null as string | null,
            name: '',
            email: '',
            nic: '',
            phone_number: '',
            birthday: null as Date | null,
            loading: false,
            tableLoading: false,

            search: '',
            first: 0,
            rows: 10
        };
    },
    mounted() {
        this.loadInitialData();
    },
    methods: {
        async loadInitialData() {
            this.tableLoading = true;
            try {
                const [deptRes, studentRes] = await Promise.all([
                    axios.get('/api/departments'),
                    axios.get('/api/students')
                ]);

                this.departments = deptRes.data.data;
                this.students = studentRes.data.data;
            } catch (error) {
                console.error("Fetch Error:", error);
            } finally {
                this.tableLoading = false;
            }
        },
        async viewStudent(id: number) {
            try {
                const response = await axios.get(`/api/students/${id}`);
                const student = response.data.data;
                this.editingStudentId = student.id;
                this.name = student.name;
                this.email = student.email;
                this.nic = student.nic;
                this.phone_number = student.phone_number;
                this.birthday = student.birthday ? new Date(student.birthday) : null;

                this.selectedDepartment = student.department;

                this.toast.add({
                    severity: 'info',
                    summary: 'Loaded',
                    detail: `Details for ${student.name} loaded into form.`,
                    life: 3000
                });
            } catch {
                this.toast.add({
                    severity: 'error',
                    summary: 'Error',
                    detail: 'Could not fetch student details.',
                    life: 3000
                });
            }
        },
        async deleteStudent(id: number) {
            this.confirm.require({
                message: 'Are you sure you want to delete this student?',
                header: 'Confirmation',
                icon: 'pi pi-exclamation-triangle',
                rejectProps: {
                    label: 'Cancel',
                    severity: 'secondary',
                    outlined: true
                },
                acceptProps: {
                    label: 'Delete',
                    severity: 'danger'
                },
                accept: async () => {
                    try {
                        await axios.delete(`/api/students/${id}`);
                        this.toast.add({
                            severity: 'success',
                            summary: 'Deleted',
                            detail: 'Student record removed.',
                            life: 3000
                        });
                        this.loadInitialData();
                    } catch {
                        this.toast.add({
                            severity: 'error',
                            summary: 'Error',
                            detail: 'Failed to delete student.',
                            life: 3000
                        });
                    }
                }
            });
        },
        focusNext(e: any) {
            const inputs = Array.from(document.querySelectorAll('input, button, .p-dropdown'));
            const index = inputs.indexOf(e.target);
            if (index > -1 && index < inputs.length - 1) {
                (inputs[index + 1] as HTMLElement).focus();
            }
        },
        async saveStudent() {
            this.loading = true;
            let formattedDate = null;
            if (this.birthday instanceof Date) {
                formattedDate = this.birthday.toISOString().split('T')[0];
            }
            const payload = {
                department: this.selectedDepartment,
                name: this.name,
                email: this.email,
                nic: this.nic,
                phone_number: this.phone_number,
                birthday: formattedDate
            };
            try {
                if (this.editingStudentId) {
                    await axios.put(`/api/students/${this.editingStudentId}`, payload);
                    this.toast.add({
                        severity: 'success',
                        summary: 'Updated',
                        detail: 'Student record updated successfully!',
                        life: 3000
                    });
                } else {
                    await axios.post('/api/students', payload);
                    this.toast.add({
                        severity: 'success',
                        summary: 'Success',
                        detail: 'Student record saved successfully!',
                        life: 3000
                    });
                }
                this.resetForm();
                this.loadInitialData();
            } catch (error: any) {
                this.toast.add({
                    severity: 'error',
                    summary: 'Error',
                    detail: error.response?.data?.message || 'Action failed.',
                    life: 3000
                });
            } finally {
                this.loading = false;
            }
        },
        resetForm() {
            this.editingStudentId = null;
            this.selectedDepartment = null;
            this.name = '';
            this.email = '';
            this.nic = '';
            this.phone_number = '';
            this.birthday = null;
        }
    },
    computed: {
        filteredStudents(): any[] {
            if (!this.search) return this.students;
            const searchTerm = this.search.toLowerCase();
            return this.students.filter(student =>
                student.name.toLowerCase().includes(searchTerm) ||
                student.nic.toLowerCase().includes(searchTerm) ||
                student.department.toLowerCase().includes(searchTerm)
            );
        }
    }
});
</script>

<template>
    <AppLayout>
        <Toast />
        <ConfirmDialog />
        <div class="p-6">
            <h1 class="text-2xl font-bold mb-6">
                {{ editingStudentId ? 'Edit Student' : 'Student Registration' }}
            </h1>
            <div class="max-w-4xl mb-12">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div class="flex flex-col gap-2">
                        <FloatLabel variant="on">
                            <PvSelect
                                id="department"
                                v-model="selectedDepartment"
                                :options="departments"
                                fluid
                            />
                            <label for="department">Select Department</label>
                        </FloatLabel>
                    </div>
                    <div class="flex flex-col gap-2">
                        <FloatLabel variant="on">
                            <InputText id="name" v-model="name" class="w-full" @keydown.enter="focusNext" />
                            <label for="name">Full Name</label>
                        </FloatLabel>
                    </div>
                    <div class="flex flex-col gap-2">
                        <FloatLabel variant="on">
                            <InputText id="email" v-model="email" class="w-full" @keydown.enter="focusNext" />
                            <label for="email">Email Address</label>
                        </FloatLabel>
                    </div>
                    <div class="flex flex-col gap-2">
                        <FloatLabel variant="on" class="w-full">
                            <DatePicker v-model="birthday" showIcon dateFormat="yy-mm-dd" class="w-full" @keydown.enter="focusNext" />
                            <label>Birthday</label>
                        </FloatLabel>
                    </div>
                    <div class="flex flex-col gap-2">
                        <FloatLabel variant="on">
                            <InputText id="nic" v-model="nic" class="w-full" @keydown.enter="focusNext" />
                            <label for="nic">NIC Number</label>
                        </FloatLabel>
                    </div>
                    <div class="flex flex-col gap-2">
                        <FloatLabel variant="on">
                            <InputText id="phone" v-model="phone_number" class="w-full" @keydown.enter="focusNext" />
                            <label for="phone">Phone Number</label>
                        </FloatLabel>
                    </div>
                </div>
                <div class="flex gap-2 justify-start">
                    <PvButton
                        :label="editingStudentId ? 'Update Student' : 'Save Student'"
                        :icon="editingStudentId ? 'pi pi-check' : 'pi pi-user-plus'"
                        class="w-full md:w-64"
                        :loading="loading"
                        @click="saveStudent"
                    />
                    <PvButton v-if="editingStudentId" label="Cancel" severity="secondary" variant="outlined" @click="resetForm" />
                </div>
            </div>

            <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold">Registered Students</h2>
                    <span class="relative">
                        <InputText
                            v-model="search"
                            placeholder="Search students..."
                            class="w-64 pl-10"
                        />
                    </span>
                </div>


                <DataTable
                    :value="filteredStudents"
                    :loading="tableLoading"
                    :paginator="true"
                    :rows="rows"
                    :first="first"
                    @page="e => { first = e.first; rows = e.rows; }"
                    stripedRows
                    responsiveLayout="scroll"
                    class="p-datatable-sm w-full"
                >
                    <Column field="name" header="Name" sortable class="px-4 py-3"></Column>
                    <Column field="department" header="Department" sortable class="px-4 py-3"></Column>
                    <Column field="nic" header="NIC" class="px-4 py-3"></Column>
                    <Column field="phone_number" header="Phone Number" class="px-4 py-3"></Column>
                    <Column header="Action" style="min-width: 8rem" class="px-4 py-3">
                        <template #body="slotProps">
                            <div class="flex gap-3">
                                <PvButton
                                    icon="pi pi-pencil"
                                    variant="text"
                                    rounded
                                    class="!text-emerald-500 hover:!bg-emerald-50"
                                    @click="viewStudent(slotProps.data.id)"
                                />
                                <PvButton
                                    icon="pi pi-trash"
                                    variant="text"
                                    rounded
                                    class="!text-red-500 hover:!bg-red-50"
                                    @click="deleteStudent(slotProps.data.id)"
                                />
                            </div>
                        </template>
                    </Column>
                </DataTable>
            </div>
        </div>
    </AppLayout>
</template>
