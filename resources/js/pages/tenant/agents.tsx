import { Building2, Plus } from 'lucide-react';

import TenantSidebarLayout from '@/layouts/tenant/tenant-sidebar-layout';
import { PageContainer } from '@/components/page-container';
import { PageHeader } from '@/components/page-header';
import { EmptyState } from '@/components/empty-state';
import { Button } from '@/components/ui/button';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: route('tenant.dashboard') },
    { title: 'Agents', href: route('tenant.agents') },
];

export default function Agents() {
    return (
        <TenantSidebarLayout breadcrumbs={breadcrumbs}>
            <Head title="Agents" />

            <PageContainer>
                <PageHeader
                    title="Agents"
                    description="Manage field agents who work with farmers and handle collections"
                    actions={
                        <Button>
                            <Plus className="mr-2 h-4 w-4" />
                            Add Agent
                        </Button>
                    }
                />

                <EmptyState
                    icon={Building2}
                    title="No agents registered yet"
                    description="Agents are field staff who interact with farmers, record collections, and manage returns. Add agents to extend your organization's reach."
                    action={
                        <Button>
                            <Plus className="mr-2 h-4 w-4" />
                            Add Agent
                        </Button>
                    }
                />
            </PageContainer>
        </TenantSidebarLayout>
    );
}
