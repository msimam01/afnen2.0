import { NavFooter } from '@/components/nav-footer';
import { NavMain } from '@/components/nav-main';
import { NavUser } from '@/components/nav-user';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/react';
import { Users, Sprout, FileText, Building2, Truck, RotateCcw, BarChart3, Settings, LayoutGrid, MapPin } from 'lucide-react';
import AppLogo from './app-logo';

const tenantNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        url: '/dashboard',
        icon: LayoutGrid,
    },
    {
        title: 'Farmers',
        url: '/farmers',
        icon: Users,
        permission: 'view farmers',
    },
    {
        title: 'Farms',
        url: '/farms',
        icon: Sprout,
        permission: 'view farms',
    },
    {
        title: 'Applications',
        url: '/applications',
        icon: FileText,
        permission: 'view applications',
    },
    {
        title: 'Centers',
        url: '/centers',
        icon: MapPin,
        permission: 'view centers',
    },
    {
        title: 'Agents',
        url: '/agents',
        icon: Building2,
        permission: 'manage agents',
    },
    {
        title: 'Collection',
        url: '/collections',
        icon: Truck,
        permission: 'record collection',
    },
    {
        title: 'Returns',
        url: '/returns',
        icon: RotateCcw,
        permission: 'record return',
    },
    {
        title: 'Reports',
        url: '/reports',
        icon: BarChart3,
        permission: 'view reports',
    },
    {
        title: 'Settings',
        url: '/settings',
        icon: Settings,
        permission: 'manage settings',
    },
];

const footerNavItems: NavItem[] = [
    {
        title: 'Documentation',
        url: 'https://laravel.com/docs/starter-kits',
        icon: LayoutGrid,
    },
];

export function TenantSidebar() {
    const { auth } = usePage().props as any;
    const userPermissions = auth?.user?.permissions || [];

    // Filter navigation items based on user permissions
    const filteredNavItems = tenantNavItems.filter(item => {
        if (!item.permission) return true; // No permission required
        if (Array.isArray(item.permission)) {
            return item.permission.some(perm => userPermissions.includes(perm));
        }
        return userPermissions.includes(item.permission);
    });

    return (
        <Sidebar collapsible="icon" variant="inset">
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" asChild>
                            <Link href="/dashboard" prefetch>
                                <AppLogo />
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>

            <SidebarContent>
                <NavMain items={filteredNavItems} />
            </SidebarContent>

            <SidebarFooter>
                <NavFooter items={footerNavItems} className="mt-auto" />
                <NavUser />
            </SidebarFooter>
        </Sidebar>
    );
}
