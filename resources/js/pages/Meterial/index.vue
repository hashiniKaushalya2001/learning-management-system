<script lang="ts">
import { FilterMatchMode } from '@primevue/core/api';
import axios from 'axios';
import PvButton from 'primevue/button';
import Column from 'primevue/column';
import ConfirmDialog from 'primevue/confirmdialog';
import DataTable from 'primevue/datatable';
import FileUpload from 'primevue/fileupload';
import FloatLabel from 'primevue/floatlabel'
import InputText from 'primevue/inputtext';
import PvSelect from 'primevue/select';
import PvTextarea from 'primevue/textarea';
import Toast from 'primevue/toast';
import { useConfirm } from 'primevue/useconfirm';
import { useToast } from 'primevue/usetoast';
import { defineComponent, ref, onMounted, watch } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';

export default defineComponent({
    name: 'MeterialIndex',
    components: {
        AppLayout, InputText, FileUpload, PvButton,
        FloatLabel, PvSelect, PvTextarea, DataTable, Column,
        Toast, ConfirmDialog
    },
    setup() {
        const toast = useToast();
        const confirm = useConfirm();
        const departments = ref<any[]>([]);
        const courses = ref<any[]>([]);
        const filteredCourses = ref<any[]>([]);
        const materialsList = ref<any[]>([]);
        const selectedDepartment = ref<any>(null);
        const selectedCourse = ref<any>(null);
        const courseCode = ref('');
        const aim = ref('');
        const lecturer = ref('');
        const semester = ref('');
        const materialRows = ref([{ duration: '', file: null as File | null }]);
        const isEditing = ref(false);
        const editingId = ref<number | null>(null);

        const filters = ref({
            global: { value: null, matchMode: FilterMatchMode.CONTAINS },
        });

        const addRow = () => materialRows.value.push({ duration: '', file: null });
        const removeRow = (index: number) => {
            if (materialRows.value.length > 1) materialRows.value.splice(index, 1);
        };

        const loadData = async () => {
            try {
                const deptRes = await axios.get('/api/departments');

                departments.value = deptRes.data.data || deptRes.data;

                const [courseRes, materialRes] = await Promise.all([
                    axios.get('/api/course'),
                    axios.get('/api/meterial')
                ]);

                courses.value = courseRes.data.data || courseRes.data;
                materialsList.value = materialRes.data.data || materialRes.data;
            } catch (error) {
                console.error("Fetch Error:", error);
            }
        };

        const getDeptName = (rowData: any) => {

            if (rowData.department && typeof rowData.department === 'object') {
                return rowData.department.name || rowData.department.department;
            }

            const deptId = rowData.department_id || rowData.department;
            const found = departments.value.find(d => String(d.id) === String(deptId));

            if (found) return found.name;

            if (rowData.course && rowData.course.department) {
                return rowData.course.department.name || rowData.course.department;
            }

            return 'N/A';
        };

        const deleteMaterial = (id: number) => {
            confirm.require({
                message: 'Are you sure you want to delete this material?',
                header: 'Confirmation',
                icon: 'pi pi-exclamation-triangle',
                accept: async () => {
                    try {
                        await axios.delete(`/api/meterial/${id}`);
                        toast.add({ severity: 'success', summary: 'Deleted', detail: 'Material removed', life: 3000 });
                        await loadData();
                    } catch {
                        toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to delete material', life: 3000 });
                    }
                }
            });
        };

        const editMaterial = (data: any) => {
            isEditing.value = true;
            editingId.value = data.id;
            aim.value = data.aim || '';
            lecturer.value = data.lecturer || '';
            semester.value = data.semester || '';

            const deptId = data.department?.id || data.department;
            selectedDepartment.value = departments.value.find(d => String(d.id) === String(deptId));

            setTimeout(() => {
                selectedCourse.value = courses.value.find(c => c.id == data.course_id);
            }, 100);
            materialRows.value = [{ duration: data.duration || '', file: null }];
        };

        const cancelEdit = () => {
            isEditing.value = false;
            editingId.value = null;
            resetForm();
        };

        const resetForm = () => {
            materialRows.value = [{ duration: '', file: null }];
            aim.value = ''; lecturer.value = ''; semester.value = '';
            selectedCourse.value = null; selectedDepartment.value = null; courseCode.value = '';
        };

        watch(selectedDepartment, (newDept) => {
            if (newDept) {
                filteredCourses.value = courses.value.filter((c: any) =>
                    String(c.department_id) === String(newDept.id) || String(c.department) === String(newDept.department)
                );
            } else {
                filteredCourses.value = [];
            }
            if (!isEditing.value) { selectedCourse.value = null; courseCode.value = ''; }
        });

        watch(selectedCourse, (courseObj) => { courseCode.value = courseObj ? courseObj.course_id : ''; });

        const onFileSelect = (event: any, index: number) => {
            if (event.files && event.files.length > 0) materialRows.value[index].file = event.files[0];
        };

        const saveMeterial = async () => {
            if (!selectedDepartment.value) {
                toast.add({ severity: 'warn', summary: 'Validation', detail: 'Department is required', life: 3000 });
                return;
            }
            const formData = new FormData();
            formData.append('department', String(selectedDepartment.value.id));
            if (selectedCourse.value) formData.append('course_id', String(selectedCourse.value.id));
            formData.append('aim', aim.value);
            formData.append('lecturer', lecturer.value);
            formData.append('semester', semester.value);
            if (isEditing.value) {
                formData.append('_method', 'PUT');
                formData.append('duration', materialRows.value[0].duration);
                if (materialRows.value[0].file) formData.append('meterial', materialRows.value[0].file);
            } else {
                materialRows.value.forEach((row, index) => {
                    if (row.file) {
                        formData.append(`meterial[${index}]`, row.file);
                        formData.append(`duration[${index}]`, row.duration);
                    }
                });
            }
            try {
                const url = isEditing.value ? `/api/meterial/${editingId.value}` : '/api/meterial';
                await axios.post(url, formData, { headers: { 'Content-Type': 'multipart/form-data' } });
                toast.add({ severity: 'success', summary: 'Success', detail: 'Successfully Saved ',life: 3000 });
                cancelEdit();
                loadData();
            } catch {
                toast.add({ severity: 'error', summary: 'Error', detail: 'Check required fields',life: 5000 });
            }
        };

        onMounted(() => loadData());

        return {
            departments, filteredCourses, selectedDepartment, selectedCourse,
            courseCode, aim, lecturer, semester, materialRows, materialsList,
            isEditing, addRow, removeRow, saveMeterial, onFileSelect,
            deleteMaterial, editMaterial, cancelEdit, getDeptName, filters
        };
    },
});
</script>

