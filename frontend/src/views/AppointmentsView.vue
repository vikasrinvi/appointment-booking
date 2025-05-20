<template>
  <div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center">
        <h1 class="text-2xl font-semibold text-gray-900">Appointments</h1>
        <router-link
          to="/appointments/create"
          class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
        >
          Book New Appointment
        </router-link>
      </div>

      <div class="mt-8">
        <div v-if="loading" class="text-center py-4">
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
        </div>

        <div v-else-if="error" class="text-red-500 text-center py-4">
          {{ error }}
        </div>

        <div v-else-if="appointments.length === 0" class="text-center py-4 text-gray-500">
          No appointments found
        </div>

        <div v-else class="bg-white shadow overflow-hidden sm:rounded-md">
          <ul class="divide-y divide-gray-200">
            <li v-for="appointment in appointments" :key="appointment.id">
              <div class="px-4 py-4 sm:px-6">
                <div class="flex items-center justify-between">
                  <div class="flex-1 min-w-0">
                    <h3 class="text-lg font-medium text-gray-900 truncate">
                      {{ appointment.title }}
                    </h3>
                    <p class="mt-1 text-sm text-gray-500">
                      {{ formatDate(appointment.start_time) }} - {{ formatTime(appointment.start_time) }}
                    </p>
                    <p v-if="appointment.description" class="mt-1 text-sm text-gray-500">
                      {{ appointment.description }}
                    </p>
                  </div>
                  <div class="flex items-center space-x-4">
                    <button
                      v-if="canCancel(appointment)"
                      @click="cancelAppointment(appointment.id)"
                      class="text-red-600 hover:text-red-900 text-sm font-medium"
                    >
                      Cancel
                    </button>
                  </div>
                </div>
                <div v-if="appointment.guests?.length" class="mt-2">
                  <span class="text-sm text-gray-500">Guests: </span>
                  <span class="text-sm text-gray-900">
                    {{ appointment.guests.map(g => g.email).join(', ') }}
                  </span>
                </div>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, computed } from 'vue'
import { useAppointmentStore } from '@/stores/appointment'
import { format, isAfter, subMinutes } from 'date-fns'

const appointmentStore = useAppointmentStore()

const loading = computed(() => appointmentStore.loading)
const error = computed(() => appointmentStore.error)
const appointments = computed(() => appointmentStore.upcomingAppointments)

onMounted(async () => {
  await appointmentStore.fetchAppointments()
})

const formatDate = (date) => {
  return format(new Date(date), 'MMMM d, yyyy')
}

const formatTime = (date) => {
  return format(new Date(date), 'h:mm a')
}

const canCancel = (appointment) => {
  const appointmentTime = new Date(appointment.start_time)
  const thirtyMinutesBefore = subMinutes(appointmentTime, 30)
  return isAfter(thirtyMinutesBefore, new Date())
}

const cancelAppointment = async (id) => {
  if (confirm('Are you sure you want to cancel this appointment?')) {
    try {
      await appointmentStore.cancelAppointment(id)
    } catch (error) {
      console.error('Failed to cancel appointment:', error)
    }
  }
}
</script> 