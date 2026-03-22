<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { BookOpen, CalendarDays, FileText, FolderGit2, LayoutGrid, List, Mail, NotebookPen, Shield, Users } from 'lucide-vue-next';
import { computed } from 'vue';
import AppLogo from '@/components/AppLogo.vue';
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { useRoles } from '@/composables/useRoles';
import NoteController from '@/actions/App/Http/Controllers/NoteController';
import { dashboard } from '@/routes';
import { index as scheduleIndex } from '@/routes/schedule';
import type { NavItem } from '@/types';

const { isAdmin, hasAnyRole, hasRole } = useRoles();

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },
    {
        title: 'Schedule',
        href: scheduleIndex(),
        icon: CalendarDays,
    },
];

const notesNavItems = computed<NavItem[]>(() => {
    if (!hasAnyRole(['dsecretary', 'notetaker'])) return [];

    const items: NavItem[] = [
        { title: 'Create Notes', href: NoteController.create.url(), icon: NotebookPen },
    ];

    if (hasRole('dsecretary')) {
        items.unshift({ title: 'View Notes', href: NoteController.index.url(), icon: List });
    }

    items.push({ title: 'Documents', href: '/documents', icon: FileText });

    return items;
});

const adminNavItems = computed<NavItem[]>(() =>
    isAdmin.value
        ? [
              { title: 'Manage Roles', href: '/admin/roles', icon: Shield },
              { title: 'Manage Users', href: '/admin/users', icon: Users },
              { title: 'Emails', href: '/admin/emails', icon: Mail },
          ]
        : [],
);

const footerNavItems: NavItem[] = [
    {
        title: 'Repository',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: FolderGit2,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#vue',
        icon: BookOpen,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
            <NavMain v-if="notesNavItems.length" :items="notesNavItems" label="Notes" />
            <NavMain v-if="adminNavItems.length" :items="adminNavItems" label="Admin" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
