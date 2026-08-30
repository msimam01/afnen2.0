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

    // Detect if we're in tenant context by checking if tenant data exists
    const isTenantContext = !!tenant;

    if (isTenantContext) {
        return <TenantSidebarLayout breadcrumbs={breadcrumbs}>{children}</TenantSidebarLayout>;
    }

    return <CentralSidebarLayout breadcrumbs={breadcrumbs}>{children}</CentralSidebarLayout>;
}
