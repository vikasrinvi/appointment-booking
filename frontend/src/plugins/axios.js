import axios from 'axios'

// Set base URL
axios.defaults.baseURL = import.meta.env.VITE_API_URL || 'http://localhost:8000'

// Get token from localStorage
const token = localStorage.getItem('token')

// If token exists, set it in axios defaults
if (token) {
  axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
}

export default axios 