export interface User
{
    id: string;
    name: string;
    email: string;
    role: string;
    status: string;
    date: string;
}

export interface UserMeta
{
    currentPage: number;
    perPage: number;
    totalPage: number
}

export interface UsersState {
    usersData: User[] | null;
    usersMeta: UserMeta | null;
    userRoles: string[] | null;
    userStatuses: string[] | null;
}

export interface TableFilters {
    name?: string,
    email?: string,
    role?: string,
    status?: string,
}
