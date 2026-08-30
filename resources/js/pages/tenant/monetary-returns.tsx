import { DollarSign } from 'lucide-react';

import TenantSidebarLayout from '@/layouts/tenant/tenant-sidebar-layout';
import { PageContainer } from '@/components/page-container';
import { PageHeader } from '@/components/page-header';
import { EmptyState } from '@/components/empty-state';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: route('tenant.dashboard') },
    { title: 'Monetary Returns', href: route('tenant.monetary-returns') },
];

export default function MonetaryReturns() {
    return (
        <TenantSidebarLayout breadcrumbs={breadcrumbs}>
            <Head title="Monetary Returns" />

            <PageContainer>
                <PageHeader
                    title="Monetary Returns"
                    description="Manage monetary returns and repayments from farmers"
                />

                <EmptyState
                    icon={DollarSign}
                    title="Monetary Returns Module Under Development"
                    description="This module will allow you to track and manage monetary returns and repayments from farmers. Coming in upcoming updates."
                />
            </PageContainer>
        </TenantSidebarLayout>
    );
}
