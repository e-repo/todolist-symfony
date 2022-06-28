export const API_V1 = {
    USER_LIST: '/api/v1/user/list' as string,
    USER_STATUS_LIST: '/api/v1/user/status/list' as string,
    USER_ROLE_LIST: '/api/v1/user/role/list' as string,
    USER_PROFILE: (id: string) => `/api/v1/user/${id}`,
};