import { NavFooter } from '@/components/nav-footer';
import { NavMain } from '@/components/nav-main';
import { NavUser } from '@/components/nav-user';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/react';
import { Building2, Layers, Wheat, BarChart3, TrendingUp, Settings, LayoutGrid } from 'lucide-react';
import AppLogo from './app-logo';

const centralNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        url: '/dashboard',
        icon: LayoutGrid,
    },
    {
        title: 'Tenants',
        url: '/tenants',
        icon: Building2,
    },
    {
        title: 'Commodities',
        url: '/commodities',
        icon: Wheat,
    },
    {
        title: 'Commodity Categories',
        url: '/commodity-categories',
        icon: Layers,
    },
    {
        title: 'Seasons',
        url: '/seasons',
        icon: Layers,
    },
    {
        title: 'Stock & Allocations',
        url: '/allocations',
        icon: BarChart3,
    },
    {
        title: 'Market Prices',
        url: '/market-prices',
        icon: TrendingUp,
    },
    {
        title: 'Reports',
        url: '/reports',
        icon: BarChart3,
    },
    {
        title: 'Settings',
        url: '/settings',
        icon: Settings,
    },
];

const footerNavItems: NavItem[] = [
    {
        title: 'Documentation',
        url: 'https://laravel.com/docs/starter-kits',
        icon: LayoutGrid,
    },
];

export function CentralSidebar() {
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
                <NavMain items={centralNavItems} />
            </SidebarContent>

            <SidebarFooter>
                <NavFooter items={footerNavItems} className="mt-auto" />
                <NavUser />
            </SidebarFooter>
        </Sidebar>
    );
}
