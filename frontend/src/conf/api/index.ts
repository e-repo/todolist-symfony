export const API = {
    DOMAIN: 'http://todo.localhost',
    V1: {
        USER_LIST: '/api/v1/user/list',
        USER_STATUS_LIST: '/api/v1/user/status/list',
        USER_ROLE_LIST: '/api/v1/user/role/list',
        USER_IMAGE_LIST: '/api/v1/user/image/list',
        USER_PROFILE: (id: string) => `/api/v1/user/${id}/profile`,
        PROFILE_CHANGE_EMAIL: '/api/v1/user/change-email',
        PROFILE_CHANGE_NAME: '/api/v1/user/change-name',
        PROFILE_IMAGE_UPLOAD: '/api/v1/user/image-upload',
        PROFILE_IMAGE_SET_ACTIVE: '/api/v1/user/image/set-active',

        TASK_LIST: '/api/v1/task/list',
        TASK_INFO: (id: string) => `/api/v1/task/${id}`,
        TASK_CREATE: '/api/v1/task/create',
        TASK_UPDATE: '/api/v1/task/update',
        TASK_DELETE: (id: string) => `/api/v1/task/${id}`,
        TASK_FULFILLED: (id: string) => `/api/v1/task/fulfilled/${id}`,
    },
    V2: {}
}
