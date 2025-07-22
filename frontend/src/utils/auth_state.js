import { ref } from "vue"
import message from '@/utils/message_state'
const storedUser = JSON.parse(localStorage.getItem('user') || '{}')
const user_name_auth = ref(storedUser.user_name || '')

const set_user = (name) => {
    user_name_auth.value = name
}

const check_login = () => {
    const storedUser = JSON.parse(localStorage.getItem('user') || '{}')
    return storedUser.token || null
}

const clear_user = () => {
    user_name_auth.value = ""
    localStorage.removeItem('user')
}


export { user_name_auth, set_user, check_login, clear_user }