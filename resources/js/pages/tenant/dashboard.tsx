import { Head, usePage } from '@inertiajs/react';
import { Users, FileText, Truck, Building2, Plus, Clock, RotateCcw } from 'lucide-react';

import TenantSidebarLayout from '@/layouts/tenant/tenant-sidebar-layout';
import { PageContainer } from '@/components/page-container';
import { PageHeader } from '@/components/page-header';
import { StatCard } from '@/components/stat-card';
import { SectionCard } from '@/components/section-card';
import { EmptyState } from '@/components/empty-state';
import { Button } from '@/components/ui/button';
import { type BreadcrumbItem } from '@/types';
import { Link } from '@inertiajs/react';

interface DashboardProps {
    tenantName?: string;
    userName?: string;
    userRole?: string;
    userPermissions?: string[];
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: route('tenant.dashboard'),
    },
];

export default function TenantDashboard({ tenantName, userName, userRole, userPermissions = [] }: DashboardProps) {
    const hasPermission = (permission: string) => userPermissions.includes(permission);

    return (
        <TenantSidebarLayout breadcrumbs={breadcrumbs}>
            <Head title="Tenant Dashboard" />

            <PageContainer>
                {/* Welcome Section */}
                <PageHeader
                    title={`Welcome back, ${userName || 'Administrator'}`}
                    description={`Manage operations for ${tenantName || 'your organization'}`}
                />

                {/* Stats Cards */}
                <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    <StatCard
                        title="Total Farmers"
                        value="—"
                        icon={Users}
                        description="No data yet"
                    />
                    <StatCard
                        title="Active Applications"
                        value="—"
                        icon={FileText}
                        description="No data yet"
                    />
                    <StatCard
                        title="Allocated Commodity"
                        value="—"
                        icon={Truck}
                        description="No data yet"
                    />
                    <StatCard
                        title="Active Agents"
                        value="—"
                        icon={Building2}
                        description="No data yet"
                    />
                    <StatCard
                        title="Pending Collections"
                        value="—"
                        icon={Clock}
                        description="No data yet"
                    />
                    <StatCard
                        title="Outstanding Returns"
                        value="—"
                        icon={RotateCcw}
                        description="No data yet"
                    />
                </div>

                {/* Quick Actions - Role-based */}
                <SectionCard title="Quick Actions" description="Actions based on your role and permissions">
                    <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                        {hasPermission('register farmers') && (
                            <Link href={route('tenant.farmers')}>
                                <Button variant="outline" className="w-full justify-start">
                                    <Plus className="mr-2 h-4 w-4" />
                                    Register Farmer
                                </Button>
                            </Link>
                        )}
                        {hasPermission('create applications') && (
                            <Link href={route('tenant.applications')}>
                                <Button variant="outline" className="w-full justify-start">
                                    <Plus className="mr-2 h-4 w-4" />
                                    Create Application
                                </Button>
                            </Link>
                        )}
                        {hasPermission('manage agents') && (
                            <Link href={route('tenant.agents')}>
                                <Button variant="outline" className="w-full justify-start">
                                    <Plus className="mr-2 h-4 w-4" />
                                    Manage Agents
                                </Button>
                            </Link>
                        )}
                        {hasPermission('view allocations') && (
                            <Link href={route('tenant.applications')}>
                                <Button variant="outline" className="w-full justify-start">
                                    <FileText className="mr-2 h-4 w-4" />
                                    View Allocations
                                </Button>
                            </Link>
                        )}
                        {hasPermission('record collection') && (
                            <Link href={route('tenant.collections')}>
                                <Button variant="outline" className="w-full justify-start">
                                    <Truck className="mr-2 h-4 w-4" />
                                    Record Collection
                                </Button>
                            </Link>
                        )}
                        {hasPermission('record return') && (
                            <Link href={route('tenant.returns')}>
                                <Button variant="outline" className="w-full justify-start">
                                    <RotateCcw className="mr-2 h-4 w-4" />
                                    Record Return
                                </Button>
                            </Link>
                        )}
                    </div>
                </SectionCard>

                {/* Role Information */}
                <SectionCard title="Your Role" description="Current user role and permissions">
                    <div className="space-y-2">
                        <p className="text-sm text-sidebar-foreground">
                            <span className="font-medium">Role:</span> {userRole || 'tenant-admin'}
                        </p>
                        <p className="text-sm text-sidebar-foreground">
                            <span className="font-medium">Organization:</span> {tenantName || 'Unknown'}
                        </p>
                    </div>
                </SectionCard>

                {/* Recent Activity */}
                <SectionCard title="Recent Activity" description="Latest actions in your organization">
                    <EmptyState
                        title="No recent activity"
                        description="Activity will appear here as you and your team use the platform."
                    />
                </SectionCard>
            </PageContainer>
        </TenantSidebarLayout>
    );
}
