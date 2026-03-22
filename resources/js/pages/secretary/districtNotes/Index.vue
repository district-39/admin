<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import NoteController from '@/actions/App/Http/Controllers/NoteController';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';

interface Attendee {
    id: number;
    email: string | null;
    is_present: boolean;
    is_gsr: boolean;
    title: string | null;
}

interface Note {
    id: number;
    district_meeting_id: number | null;
    file_id: number | null;
    attendees: Attendee[];
    district_meeting: { id: number } | null;
    created_at: string;
}

defineProps<{
    notes: Note[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Notes', href: NoteController.index.url() },
];

function deleteNote(note: Note): void {
    if (!confirm('Are you sure you want to delete this note?')) return;
    router.delete(NoteController.destroy.url(note.id));
}

function formatDate(dateStr: string): string {
    return new Date(dateStr).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
    });
}
</script>

<template>
    <Head title="Notes" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-4xl space-y-6 p-6">
            <div class="flex items-center justify-between">
                <Heading title="District Notes" description="Meeting notes and attendance records" />
                <Link :href="NoteController.create.url()">
                    <Button>Create Note</Button>
                </Link>
            </div>

            <div v-if="notes.length" class="divide-y rounded-lg border">
                <div
                    v-for="note in notes"
                    :key="note.id"
                    class="flex items-center justify-between p-4"
                >
                    <div class="space-y-1">
                        <span class="font-medium">
                            Note #{{ note.id }}
                            <span v-if="note.district_meeting" class="text-muted-foreground">
                                — Meeting #{{ note.district_meeting.id }}
                            </span>
                        </span>
                        <p class="text-sm text-muted-foreground">
                            {{ note.attendees.length }} attendees · Created {{ formatDate(note.created_at) }}
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <Link :href="NoteController.edit.url(note.id)">
                            <Button variant="outline" size="sm">Edit</Button>
                        </Link>
                        <Button
                            variant="destructive"
                            size="sm"
                            @click="deleteNote(note)"
                        >
                            Delete
                        </Button>
                    </div>
                </div>
            </div>
            <p v-else class="text-sm text-muted-foreground">
                No notes yet. Create one to get started.
            </p>
        </div>
    </AppLayout>
</template>
