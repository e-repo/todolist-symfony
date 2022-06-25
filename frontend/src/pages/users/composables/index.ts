import { BADGE, UserRoleClassBadge, UserStatusClassBadge } from "@/conf"
import { RoleName } from "@/pages/users/enums/RoleName"

export function useRoleClasses(role: string): string
{
    const roleToUpper = role.toUpperCase()

    return typeof BADGE.USER_ROLE[roleToUpper as keyof UserRoleClassBadge] === 'object'
        ? BADGE.USER_ROLE[roleToUpper as keyof UserRoleClassBadge].class
        : ''
}

export function useRoleNames(role: string): string
{
    const roleToUpper = role.toUpperCase()
    return RoleName[roleToUpper as keyof typeof RoleName]
}

export function useStatusClasses(status: string): string
{
    const statusToUpper = status.toUpperCase()

    return typeof BADGE.USER_STATUS[statusToUpper as keyof UserStatusClassBadge] === 'object'
        ? BADGE.USER_STATUS[statusToUpper as keyof UserStatusClassBadge].class
        : ''
}