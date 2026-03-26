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
        PvToast: Toast
    },

    data() {
        return {
            department: '',
            departments: [] as string[],
            isAddingDepartment: false,
            courses: [] as Course[],
            editingIndex: null as number | null,
        };
    },

    watch: {
        department() {
            if (!this.isAddingDepartment) {
                this.editingIndex = null;
                this.fetchCourses();
            }
        }
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
                return;
            }
            try {
                const res = await axios.get(`/api/course/department/${this.department}`);
                // Fix: Initialize as empty array if no data exists to prevent empty row
                this.courses = res.data.data || [];
            } catch (e) {
                console.error(e);
                this.courses = [];
            }
        },

        enableAddDepartment() {
            this.department = '';
            this.isAddingDepartment = true;
            this.courses = []; // Fix: Don't show empty row until 'Add Course' is clicked
        },

        addCourseRow() {
            this.courses.push({
                id: null,
                course_id: '',
                course: ''
            });
            this.editingIndex = this.courses.length - 1;
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
                        life: 3000
                    });
                }
                this.courses.splice(index, 1);
                // Fix: Removed logic that pushed empty row when length hit 0
            } catch {
                this.$toast.add({
                    severity: 'error',
                    summary: 'Error',
                    detail: 'Failed to delete course',
                    life: 3000
                });
            }
        },

        editCourseRow(index: number) {
            this.editingIndex = index;
        },

        async saveCourses() {
            if (!this.department) {
                this.$toast.add({ severity: 'warn', summary: 'Warning', detail: 'Department is required', life: 3000 });
                return;
            }

            for (const [i, course] of this.courses.entries()) {
                if (!course.course_id || !course.course) {
                    this.$toast.add({
                        severity: 'warn',
                        summary: 'Warning',
                        detail: `Course ID and Name are required in row ${i + 1}`,
                        life: 3000
                    });
                    return;
                }
            }

            try {
                for (const course of this.courses) {
                    if (course.id) {
                        await axios.put(`/api/course/${course.id}`, {
                            id: course.id,
                            course: course.course
                        });
                    } else {
                        await axios.post('/api/course', {
                            department: this.department,
                            courses: [course]
                        });
                    }
                }

                this.$toast.add({
                    severity: 'success',
                    summary: 'Saved',
                    detail: 'Courses saved successfully',
                    life: 3000
                });

                this.editingIndex = null;
                this.isAddingDepartment = false;
                this.fetchCourses();
                this.fetchDepartments();
            } catch {
                this.$toast.add({
                    severity: 'error',
                    summary: 'Error',
                    detail: 'Saving failed',
                    life: 3000
                });
            }
        }
    }
});
</script>

<template>
    <AppLayout>
        <PvToast position="top-right" />

        <div class="p-6">
            <h1 class="text-2xl font-bold mb-6">Course Management</h1>

            <div class="mb-8 max-w-2xl">
                <h2 class="text-lg font-semibold mb-3 text-gray-700">Department</h2>
                <div class="flex items-center gap-3">
                    <PvSelect
                        v-if="!isAddingDepartment"
                        v-model="department"
                        :options="departments"
                        placeholder="Select Department"
                        class="w-full md:w-96"
                    />

                    <InputText
                        v-if="isAddingDepartment"
                        v-model="department"
                        placeholder="Enter New Department"
                        class="w-full md:w-96"
                    />

                    <PvButton
                        label="Add"
                        icon="pi pi-plus"
                        severity="secondary"
                        variant="outlined"
                        @click="enableAddDepartment"
                    />
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-gray-700">Courses</h2>
                    <PvButton
                        label="Add Course"
                        icon="pi pi-plus"
                        severity="secondary"
                        variant="outlined"
                        size="small"
                        @click="addCourseRow"
                    />
                </div>

                <DataTable :value="courses" class="p-datatable-sm" responsiveLayout="scroll">
                    <Column header="#" class="w-16 text-center" headerClass="justify-center">
                        <template #body="slotProps">
                            {{ slotProps.index + 1 }}
                        </template>
                    </Column>

                    <Column header="Course ID" class="text-center" headerClass="justify-center">
                        <template #body="{ data, index }">
                            <InputText
                                v-model="data.course_id"
                                class="w-full"
                                :readonly="!isAddingDepartment && editingIndex !== index && data.id !== null"
                            />
                        </template>
                    </Column>

                    <Column header="Course Name" class="text-center" headerClass="justify-center">
                        <template #body="{ data, index }">
                            <InputText
                                v-model="data.course"
                                class="w-full"
                                :readonly="!isAddingDepartment && editingIndex !== index && data.id !== null"
                            />
                        </template>
                    </Column>

                    <Column header="Action" class="w-32 text-center" headerClass="justify-center">
                        <template #body="{ index }">
                            <div class="flex justify-center gap-1">
                                <PvButton
                                    v-if="!isAddingDepartment"
                                    icon="pi pi-pencil"
                                    variant="text"
                                    rounded
                                    class="!text-emerald-500 hover:!bg-emerald-50 !p-1"
                                    @click="editCourseRow(index)"
                                />
                                <PvButton
                                    v-if="isAddingDepartment || editingIndex === index"
                                    icon="pi pi-trash"
                                    variant="text"
                                    rounded
                                    class="!text-red-500 hover:!bg-red-50 !p-1"
                                    @click="removeCourseRow(index)"
                                />
                            </div>
                        </template>
                    </Column>
                </DataTable>

                <div class="flex justify-end mt-6">
                    <PvButton
                        label="Save All"
                        icon="pi pi-check"
                        class="bg-emerald-500 border-none px-6"
                        @click="saveCourses"
                    />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
