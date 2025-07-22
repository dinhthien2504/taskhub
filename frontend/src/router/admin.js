const admin = [
    {
        path: '/admin',
        component: () => import('@/layouts/adminLayout.vue'),
        children: [
            {
                path: '',
                name: 'admin-dashboard',
                component: () => import('@/pages/admins/dashboard.vue')
            },
            {
                path: 'roles',
                name: 'roles',
                component: () => import('@/pages/admins/roles/index.vue')
            },
            {
                path: 'permissions',
                name: 'permissions',
                component: () => import('@/pages/admins/permissions/index.vue')
            },
            {
                path: 'users',
                name: 'users',
                component: () => import('@/pages/admins/users/index.vue')
            },
            {
                path: 'projects',
                name: 'admin-projects',
                component: () => import('@/pages/admins/projects/index.vue')
            },
            {
                path: 'project/:id/tasks',
                name: 'admin-project-tasks',
                component: () => import('@/pages/admins/tasks/index.vue')
            },
            {
                path: 'project/:project_id/tasks/:task_id',
                name: 'admin-project-task-detail',
                component: () => import('@/pages/admins/tasks/task_detail.vue')
            },
            {
                path: 'logs',
                name: 'admin-logs',
                component: () => import('@/pages/admins/logs/index.vue')
            },
            {
                path: 'campaigns',
                name: 'admin-campaigns',
                component: () => import('@/pages/admins/campaigns/index.vue')
            },
            {
                path: 'email-templates',
                name: 'admin-email-templates',
                component: () => import('@/pages/admins/email_templates/index.vue')
            },
            {
                path: 'check-in-logs',
                name: 'admin-check-in-logs',
                component: () => import('@/pages/admins/check_in_logs/index.vue')
            }
        ]
    }
]
export default admin