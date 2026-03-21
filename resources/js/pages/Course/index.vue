<script setup lang="ts">
import axios from 'axios'
import PvButton from 'primevue/button'
import { useToast } from 'primevue/usetoast'
import { ref, onMounted, watch } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'

const toast = useToast()

interface Course {
    id: number | null
    course_id: string
    course: string
}

const department = ref('')
const departments = ref<string[]>([])
const isAddingDepartment = ref(false)

const courses = ref<Course[]>([
])

const editingIndex = ref<number | null>(null)

const fetchDepartments = async () => {
    try{
        const res = await axios.get('/api/departments')
        departments.value = res.data.data
    }catch(e){
        console.error(e)
    }
}

const fetchCourses = async () => {
    if(!department.value) return

    try{
        const res = await axios.get(`/api/course/department/${department.value}`)

        courses.value = res.data.data.length
            ? res.data.data
            : [{ id:null, course_id:'', course:'' }]
    }catch(e){
        console.error(e)
    }
}

watch(department, () => {
    if(!isAddingDepartment.value){
        editingIndex.value = null
        fetchCourses()
    }
})

onMounted(()=>{
    fetchDepartments()
})

const enableAddDepartment = () => {
    department.value=''
    isAddingDepartment.value=true
    courses.value=[{id:null,course_id:'',course:''}]
}

const addCourseRow = () => {

    courses.value.push({
        id: null,
        course_id: '',
        course: ''
    })

    editingIndex.value = courses.value.length - 1
}

const removeCourseRow = async (index:number) => {

    const course = courses.value[index]

    try{

        if(course.id){
            await axios.delete(`/api/course/${course.id}`)

            toast.add({
                severity:'success',
                summary:'Deleted',
                detail:'Course removed successfully',
                life:3000
            })
        }

        courses.value.splice(index,1)

        if(courses.value.length === 0){
            courses.value.push({id:null,course_id:'',course:''})
        }

    }catch(error){
        toast.add({
            severity:'error',
            summary:'Error',
            detail:'Failed to delete course',
            life:3000
        })
        console.error(error)
    }
}

const editCourseRow = (index:number) => {
    editingIndex.value = index
}

const saveCourses = async () => {

    if(!department.value){
        toast.add({severity:'warn',summary:'Warning',detail:'Department is required',life:3000})
        return
    }

    for(const [i, course] of courses.value.entries()){
        if(!course.course_id || !course.course){
            toast.add({
                severity:'warn',
                summary:'Warning',
                detail:`Course ID and Name are required in row ${i + 1}`,
                life:3000
            })
            return
        }
    }

    try{

        for(const course of courses.value){

            if(course.id){
                await axios.put(`/api/course/${course.id}`,{
                    id: course.id,
                    course: course.course
                })
            }

            else{
                await axios.post('/api/course',{
                    department: department.value,
                    courses:[course]
                })
            }

        }

        toast.add({
            severity:'success',
            summary:'Saved',
            detail:'Courses saved successfully',
            life:3000
        })

        editingIndex.value = null
        isAddingDepartment.value = false

        fetchCourses()
        fetchDepartments()

    }catch(error){
        toast.add({
            severity:'error',
            summary:'Error',
            detail:'Saving failed',
            life:3000
        })
        console.error(error)
    }
}
</script>

<template>
    <AppLayout>

        <div class="p-6">

            <h1 class="text-2xl font-bold mb-6">
                Course Management
            </h1>

                <h2 class="text-lg font-semibold mb-3">
                    Department
                </h2>

                <div class="flex gap-3">

                    <select
                        v-if="!isAddingDepartment"
                        v-model="department"
                        class="border p-2 rounded w-96"
                    >
                        <option value="">Select Department</option>
                        <option
                            v-for="dept in departments"
                            :key="dept"
                            :value="dept"
                        >
                            {{ dept }}
                        </option>
                    </select>

                    <input
                        v-if="isAddingDepartment"
                        v-model="department"
                        type="text"
                        placeholder="Enter New Department"
                        class="border p-2 rounded w-96"
                    />

                    <button
                        @click="enableAddDepartment"
                        class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700"
                    >
                        +
                    </button>

                </div>
            </div>

            <div class="p-5">

                <h2 class="text-lg font-semibold mb-4">
                    Courses
                </h2>

                <table class="w-full border border-gray-300">

                    <thead class="bg-gray-100">
                    <tr>
                        <th class="border p-2 w-20">#</th>
                        <th class="border p-2 w-40">Course ID</th>
                        <th class="border p-2">Course Name</th>
                        <th class="border p-2 w-40">Action</th>
                    </tr>
                    </thead>

                    <tbody>
                    <tr v-for="(course,index) in courses" :key="index">

                        <td class="border p-2 text-center">
                            {{ index + 1 }}
                        </td>

                        <td class="border p-2">
                            <input
                                v-model="course.course_id"
                                type="text"
                                placeholder="Course ID"
                                class="border p-2 rounded w-full"
                                :readonly="!isAddingDepartment && editingIndex !== index && course.id !== null"
                            />
                        </td>

                        <td class="border p-2">
                            <input
                                v-model="course.course"
                                type="text"
                                placeholder="Course Name"
                                class="border p-2 rounded w-full"
                                :readonly="!isAddingDepartment && editingIndex !== index && course.id !== null"
                            />
                        </td>

                        <td class="border p-2 text-center space-x-2">

                            <button
                                v-if="!isAddingDepartment"
                                @click="editCourseRow(index)"
                                class="bg-yellow-500 text-white px-3 py-1 rounded"
                            >
                                Edit
                            </button>

                            <button
                                v-if="isAddingDepartment || editingIndex === index"
                                @click="removeCourseRow(index)"
                                class="bg-red-600 text-white px-3 py-1 rounded"
                            >
                                Remove
                            </button>

                        </td>

                    </tr>
                    </tbody>

                </table>

                <div class="flex justify-between mt-4">

                    <button
                        @click="addCourseRow"
                        class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700"
                    >
                        +
                    </button>

                    <PvButton
                        label="Save All"
                        @click="saveCourses"
                        class="!bg-blue-600 !text-white !px-4 !py-2 !rounded !hover:bg-blue-700 "
                    />

                </div>

            </div>


    </AppLayout>
</template>