<template>
    <AppLayout>
        <Toast position="top-right" />
        <ConfirmDialog />
        <div class="p-6">
            <h1 class="text-2xl font-bold mb-6">{{ isEditing ? 'Edit Material' : 'Material Management' }}</h1>

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
                        placeholder="Select a Course"
                        fluid
                        :disabled="!selectedDepartment"
                    >
                        <template #option="slotProps">
                            <div class="flex justify-between">
                                <span>{{ slotProps.option.course }}</span>
                                <span class="text-gray-400 text-sm ml-4">({{ slotProps.option.course_id }})</span>
                            </div>
                        </template>

                        <template #value="slotProps">
                            <div v-if="slotProps.value">
                                {{ slotProps.value.course }} ({{ slotProps.value.course_id }})
                            </div>
                            <span v-else>{{ slotProps.placeholder }}</span>
                        </template>
                    </PvSelect>
                    <label for="course"></label>
                </FloatLabel>
                <FloatLabel variant="on">
                    <InputText id="lecturer" v-model="lecturer" class="w-full" />
                    <label for="lecturer">Lecturer</label>
                </FloatLabel>
                <FloatLabel variant="on">
                    <InputText id="semester" v-model="semester" class="w-full" />
                    <label for="semester">Semester</label>
                </FloatLabel>

                    <FloatLabel variant="on">
                        <PvTextarea id="aim" v-model="aim" autoResize rows="3" class="w-full" />
                        <label for="aim">Aim </label>
                    </FloatLabel>

            </div>

            <div class="mt-8 border-t pt-6">
                <h3 class="text-lg font-semibold mb-4 text-gray-700">File Attachments</h3>
                <div v-for="(row, index) in materialRows" :key="index" class="flex flex-col gap-4 mb-6 p-4 bg-gray-50 border border-gray-200 rounded-lg">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-center">
                        <FloatLabel variant="on">
                            <InputText v-model="row.duration" class="w-full" />
                            <label>Week Number</label>
                        </FloatLabel>
                        <div class="md:col-span-2">
                            <FileUpload mode="basic" name="file" accept=".pdf" :maxFileSize="2048000" @select="onFileSelect($event, index)" />
                        </div>
                    </div>
                    <div class="flex justify-start gap-2" v-if="!isEditing">
                        <PvButton v-if="index === 0" icon="pi pi-plus" label="Add More" severity="success" variant="text" @click="addRow" />
                        <PvButton v-else icon="pi pi-trash" label="Remove" severity="danger" variant="text" @click="removeRow(index)" />
                    </div>
                </div>
                <div class="flex gap-3">
                    <PvButton :label="isEditing ? 'Update Material' : 'Save '" icon="pi pi-check" @click="saveMeterial" />
                    <PvButton v-if="isEditing" label="Cancel" icon="pi pi-times" severity="secondary" @click="cancelEdit" />
                </div>
            </div>

            <hr class="my-10" />

            <div class="mb-4 flex justify-end">
                <span class="relative">
                    <InputText
                        v-model="filters['global'].value"
                        placeholder="Search Course Name..."
                        class="w-64 pl-10"
                    />
                </span>
            </div>

            <DataTable
                :value="materialsList"
                :filters="filters"
                paginator
                :rows="10"
                tableStyle="min-width: 50rem"
                class="shadow-sm border rounded"
            >
                <Column field="id" header="ID" class="w-16" />
                <Column header="Department">
                    <template #body="slotProps">
                        {{ getDeptName(slotProps.data) }} </template>
                </Column>
                <Column field="course.course" header="Course Name" filterField="course.course" />
                <Column field="duration" header="Duration" />
                <Column field="meterial" header="Uploaded File">
                    <template #body="slotProps">
                        <a :href="`/download-pdf/${slotProps.data.meterial}`" target="_blank" class="text-blue-600 hover:underline flex items-center gap-2">
                            <span class="pi pi-file-pdf"></span>View PDF
                        </a>
                    </template>
                </Column>
                <Column header="Actions" style="width: 10%">
                    <template #body="slotProps">
                        <div class="flex gap-2">
                            <PvButton icon="pi pi-pencil" variant="text" rounded class="!text-emerald-500" @click="editMaterial(slotProps.data)" />
                            <PvButton icon="pi pi-trash" variant="text" rounded class="!text-red-500" @click="deleteMaterial(slotProps.data.id)" />
                        </div>
                    </template>
                </Column>
            </DataTable>
        </div>
    </AppLayout>
</template>
