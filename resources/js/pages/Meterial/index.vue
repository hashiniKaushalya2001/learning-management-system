<script lang="ts">
import axios from 'axios';
import PvButton from 'primevue/button';
import FileUpload from 'primevue/fileupload';
import FloatLabel from 'primevue/floatlabel'
import InputText from 'primevue/inputtext';
import PvSelect from 'primevue/select';
import PvTextarea from 'primevue/textarea';
import { useToast } from 'primevue/usetoast';
import { defineComponent, ref, onMounted, watch } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';

export default defineComponent({
    name: 'MeterialIndex',
    components: { AppLayout, InputText, FileUpload, PvButton, FloatLabel, PvSelect, PvTextarea },

    setup() {
        const toast = useToast();

        const departments = ref<any[]>([]);
        const courses = ref<any[]>([]);
        const filteredCourses = ref<any[]>([]);

        const selectedDepartment = ref<any>(null);
        const selectedCourse = ref<any>(null);
        const courseCode = ref('');

        const aim = ref('');
        const lecturer = ref('');
        const semester = ref('');
        const materialRows = ref([
            { duration: '', file: null as File | null }
        ]);

        const addRow = () => {
            materialRows.value.push({ duration: '', file: null });
        };

        const removeRow = (index: number) => {
            if (materialRows.value.length > 1) {
                materialRows.value.splice(index, 1);
            }
        };

        const loadData = async () => {
            try {
                const [deptRes, courseRes] = await Promise.all([
                    axios.get('/api/departments'),
                    axios.get('/api/course')
                ]);

                departments.value = deptRes.data.data.map((dept: any, index: number) => {

                    return typeof dept === 'string'
                        ? { id: index + 1, name: dept }
                        : dept;
                });

                courses.value = courseRes.data.data;
            } catch (error) {
                console.error("Fetch Error:", error);
            }
        };

        watch(selectedDepartment, (newDept) => {
            if (newDept) {
                filteredCourses.value = courses.value.filter((c: any) => {
                    return String(c.department) === String(newDept.name);
                });
            } else {
                filteredCourses.value = [];
            }
            selectedCourse.value = null;
            courseCode.value = '';
        });

        watch(selectedCourse, (courseObj) => {
            courseCode.value = courseObj ? courseObj.course_id : '';
        });

        const onFileSelect = (event: any, index: number) => {
            if (event.files && event.files.length > 0) {
                materialRows.value[index].file = event.files[0];
            }
        };

        const saveMeterial = async () => {
            const formData = new FormData();

            if (selectedDepartment.value) {
                formData.append('department', selectedDepartment.value.id);
            }

            if (selectedCourse.value) {
                formData.append('course_id', selectedCourse.value.id);
            }

            formData.append('aim', aim.value);
            formData.append('lecturer', lecturer.value);
            formData.append('semester', semester.value);

            materialRows.value.forEach((row, index) => {
                if (row.file) {
                    formData.append(`meterial[${index}]`, row.file);
                    formData.append(`duration[${index}]`, row.duration);
                }
            });

            try {
                await axios.post('/api/meterial', formData, {
                    headers: { 'Content-Type': 'multipart/form-data' }
                });
                toast.add({
                    severity: 'success',
                    summary: 'Success',
                    detail: 'Material saved successfully!'
                });

                materialRows.value = [{ duration: '', file: null }];
                aim.value = '';
                lecturer.value = '';
                semester.value = '';
                selectedCourse.value = null;
                selectedDepartment.value = null;
            } catch (error: any) {
                console.error("Save Error:", error.response?.data);
                toast.add

                ({
                    severity: 'error',
                    summary: 'Error',
                    detail: error.response?.data?.message || 'Validation failed.'
                });
            }
        };

        onMounted(() => {
            loadData();
        });

        return {
            departments,
            filteredCourses,
            selectedDepartment,
            selectedCourse,
            courseCode,
            aim,
            lecturer,
            semester,
            materialRows,
            addRow,
            removeRow,
            saveMeterial,
            onFileSelect
        };
    },
});
</script>

<template>
    <AppLayout>
        <div class="p-6">
            <h1 class="text-2xl font-bold mb-6">Material Management</h1>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="flex flex-col gap-2">
                    <FloatLabel variant="on">
                        <PvSelect
                            id="department"
                            v-model="selectedDepartment"
                            :options="departments"
                            optionLabel="name"
                            fluid
                        />
                        <label for="department">Department</label>
                    </FloatLabel>
                </div>

                <div class="flex flex-col gap-2">
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
                </div>
            </div>

            <div class="flex flex-col gap-6 max-w-md">
                <FloatLabel variant="on">
                    <InputText id="courseCode" v-model="courseCode" class="w-full" readonly />
                    <label for="courseCode">Course Code</label>
                </FloatLabel>

                <FloatLabel variant="on">
                    <PvTextarea id="aim" v-model="aim" autoResize rows="3" class="w-full" />
                    <label for="aim">Aim</label>
                </FloatLabel>

                <FloatLabel variant="on">
                    <InputText id="lecturer" v-model="lecturer" class="w-full" />
                    <label for="lecturer">Lecturer</label>
                </FloatLabel>

                <FloatLabel variant="on">
                    <InputText id="semester" v-model="semester" class="w-full" />
                    <label for="semester">Semester</label>
                </FloatLabel>

                <div v-for="(row, index) in materialRows" :key="index" class="flex flex-col gap-4 border-b pb-6 mb-4">
                    <div class="flex items-center gap-4">
                        <div class="w-1/3">
                            <FloatLabel variant="on">
                                <InputText v-model="row.duration" class="w-full" />
                                <label>Duration</label>
                            </FloatLabel>
                        </div>

                        <div class="flex-grow">
                            <FileUpload mode="basic"
                                        name="file"
                                        accept=".pdf" :maxFileSize="2048000"
                                        @select="onFileSelect($event, index)" class="w-full" />
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <PvButton v-if="index === 0"
                                  icon="pi pi-plus"
                                  label="Add More"
                                  severity="success"
                                  variant="text"
                                  @click="addRow"

                        />
                        <PvButton v-else
                                  icon="pi pi-trash"
                                  label="Remove"

                                  severity="danger"
                                  variant="text"
                                  @click="removeRow(index)"
                        />
                    </div>
                    <PvButton v-if="index === materialRows.length - 1"
                              label="Save All Materials"
                              icon="pi pi-check"
                              class="w-full md:w-48 mt-2"
                              @click="saveMeterial"
                    />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
