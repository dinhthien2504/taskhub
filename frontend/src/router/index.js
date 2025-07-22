import { createRouter, createWebHistory } from 'vue-router'
import admin from './admin'

const routes = [
    {
        path: '/',
        component: () => import('@/layouts/client.vue'),
        children: [
            {
                path: '/unauthorized',
                name: 'Unauthorized',
                component: () => import('@/pages/unauthorized.vue'),
            },
            {
                path: '',
                name: 'home',
                component: () => import('@/pages/clients/home.vue'),
            },
            {
                path: 'register',
                name: 'register',
                component: () => import('@/pages/clients/auth/register.vue')
            },
            {
                path: 'login',
                name: 'login',
                component: () => import('@/pages/clients/auth/login.vue')
            },
            {
                path: 'verify-email',
                name: 'verify-email',
                component: () => import('@/pages/clients/auth/verify-email.vue')
            },
            {
                path: 'forgot-password',
                name: 'forgot-password',
                component: () => import('@/pages/clients/auth/forgot-password.vue')
            },
            {
                path: 'password-reset/:token',
                name: 'password-reset',
                component: () => import('@/pages/clients/auth/password-reset.vue')
            },
            {
                path: 'change-password',
                name: 'change-password',
                component: () => import('@/pages/clients/auth/change-password.vue')
            },
            {
                path: 'profile',
                name: 'profile',
                component: () => import('@/pages/clients/auth/profile.vue')
            },
            {
                path: 'projects',
                name: 'projects',
                component: () => import('@/pages/clients/projects/index.vue')
            },
            {
                path: 'project/:id/tasks',
                name: 'project-tasks',
                component: () => import('@/pages/clients/tasks/index.vue')
            },
            {
                path: 'project/:project_id/tasks/:task_id',
                name: 'project-task-detail',
                component: () => import('@/pages/clients/tasks/task_detail.vue')
            },
            {
                path: 'unsubscribe-birthdate',
                name: 'unsubscribe-birthdate',
                component: () => import('@/pages/clients/auth/unsubscribe_birthdate.vue')
            }
        ]
    },
    ...admin
]

const router = createRouter({
    history: createWebHistory(),
    routes
})
export default router