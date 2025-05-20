import { defineStore } from 'pinia'
import axios from '@/plugins/axios'
import { parseISO } from 'date-fns'
import { formatInTimeZone } from 'date-fns-tz'

export const useAppointmentStore = defineStore('appointment', {
  state: () => ({
    appointments: [],
    loading: false,
    error: null
  }),

  getters: {
    upcomingAppointments: (state) => {
      const now = new Date()
      return state.appointments
        .filter(appointment => {
          const appointmentDate = parseISO(appointment.start_time)
          return appointmentDate > now
        })
        .sort((a, b) => parseISO(a.start_time) - parseISO(b.start_time))
    },

    pastAppointments: (state) => {
      const now = new Date()
      return state.appointments
        .filter(appointment => {
          const appointmentDate = parseISO(appointment.start_time)
          return appointmentDate <= now
        })
        .sort((a, b) => parseISO(b.start_time) - parseISO(a.start_time))
    }
  },

  actions: {
    async fetchAppointments() {
      this.loading = true
      this.error = null
      try {
        const response = await axios.get('/api/appointments')
        // Check if response.data is an array or has an appointments property
        const appointmentsData = Array.isArray(response.data) 
          ? response.data 
          : response.data.appointments || []
        
        this.appointments = appointmentsData.map(appointment => ({
          ...appointment,
          formattedStartTime: formatInTimeZone(
            parseISO(appointment.start_time),
            'Asia/Kolkata',
            'h:mm a'
          ),
          formattedDate: formatInTimeZone(
            parseISO(appointment.start_time),
            'Asia/Kolkata',
            'MMMM d, yyyy'
          )
        }))
      } catch (error) {
        console.error('Error fetching appointments:', error)
        this.error = error.response?.data?.message || 'Failed to fetch appointments'
        throw error
      } finally {
        this.loading = false
      }
    },

    async createAppointment(appointmentData) {
      this.loading = true
      this.error = null
      try {
        const response = await axios.post('/api/appointments', appointmentData)
        // Check if response.data has an appointment property
        const newAppointment = response.data.appointment || response.data
        
        this.appointments.push({
          ...newAppointment,
          formattedStartTime: formatInTimeZone(
            parseISO(newAppointment.start_time),
            'Asia/Kolkata',
            'h:mm a'
          ),
          formattedDate: formatInTimeZone(
            parseISO(newAppointment.start_time),
            'Asia/Kolkata',
            'MMMM d, yyyy'
          )
        })
        return newAppointment
      } catch (error) {
        console.error('Error creating appointment:', error)
        this.error = error.response?.data?.message || 'Failed to create appointment'
        throw error
      } finally {
        this.loading = false
      }
    },

    async cancelAppointment(id) {
      this.loading = true
      this.error = null
      try {
        await axios.delete(`/api/appointments/${id}`)
        this.appointments = this.appointments.filter(a => a.id !== id)
      } catch (error) {
        console.error('Error canceling appointment:', error)
        this.error = error.response?.data?.message || 'Failed to cancel appointment'
        throw error
      } finally {
        this.loading = false
      }
    }
  }
}) 