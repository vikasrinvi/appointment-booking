<template>
  <div class="py-6">
    <Alert
      :show="!!alertMessage"
      :type="alertType"
      :message="alertMessage"
      @close="clearAlert"
    />

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Calendar</h1>
        <div class="flex space-x-4">
          <button
            @click="previousMonth"
            class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
          >
            Previous
          </button>
          <button
            @click="nextMonth"
            class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
          >
            Next
          </button>
        </div>
      </div>

      <div class="bg-white shadow rounded-lg">
        <!-- Calendar Header -->
        <div class="grid grid-cols-7 gap-px bg-gray-200">
          <div
            v-for="day in weekDays"
            :key="day"
            class="bg-gray-50 py-2 text-center text-sm font-semibold text-gray-700"
          >
            {{ day }}
          </div>
        </div>

        <!-- Calendar Grid -->
        <div class="grid grid-cols-7 gap-px bg-gray-200">
          <div
            v-for="(day, index) in calendarDays"
            :key="index"
            class="bg-white min-h-[120px] p-2"
            :class="{
              'bg-gray-50': !day.isCurrentMonth,
              'cursor-pointer hover:bg-blue-50': day.isCurrentMonth && !day.isPast
            }"
            @click="day.isCurrentMonth && !day.isPast ? openBookingModal(day.date) : null"
          >
            <div class="flex justify-between">
              <span
                class="text-sm"
                :class="{
                  'text-gray-400': !day.isCurrentMonth,
                  'text-gray-900': day.isCurrentMonth,
                  'font-semibold': day.isToday
                }"
              >
                {{ day.dayNumber }}
              </span>
              <span
                v-if="day.isToday"
                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800"
              >
                Today
              </span>
            </div>

            <!-- Appointment Indicators -->
            <div class="mt-2 space-y-1">
              <div
                v-for="appointment in getAppointmentsForDay(day.date)"
                :key="appointment.id"
                class="text-xs p-1 rounded truncate"
                :class="getAppointmentClass(appointment)"
                @click.stop="openAppointmentDetails(appointment)"
              >
                {{ formatAppointmentTime(appointment) }} - {{ appointment.title }}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Booking Modal -->
    <div
      v-if="showBookingModal"
      class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center p-4"
    >
      <div class="bg-white rounded-lg max-w-lg w-full p-6">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-medium text-gray-900">
            Book Appointment for {{ formatSelectedDate }}
          </h3>
          <button
            @click="closeBookingModal"
            class="text-gray-400 hover:text-gray-500"
          >
            <span class="sr-only">Close</span>
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <form @submit.prevent="handleBookingSubmit">
          <div class="space-y-4">
            <div>
              <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
              <input
                type="text"
                id="title"
                v-model="bookingForm.title"
                required
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
              />
            </div>

            <div>
              <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
              <textarea
                id="description"
                v-model="bookingForm.description"
                rows="3"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
              ></textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div>
                <label for="start_time" class="block text-sm font-medium text-gray-700">Start Time</label>
                <input
                  type="time"
                  id="start_time"
                  v-model="bookingForm.start_time"
                  required
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                />
              </div>

              <div>
                <label for="end_time" class="block text-sm font-medium text-gray-700">End Time</label>
                <input
                  type="time"
                  id="end_time"
                  v-model="bookingForm.end_time"
                  required
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                />
              </div>
            </div>

            <div>
              <label for="timezone" class="block text-sm font-medium text-gray-700">Timezone</label>
              <select
                id="timezone"
                v-model="bookingForm.timezone"
                required
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
              >
                <option v-for="tz in timezones" :key="tz" :value="tz">{{ tz }}</option>
              </select>
            </div>

            <div>
              <label for="reminder_minutes" class="block text-sm font-medium text-gray-700">Reminder</label>
              <select
                id="reminder_minutes"
                v-model="bookingForm.reminder_minutes"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
              >
                <option value="15">15 minutes before</option>
                <option value="30">30 minutes before</option>
                <option value="60">1 hour before</option>
                <option value="120">2 hours before</option>
              </select>
            </div>
          </div>

          <div class="mt-6 flex justify-end space-x-3">
            <button
              type="button"
              @click="closeBookingModal"
              class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
              Cancel
            </button>
            <button
              type="submit"
              :disabled="loading"
              class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
              <span v-if="loading">Booking...</span>
              <span v-else>Book Appointment</span>
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Appointment Details Modal -->
    <div
      v-if="selectedAppointment"
      class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center p-4"
    >
      <div class="bg-white rounded-lg max-w-lg w-full p-6">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-medium text-gray-900">
            {{ selectedAppointment.title }}
          </h3>
          <button
            @click="closeAppointmentDetails"
            class="text-gray-400 hover:text-gray-500"
          >
            <span class="sr-only">Close</span>
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <div class="space-y-4">
          <div>
            <h4 class="text-sm font-medium text-gray-500">Time</h4>
            <p class="mt-1 text-sm text-gray-900">
              {{ formatAppointmentDateTime(selectedAppointment) }}
            </p>
          </div>

          <div v-if="selectedAppointment.description">
            <h4 class="text-sm font-medium text-gray-500">Description</h4>
            <p class="mt-1 text-sm text-gray-900">{{ selectedAppointment.description }}</p>
          </div>

          <div v-if="selectedAppointment.guests?.length">
            <h4 class="text-sm font-medium text-gray-500">Guests</h4>
            <ul class="mt-1 text-sm text-gray-900">
              <li v-for="guest in selectedAppointment.guests" :key="guest.email">
                {{ guest.email }}
              </li>
            </ul>
          </div>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
          <button
            v-if="canCancel(selectedAppointment)"
            @click="cancelAppointment(selectedAppointment.id)"
            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
          >
            Cancel Appointment
          </button>
          <button
            @click="closeAppointmentDetails"
            class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
          >
            Close
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useAppointmentStore } from '@/stores/appointment'
import { useAuthStore } from '@/stores/auth'
import { useRouter } from 'vue-router'
import {
  startOfMonth,
  endOfMonth,
  eachDayOfInterval,
  format,
  addMonths,
  subMonths,
  isSameMonth,
  isToday,
  isPast,
  parseISO,
  setHours,
  setMinutes
} from 'date-fns'
import { formatInTimeZone, toZonedTime } from 'date-fns-tz'
import Alert from '@/components/Alert.vue'

