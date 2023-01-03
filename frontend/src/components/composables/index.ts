import axios, { AxiosRequestConfig } from "axios"
import { Router } from "vue-router"

export function useCreateAuthHeader(jwt: string): AxiosRequestConfig
{
    return {
        headers: {
            Authorization: `Bearer ${jwt}`
        }
    }
}

export function useGetResource(
    url: string,
    config: AxiosRequestConfig,
    refreshTokenCallback: (error: object, router: Router | null) => void,
    router: Router
): Promise<any>
{
    return axios
        .get(url, config)
        .then(response => response.data)
        .catch(error => refreshTokenCallback(error, router))
}

export function useDeleteResource(
    url: string,
    config: AxiosRequestConfig,
    refreshTokenCallback: (error: object, router: Router | null) => void,
    router: Router
): Promise<any>
{
    return axios
        .delete(url, config)
        .then(response => response.data)
        .catch(error => refreshTokenCallback(error, router))
}

export function usePatchResource(
    url: string,
    data: object = {},
    config: AxiosRequestConfig,
    refreshTokenCallback: (error: object, router: Router | null) => void,
    router: Router
): Promise<any>
{
    return axios
        .patch(url, data, config)
        .then(response => response.data)
        .catch(error => refreshTokenCallback(error, router))
}

export function usePostResource(
    url: string,
    data: object = {},
    config: AxiosRequestConfig,
    refreshTokenCallback: (error: object, router: Router | null) => void,
    router: Router
): Promise<any>
{
    return axios
        .post(url, data, config)
        .then(response => response.data)
        .catch(error => refreshTokenCallback(error, router))
}

export function usePutResource(
    url: string,
    data: object = {},
    config: AxiosRequestConfig,
    refreshTokenCallback: (error: object, router: Router | null) => void,
    router: Router
): Promise<any>
{
    return axios
        .put(url, data, config)
        .then(response => response.data)
        .catch(error => refreshTokenCallback(error, router))
}
