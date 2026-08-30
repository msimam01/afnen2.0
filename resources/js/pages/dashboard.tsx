import { Building2, Layers, Wheat, BarChart3, Plus } from 'lucide-react';

import { PageContainer } from '@/components/page-container';
import { PageHeader } from '@/components/page-header';
import { StatCard } from '@/components/stat-card';
import { SectionCard } from '@/components/section-card';
import { EmptyState } from '@/components/empty-state';
import { Button } from '@/components/ui/button';
import { type BreadcrumbItem } from '@/types';
import { Head, usePage } from '@inertiajs/react';
import { Link } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

export default function Dashboard() {
    const { auth } = usePage().props as any;

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Central Dashboard" />

            <PageContainer>
                {/* Welcome Section */}
                <PageHeader
                    title={`Welcome back, ${auth?.user?.name || 'Administrator'}`}
                    description="Manage AFNEN operations across all participating organizations"
                />

                {/* Stats Cards */}
                <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                    <StatCard
                        title="Total Tenants"
                        value="—"
                        icon={Building2}
                        description="No data yet"
                    />
                    <StatCard
                        title="Active Tenants"
                        value="—"
                        icon={Building2}
                        description="No data yet"
                    />
                    <StatCard
                        title="Active Seasons"
                        value="—"
                        icon={Layers}
                        description="No data yet"
                    />
                    <StatCard
                        title="Available Commodity Stock"
                        value="—"
                        icon={Wheat}
                        description="No data yet"
                    />
                </div>

                {/* Quick Actions */}
                <SectionCard title="Quick Actions" description="Frequently used actions">
                    <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                        <Link href="/tenants">
                            <Button variant="outline" className="w-full justify-start">
                                <Plus className="mr-2 h-4 w-4" />
                                Add Tenant
                            </Button>
                        </Link>
                        <Link href="/seasons">
                            <Button variant="outline" className="w-full justify-start">
                                <Plus className="mr-2 h-4 w-4" />
                                Create Season
                            </Button>
                        </Link>
                        <Link href="/commodities">
                            <Button variant="outline" className="w-full justify-start">
                                <Plus className="mr-2 h-4 w-4" />
                                Add Commodity
                            </Button>
                        </Link>
                        <Link href="/allocations">
                            <Button variant="outline" className="w-full justify-start">
                                <Plus className="mr-2 h-4 w-4" />
                                Allocate Stock
                            </Button>
                        </Link>
                    </div>
                </SectionCard>

                {/* Recent Activity */}
                <SectionCard title="Recent Activity" description="Latest actions across the platform">
                    <EmptyState
                        title="No recent activity"
                        description="Activity will appear here as you and other administrators use the platform."
                    />
                </SectionCard>
            </PageContainer>
        </AppLayout>
    );
}
