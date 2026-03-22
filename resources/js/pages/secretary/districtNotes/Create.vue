<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import NoteController from '@/actions/App/Http/Controllers/NoteController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';

interface DistrictMeeting {
    id: number;
}

defineProps<{
    districtMeetings: DistrictMeeting[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Notes', href: NoteController.index.url() },
    { title: 'Create', href: NoteController.create.url() },
];

const form = useForm({
    district_meeting_id: '' as string | number,
    file_id: null as number | null,
    attendees: [] as {
        user_id: number | null;
        email: string;
        is_present: boolean;
        is_gsr: boolean;
        title: string;
    }[],
});

function addAttendee(): void {
    form.attendees.push({
        user_id: null,
        email: '',
        is_present: false,
        is_gsr: true,
        title: '',
    });
}

function removeAttendee(index: number): void {
    form.attendees.splice(index, 1);
}

function submit(): void {
    form.post(NoteController.store.url(), {
        preserveScroll: true,
    });
}
</script>

<template>
    <Head title="Create Note" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto w-full max-w-2xl space-y-6 p-6">
            <Heading title="Create Note" description="Create a new set of meeting notes" />

            <form class="space-y-6" @submit.prevent="submit">
                <div class="grid gap-2">
                    <Label for="district_meeting_id">District Meeting</Label>
                    <select
                        id="district_meeting_id"
                        v-model="form.district_meeting_id"
                        class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs transition-colors placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                    >
                        <option value="">None</option>
                        <option
                            v-for="meeting in districtMeetings"
                            :key="meeting.id"
                            :value="meeting.id"
                        >
                            Meeting #{{ meeting.id }}
                        </option>
                    </select>
                    <InputError :message="form.errors.district_meeting_id" />
                </div>

                <!-- Attendees -->
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <Label>Attendees</Label>
                        <Button type="button" variant="outline" size="sm" @click="addAttendee">
                            Add Attendee
                        </Button>
                    </div>

                    <div
                        v-for="(attendee, index) in form.attendees"
                        :key="index"
                        class="space-y-3 rounded-lg border p-4"
                    >
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium">Attendee {{ index + 1 }}</span>
                            <Button
                                type="button"
                                variant="ghost"
                                size="sm"
                                @click="removeAttendee(index)"
                            >
                                Remove
                            </Button>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="grid gap-2">
                                <Label :for="`attendee-email-${index}`">Email</Label>
                                <input
                                    :id="`attendee-email-${index}`"
                                    v-model="attendee.email"
                                    type="email"
                                    class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs transition-colors placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                                    placeholder="Email address"
                                />
                            </div>
                            <div class="grid gap-2">
                                <Label :for="`attendee-title-${index}`">Title</Label>
                                <input
                                    :id="`attendee-title-${index}`"
                                    v-model="attendee.title"
                                    type="text"
                                    class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs transition-colors placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                                    placeholder="Title (optional)"
                                />
                            </div>
                        </div>

                        <div class="flex gap-6">
                            <label class="flex items-center gap-2 text-sm">
                                <input v-model="attendee.is_present" type="checkbox" />
                                Present
                            </label>
                            <label class="flex items-center gap-2 text-sm">
                                <input v-model="attendee.is_gsr" type="checkbox" />
                                GSR
                            </label>
                        </div>
                    </div>
                </div>

                <div class="flex gap-3">
                    <Button type="submit" :disabled="form.processing">
                        Create Note
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
