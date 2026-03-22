<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import EmailController from '@/actions/App/Http/Controllers/Admin/EmailController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';

interface Email {
    id: number;
    subject: string;
    to: string;
    from: string;
    cc: string | null;
    bcc: string | null;
    content: string;
    status: string;
}

const props = defineProps<{
    email: Email;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin/roles' },
    { title: 'Emails', href: '/admin/emails' },
    { title: 'Edit', href: `/admin/emails/${props.email.id}/edit` },
];
</script>

<template>
    <Head title="Edit Email" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-2xl space-y-6 p-6">
            <Heading title="Edit Email" description="Update this email" />

            <Form
                method="put"
                :action="EmailController.update.url({ email: email.id })"
                class="space-y-6"
                v-slot="{ errors, processing }"
            >
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="grid gap-2">
                        <Label for="to">To</Label>
                        <Input id="to" name="to" type="email" required :default-value="email.to" placeholder="recipient@example.com" />
                        <InputError :message="errors.to" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="from">From</Label>
                        <Input id="from" name="from" type="email" required :default-value="email.from" placeholder="sender@example.com" />
                        <InputError :message="errors.from" />
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="grid gap-2">
                        <Label for="cc">CC</Label>
                        <Input id="cc" name="cc" type="email" :default-value="email.cc ?? ''" placeholder="cc@example.com" />
                        <InputError :message="errors.cc" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="bcc">BCC</Label>
                        <Input id="bcc" name="bcc" type="email" :default-value="email.bcc ?? ''" placeholder="bcc@example.com" />
                        <InputError :message="errors.bcc" />
                    </div>
                </div>

                <div class="grid gap-2">
                    <Label for="subject">Subject</Label>
                    <Input id="subject" name="subject" required :default-value="email.subject" placeholder="Email subject" />
                    <InputError :message="errors.subject" />
                </div>

                <div class="grid gap-2">
                    <Label for="content">Content</Label>
                    <textarea
                        id="content"
                        name="content"
                        required
                        rows="10"
                        placeholder="Email body..."
                        class="flex w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                    >{{ email.content }}</textarea>
                    <InputError :message="errors.content" />
                </div>

                <Button :disabled="processing">Update Email</Button>
            </Form>
        </div>
    </AppLayout>
</template>
