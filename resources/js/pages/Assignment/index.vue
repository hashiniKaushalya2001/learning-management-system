<script lang="ts">
import { FilterMatchMode } from '@primevue/core/api';
import axios from 'axios';
import PvButton from 'primevue/button';
import Column from 'primevue/column';
import ConfirmDialog from 'primevue/confirmdialog';
import DataTable from 'primevue/datatable';
import DatePicker from 'primevue/datepicker';
import FileUpload from 'primevue/fileupload';
import FloatLabel from 'primevue/floatlabel';
import InputText from 'primevue/inputtext';
import PvSelect from 'primevue/select';
import PvTextarea from 'primevue/textarea';
import Toast from 'primevue/toast';
import { useConfirm } from 'primevue/useconfirm';
import { useToast } from 'primevue/usetoast';
import { defineComponent, ref, onMounted, watch } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';

export default defineComponent({
    name: 'AssignmentIndex',
    components: {
        AppLayout, InputText, FileUpload, PvButton,
        FloatLabel, PvSelect, PvTextarea, DataTable, Column,
        Toast, ConfirmDialog, DatePicker
    },
    setup() {
        const toast = useToast();
        const confirm = useConfirm();

        const departments = ref<any[]>([]);
        const courses = ref<any[]>([]);
        const filteredCourses = ref<any[]>([]);
        const assignmentsList = ref<any[]>([]);

        const selectedDepartment = ref<any>(null);
        const selectedCourse = ref<any>(null);
        const duration = ref('');
        const instruction = ref('');
        const dueDate = ref<Date | null>(null);
        const dueTime = ref<Date | null>(null);
        const instructionFile = ref<File | null>(null);

        const isEditing = ref(false);
        const editingId = ref<number | null>(null);
        const filters = ref({
            global: { value: null, matchMode: FilterMatchMode.CONTAINS },
        });

        const loadData = async () => {
            try {
                const [deptRes, courseRes, assignRes] = await Promise.all([
                    axios.get('/api/departments'),
                    axios.get('/api/course'),
                    axios.get('/api/assignment')
                ]);
                departments.value = deptRes.data.data || deptRes.data;
                courses.value = courseRes.data.data || courseRes.data;
                assignmentsList.value = assignRes.data.data || assignRes.data;
            } catch (error) {
                console.error("Fetch Error:", error);
            }
        };

        const onFileSelect = (event: any) => {
            if (event.files && event.files.length > 0) {
                instructionFile.value = event.files[0];
            }
        };

        const saveAssignment = async () => {
            if (!selectedDepartment.value || !selectedCourse.value || !duration.value || !dueDate.value || !dueTime.value) {
                toast.add({ severity: 'warn', summary: 'Validation', detail: 'Please fill all required fields', life: 3000 });
                return;
            }

            const formData = new FormData();

            formData.append('department_id', String(selectedDepartment.value.id));
            formData.append('course_id', String(selectedCourse.value.id));

            formData.append('duration', duration.value);
            formData.append('instruction', instruction.value);

            const formattedDate = dueDate.value.toISOString().split('T')[0];
            formData.append('due_date', formattedDate);

            const hours = String(dueTime.value.getHours()).padStart(2, '0');
            const minutes = String(dueTime.value.getMinutes()).padStart(2, '0');
            formData.append('due_time', `${hours}:${minutes}:00`);

            if (instructionFile.value) {
                formData.append('instruction_file', instructionFile.value);
            }

            if (isEditing.value) {
                formData.append('_method', 'PUT');
            }

            try {
                const url = isEditing.value ? `/api/assignment/${editingId.value}` : '/api/assignment';
                await axios.post(url, formData, { headers: { 'Content-Type': 'multipart/form-data' } });

                toast.add({ severity: 'success', summary: 'Success', detail: 'Assignment saved successfully', life: 3000 });
                cancelEdit();
                loadData();
            } catch (error: any) {
                console.error("Save Error:", error.response?.data);
                toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to save assignment', life: 5000 });
            }
        };

        const editAssignment = (data: any) => {
            isEditing.value = true;
            editingId.value = data.id;

            duration.value = data.duration || data.Duration;
            instruction.value = data.instruction;
            dueDate.value = new Date(data.due_date);

            const timeParts = data.due_time.split(':');
            const t = new Date();
            t.setHours(parseInt(timeParts[0]), parseInt(timeParts[1]), 0);
            dueTime.value = t;

            selectedDepartment.value = departments.value.find(d => d.id === data.department_id);

            setTimeout(() => {

                selectedCourse.value = courses.value.find(c => c.id === data.course_id);
            }, 100);
        };

        const deleteAssignment = (id: number) => {
            confirm.require({
                message: 'Are you sure you want to delete this assignment?',
                header: 'Confirmation',
                icon: 'pi pi-exclamation-triangle',
                accept: async () => {
                    try {
                        await axios.delete(`/api/assignment/${id}`);
                        toast.add({ severity: 'success', summary: 'Deleted', detail: 'Assignment removed', life: 3000 });
                        await loadData();
                    } catch {
                        toast.add({ severity: 'error', summary: 'Error', detail: 'Delete failed', life: 3000 });
                    }
                }
            });
        };

        const resetForm = () => {
            duration.value = ''; instruction.value = ''; dueDate.value = null; dueTime.value = null;
            selectedCourse.value = null; selectedDepartment.value = null;
            instructionFile.value = null;
        };

        const cancelEdit = () => {
            isEditing.value = false;
            editingId.value = null;
            resetForm();
        };

        watch(selectedDepartment, (newDept) => {
            if (newDept) {
                filteredCourses.value = courses.value.filter((c: any) =>
                    String(c.department) === String(newDept.department)
                );
            } else {
                filteredCourses.value = [];
            }
            if (!isEditing.value) {
                selectedCourse.value = null;
            }
        });

        onMounted(() => loadData());

        return {
            departments, filteredCourses, selectedDepartment, selectedCourse,
            duration, instruction, dueDate, dueTime, assignmentsList, isEditing,
            saveAssignment, onFileSelect, deleteAssignment, editAssignment,
            cancelEdit, filters
        };
    },
});
</script>

