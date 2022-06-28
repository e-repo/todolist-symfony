import axios, { AxiosRequestConfig } from "axios"
import { Router } from "vue-router";

interface GetResourceConfig extends AxiosRequestConfig {
    router: Router
    refreshTokenAction: (error: object) => void
}

export function useCreateAuthHeader(jwt: string): string {
    return `Bearer ${jwt}`
}

export function useGetResource(
    url: string,
    config: GetResourceConfig,
): Promise<any>
{
    return axios
        .get(url, config)
        .then(response => response.data)
        .catch(error => config.refreshTokenAction(error. config.router))
}