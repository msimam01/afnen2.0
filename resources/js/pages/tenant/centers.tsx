import { MapPin, Plus } from 'lucide-react';

import TenantSidebarLayout from '@/layouts/tenant/tenant-sidebar-layout';
import { PageContainer } from '@/components/page-container';
import { PageHeader } from '@/components/page-header';
import { EmptyState } from '@/components/empty-state';
import { Button } from '@/components/ui/button';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Centers', href: '/centers' },
];

export default function Centers() {
    return (
        <TenantSidebarLayout breadcrumbs={breadcrumbs}>
            <Head title="Centers" />

            <PageContainer>
                <PageHeader
                    title="Centers"
                    description="Manage collection and distribution centers in your area"
                    actions={
                        <Button>
                            <Plus className="mr-2 h-4 w-4" />
                            Add Center
                        </Button>
                    }
                />

                <EmptyState
                    icon={MapPin}
                    title="No centers established yet"
                    description="Centers are physical locations where commodities are stored, distributed to farmers, and collections are received."
                    action={
                        <Button>
                            <Plus className="mr-2 h-4 w-4" />
                            Add Center
                        </Button>
                    }
                />
            </PageContainer>
        </TenantSidebarLayout>
    );
}
