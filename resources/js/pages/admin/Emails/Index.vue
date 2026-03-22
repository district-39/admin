<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import EmailController from '@/actions/App/Http/Controllers/Admin/EmailController';
import Heading from '@/components/Heading.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
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
    status: 'draft' | 'sent' | 'failed';
    date_sent: string | null;
    created_at: string;
}

interface PaginatedEmails {
    data: Email[];
    current_page: number;
    last_page: number;
    next_page_url: string | null;
    prev_page_url: string | null;
}

defineProps<{
    emails: PaginatedEmails;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin/roles' },
    { title: 'Emails', href: '/admin/emails' },
];

function deleteEmail(email: Email): void {
    if (!confirm(`Are you sure you want to delete "${email.subject}"?`)) {
        return;
    }
    router.delete(EmailController.destroy.url({ email: email.id }));
}

function sendEmail(email: Email): void {
    router.patch(EmailController.send.url({ email: email.id }));
}

const statusVariant: Record<string, 'default' | 'secondary' | 'destructive' | 'outline'> = {
    draft: 'secondary',
    sent: 'default',
    failed: 'destructive',
};

function formatDate(dateStr: string | null): string {
    if (!dateStr) {
        return '—';
    }
    return new Date(dateStr).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
        hour: 'numeric',
        minute: '2-digit',
    });
}
</script>

<template>
    <Head title="Emails" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-4xl space-y-6 p-6">
            <div class="flex items-center justify-between">
                <Heading title="Emails" description="Manage and review emails" />
                <Link :href="EmailController.create.url()">
                    <Button>Compose Email</Button>
                </Link>
            </div>

            <div v-if="emails.data.length" class="divide-y rounded-lg border">
                <div
                    v-for="email in emails.data"
                    :key="email.id"
                    class="flex items-center justify-between gap-4 p-4"
                >
                    <div class="min-w-0 space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="truncate font-medium">{{ email.subject }}</span>
                            <Badge :variant="statusVariant[email.status]" class="capitalize">{{ email.status }}</Badge>
                        </div>
                        <p class="text-sm text-muted-foreground">
                            To: {{ email.to }}
                            <template v-if="email.cc"> · CC: {{ email.cc }}</template>
                        </p>
                        <p class="text-xs text-muted-foreground">
                            {{ email.status === 'sent' ? `Sent ${formatDate(email.date_sent)}` : `Created ${formatDate(email.created_at)}` }}
                        </p>
                    </div>
                    <div class="flex shrink-0 items-center gap-2">
                        <Button
                            v-if="email.status === 'draft'"
                            size="sm"
                            @click="sendEmail(email)"
                        >
                            Send
                        </Button>
                        <Link :href="EmailController.edit.url({ email: email.id })">
                            <Button variant="outline" size="sm">Edit</Button>
                        </Link>
                        <Button
                            variant="destructive"
                            size="sm"
                            @click="deleteEmail(email)"
                        >
                            Delete
                        </Button>
                    </div>
                </div>
            </div>
            <p v-else class="text-sm text-muted-foreground">No emails yet.</p>

            <div v-if="emails.last_page > 1" class="flex justify-center gap-2">
                <Link v-if="emails.prev_page_url" :href="emails.prev_page_url">
                    <Button variant="outline" size="sm">Previous</Button>
                </Link>
                <Link v-if="emails.next_page_url" :href="emails.next_page_url">
                    <Button variant="outline" size="sm">Next</Button>
                </Link>
            </div>
        </div>
    </AppLayout>
</template>
