import { defineStore } from 'pinia'
import axios from '@/plugins/axios'
import { parseISO } from 'date-fns'
import { formatInTimeZone } from 'date-fns-tz'

export const useAppointmentStore = defineStore('appointment', {
  state: () => ({
    upcoming: [],
    past: [],
    cancelled: [],
    loading: false,
    error: null
  }),

  getters: {
    allAppointments: (state) => ({
      upcoming: state.upcoming,
      past: state.past,
      cancelled: state.cancelled
    })
  },

  actions: {
    async fetchAppointments() {
      this.loading = true
      this.error = null
      try {
        const response = await axios.get('/api/appointments')
        this.upcoming = response.data.upcoming
        this.past = response.data.past
        this.cancelled = response.data.cancelled
      } catch (error) {
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
        await this.fetchAppointments() // Refresh all appointments
        return response.data
      } catch (error) {
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
        await this.fetchAppointments() // Refresh all appointments
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to cancel appointment'
        throw error
      } finally {
        this.loading = false
      }
    }
  }
}) 