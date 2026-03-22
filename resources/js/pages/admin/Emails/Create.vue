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

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin/roles' },
    { title: 'Emails', href: '/admin/emails' },
    { title: 'Compose', href: '/admin/emails/create' },
];
</script>

<template>
    <Head title="Compose Email" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-2xl space-y-6 p-6">
            <Heading title="Compose Email" description="Create a new email" />

            <Form
                method="post"
                :action="EmailController.store.url()"
                class="space-y-6"
                v-slot="{ errors, processing }"
            >
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="grid gap-2">
                        <Label for="to">To</Label>
                        <Input id="to" name="to" type="email" required placeholder="recipient@example.com" />
                        <InputError :message="errors.to" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="from">From</Label>
                        <Input id="from" name="from" type="email" required placeholder="sender@example.com" />
                        <InputError :message="errors.from" />
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="grid gap-2">
                        <Label for="cc">CC</Label>
                        <Input id="cc" name="cc" type="email" placeholder="cc@example.com" />
                        <InputError :message="errors.cc" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="bcc">BCC</Label>
                        <Input id="bcc" name="bcc" type="email" placeholder="bcc@example.com" />
                        <InputError :message="errors.bcc" />
                    </div>
                </div>

                <div class="grid gap-2">
                    <Label for="subject">Subject</Label>
                    <Input id="subject" name="subject" required placeholder="Email subject" />
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
                    ></textarea>
                    <InputError :message="errors.content" />
                </div>

                <Button :disabled="processing">Save as Draft</Button>
            </Form>
        </div>
    </AppLayout>
</template>
