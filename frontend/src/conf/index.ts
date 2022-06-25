interface ClassBadge {
    class: string;
}

export interface UserRoleClassBadge {
    ROLE_USER: ClassBadge;
    ROLE_ADMIN: ClassBadge;
}

export interface UserStatusClassBadge {
    ACTIVE: ClassBadge;
    WAIT: ClassBadge;
    BLOCKED: ClassBadge;
}

export interface Badge {
    USER_ROLE: UserRoleClassBadge;
    USER_STATUS: UserStatusClassBadge;
}

export const BADGE: Badge = {
    USER_ROLE: {
        ROLE_USER: {
            class: 'bg-info',
        },
        ROLE_ADMIN: {
            class: 'bg-danger',
        },
    },
    USER_STATUS: {
        ACTIVE: {
            class: 'bg-success'
        },
        WAIT: {
            class: 'bg-warning'
        },
        BLOCKED: {
            class: 'bg-danger'
        },
    }
}