import { Truck, Plus } from 'lucide-react';

import TenantSidebarLayout from '@/layouts/tenant/tenant-sidebar-layout';
import { PageContainer } from '@/components/page-container';
import { PageHeader } from '@/components/page-header';
import { EmptyState } from '@/components/empty-state';
import { Button } from '@/components/ui/button';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: route('tenant.dashboard') },
    { title: 'Collection', href: route('tenant.collections') },
];

export default function Collections() {
    return (
        <TenantSidebarLayout breadcrumbs={breadcrumbs}>
            <Head title="Collection" />

            <PageContainer>
                <PageHeader
                    title="Collection"
                    description="Record and track commodity collections from farmers"
                    actions={
                        <Button>
                            <Plus className="mr-2 h-4 w-4" />
                            Record Collection
                        </Button>
                    }
                />

                <EmptyState
                    icon={Truck}
                    title="No collections recorded yet"
                    description="Collections represent the actual distribution of commodities to farmers based on approved applications. Track quantities, dates, and recipient information."
                    action={
                        <Button>
                            <Plus className="mr-2 h-4 w-4" />
                            Record Collection
                        </Button>
                    }
                />
            </PageContainer>
        </TenantSidebarLayout>
    );
}
