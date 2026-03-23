<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { ShieldX, LinkIcon } from 'lucide-vue-next';
import TextLink from '@/components/TextLink.vue';
import AuthBase from '@/layouts/AuthLayout.vue';
import { login } from '@/routes';

const props = defineProps<{
    reason: 'missing' | 'expired';
}>();

const content = {
    missing: {
        icon: ShieldX,
        title: 'Invitation Required',
        message: 'Registration is available by invitation only. Please contact an administrator to request access.',
    },
    expired: {
        icon: LinkIcon,
        title: 'Invitation Link Expired',
        message: 'This invitation link is no longer valid. Please reach out to an administrator to have a new invitation sent to you.',
    },
};

const current = content[props.reason];
</script>

<template>
    <AuthBase :title="current.title">
        <Head title="Registration Unavailable" />

        <div class="flex flex-col items-center gap-6 text-center">
            <div class="flex h-16 w-16 items-center justify-center rounded-full bg-muted">
                <component :is="current.icon" class="h-8 w-8 text-muted-foreground" />
            </div>

            <p class="text-sm text-muted-foreground">
                {{ current.message }}
            </p>

            <TextLink
                :href="login()"
                class="text-sm underline underline-offset-4"
            >
                Back to login
            </TextLink>
        </div>
    </AuthBase>
</template>
