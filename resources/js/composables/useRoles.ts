import { usePage } from '@inertiajs/vue3';
import { computed, type ComputedRef } from 'vue';
import type { Auth } from '@/types/auth';

export type UseRolesReturn = {
    isAdmin: ComputedRef<boolean>;
    hasRole: (slug: string) => boolean;
    hasAnyRole: (slugs: string[]) => boolean;
};

export function useRoles(): UseRolesReturn {
    const auth = computed(() => usePage().props.auth as Auth);
    const roles = computed(() => auth.value?.user?.roles ?? []);

    const hasRole = (slug: string): boolean => {
        return roles.value.some((role) => role.slug === slug);
    };

    const hasAnyRole = (slugs: string[]): boolean => {
        return roles.value.some((role) => slugs.includes(role.slug));
    };

    const isAdmin = computed(() => hasRole('admin'));

    return { isAdmin, hasRole, hasAnyRole };
}
