<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import RecentUpdates from '@/components/RecentUpdates.vue';
import type { Update } from '@/components/RecentUpdates.vue';
import ServicePositionCard from '@/components/ServicePositionCard.vue';
import type { ServicePositionCardProps } from '@/components/ServicePositionCard.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import type { BreadcrumbItem } from '@/types';

defineProps<{
    updates: Update[];
    servicePositions: ServicePositionCardProps[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
    },
];
</script>

<template>
    
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div>
            
        </div>
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                <ServicePositionCard
                    v-for="(position, index) in servicePositions"
                    :key="index"
                    v-bind="position"
                />
            </div>
            <RecentUpdates :updates="updates" />
        </div>
    </AppLayout>
</template>