<template>
    <AppLayout>
        <Toast position="top-right" />
        <ConfirmDialog />
        <div class="p-6">
            <h1 class="text-2xl font-bold mb-6">
                {{ isEditing ? 'Edit Assignment' : 'Create Assignment' }}
            </h1>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-8 mb-6">
                <FloatLabel variant="on">
                    <PvSelect
                        id="department"
                        v-model="selectedDepartment"
                        :options="departments"
                        optionLabel="department"
                        fluid
                    />
                    <label for="department">Department</label>
                </FloatLabel>

                <FloatLabel variant="on">
                    <PvSelect
                        id="course"
                        v-model="selectedCourse"
                        :options="filteredCourses"
                        optionLabel="course"
                        fluid
                        :disabled="!selectedDepartment"
                    />
                    <label for="course">Course</label>
                </FloatLabel>

                <FloatLabel variant="on">
                    <InputText id="duration" v-model="duration" class="w-full" />
                    <label for="duration">Duration for the assignment</label>
                </FloatLabel>

                <div class="grid grid-cols-2 gap-4">
                    <FloatLabel variant="on">
                        <DatePicker
                            id="due_date"
                            v-model="dueDate"
                            dateFormat="yy-mm-dd"
                            class="w-full"
                            showIcon
                            fluid
                        />
                        <label for="due_date">Due Date</label>
                    </FloatLabel>

                    <FloatLabel variant="on">
                        <DatePicker
                            id="due_time"
                            v-model="dueTime"
                            timeOnly
                            hourFormat="12"
                            class="w-full"
                            showIcon
                            iconDisplay="input"
                            fluid
                        >
                            <template #inputicon="slotProps">
                                <i class="pi pi-clock" @click="slotProps.clickCallback" />
                            </template>
                        </DatePicker>
                        <label for="due_time">Due Time</label>
                    </FloatLabel>
                </div>

                <div class="md:col-span-2">
                    <FloatLabel variant="on">
                        <PvTextarea id="instruction" v-model="instruction" autoResize rows="3" class="w-full" />
                        <label for="instruction">Instructions</label>
                    </FloatLabel>
                </div>
            </div>

            <div class="mt-8 border-t pt-6">
                <h3 class="text-lg font-semibold mb-4 text-gray-700">Assignment Brief (PDF)</h3>
                <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg mb-6">
                    <FileUpload
                        mode="basic"
                        name="instruction_file"
                        accept=".pdf"
                        :maxFileSize="2048000"
                        @select="onFileSelect"
                    />
                    <small class="text-gray-500 block mt-2">Upload supporting documents or templates for students.</small>
                </div>

                <div class="flex gap-3">
                    <PvButton
                        :label="isEditing ? 'Update Assignment' : 'Create Assignment'"
                        icon="pi pi-check"
                        @click="saveAssignment"
                    />
                    <PvButton v-if="isEditing" label="Cancel" icon="pi pi-times" severity="secondary" @click="cancelEdit" />
                </div>
            </div>

            <hr class="my-10" />

            <div class="mb-4 flex justify-end">
                <InputText
                    v-model="filters['global'].value"
                    placeholder="Search Assignments..."
                    class="w-64"
                />
            </div>

            <DataTable
                :value="assignmentsList"
                :filters="filters"
                paginator
                :rows="10"
                tableStyle="min-width: 50rem"
                class="shadow-sm border rounded"
            >
                <Column header="Department" sortable field="department.department">
                    <template #body="slotProps">
                        {{
                            slotProps.data.department?.department ||
                            slotProps.data.course?.department?.department ||
                            'N/A'
                        }}
                    </template>
                </Column>
                <Column header="Course" sortable field="course.course">
                    <template #body="slotProps">
                        {{ slotProps.data.course?.course || 'N/A' }}
                    </template>
                </Column>
                <Column field="due_date" header="Due Date" sortable>
                    <template #body="slotProps">
                        {{ new Date(slotProps.data.due_date).toLocaleDateString([], {year: 'numeric', month: 'short', day: 'numeric'}) }}
                    </template>
                </Column>
                <Column field="duration" header="Duration" sortable>
                    <template #body="slotProps">
                        {{ slotProps.data.duration || slotProps.data.Duration }}
                    </template>
                </Column>
                <Column header="Brief">
                    <template #body="slotProps">
                        <a v-if="slotProps.data.file_path"
                           :href="`/storage/${slotProps.data.file_path}`"
                           target="_blank"
                           class="text-blue-600 hover:underline">
                            <span class="pi pi-file-pdf"></span> View
                        </a>
                        <span v-else class="text-gray-400">No file</span>
                    </template>
                </Column>
                <Column header="Actions" style="width: 10%">
                    <template #body="slotProps">
                        <div class="flex gap-2">
                            <PvButton icon="pi pi-pencil" variant="text" rounded class="!text-emerald-500" @click="editAssignment(slotProps.data)" />
                            <PvButton icon="pi pi-trash" variant="text" rounded class="!text-red-500" @click="deleteAssignment(slotProps.data.id)" />
                        </div>
                    </template>
                </Column>
            </DataTable>
        </div>
    </AppLayout>
</template>
