<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import ScheduleController from '@/actions/App/Http/Controllers/ScheduleController';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { index } from '@/routes/schedule';
import type { BreadcrumbItem } from '@/types';

interface Event {
    id: number;
    title: string;
    description: string | null;
    date: string;
    start_time: string | null;
    end_time: string | null;
    location: string | null;
    status: 'pending' | 'approved' | 'rejected';
    submitted_by: number | null;
    submitted_by_user?: { id: number; name: string } | null;
}

const props = defineProps<{
    events: Event[];
    pendingEvents: Event[];
    isAdmin: boolean;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Schedule',
        href: index(),
    },
];

const showForm = ref(false);

const form = useForm({
    title: '',
    description: '',
    date: '',
    start_time: '',
    end_time: '',
    location: '',
});

function submitEvent(): void {
    form.post(ScheduleController.store.url(), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            showForm.value = false;
        },
    });
}

function approveEvent(event: Event): void {
    router.patch(ScheduleController.approve.url({ event: event.id }), {}, {
        preserveScroll: true,
    });
}

function rejectEvent(event: Event): void {
    router.patch(ScheduleController.reject.url({ event: event.id }), {}, {
        preserveScroll: true,
    });
}

function formatDate(dateStr: string): string {
    return new Date(dateStr + 'T00:00:00').toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
    });
}

function formatTime(time: string | null): string {
    if (!time) {
        return '';
    }
    const [h, m] = time.split(':');
    const d = new Date();
    d.setHours(parseInt(h), parseInt(m));
    return d.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' });
}
</script>

<template>
    <Head title="Schedule" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-4">
            <!-- Pending events for admins -->
            <div v-if="isAdmin && pendingEvents.length">
                <Heading
                    variant="small"
                    title="Pending Submissions"
                    description="Events submitted by members awaiting your approval"
                />

                <div class="mt-4 space-y-3">
                    <div
                        v-for="event in pendingEvents"
                        :key="event.id"
                        class="flex items-start justify-between gap-4 rounded-lg border border-yellow-500/30 bg-yellow-50/50 px-5 py-4 dark:bg-yellow-950/10"
                    >
                        <div class="min-w-0">
                            <h3 class="text-sm font-semibold">{{ event.title }}</h3>
                            <p v-if="event.description" class="mt-1 text-sm text-muted-foreground">
                                {{ event.description }}
                            </p>
                            <div class="mt-2 flex flex-wrap gap-x-4 gap-y-1 text-xs text-muted-foreground">
                                <span>{{ formatDate(event.date) }}</span>
                                <span v-if="event.start_time">{{ formatTime(event.start_time) }}<template v-if="event.end_time"> – {{ formatTime(event.end_time) }}</template></span>
                                <span v-if="event.location">{{ event.location }}</span>
                            </div>
                            <p v-if="event.submitted_by_user" class="mt-1 text-xs text-muted-foreground">
                                Submitted by {{ event.submitted_by_user.name }}
                            </p>
                        </div>
                        <div class="flex shrink-0 gap-2">
                            <Button size="sm" @click="approveEvent(event)">
                                Approve
                            </Button>
                            <Button size="sm" variant="outline" @click="rejectEvent(event)">
                                Reject
                            </Button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Upcoming events -->
            <div>
                <div class="flex items-center justify-between">
                    <Heading
                        variant="small"
                        title="Schedule"
                        description="Upcoming events and meetings"
                    />
                    <Button v-if="!showForm" size="sm" variant="outline" @click="showForm = true">
                        Submit Event
                    </Button>
                </div>

                <!-- Submit event form -->
                <div v-if="showForm" class="mt-4 rounded-lg border p-5">
                    <h3 class="mb-4 text-sm font-semibold">Submit a new event</h3>
                    <form class="space-y-4" @submit.prevent="submitEvent">
                        <div class="grid gap-2">
                            <Label for="event-title">Title</Label>
                            <Input
                                id="event-title"
                                v-model="form.title"
                                required
                                placeholder="Event title"
                            />
                            <p v-if="form.errors.title" class="text-sm text-destructive">{{ form.errors.title }}</p>
                        </div>

                        <div class="grid gap-2">
                            <Label for="event-description">Description</Label>
                            <Input
                                id="event-description"
                                v-model="form.description"
                                placeholder="Brief description (optional)"
                            />
                            <p v-if="form.errors.description" class="text-sm text-destructive">{{ form.errors.description }}</p>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-3">
                            <div class="grid gap-2">
                                <Label for="event-date">Date</Label>
                                <Input
                                    id="event-date"
                                    v-model="form.date"
                                    type="date"
                                    required
                                />
                                <p v-if="form.errors.date" class="text-sm text-destructive">{{ form.errors.date }}</p>
                            </div>
                            <div class="grid gap-2">
                                <Label for="event-start">Start Time</Label>
                                <Input
                                    id="event-start"
                                    v-model="form.start_time"
                                    type="time"
                                />
                                <p v-if="form.errors.start_time" class="text-sm text-destructive">{{ form.errors.start_time }}</p>
                            </div>
                            <div class="grid gap-2">
                                <Label for="event-end">End Time</Label>
                                <Input
                                    id="event-end"
                                    v-model="form.end_time"
                                    type="time"
                                />
                                <p v-if="form.errors.end_time" class="text-sm text-destructive">{{ form.errors.end_time }}</p>
                            </div>
                        </div>

                        <div class="grid gap-2">
                            <Label for="event-location">Location</Label>
                            <Input
                                id="event-location"
                                v-model="form.location"
                                placeholder="Location (optional)"
                            />
                            <p v-if="form.errors.location" class="text-sm text-destructive">{{ form.errors.location }}</p>
                        </div>

                        <div class="flex gap-3">
                            <Button type="submit" :disabled="form.processing">
                                Submit for Review
                            </Button>
                            <Button type="button" variant="ghost" @click="showForm = false; form.reset()">
                                Cancel
                            </Button>
                        </div>
                    </form>
                </div>

                <!-- Events list -->
                <div v-if="events.length" class="mt-4 space-y-3">
                    <div
                        v-for="event in events"
                        :key="event.id"
                        class="rounded-lg border px-5 py-4"
                    >
                        <div class="flex items-start justify-between gap-4">
                            <div class="min-w-0">
                                <h3 class="text-sm font-semibold">{{ event.title }}</h3>
                                <p v-if="event.description" class="mt-1 text-sm text-muted-foreground">
                                    {{ event.description }}
                                </p>
                            </div>
                            <time class="shrink-0 text-xs text-muted-foreground">{{ formatDate(event.date) }}</time>
                        </div>
                        <div class="mt-2 flex flex-wrap gap-x-4 gap-y-1 text-xs text-muted-foreground">
                            <span v-if="event.start_time">{{ formatTime(event.start_time) }}<template v-if="event.end_time"> – {{ formatTime(event.end_time) }}</template></span>
                            <span v-if="event.location">{{ event.location }}</span>
                        </div>
                    </div>
                </div>
                <p v-else class="mt-4 text-sm text-muted-foreground">
                    No upcoming events.
                </p>
            </div>
        </div>
    </AppLayout>
</template>
