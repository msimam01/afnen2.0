import { CalendarDays, Plus } from 'lucide-react';

import { PageContainer } from '@/components/page-container';
import { PageHeader } from '@/components/page-header';
import { EmptyState } from '@/components/empty-state';
import { Button } from '@/components/ui/button';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Seasons', href: '/seasons' },
];

export default function Seasons() {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Seasons" />

            <PageContainer>
                <PageHeader
                    title="Seasons"
                    description="Define agricultural seasons for commodity allocation and collection cycles"
                    actions={
                        <Button>
                            <Plus className="mr-2 h-4 w-4" />
                            Create Season
                        </Button>
                    }
                />

                <EmptyState
                    icon={CalendarDays}
                    title="No seasons yet"
                    description="Seasons define time periods for agricultural activities (e.g., 2024 Planting Season, 2024 Harvest Season)."
                    action={
                        <Button>
                            <Plus className="mr-2 h-4 w-4" />
                            Create Season
                        </Button>
                    }
                />
            </PageContainer>
        </AppLayout>
    );
}
