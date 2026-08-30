import { Building2, Plus } from 'lucide-react';

import { PageContainer } from '@/components/page-container';
import { PageHeader } from '@/components/page-header';
import { EmptyState } from '@/components/empty-state';
import { Button } from '@/components/ui/button';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Tenants', href: '/tenants' },
];

export default function Tenants() {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Tenants" />

            <PageContainer>
                <PageHeader
                    title="Tenants"
                    description="Manage participating organizations and their access to the AFNEN platform"
                    actions={
                        <Button>
                            <Plus className="mr-2 h-4 w-4" />
                            Add Tenant
                        </Button>
                    }
                />

                <EmptyState
                    icon={Building2}
                    title="No tenants yet"
                    description="Tenants are organizations that participate in the AFNEN program. Add your first tenant to get started."
                    action={
                        <Button>
                            <Plus className="mr-2 h-4 w-4" />
                            Add Tenant
                        </Button>
                    }
                />
            </PageContainer>
        </AppLayout>
    );
}
