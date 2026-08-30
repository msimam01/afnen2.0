import { RotateCcw, Plus } from 'lucide-react';

import TenantSidebarLayout from '@/layouts/tenant/tenant-sidebar-layout';
import { PageContainer } from '@/components/page-container';
import { PageHeader } from '@/components/page-header';
import { EmptyState } from '@/components/empty-state';
import { Button } from '@/components/ui/button';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: route('tenant.dashboard') },
    { title: 'Returns', href: route('tenant.returns') },
];

export default function Returns() {
    return (
        <TenantSidebarLayout breadcrumbs={breadcrumbs}>
            <Head title="Returns" />

            <PageContainer>
                <PageHeader
                    title="Returns"
                    description="Record and track farmer returns and repayments"
                    actions={
                        <Button>
                            <Plus className="mr-2 h-4 w-4" />
                            Record Return
                        </Button>
                    }
                />

                <EmptyState
                    icon={RotateCcw}
                    title="No returns recorded yet"
                    description="Returns represent farmer repayments for received commodities. Track repayment amounts, dates, and outstanding balances."
                    action={
                        <Button>
                            <Plus className="mr-2 h-4 w-4" />
                            Record Return
                        </Button>
                    }
                />
            </PageContainer>
        </TenantSidebarLayout>
    );
}
