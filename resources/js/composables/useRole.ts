import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

export default function useRole() {
    const roles = computed<string[]>(() => {
        const props = usePage().props as { auth?: { roles?: string[] } }
        return props.auth?.roles ?? []
    })

    const hasRole = (role: string) => roles.value.includes(role)
    const hasAnyRole = (...checks: string[]) => checks.some(r => roles.value.includes(r))
    console.log(roles.value)

    return { roles, hasRole, hasAnyRole }
}
