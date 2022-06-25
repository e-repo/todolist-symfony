export interface UsersData
{
    id: string;
    name: string;
    email: string;
    role: string;
    status: string;
    date: string;
}

export interface UsersMeta
{
    currentPage: number;
    perPage: number;
    totalPage: number
}

export interface UsersState {
    usersData: UsersData[] | null;
    usersMeta: UsersMeta | null;
    userRoles: string[] | null;
    userStatuses: string[] | null;
}

export interface TableFilters {
    name?: string,
    email?: string,
    role?: string,
    status?: string,
}