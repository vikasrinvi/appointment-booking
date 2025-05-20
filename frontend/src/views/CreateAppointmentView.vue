<template>
  <div class="py-6">
    <!-- Alert Component -->
    <div v-if="alert.show" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-4">
      <div
        :class="{
          'bg-red-50 border-red-400 text-red-700': alert.type === 'error',
          'bg-green-50 border-green-400 text-green-700': alert.type === 'success'
        }"
        class="border rounded-md p-4"
      >
        <div class="flex">
          <div class="flex-shrink-0">
            <svg
              v-if="alert.type === 'error'"
              class="h-5 w-5 text-red-400"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                clip-rule="evenodd"
              />
            </svg>
            <svg
              v-else
              class="h-5 w-5 text-green-400"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                clip-rule="evenodd"
              />
            </svg>
          </div>
          <div class="ml-3">
            <p class="text-sm font-medium">{{ alert.message }}</p>
          </div>
          <div class="ml-auto pl-3">
            <div class="-mx-1.5 -my-1.5">
              <button
                @click="alert.show = false"
                class="inline-flex rounded-md p-1.5 focus:outline-none focus:ring-2 focus:ring-offset-2"
                :class="{
                  'bg-red-50 text-red-500 hover:bg-red-100 focus:ring-red-600': alert.type === 'error',
                  'bg-green-50 text-green-500 hover:bg-green-100 focus:ring-green-600': alert.type === 'success'
                }"
              >
                <span class="sr-only">Dismiss</span>
                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                  <path
                    fill-rule="evenodd"
                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                    clip-rule="evenodd"
                  />
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="md:grid md:grid-cols-3 md:gap-6">
        <div class="md:col-span-1">
          <div class="px-4 sm:px-0">
            <h3 class="text-lg font-medium leading-6 text-gray-900">Book New Appointment</h3>
            <p class="mt-1 text-sm text-gray-600">
              Fill in the details below to schedule a new appointment.
            </p>
          </div>
        </div>

        <div class="mt-5 md:mt-0 md:col-span-2">
          <form @submit.prevent="handleSubmit">
            <div class="shadow sm:rounded-md sm:overflow-hidden">
              <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
                <div>
                  <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                  <input
                    type="text"
                    id="title"
                    v-model="form.title"
                    required
                    class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                  />
                </div>

                <div>
                  <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                  <textarea
                    id="description"
                    v-model="form.description"
                    rows="3"
                    class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                  ></textarea>
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                  <div>
                    <label for="start_time" class="block text-sm font-medium text-gray-700">Start Time</label>
                    <input
                      type="datetime-local"
                      id="start_time"
                      v-model="form.start_time"
                      required
                      class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                    />
                  </div>

                  <div>
                    <label for="end_time" class="block text-sm font-medium text-gray-700">End Time</label>
                    <input
                      type="datetime-local"
                      id="end_time"
                      v-model="form.end_time"
                      required
                      class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                    />
                  </div>
                </div>

                <div>
                  <label for="timezone" class="block text-sm font-medium text-gray-700">Timezone</label>
                  <select
                    id="timezone"
                    v-model="form.timezone"
                    required
                    class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                  >
                    <option v-for="tz in timezones" :key="tz" :value="tz">{{ tz }}</option>
                  </select>
                </div>

                <div>
                  <label for="reminder_minutes" class="block text-sm font-medium text-gray-700">Reminder Time</label>
                  <select
                    id="reminder_minutes"
                    v-model="form.reminder_minutes"
                    required
                    class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                  >
                    <option value="15">15 minutes before</option>
                    <option value="30">30 minutes before</option>
                    <option value="60">1 hour before</option>
                    <option value="120">2 hours before</option>
                  </select>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700">Guests</label>
                  <div class="mt-2 space-y-2">
                    <div v-for="(guest, index) in form.guests" :key="index" class="flex space-x-2">
                      <input
                        type="email"
                        v-model="guest.email"
                        placeholder="Guest email"
                        required
                        class="flex-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                      />
                      <button
                        type="button"
                        @click="removeGuest(index)"
                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                      >
                        Remove
                      </button>
                    </div>
                    <button
                      type="button"
                      @click="addGuest"
                      class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                    >
                      Add Guest
                    </button>
                  </div>
                </div>
              </div>

              <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                <button
                  type="submit"
                  :disabled="loading"
                  class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                >
                  <span v-if="loading">Creating...</span>
                  <span v-else>Create Appointment</span>
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAppointmentStore } from '@/stores/appointment'
import { useAuthStore } from '@/stores/auth'
import { formatInTimeZone } from 'date-fns-tz'
import { setMinutes, setHours, parseISO } from 'date-fns'

const router = useRouter()
const appointmentStore = useAppointmentStore()
const authStore = useAuthStore()

// Add alert state
const alert = ref({
  show: false,
  type: 'success',
  message: ''
})

const showAlert = (message, type = 'success') => {
  alert.value = {
    show: true,
    type,
    message
  }
  // Auto-hide alert after 5 seconds
  setTimeout(() => {
    alert.value.show = false
  }, 5000)
}

const form = ref({
  title: '',
  description: '',
  start_time: '',
  end_time: '',
  timezone: Intl.DateTimeFormat().resolvedOptions().timeZone,
  reminder_minutes: 30,
  guests: []
})

const loading = computed(() => appointmentStore.loading)
const error = computed(() => appointmentStore.error)

// Get list of timezones

const timezones = Intl.supportedValuesOf('timeZone').map(tz => {
  return tz === 'Asia/Calcutta' ? 'Asia/Kolkata' : tz;
});

// Check authentication on mount
onMounted(() => {
  if (!authStore.isAuthenticated) {
    showAlert('Please login to access this feature', 'error')
    router.push('/login')
  }
})

const addGuest = () => {
  form.value.guests.push({ email: '' })
}

const removeGuest = (index) => {
  form.value.guests.splice(index, 1)
}

const handleSubmit = async () => {
  try {
    if (!authStore.isAuthenticated) {
      showAlert('Please login to book appointments', 'error')
      router.push('/login')
      return
    }

    // Parse the datetime-local input value
    const startDate = new Date(form.value.start_time)
    const endDate = new Date(form.value.end_time)

    // Validate dates
    if (isNaN(startDate.getTime()) || isNaN(endDate.getTime())) {
      showAlert('Please enter valid start and end times', 'error')
      return
    }

    if (endDate <= startDate) {
      showAlert('End time must be after start time', 'error')
      return
    }

    await appointmentStore.createAppointment({
      ...form.value,
      start_time: startDate.toISOString(),
      end_time: endDate.toISOString()
    })

    showAlert('Appointment booked successfully!', 'success')
    router.push('/appointments')
  } catch (error) {
    console.error('Failed to create appointment:', error)
    showAlert(error.response?.data?.message || 'Failed to create appointment', 'error')
  }
}
</script> 