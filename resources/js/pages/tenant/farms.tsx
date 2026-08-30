import { Sprout, Plus } from 'lucide-react';

import TenantSidebarLayout from '@/layouts/tenant/tenant-sidebar-layout';
import { PageContainer } from '@/components/page-container';
import { PageHeader } from '@/components/page-header';
import { EmptyState } from '@/components/empty-state';
import { Button } from '@/components/ui/button';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Farms', href: '/farms' },
];

export default function Farms() {
    return (
        <TenantSidebarLayout breadcrumbs={breadcrumbs}>
            <Head title="Farms" />

            <PageContainer>
                <PageHeader
                    title="Farms"
                    description="Manage farm locations and details for registered farmers"
                    actions={
                        <Button>
                            <Plus className="mr-2 h-4 w-4" />
                            Add Farm
                        </Button>
                    }
                />

                <EmptyState
                    icon={Sprout}
                    title="No farms registered yet"
                    description="Farms represent the agricultural land where farmers cultivate crops. Link farms to farmers for better tracking."
                    action={
                        <Button>
                            <Plus className="mr-2 h-4 w-4" />
                            Add Farm
                        </Button>
                    }
                />
            </PageContainer>
        </TenantSidebarLayout>
    );
}