const router = useRouter()
const appointmentStore = useAppointmentStore()
const authStore = useAuthStore()

const alertMessage = ref('')
const alertType = ref('success')

const showAlert = (message, type = 'success') => {
  alertMessage.value = message
  alertType.value = type
  setTimeout(() => {
    clearAlert()
  }, 5000)
}

const clearAlert = () => {
  alertMessage.value = ''
}

const timezones = Intl.supportedValuesOf('timeZone').map(tz => {
  return tz === 'Asia/Calcutta' ? 'Asia/Kolkata' : tz;
});


const currentDate = ref(new Date())
const showBookingModal = ref(false)
const selectedDate = ref(null)
const selectedAppointment = ref(null)

const weekDays = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']

const calendarDays = computed(() => {
  const monthStart = startOfMonth(currentDate.value)
  const monthEnd = endOfMonth(currentDate.value)
  const startDate = new Date(monthStart)
  startDate.setDate(startDate.getDate() - startDate.getDay())
  
  const endDate = new Date(monthEnd)
  endDate.setDate(endDate.getDate() + (6 - endDate.getDay()))

  return eachDayOfInterval({ start: startDate, end: endDate }).map(date => ({
    date,
    dayNumber: date.getDate(),
    isCurrentMonth: isSameMonth(date, currentDate.value),
    isToday: isToday(date),
    isPast: isPast(date)
  }))
})

const formatSelectedDate = computed(() => {
  return selectedDate.value ? format(selectedDate.value, 'MMMM d, yyyy') : ''
})

const bookingForm = ref({
  title: '',
  description: '',
  start_time: '',
  end_time: '',
  timezone: Intl.DateTimeFormat().resolvedOptions().timeZone,
  reminder_minutes: 30
})

