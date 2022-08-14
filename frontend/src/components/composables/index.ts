import axios, { AxiosRequestConfig } from "axios"
import { Router } from "vue-router";

interface GetResourceConfig extends AxiosRequestConfig {
    router: Router
    refreshTokenAction: (error: object, router: Router | null) => void
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
        .catch(error => config.refreshTokenAction(error, config.router))
}

export function usePatchResource(
    url: string,
    data: object = {},
    config: GetResourceConfig,
): Promise<any>
{
    return axios
        .patch(url, data, config)
        .then(response => response.data)
        .catch(error => config.refreshTokenAction(error, config.router))
}