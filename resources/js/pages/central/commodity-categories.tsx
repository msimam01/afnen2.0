import { Layers, Plus } from 'lucide-react';

import { PageContainer } from '@/components/page-container';
import { PageHeader } from '@/components/page-header';
import { EmptyState } from '@/components/empty-state';
import { Button } from '@/components/ui/button';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Commodity Categories', href: '/commodity-categories' },
];

export default function CommodityCategories() {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Commodity Categories" />

            <PageContainer>
                <PageHeader
                    title="Commodity Categories"
                    description="Organize commodities into categories for better management and reporting"
                    actions={
                        <Button>
                            <Plus className="mr-2 h-4 w-4" />
                            Add Category
                        </Button>
                    }
                />

                <EmptyState
                    icon={Layers}
                    title="No commodity categories yet"
                    description="Categories help organize commodities (e.g., Fertilizers, Seeds, Equipment) for easier management."
                    action={
                        <Button>
                            <Plus className="mr-2 h-4 w-4" />
                            Add Category
                        </Button>
                    }
                />
            </PageContainer>
        </AppLayout>
    );
}
