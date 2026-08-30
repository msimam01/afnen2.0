import { LayoutDashboard, Building2, Wheat, Layers, Package, TrendingUp, BarChart3, Settings, Users, Sprout, FileText, MapPin, UserCog, Truck, RotateCcw, DollarSign } from 'lucide-react';
import { type NavItem } from '@/types';

export interface NavigationConfig {
    title: string;
    items: NavItem[];
}

export const centralNavigation: NavigationConfig = {
    title: 'Central Administration',
    items: [
        {
            title: 'Dashboard',
            url: '/dashboard',
            icon: LayoutDashboard,
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
            icon: Package,
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
    ],
};

export const tenantNavigation: NavigationConfig = {
    title: 'Tenant Administration',
    items: [
        {
            title: 'Dashboard',
            url: '/dashboard',
            icon: LayoutDashboard,
            // No permission required - always visible
        },
        {
            title: 'Farmers',
            url: '/farmers',
            icon: Users,
            permission: 'farmers.view',
        },
        {
            title: 'Farms',
            url: '/farms',
            icon: Sprout,
            permission: 'farms.view',
        },
        {
            title: 'Applications',
            url: '/applications',
            icon: FileText,
            permission: 'applications.view',
        },
        {
            title: 'Centers',
            url: '/centers',
            icon: MapPin,
            permission: 'centers.view',
        },
        {
            title: 'Agents',
            url: '/agents',
            icon: UserCog,
            permission: 'agents.view',
        },
        {
            title: 'Collection Verification',
            url: '/collections',
            icon: Truck,
            permission: 'collection-verifications.view',
        },
        {
            title: 'Return Verification',
            url: '/returns',
            icon: RotateCcw,
            permission: 'return-verifications.view',
        },
        {
            title: 'Commodity Returns',
            url: '/commodity-returns',
            icon: Wheat,
            permission: 'commodity-returns.view',
        },
        {
            title: 'Monetary Returns',
            url: '/monetary-returns',
            icon: DollarSign,
            permission: 'monetary-returns.view',
        },
        {
            title: 'Reports',
            url: '/reports',
            icon: BarChart3,
            permission: 'reports.view',
        },
        {
            title: 'Settings',
            url: '/settings',
            icon: Settings,
            permission: 'settings.view',
        },
    ],
};
