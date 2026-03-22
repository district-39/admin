<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import Heading from '@/components/Heading.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import type { BreadcrumbItem } from '@/types';
import type { Role, User } from '@/types/auth';

type Props = {
    users: (User & { roles: Role[] })[];
    roles: Role[];
};

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin/roles' },
    { title: 'Users', href: '/admin/users' },
];

const editingUserId = ref<number | null>(null);
const selectedRoleIds = ref<number[]>([]);

function startEditing(user: User & { roles: Role[] }) {
    editingUserId.value = user.id;
    selectedRoleIds.value = user.roles.map((r) => r.id);
}

function cancelEditing() {
    editingUserId.value = null;
    selectedRoleIds.value = [];
}

function toggleRole(roleId: number) {
    const index = selectedRoleIds.value.indexOf(roleId);
    if (index === -1) {
        selectedRoleIds.value.push(roleId);
    } else {
        selectedRoleIds.value.splice(index, 1);
    }
}

function saveRoles(userId: number) {
    router.put(
        `/admin/users/${userId}/roles`,
        { role_ids: selectedRoleIds.value },
        {
            onSuccess: () => {
                editingUserId.value = null;
                selectedRoleIds.value = [];
            },
        },
    );
}
</script>

<template>
    <Head title="Manage Users" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-4xl space-y-6 p-6">
            <Heading title="Users" description="Assign roles to users" />

            <div class="divide-y rounded-lg border">
                <div
                    v-for="user in props.users"
                    :key="user.id"
                    class="p-4"
                >
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-medium">{{ user.name }}</p>
                            <p class="text-sm text-muted-foreground">{{ user.email }}</p>
                        </div>
                        <div v-if="editingUserId !== user.id" class="flex items-center gap-2">
                            <Badge v-for="role in user.roles" :key="role.id" variant="secondary">
                                {{ role.name }}
                            </Badge>
                            <Button variant="outline" size="sm" @click="startEditing(user)">
                                Edit Roles
                            </Button>
                        </div>
                        <div v-else class="flex items-center gap-2">
                            <Button size="sm" @click="saveRoles(user.id)">Save</Button>
                            <Button variant="outline" size="sm" @click="cancelEditing()">Cancel</Button>
                        </div>
                    </div>

                    <div v-if="editingUserId === user.id" class="mt-3 flex flex-wrap gap-4">
                        <label
                            v-for="role in props.roles"
                            :key="role.id"
                            class="flex items-center gap-2 text-sm"
                        >
                            <Checkbox
                                :checked="selectedRoleIds.includes(role.id)"
                                @update:checked="toggleRole(role.id)"
                            />
                            {{ role.name }}
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
