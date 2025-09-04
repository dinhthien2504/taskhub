import { ref } from "vue"
import message from '@/utils/message_state'
const storedUser = JSON.parse(localStorage.getItem('user') || '{}')
const user_name_auth = ref(storedUser.user_name || '')
const role = ref(storedUser.role || '')
const avatar = ref(storedUser.avatar || '')

const set_user = (name) => {
    user_name_auth.value = name
    let user = JSON.parse(localStorage.getItem('user')) || {}
    user.user_name = name
    localStorage.setItem('user', JSON.stringify(user))
}
const set_avatar = (Avatar) => {
    avatar.value = Avatar

    let user = JSON.parse(localStorage.getItem('user')) || {}
    user.avatar = Avatar
    localStorage.setItem('user', JSON.stringify(user))
}

const check_login = () => {
    const storedUser = JSON.parse(localStorage.getItem('user') || '{}')
    return storedUser.token || null
}

const clear_user = () => {
    user_name_auth.value = ""
    localStorage.removeItem('user')
}


export { user_name_auth, set_user, check_login, clear_user, role, set_avatar, avatar }