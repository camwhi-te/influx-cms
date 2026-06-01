import { Link, usePage } from '@inertiajs/react';
import { BookOpen, Cog, FolderGit2 } from 'lucide-react';
import AppLogo from '@/components/app-logo';
import { NavFooter } from '@/components/nav-footer';
import { NavMain } from '@/components/nav-main';
import { NavUser } from '@/components/nav-user';
import { GroupSwitcher } from '@/components/group-switcher';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import type { NavItem } from '@/types';

export function AppSidebar({ mainNavItems }: { mainNavItems: NavItem[] }) {
    const page = usePage();
    const dashboardUrl = page.props.currentGroup
        ? dashboard(page.props.currentGroup.slug)
        : '/';

    const isAdmin = page.props.auth?.isAdmin && page.url.startsWith('/admin');

    const footerNavItems: NavItem[] = [
        {
            title: 'Repository',
            href: 'https://github.com/laravel/react-starter-kit',
            icon: FolderGit2,
        },
        {
            title: 'Documentation',
            href: 'https://laravel.com/docs/starter-kits#react',
            icon: BookOpen,
        },
    ];

    return (
        <Sidebar collapsible="icon" variant="inset">
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" asChild>
                            <Link href={dashboardUrl} prefetch>
                                <AppLogo isAdmin={isAdmin} />
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
                {!isAdmin && (
                    <SidebarMenu>
                        <SidebarMenuItem>
                            <GroupSwitcher />
                        </SidebarMenuItem>
                    </SidebarMenu>
                )}
            </SidebarHeader>

            <SidebarContent>
                <NavMain items={mainNavItems} isAdmin={page.props.auth?.isAdmin && !page.url.startsWith('/admin')} />
            </SidebarContent>
            {!isAdmin && (
                <SidebarFooter>
                    <NavFooter items={footerNavItems} className="mt-auto" />
                    <NavUser />
                </SidebarFooter>
            )}
        </Sidebar>
    );
}
