<script lang="ts">
import axios from 'axios';
import PvButton from 'primevue/button';
import Column from 'primevue/column';
import DataTable from 'primevue/datatable';
import InputText from 'primevue/inputtext';
import Select from 'primevue/select';
import Toast from 'primevue/toast';
import { defineComponent } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import 'primeicons/primeicons.css';

interface Course {
    id: number | null;
    course_id: string;
    course: string;
}

export default defineComponent({
    name: 'CourseManagement',
    components: {
        AppLayout,
        PvButton,
        InputText,
        PvSelect: Select,
        DataTable,
        Column,
        PvToast: Toast,
    },

    data() {
        return {
            department: '',
            departments: [] as string[],
            isAddingDepartment: false,
            courses: [] as Course[],
            editingIndex: null as number | null,
            search: '',
            first: 0,
            rows: 10,
            totalRecords: 0,
        };
    },

    watch: {
        department() {
            if (!this.isAddingDepartment) {
                this.editingIndex = null;
                this.fetchCourses();
            }
        },
    },

    mounted() {
        this.fetchDepartments();
    },

    methods: {
        async fetchDepartments() {
            try {
                const res = await axios.get('/api/departments');
                this.departments = res.data.data;
            } catch (e) {
                console.error(e);
            }
        },

        async fetchCourses() {
            if (!this.department) {
                this.courses = [];
                this.totalRecords = 0;
                return;
            }
            try {
                const res = await axios.get(
                    `/api/course/department/${this.department}`,
                );
                this.courses = res.data.data || [];
                this.totalRecords = this.courses.length;
                this.first = 0;
            } catch (e) {
                console.error(e);
                this.courses = [];
                this.totalRecords = 0;
            }
        },

        addCourseRow() {
            this.courses.push({
                id: null,
                course_id: '',
                course: '',
            });
            this.editingIndex = this.courses.length - 1;
            this.totalRecords = this.courses.length;

            this.first =
                Math.floor((this.totalRecords - 1) / this.rows) * this.rows;
        },

        async removeCourseRow(index: number) {
            const course = this.courses[index];
            try {
                if (course.id) {
                    await axios.delete(`/api/course/${course.id}`);
                    this.$toast.add({
                        severity: 'success',
                        summary: 'Deleted',
                        detail: 'Course removed successfully',
                        life: 3000,
                    });
                }
                this.courses.splice(index, 1);
                this.totalRecords = this.courses.length;
            } catch {
                this.$toast.add({
                    severity: 'error',
                    summary: 'Error',
                    detail: 'Failed to delete course',
                    life: 3000,
                });
            }
        },

        editCourseRow(index: number) {
            this.editingIndex = index;
        },

        async saveCourses() {
            if (!this.department) {
                this.$toast.add({
                    severity: 'warn',
                    summary: 'Warning',
                    detail: 'Department is required',
                    life: 3000,
                });
                return;
            }
            if (this.courses.length === 0) {
                this.$toast.add({
                    severity: 'warn',
                    summary: 'Warning',
                    detail: 'You must add at least one course before saving.',
                    life: 3000
                });
                return;
            }

            for (const [i, course] of this.courses.entries()) {
                if (!course.course_id || !course.course) {
                    this.$toast.add({
                        severity: 'warn',
                        summary: 'Warning',
                        detail: `Course ID and Name are required in row ${i + 1}`,
                        life: 3000,
                    });
                    return;
                }
            }

            try {
                for (const course of this.courses) {
                    if (course.id) {
                        await axios.put(`/api/course/${course.id}`, {
                            id: course.id,
                            course: course.course,
                        });
                    } else {
                        await axios.post('/api/course', {
                            department: this.department,
                            courses: [course],
                        });
                    }
                }

                this.$toast.add({
                    severity: 'success',
                    summary: 'Saved',
                    detail: 'Courses saved successfully',
                    life: 3000,
                });

                this.editingIndex = null;
                this.isAddingDepartment = false;
                this.fetchCourses();
                this.fetchDepartments();
            } catch (error: any) {
                if (error.response && error.response.status === 422) {
                    const errors = error.response.data.errors;

                    Object.keys(errors).forEach((key) => {
                        let message = errors[key][0];
                        let summary = 'Validation Error';

                        if (key.includes('.')) {
                            const parts = key.split('.');
                            const rowIndex = parseInt(parts[1]) + 1;

                            summary = `Row ${rowIndex} Error`;
                            message = message.replace(key, 'Course ID');
                        }

                        this.$toast.add({
                            severity: 'error',
                            summary: summary,
                            detail: message,
                            life: 5000,
                        });
                    });
                } else {
                    this.$toast.add({
                        severity: 'error',
                        summary: 'Error',
                        detail: 'An unexpected error occurred while saving.',
                        life: 3000,
                    });
                }
            }
        },
    },
});
</script>

