import { FileText, Download } from 'lucide-react';

import CentralSidebarLayout from '@/layouts/central/central-sidebar-layout';
import { PageContainer } from '@/components/page-container';
import { PageHeader } from '@/components/page-header';
import { EmptyState } from '@/components/empty-state';
import { Button } from '@/components/ui/button';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Reports', href: '/reports' },
];

export default function Reports() {
    return (
        <CentralSidebarLayout breadcrumbs={breadcrumbs}>
            <Head title="Reports" />

            <PageContainer>
                <PageHeader
                    title="Reports"
                    description="Generate and view comprehensive reports on AFNEN operations across all tenants"
                />

                <EmptyState
                    icon={FileText}
                    title="No reports available yet"
                    description="Reports will be available once data is collected from tenants and operations are tracked."
                    action={
                        <Button variant="outline">
                            <Download className="mr-2 h-4 w-4" />
                            Generate Sample Report
                        </Button>
                    }
                />
            </PageContainer>
        </CentralSidebarLayout>
    );
}
