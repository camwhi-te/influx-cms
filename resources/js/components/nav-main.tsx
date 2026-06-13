import { Link, usePage } from '@inertiajs/react';
import {
    SidebarGroup,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { useCurrentUrl } from '@/hooks/use-current-url';
import type { NavItem } from '@/types';
import { Cog } from 'lucide-react';


export function NavMain({ items = [], isAdmin = false }: { items: NavItem[]; isAdmin: boolean }) {
    const { isCurrentUrl } = useCurrentUrl();
    const page = usePage();

    const permissions = page.props.auth?.permissions || [];
    const hasPermission = (permission: string) => permissions.includes(permission);

    return (
        <SidebarGroup className="px-2 py-0">
            <SidebarMenu>
                {items.map((item) => (
                    (item.hasPermission && hasPermission(item.hasPermission)) && (
                        <SidebarMenuItem key={item.title}>
                            <SidebarMenuButton
                                asChild
                                isActive={isCurrentUrl(item.href)}
                                tooltip={{ children: item.title }}
                            >
                                <Link href={item.href} prefetch>
                                    {item.icon && <item.icon />}
                                    <span>{item.title}</span>
                                </Link>
                            </SidebarMenuButton>
                        </SidebarMenuItem>
                    )))
                }
                {isAdmin && (
                    <SidebarMenuItem>
                        <SidebarMenuButton
                            asChild
                            tooltip={{ children: 'Admin' }}
                        >
                            <Link href={'/admin'} prefetch>
                                <Cog />
                                <span>Admin Settings</span>
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                )}
            </SidebarMenu>
        </SidebarGroup>
    );
}
