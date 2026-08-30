import { AppContent } from '@/components/app-content';
import { AppShell } from '@/components/app-shell';
import { TenantSidebar } from '@/components/tenant-sidebar';
import { TenantSidebarHeader } from '@/components/tenant-sidebar-header';
import { type BreadcrumbItem } from '@/types';

export default function TenantSidebarLayout({ children, breadcrumbs = [] }: { children: React.ReactNode; breadcrumbs?: BreadcrumbItem[] }) {
    return (
        <AppShell variant="sidebar">
            <TenantSidebar />
            <AppContent variant="sidebar">
                <TenantSidebarHeader breadcrumbs={breadcrumbs} />
                {children}
            </AppContent>
        </AppShell>
    );
}
