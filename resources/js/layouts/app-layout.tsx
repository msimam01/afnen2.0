import CentralSidebarLayout from '@/layouts/central/central-sidebar-layout';
import TenantSidebarLayout from '@/layouts/tenant/tenant-sidebar-layout';
import { type BreadcrumbItem } from '@/types';
import { usePage } from '@inertiajs/react';

interface AppLayoutProps {
    children: React.ReactNode;
    breadcrumbs?: BreadcrumbItem[];
}

export default function AppLayout({ children, breadcrumbs = [] }: AppLayoutProps) {
    const { tenant } = usePage().props as any;
    const url = window.location.pathname;

    // Detect if we're in tenant context by checking if we're accessing a tenant domain
    // Central admin pages like /tenants/{id} should still use central sidebar even if they have tenant data
    const isTenantContext = tenant && !url.startsWith('/tenants/');

    if (isTenantContext) {
        return <TenantSidebarLayout breadcrumbs={breadcrumbs}>{children}</TenantSidebarLayout>;
    }

    return <CentralSidebarLayout breadcrumbs={breadcrumbs}>{children}</CentralSidebarLayout>;
}
