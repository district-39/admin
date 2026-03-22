<script setup lang="ts">
export interface MeetingInfo {
    date: string;
    file_url?: string | null;
}

export interface ServicePositionCardProps {
    title: string;
    description: string;
    role: string | null;
    nextMeeting: MeetingInfo | null;
    lastMeeting: MeetingInfo | null;
}

defineProps<ServicePositionCardProps>();
</script>

<template>
    <div class="flex flex-col justify-between rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border">
        <div>
            <h3 class="text-sm font-semibold">{{ title }}</h3>
            <p class="mt-1 text-xs text-muted-foreground">{{ description }}</p>
            <div class="mt-3 space-y-1 text-sm">
                <p v-if="nextMeeting">
                    Next meeting will be <span class="font-semibold">{{ nextMeeting.date }}</span>
                </p>
                <div v-if="lastMeeting" class="flex items-center justify-between gap-2">
                    <p>
                        Last meeting was <span class="font-semibold">{{ lastMeeting.date }}</span>
                    </p>
                    <a
                        v-if="lastMeeting.file_url"
                        :href="lastMeeting.file_url"
                        target="_blank"
                        class="shrink-0 text-xs text-primary hover:underline"
                    >View Notes</a>
                </div>
            </div>
        </div>
        <p v-if="role" class="mt-3 text-xs text-muted-foreground">
            You are a <span class="capitalize">{{ role }}</span> for {{ title }}. <a href="#" class="text-primary hover:underline">Update service position.</a>
        </p>
    </div>
</template>
