import { FileText, Plus } from 'lucide-react';

import TenantSidebarLayout from '@/layouts/tenant/tenant-sidebar-layout';
import { PageContainer } from '@/components/page-container';
import { PageHeader } from '@/components/page-header';
import { EmptyState } from '@/components/empty-state';
import { Button } from '@/components/ui/button';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: route('tenant.dashboard') },
    { title: 'Applications', href: route('tenant.applications') },
];

export default function Applications() {
    return (
        <TenantSidebarLayout breadcrumbs={breadcrumbs}>
            <Head title="Applications" />

            <PageContainer>
                <PageHeader
                    title="Applications"
                    description="Manage farmer applications for commodity allocation"
                    actions={
                        <Button>
                            <Plus className="mr-2 h-4 w-4" />
                            Create Application
                        </Button>
                    }
                />

                <EmptyState
                    icon={FileText}
                    title="No applications yet"
                    description="Applications represent farmer requests for agricultural commodities. Process applications to allocate resources based on need and availability."
                    action={
                        <Button>
                            <Plus className="mr-2 h-4 w-4" />
                            Create Application
                        </Button>
                    }
                />
            </PageContainer>
        </TenantSidebarLayout>
    );
}
