<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import type { BreadcrumbItem } from '@/types';
import type { Role } from '@/types/auth';

type RoleWithCount = Role & { users_count: number };

type Props = {
    roles: RoleWithCount[];
};

defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin/roles' },
    { title: 'Roles', href: '/admin/roles' },
];

function deleteRole(role: RoleWithCount) {
    if (role.slug === 'admin') return;
    if (!confirm(`Are you sure you want to delete the "${role.name}" role?`)) return;

    router.delete(`/admin/roles/${role.id}`);
}
</script>

<template>
    <Head title="Manage Roles" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-4xl space-y-6 p-6">
            <div class="flex items-center justify-between">
                <Heading title="Roles" description="Manage roles available in the system" />
                <Link href="/admin/roles/create">
                    <Button>Create Role</Button>
                </Link>
            </div>

            <div class="divide-y rounded-lg border">
                <div
                    v-for="role in roles"
                    :key="role.id"
                    class="flex items-center justify-between p-4"
                >
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="font-medium">{{ role.name }}</span>
                            <Badge variant="secondary">{{ role.users_count }} users</Badge>
                        </div>
                        <p v-if="role.description" class="text-sm text-muted-foreground">
                            {{ role.description }}
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <Link :href="`/admin/roles/${role.id}/edit`">
                            <Button variant="outline" size="sm">Edit</Button>
                        </Link>
                        <Button
                            v-if="role.slug !== 'admin'"
                            variant="destructive"
                            size="sm"
                            @click="deleteRole(role)"
                        >
                            Delete
                        </Button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
