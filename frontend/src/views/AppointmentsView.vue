<template>
  <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Appointments</h1>
        <router-link
          to="/appointments/create"
          class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700"
        >
          Create Appointment
        </router-link>
      </div>

      <!-- Search and Filter Section -->
      <div class="mb-6 flex flex-col sm:flex-row gap-4">
        <div class="flex-1">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search appointments..."
            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
          />
        </div>
        <div class="flex gap-4">
          <select
            v-model="dateFilter"
            class="px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
          >
            <option value="all">All Dates</option>
            <option value="today">Today</option>
            <option value="week">This Week</option>
            <option value="month">This Month</option>
          </select>
          <select
            v-model="statusFilter"
            class="px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
          >
            <option value="all">All Status</option>
            <option value="upcoming">Upcoming</option>
            <option value="past">Past</option>
            <option value="cancelled">Cancelled</option>
          </select>
        </div>
      </div>

      <div v-if="loading" class="text-center py-4">
        Loading appointments...
      </div>

      <div v-else-if="error" class="text-red-500 text-center py-4">
        {{ error }}
      </div>

      <div v-else>
        <!-- Upcoming Appointments -->
        <div v-if="showSection('upcoming')" class="mb-8">
          <h2 class="text-lg font-medium text-gray-900 mb-4">Upcoming Appointments</h2>
          <div v-if="!filteredUpcoming.length" class="text-gray-500 text-center py-4">
            No upcoming appointments
          </div>
          <div v-else class="bg-white shadow overflow-hidden sm:rounded-md">
            <ul class="divide-y divide-gray-200">
              <li v-for="appointment in filteredUpcoming" :key="appointment.id">
                <div class="px-4 py-4 sm:px-6">
                  <div class="flex items-center justify-between">
                    <div class="flex-1 min-w-0">
                      <h3 class="text-lg font-medium text-gray-900 truncate">
                        {{ appointment.title }}
                      </h3>
                      <p class="mt-1 text-sm text-gray-500">
                        {{ formatDate(appointment.start_time) }} - {{ formatTime(appointment.start_time) }}
                        ({{ appointment.timezone }})
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
                      {{ appointment.guests.map(g => g.name || g.email).join(', ') }}
                    </span>
                  </div>
                </div>
              </li>
            </ul>
          </div>
        </div>

        <!-- Past Appointments -->
        <div v-if="showSection('past')" class="mb-8">
          <h2 class="text-lg font-medium text-gray-900 mb-4">Past Appointments</h2>
          <div v-if="!filteredPast.length" class="text-gray-500 text-center py-4">
            No past appointments
          </div>
          <div v-else class="bg-white shadow overflow-hidden sm:rounded-md">
            <ul class="divide-y divide-gray-200">
              <li v-for="appointment in filteredPast" :key="appointment.id">
                <div class="px-4 py-4 sm:px-6">
                  <div class="flex-1 min-w-0">
                    <h3 class="text-lg font-medium text-gray-900 truncate">
                      {{ appointment.title }}
                    </h3>
                    <p class="mt-1 text-sm text-gray-500">
                      {{ formatDate(appointment.start_time) }} - {{ formatTime(appointment.start_time) }}
                      ({{ appointment.timezone }})
                    </p>
                    <p v-if="appointment.description" class="mt-1 text-sm text-gray-500">
                      {{ appointment.description }}
                    </p>
                  </div>
                  <div v-if="appointment.guests?.length" class="mt-2">
                    <span class="text-sm text-gray-500">Guests: </span>
                    <span class="text-sm text-gray-900">
                      {{ appointment.guests.map(g => g.name || g.email).join(', ') }}
                    </span>
                  </div>
                </div>
              </li>
            </ul>
          </div>
        </div>

        <!-- Cancelled Appointments -->
        <div v-if="showSection('cancelled')">
          <h2 class="text-lg font-medium text-gray-900 mb-4">Cancelled Appointments</h2>
          <div v-if="!filteredCancelled.length" class="text-gray-500 text-center py-4">
            No cancelled appointments
          </div>
          <div v-else class="bg-white shadow overflow-hidden sm:rounded-md">
            <ul class="divide-y divide-gray-200">
              <li v-for="appointment in filteredCancelled" :key="appointment.id">
                <div class="px-4 py-4 sm:px-6">
                  <div class="flex-1 min-w-0">
                    <h3 class="text-lg font-medium text-gray-900 truncate">
                      {{ appointment.title }}
                    </h3>
                    <p class="mt-1 text-sm text-gray-500">
                      {{ formatDate(appointment.start_time) }} - {{ formatTime(appointment.start_time) }}
                      ({{ appointment.timezone }})
                    </p>
                    <p v-if="appointment.description" class="mt-1 text-sm text-gray-500">
                      {{ appointment.description }}
                    </p>
                  </div>
                  <div v-if="appointment.guests?.length" class="mt-2">
                    <span class="text-sm text-gray-500">Guests: </span>
                    <span class="text-sm text-gray-900">
                      {{ appointment.guests.map(g => g.name || g.email).join(', ') }}
                    </span>
                  </div>
                </div>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, computed, ref } from 'vue'
import { useAppointmentStore } from '@/stores/appointment'
import { format, isAfter, subMinutes, isToday, isThisWeek, isThisMonth } from 'date-fns'

const appointmentStore = useAppointmentStore()

const loading = computed(() => appointmentStore.loading)
const error = computed(() => appointmentStore.error)
const upcoming = computed(() => appointmentStore.upcoming)
const past = computed(() => appointmentStore.past)
const cancelled = computed(() => appointmentStore.cancelled)

// Search and filter state
const searchQuery = ref('')
const dateFilter = ref('all')
const statusFilter = ref('all')

// Filtered appointments
const filteredUpcoming = computed(() => {
  return filterAppointments(upcoming.value)
})

const filteredPast = computed(() => {
  return filterAppointments(past.value)
})

const filteredCancelled = computed(() => {
  return filterAppointments(cancelled.value)
})

// Helper functions
const formatDate = (date) => {
  return format(new Date(date), 'MMMM d, yyyy')
}

const formatTime = (date) => {
  return format(new Date(date), 'h:mm a')
}

const canCancel = (appointment) => {
  const appointmentTime = new Date(appointment.start_time)
  const thirtyMinutesBefore = subMinutes(appointmentTime, 30)
  return isAfter(appointmentTime, new Date()) && isAfter(thirtyMinutesBefore, new Date())
}

const filterAppointments = (appointments) => {
  return appointments.filter(appointment => {
    const matchesSearch = searchQuery.value === '' || 
      appointment.title.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      appointment.description?.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      appointment.guests?.some(g => 
        g.name?.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
        g.email.toLowerCase().includes(searchQuery.value.toLowerCase())
      )

    const matchesDate = dateFilter.value === 'all' || 
      (dateFilter.value === 'today' && isToday(new Date(appointment.start_time))) ||
      (dateFilter.value === 'week' && isThisWeek(new Date(appointment.start_time))) ||
      (dateFilter.value === 'month' && isThisMonth(new Date(appointment.start_time)))

    return matchesSearch && matchesDate
  })
}

const showSection = (section) => {
  if (statusFilter.value === 'all') return true
  return statusFilter.value === section
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

onMounted(() => {
  appointmentStore.fetchAppointments()
})
</script> 