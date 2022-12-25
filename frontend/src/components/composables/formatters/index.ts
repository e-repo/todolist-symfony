import moment from "moment"
import { API_V1 } from "@/conf/api"

export function useDateTimeToFormat(dateTime: string, format: string = 'DD.MM.YYYY hh:mm:ss'): string
{
    return moment(dateTime).format(format)
}

export function useToAbsolutePath(path: string): string
{
    return API_V1.DOMAIN + path
}
