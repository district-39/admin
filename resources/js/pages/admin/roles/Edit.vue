<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import type { BreadcrumbItem } from '@/types';
import type { Role } from '@/types/auth';

type Props = {
    role: Role;
};

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin/roles' },
    { title: 'Roles', href: '/admin/roles' },
    { title: 'Edit', href: `/admin/roles/${props.role.id}/edit` },
];
</script>

<template>
    <Head title="Edit Role" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-2xl space-y-6 p-6">
            <Heading title="Edit Role" :description="`Update the ${role.name} role`" />

            <Form
                method="put"
                :action="`/admin/roles/${role.id}`"
                class="space-y-6"
                v-slot="{ errors, processing }"
            >
                <div class="grid gap-2">
                    <Label for="name">Name</Label>
                    <Input id="name" name="name" :default-value="role.name" required placeholder="Role name" />
                    <InputError :message="errors.name" />
                </div>

                <div class="grid gap-2">
                    <Label for="description">Description</Label>
                    <Input
                        id="description"
                        name="description"
                        :default-value="role.description ?? ''"
                        placeholder="Optional description"
                    />
                    <InputError :message="errors.description" />
                </div>

                <Button :disabled="processing">Save Changes</Button>
            </Form>
        </div>
    </AppLayout>
</template>
