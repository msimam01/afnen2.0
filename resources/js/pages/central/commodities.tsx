import { Wheat, Plus } from 'lucide-react';

import CentralSidebarLayout from '@/layouts/central/central-sidebar-layout';
import { PageContainer } from '@/components/page-container';
import { PageHeader } from '@/components/page-header';
import { EmptyState } from '@/components/empty-state';
import { Button } from '@/components/ui/button';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Commodities', href: '/commodities' },
];

export default function Commodities() {
    return (
        <CentralSidebarLayout breadcrumbs={breadcrumbs}>
            <Head title="Commodities" />

            <PageContainer>
                <PageHeader
                    title="Commodities"
                    description="Manage agricultural commodities available for allocation to tenants"
                    actions={
                        <Button>
                            <Plus className="mr-2 h-4 w-4" />
                            Add Commodity
                        </Button>
                    }
                />

                <EmptyState
                    icon={Wheat}
                    title="No commodities yet"
                    description="Commodities are agricultural inputs (fertilizers, seeds, etc.) that can be allocated to participating organizations."
                    action={
                        <Button>
                            <Plus className="mr-2 h-4 w-4" />
                            Add Commodity
                        </Button>
                    }
                />
            </PageContainer>
        </CentralSidebarLayout>
    );
}
