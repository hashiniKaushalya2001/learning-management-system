<script lang="ts">
import axios from 'axios';
import PvButton from 'primevue/button';
import DatePicker from 'primevue/datepicker';
import FloatLabel from 'primevue/floatlabel';
import InputText from 'primevue/inputtext';
import PvSelect from 'primevue/select';
import { useToast } from 'primevue/usetoast';
import { defineComponent } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';

export default defineComponent({
    name: 'StudentIndex',
    components: { AppLayout, InputText, PvButton, FloatLabel, PvSelect, DatePicker },

    setup() {
        const toast = useToast();
        return { toast };
    },

    data() {
        return {
            departments: [] as any[],

            selectedDepartment: null as any,
            name: '',
            email: '',
            nic: '',
            phone_number: '',
            birthday: null as Date | null,

            loading: false
        };
    },

    mounted() {
        this.loadInitialData();
    },

    methods: {
        async loadInitialData() {
            try {
                const response = await axios.get('/api/departments');
                this.departments = response.data.data;
            } catch (error) {
                console.error("Fetch Error:", error);
            }
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
                department_id: this.selectedDepartment?.id,
                name: this.name,
                email: this.email,
                nic: this.nic,
                phone_number: this.phone_number,
                birthday: formattedDate
            };

            try {
                await axios.post('/api/students', payload);

                this.toast.add({
                    severity: 'success',
                    summary: 'Success',
                    detail: 'Student record saved successfully!'
                });

                this.resetForm();
            } catch (error: any) {
                this.toast.add({
                    severity: 'error',
                    summary: 'Error',
                    detail: error.response?.data?.message || 'Save failed.'
                });
            } finally {
                this.loading = false;
            }
        },

        resetForm() {
            this.selectedDepartment = null;
            this.name = '';
            this.email = '';
            this.nic = '';
            this.phone_number = '';
            this.birthday = null;
        }
    }
});
</script>

<template>
    <AppLayout>
        <div class="p-6">
            <h1 class="text-2xl font-bold mb-6">Student Registration</h1>

            <div class="max-w-4xl">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">

                    <div class="flex flex-col gap-2">
                        <FloatLabel variant="on">
                            <PvSelect
                                id="department"
                                v-model="selectedDepartment"
                                :options="departments"
                                optionLabel="name"
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
                            <DatePicker
                                v-model="birthday"
                                showIcon
                                dateFormat="yy-mm-dd"
                                class="w-full"
                                @keydown.enter="focusNext"
                            />
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

                <div class="flex justify-start">
                    <PvButton
                        label="Save Student Record"
                        icon="pi pi-user-plus"
                        class="w-full md:w-64"
                        :loading="loading"
                        @click="saveStudent"
                    />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
