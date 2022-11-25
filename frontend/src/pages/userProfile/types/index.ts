export interface UserProfile {
    name: string;
    email: string;
    createdAt: number | null;
    role: string;
    status: string;
}

interface FormField {
    fieldValue: string,
    isValid: boolean
    errorMessage?: string,
}

export interface UserNameForm {
    first: FormField;
    last: FormField;
}

export interface ChangingEmailForm {
    email: FormField
}

export interface Image {
    filename: string;
    filepath: string;
    isActive: boolean;
    originalFilename: string;
    uuid: string
}
