import { BarChart3, Plus } from 'lucide-react';

import { PageContainer } from '@/components/page-container';
import { PageHeader } from '@/components/page-header';
import { EmptyState } from '@/components/empty-state';
import { Button } from '@/components/ui/button';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Stock & Allocations', href: '/allocations' },
];

export default function Allocations() {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Stock & Allocations" />

            <PageContainer>
                <PageHeader
                    title="Stock & Allocations"
                    description="Manage commodity stock levels and allocate resources to participating tenants"
                    actions={
                        <Button>
                            <Plus className="mr-2 h-4 w-4" />
                            Allocate Stock
                        </Button>
                    }
                />

                <EmptyState
                    icon={BarChart3}
                    title="No allocations yet"
                    description="Allocate commodities to tenants based on their needs and available stock. Track allocation status and distribution."
                    action={
                        <Button>
                            <Plus className="mr-2 h-4 w-4" />
                            Allocate Stock
                        </Button>
                    }
                />
            </PageContainer>
        </AppLayout>
    );
}
