import axios from 'axios'
import { clear_user } from '@/utils/auth_state'
const url = import.meta.env.VITE_API_URL
const api_url = url ? url + '/api' : 'http://localhost:8000/api'
const api = axios.create({
    baseURL: api_url
})
api.interceptors.request.use(config => {
    const storedUser = JSON.parse(localStorage.getItem('user') || '{}')
    const token = storedUser.token
    const expires_at = storedUser.expires_at
    if (token) {
        const now = Date.now();
        const expireTime = new Date(expires_at).getTime();

        if (expireTime > now) {
            config.headers.Authorization = `Bearer ${token}`;
        } else {
            clear_user()
            window.location.href = '/'
        }
    }
    return config
}, error => Promise.reject(error))

api.interceptors.response.use(
    response => response,
    error => {
        if (error.response) {
            if (error.response.status === 401) {
                clear_user()
                window.location.href = '/'
            } else if (error.response.status === 403) {
                window.location.href = '/unauthorized'
            }
        }
        return Promise.reject(error)
    }
)
export default api
