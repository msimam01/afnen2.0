import { TrendingUp, Plus } from 'lucide-react';

import { PageContainer } from '@/components/page-container';
import { PageHeader } from '@/components/page-header';
import { EmptyState } from '@/components/empty-state';
import { Button } from '@/components/ui/button';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Market Prices', href: '/market-prices' },
];

export default function MarketPrices() {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Market Prices" />

            <PageContainer>
                <PageHeader
                    title="Market Prices"
                    description="Monitor and record agricultural commodity market prices for informed decision-making"
                    actions={
                        <Button>
                            <Plus className="mr-2 h-4 w-4" />
                            Add Price
                        </Button>
                    }
                />

                <EmptyState
                    icon={TrendingUp}
                    title="No market prices yet"
                    description="Track market prices for commodities to support allocation decisions and farmer repayments."
                    action={
                        <Button>
                            <Plus className="mr-2 h-4 w-4" />
                            Add Price
                        </Button>
                    }
                />
            </PageContainer>
        </AppLayout>
    );
}
