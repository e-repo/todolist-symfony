export interface UserProfile {
    name: string;
    email: string;
    createdAt: number | null;
    role: string;
    status: string;
}

export interface UserName {
    first: string;
    last: string;
}

export interface ChangingEmailForm {
    email: string
}