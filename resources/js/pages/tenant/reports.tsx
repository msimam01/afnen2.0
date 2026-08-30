import { FileText, Download } from 'lucide-react';

import TenantSidebarLayout from '@/layouts/tenant/tenant-sidebar-layout';
import { PageContainer } from '@/components/page-container';
import { PageHeader } from '@/components/page-header';
import { EmptyState } from '@/components/empty-state';
import { Button } from '@/components/ui/button';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: route('tenant.dashboard') },
    { title: 'Reports', href: route('tenant.reports') },
];

export default function Reports() {
    return (
        <TenantSidebarLayout breadcrumbs={breadcrumbs}>
            <Head title="Reports" />

            <PageContainer>
                <PageHeader
                    title="Reports"
                    description="Generate and view comprehensive reports on your organization's operations"
                />

                <EmptyState
                    icon={FileText}
                    title="No reports available yet"
                    description="Reports will be available once your organization has farmers, applications, collections, and returns data."
                    action={
                        <Button variant="outline">
                            <Download className="mr-2 h-4 w-4" />
                            Generate Sample Report
                        </Button>
                    }
                />
            </PageContainer>
        </TenantSidebarLayout>
    );
}