<template>
    <AppLayout>
        <PvToast position="top-right" />

        <div class="p-6">
            <h1 class="mb-6 text-2xl font-bold">Course Management</h1>

            <div class="mb-8 max-w-2xl">
                <h2 class="mb-3 text-lg font-semibold text-gray-700">
                    Department
                </h2>
                <div class="flex items-center gap-3">
                    <PvSelect
                        v-if="!isAddingDepartment"
                        v-model="department"
                        :options="departments"
                        optionLabel="department"  optionValue="department" placeholder="Select Department"
                        class="w-full md:w-96"
                    />

                    <InputText
                        v-if="isAddingDepartment"
                        v-model="department"
                        placeholder="Enter New Department"
                        class="w-full md:w-96"
                    />

                </div>
            </div>

            <div
                class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm"
            >
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-700">Courses</h2>

                    <span class="relative">
                        <InputText
                            v-model="search"
                            placeholder="Search courses..."
                            class="w-64 pl-10"
                            size="small"
                        />
                    </span>
                </div>

                <DataTable
                    :value="
                        courses.filter(
                            (c) =>
                                c.course
                                    .toLowerCase()
                                    .includes(search.toLowerCase()) ||
                                c.course_id
                                    .toLowerCase()
                                    .includes(search.toLowerCase()),
                        )
                    "
                    :paginator="true"
                    :rows="rows"
                    :first="first"
                    :totalRecords="totalRecords"
                    @page="
                        (e) => {
                            first = e.first;
                            rows = e.rows;
                        }
                    "
                    class="p-datatable-sm"
                    responsiveLayout="scroll"
                >
                    <Column
                        header="#"
                        class="w-16 text-center"
                        headerClass="justify-center"
                    >
                        <template #body="slotProps">
                            {{ first + slotProps.index + 1 }}
                        </template>
                    </Column>

                    <Column
                        header="Course ID"
                        class="text-center"
                        headerClass="justify-center"
                    >
                        <template #body="{ data, index }">
                            <InputText
                                v-model="data.course_id"
                                class="w-full"
                                :readonly="
                                    !isAddingDepartment &&
                                    editingIndex !== index &&
                                    data.id !== null
                                "
                            />
                        </template>
                    </Column>

                    <Column
                        header="Course Name"
                        class="text-center"
                        headerClass="justify-center"
                    >
                        <template #body="{ data, index }">
                            <InputText
                                v-model="data.course"
                                class="w-full"
                                :readonly="
                                    !isAddingDepartment &&
                                    editingIndex !== index &&
                                    data.id !== null
                                "
                            />
                        </template>
                    </Column>

                    <Column
                        header="Action"
                        class="w-32 text-center"
                        headerClass="justify-center"
                    >
                        <template #body="{ index }">
                            <div class="flex justify-center gap-1">
                                <PvButton
                                    v-if="!isAddingDepartment"
                                    icon="pi pi-pencil"
                                    variant="text"
                                    rounded
                                    class="!p-1 !text-emerald-500 hover:!bg-emerald-50"
                                    @click="editCourseRow(index)"
                                />
                                <PvButton
                                    v-if="
                                        isAddingDepartment ||
                                        editingIndex === index
                                    "
                                    icon="pi pi-trash"
                                    variant="text"
                                    rounded
                                    class="!p-1 !text-red-500 hover:!bg-red-50"
                                    @click="removeCourseRow(index)"
                                />
                            </div>
                        </template>
                    </Column>
                </DataTable>

                <div class="mt-4">
                    <PvButton
                        label="Add Course"
                        icon="pi pi-plus"
                        severity="secondary"
                        variant="text"
                        size="small"
                        class="!text-gray-600 hover:!bg-gray-100"
                        @click="addCourseRow"
                    />
                </div>

                <div class="mt-6 flex justify-end border-t pt-4">
                    <PvButton
                        label="Save"
                        icon="pi pi-check"
                        class="border-none bg-emerald-500 px-6 text-white"
                        @click="saveCourses"
                    />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
