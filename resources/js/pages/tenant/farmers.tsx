import { Users, Plus } from 'lucide-react';

import TenantSidebarLayout from '@/layouts/tenant/tenant-sidebar-layout';
import { PageContainer } from '@/components/page-container';
import { PageHeader } from '@/components/page-header';
import { EmptyState } from '@/components/empty-state';
import { Button } from '@/components/ui/button';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Farmers', href: '/farmers' },
];

export default function Farmers() {
    return (
        <TenantSidebarLayout breadcrumbs={breadcrumbs}>
            <Head title="Farmers" />

            <PageContainer>
                <PageHeader
                    title="Farmers"
                    description="Manage farmers registered under your organization"
                    actions={
                        <Button>
                            <Plus className="mr-2 h-4 w-4" />
                            Register Farmer
                        </Button>
                    }
                />

                <EmptyState
                    icon={Users}
                    title="No farmers registered yet"
                    description="Farmers are the primary beneficiaries of the AFNEN program. Register farmers to track their applications, collections, and returns."
                    action={
                        <Button>
                            <Plus className="mr-2 h-4 w-4" />
                            Register Farmer
                        </Button>
                    }
                />
            </PageContainer>
        </TenantSidebarLayout>
    );
}
