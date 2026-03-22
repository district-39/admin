<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import ServicePositionController from '@/actions/App/Http/Controllers/Settings/ServicePositionController';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { edit } from '@/routes/service-positions';
import type { BreadcrumbItem } from '@/types';
import type { Role } from '@/types/auth';

type Props = {
    userRoles: Role[];
    availableRoles: Role[];
};

const props = defineProps<Props>();

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Service Positions',
        href: edit(),
    },
];

const showDistrict = ref(true);
const showIntergroup = ref(true);
const selectedRoleId = ref<string>('');

const addableRoles = computed(() => {
    const userRoleIds = props.userRoles.map((r) => r.id);
    return props.availableRoles.filter((r) => !userRoleIds.includes(r.id));
});

function addRole(): void {
    if (!selectedRoleId.value) {
        return;
    }

    router.post(
        ServicePositionController.addRole.url(),
        { role_id: selectedRoleId.value },
        {
            preserveScroll: true,
            onSuccess: () => {
                selectedRoleId.value = '';
            },
        },
    );
}

function removeRole(role: Role): void {
    router.delete(ServicePositionController.removeRole.url({ role: role.id }), {
        preserveScroll: true,
    });
}
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Service Positions" />

        <h1 class="sr-only">Service Positions</h1>

        <SettingsLayout>
            <div class="flex flex-col space-y-6">
                <Heading
                    variant="small"
                    title="Dashboard visibility"
                    description="Choose which service positions appear on your dashboard"
                />

                <div class="flex flex-col gap-3">
                    <div class="flex items-center gap-2">
                        <Checkbox
                            id="show-district"
                            :checked="showDistrict"
                            @update:checked="showDistrict = $event"
                        />
                        <Label for="show-district" class="cursor-pointer">
                            Show Service Positions for: District
                        </Label>
                    </div>
                    <div class="flex items-center gap-2">
                        <Checkbox
                            id="show-intergroup"
                            :checked="showIntergroup"
                            @update:checked="showIntergroup = $event"
                        />
                        <Label for="show-intergroup" class="cursor-pointer">
                            Show Service Positions for: Intergroup
                        </Label>
                    </div>
                </div>
            </div>

            <div class="flex flex-col space-y-6">
                <Heading
                    variant="small"
                    title="Your service positions"
                    description="Manage the roles associated with your account"
                />

                <div v-if="userRoles.length" class="space-y-2">
                    <div
                        v-for="role in userRoles"
                        :key="role.id"
                        class="flex items-center justify-between rounded-lg border px-4 py-3"
                    >
                        <div>
                            <p class="text-sm font-medium">{{ role.name }}</p>
                            <p v-if="role.description" class="text-xs text-muted-foreground">
                                {{ role.description }}
                            </p>
                        </div>
                        <Button
                            variant="ghost"
                            size="sm"
                            class="text-destructive hover:text-destructive"
                            @click="removeRole(role)"
                        >
                            Remove
                        </Button>
                    </div>
                </div>
                <p v-else class="text-sm text-muted-foreground">
                    You don't have any service positions yet.
                </p>

                <div v-if="addableRoles.length" class="flex items-end gap-3">
                    <div class="flex-1">
                        <Label for="add-role" class="mb-2 block">Add a role</Label>
                        <Select v-model="selectedRoleId">
                            <SelectTrigger id="add-role">
                                <SelectValue placeholder="Select a role..." />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem
                                    v-for="role in addableRoles"
                                    :key="role.id"
                                    :value="String(role.id)"
                                >
                                    {{ role.name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                    </div>
                    <Button :disabled="!selectedRoleId" @click="addRole">
                        Add Role
                    </Button>
                </div>
            </div>
        </SettingsLayout>
    </AppLayout>
</template>
