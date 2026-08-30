import { Settings as SettingsIcon } from 'lucide-react';

import TenantSidebarLayout from '@/layouts/tenant/tenant-sidebar-layout';
import { PageContainer } from '@/components/page-container';
import { PageHeader } from '@/components/page-header';
import { EmptyState } from '@/components/empty-state';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Settings', href: '/settings' },
];

export default function Settings() {
    return (
        <TenantSidebarLayout breadcrumbs={breadcrumbs}>
            <Head title="Settings" />

            <PageContainer>
                <PageHeader
                    title="Settings"
                    description="Configure your organization's preferences and settings"
                />

                <EmptyState
                    icon={SettingsIcon}
                    title="Settings configuration coming soon"
                    description="Organization settings, user management, and configuration options will be available in upcoming updates."
                />
            </PageContainer>
        </TenantSidebarLayout>
    );
}