const loading = computed(() => appointmentStore.loading)

onMounted(async () => {
  if (!authStore.isAuthenticated) {
    showAlert('Please login to access this feature', 'error')
    router.push('/login')
    return
  }
  await appointmentStore.fetchAppointments()
})

const previousMonth = () => {
  currentDate.value = subMonths(currentDate.value, 1)
}

const nextMonth = () => {
  currentDate.value = addMonths(currentDate.value, 1)
}

const openBookingModal = (date) => {
  selectedDate.value = date
  showBookingModal.value = true
}

const closeBookingModal = () => {
  showBookingModal.value = false
  selectedDate.value = null
  bookingForm.value = {
    title: '',
    description: '',
    start_time: '',
    end_time: '',
    timezone: Intl.DateTimeFormat().resolvedOptions().timeZone,
    reminder_minutes: 30
  }
}

const handleBookingSubmit = async () => {
  try {
    const [startHours, startMinutes] = bookingForm.value.start_time.split(':').map(Number)
    const [endHours, endMinutes] = bookingForm.value.end_time.split(':').map(Number)
    
    // Create dates in local timezone
    const startDateTime = setMinutes(setHours(selectedDate.value, startHours), startMinutes)
    const endDateTime = setMinutes(setHours(selectedDate.value, endHours), endMinutes)
    
    // Convert to UTC for storage
    const startUTC = startDateTime.toISOString()
    const endUTC = endDateTime.toISOString()
    
    const appointmentData = {
      title: bookingForm.value.title,
      description: bookingForm.value.description,
      start_time: startUTC,
      end_time: endUTC,
      timezone: 'Asia/Kolkata',
      reminder_minutes: bookingForm.value.reminder_minutes
    }

    await appointmentStore.createAppointment(appointmentData)
    showAlert('Appointment booked successfully!')
    closeBookingModal()
    await appointmentStore.fetchAppointments()
  } catch (error) {
    console.error('Failed to create appointment:', error)
    showAlert(error.response?.data?.message || 'Failed to create appointment', 'error')
  }
}

const getAppointmentsForDay = (date) => {
  return appointmentStore.appointments.filter(appointment => {
    const appointmentDate = toZonedTime(parseISO(appointment.start_time), 'Asia/Kolkata')
    return (
      appointmentDate.getDate() === date.getDate() &&
      appointmentDate.getMonth() === date.getMonth() &&
      appointmentDate.getFullYear() === date.getFullYear()
    )
  })
}

const formatAppointmentTime = (appointment) => {
  const date = parseISO(appointment.start_time)
  return formatInTimeZone(date, 'Asia/Kolkata', 'h:mm a')
}

const formatAppointmentDateTime = (appointment) => {
  const date = parseISO(appointment.start_time)
  return formatInTimeZone(date, 'Asia/Kolkata', 'MMMM d, yyyy h:mm a')
}

const getAppointmentClass = (appointment) => {
  const now = new Date()
  const appointmentTime = new Date(appointment.start_time)
  
  if (appointmentTime < now) {
    return 'bg-gray-100 text-gray-600'
  }
  
  return 'bg-blue-100 text-blue-800'
}

const openAppointmentDetails = (appointment) => {
  selectedAppointment.value = appointment
}

const closeAppointmentDetails = () => {
  selectedAppointment.value = null
}

const canCancel = (appointment) => {
  const appointmentTime = toZonedTime(parseISO(appointment.start_time), 'Asia/Kolkata')
  const thirtyMinutesBefore = new Date(appointmentTime.getTime() - 30 * 60000)
  return thirtyMinutesBefore > new Date()
}

const cancelAppointment = async (id) => {
  if (confirm('Are you sure you want to cancel this appointment?')) {
    try {
      await appointmentStore.cancelAppointment(id)
      showAlert('Appointment cancelled successfully', 'success')
      closeAppointmentDetails()
      await appointmentStore.fetchAppointments()
    } catch (error) {
      console.error('Failed to cancel appointment:', error)
      showAlert(error.response?.data?.message || 'Failed to cancel appointment', 'error')
    }
  }
}
</script> 