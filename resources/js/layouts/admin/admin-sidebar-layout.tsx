import { AppContent } from '@/components/app-content';
import { AppShell } from '@/components/app-shell';
import { AppSidebar } from '@/components/app-sidebar';
import { AppSidebarHeader } from '@/components/app-sidebar-header';
import type { AppLayoutProps, NavItem } from '@/types';
import { ChartBar, ShieldCheck } from 'lucide-react';

/**
 * Admin Sidebar Layout
 * 
 * Similar to the standard app layout but intended for administrative pages.
 * Can be customized with admin-specific components and styles.
 */

export default function AdminSidebarLayout({
    children,
    breadcrumbs = [],
}: AppLayoutProps) {
    const items: NavItem[] = [
        {
            title: 'Overview',
            href: '/admin',
            icon: ChartBar,
        },
    ];

    return (
        <AppShell variant="sidebar">
            <AppSidebar mainNavItems={items} />
            <AppContent variant="sidebar" className="overflow-x-hidden">
                <AppSidebarHeader breadcrumbs={breadcrumbs} />
                {children}
            </AppContent>
        </AppShell>
    );
}
