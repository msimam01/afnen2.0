import { AppContent } from '@/components/app-content';
import { AppShell } from '@/components/app-shell';
import { CentralSidebar } from '@/components/central-sidebar';
import { CentralSidebarHeader } from '@/components/central-sidebar-header';
import { type BreadcrumbItem } from '@/types';

export default function CentralSidebarLayout({ children, breadcrumbs = [] }: { children: React.ReactNode; breadcrumbs?: BreadcrumbItem[] }) {
    return (
        <AppShell variant="sidebar">
            <CentralSidebar />
            <AppContent variant="sidebar">
                <CentralSidebarHeader breadcrumbs={breadcrumbs} />
                {children}
            </AppContent>
        </AppShell>
    );
}
