import moment from "moment"

export function useDateTimeToFormat(dateTime: string, format: string = 'DD.MM.YYYY hh:mm:ss'): string
{
    return moment(dateTime).format(format)
}