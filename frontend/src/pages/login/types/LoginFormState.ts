export interface LoginFormState {
    email: string;
    password: string;
    isValidEmail: boolean;
    isValidPassword: boolean;
    errorMessage: string | null;
}