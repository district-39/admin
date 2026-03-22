<script setup lang="ts">
export interface Update {
    id: number;
    title: string;
    summary: string;
    date: string;
    update_type: 'intergroup' | 'district' | 'area' | 'general';
    url: string | null;
    file_id: number | null;
    user_id: number | null;
}

defineProps<{
    updates: Update[];
}>();
</script>

<template>
    <div class="rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
        <div class="border-b border-sidebar-border/70 px-5 py-4 dark:border-sidebar-border">
            <h2 class="text-base font-semibold">What's New</h2>
        </div>
        <div v-if="updates.length" class="divide-y divide-sidebar-border/70 dark:divide-sidebar-border">
            <div
                v-for="update in updates"
                :key="update.id"
                class="px-5 py-4"
            >
                <div class="flex items-start justify-between gap-4">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <h3 class="text-sm font-medium">{{ update.title }}</h3>
                            <span class="rounded-full bg-muted px-2 py-0.5 text-xs capitalize text-muted-foreground">{{ update.update_type }}</span>
                        </div>
                        <p class="mt-1 text-sm text-muted-foreground">{{ update.summary }}</p>
                        <a
                            v-if="update.url"
                            :href="update.url"
                            target="_blank"
                            class="mt-1 inline-block text-xs text-primary hover:underline"
                        >Read more</a>
                    </div>
                    <time class="shrink-0 text-xs text-muted-foreground">{{ update.date }}</time>
                </div>
            </div>
        </div>
        <div v-else class="px-5 py-8 text-center text-sm text-muted-foreground">
            No recent updates.
        </div>
    </div>
</template>
