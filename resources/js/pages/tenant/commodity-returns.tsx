import { Wheat } from 'lucide-react';

import TenantSidebarLayout from '@/layouts/tenant/tenant-sidebar-layout';
import { PageContainer } from '@/components/page-container';
import { PageHeader } from '@/components/page-header';
import { EmptyState } from '@/components/empty-state';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: route('tenant.dashboard') },
    { title: 'Commodity Returns', href: route('tenant.commodity-returns') },
];

export default function CommodityReturns() {
    return (
        <TenantSidebarLayout breadcrumbs={breadcrumbs}>
            <Head title="Commodity Returns" />

            <PageContainer>
                <PageHeader
                    title="Commodity Returns"
                    description="Manage commodity returns from farmers"
                />

                <EmptyState
                    icon={Wheat}
                    title="Commodity Returns Module Under Development"
                    description="This module will allow you to track and manage commodity returns from farmers. Coming in upcoming updates."
                />
            </PageContainer>
        </TenantSidebarLayout>
    );
}
