import { Building2, Edit, Eye, Plus, Search } from 'lucide-react';
import { Head, Link, router } from '@inertiajs/react';
import { useState } from 'react';

import { PageContainer } from '@/components/page-container';
import { PageHeader } from '@/components/page-header';
import { EmptyState } from '@/components/empty-state';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Badge } from '@/components/ui/badge';
import { type BreadcrumbItem } from '@/types';
import AppLayout from '@/layouts/app-layout';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Tenants', href: '/tenants' },
];

interface Tenant {
    id: string;
    data: {
        name: string;
        description?: string;
    };
    status: string;
    provisioning_status: string;
    created_at: string;
    activated_at?: string;
    domains: Array<{
        domain: string;
    }>;
}

interface PaginatedTenants {
    data: Tenant[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
}

interface Props {
    tenants: PaginatedTenants;
    filters: {
        search?: string;
    };
}

export default function TenantIndex({ tenants, filters }: Props) {
    const [search, setSearch] = useState(filters.search || '');

    const handleSearch = (e: React.FormEvent) => {
        e.preventDefault();
        router.get('/tenants', { search }, { preserveState: true });
    };

    const getStatusBadge = (status: string) => {
        const variants: Record<string, 'default' | 'secondary' | 'destructive' | 'outline'> = {
            active: 'default',
            inactive: 'secondary',
            suspended: 'destructive',
        };
        return (
            <Badge variant={variants[status] || 'outline'}>
                {status.charAt(0).toUpperCase() + status.slice(1)}
            </Badge>
        );
    };

    const getProvisioningBadge = (status: string) => {
        const variants: Record<string, 'default' | 'secondary' | 'destructive' | 'outline'> = {
            ready: 'default',
            pending: 'secondary',
            provisioning: 'secondary',
            failed: 'destructive',
        };
        return (
            <Badge variant={variants[status] || 'outline'}>
                {status.charAt(0).toUpperCase() + status.slice(1)}
            </Badge>
        );
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Tenants" />

            <PageContainer>
                <PageHeader
                    title="Tenants"
                    description="Manage organizations participating in AFNEN"
                    actions={
                        <Link href="/tenants/create">
                            <Button>
                                <Plus className="mr-2 h-4 w-4" />
                                Create Tenant
                            </Button>
                        </Link>
                    }
                />

                <div className="mb-6">
                    <form onSubmit={handleSearch} className="flex gap-2">
                        <div className="relative flex-1">
                            <Search className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                            <Input
                                type="search"
                                placeholder="Search tenants by name or slug..."
                                value={search}
                                onChange={(e) => setSearch(e.target.value)}
                                className="pl-10"
                            />
                        </div>
                        <Button type="submit">Search</Button>
                    </form>
                </div>

                {tenants.data.length === 0 ? (
                    <EmptyState
                        icon={Building2}
                        title="No tenants found"
                        description={search ? 'No tenants match your search criteria.' : 'Tenants are organizations that participate in the AFNEN program. Add your first tenant to get started.'}
                        action={
                            !search && (
                                <Link href="/tenants/create">
                                    <Button>
                                        <Plus className="mr-2 h-4 w-4" />
                                        Create Tenant
                                    </Button>
                                </Link>
                            )
                        }
                    />
                ) : (
                    <div className="rounded-lg border border-sidebar-border bg-sidebar">
                        <div className="overflow-x-auto">
                            <table className="w-full">
                                <thead>
                                    <tr className="border-b border-sidebar-border bg-sidebar-accent/50">
                                        <th className="px-6 py-3 text-left text-sm font-semibold text-sidebar-foreground">Organization</th>
                                        <th className="px-6 py-3 text-left text-sm font-semibold text-sidebar-foreground">Domain</th>
                                        <th className="px-6 py-3 text-left text-sm font-semibold text-sidebar-foreground">Status</th>
                                        <th className="px-6 py-3 text-left text-sm font-semibold text-sidebar-foreground">Provisioning</th>
                                        <th className="px-6 py-3 text-left text-sm font-semibold text-sidebar-foreground">Created</th>
                                        <th className="px-6 py-3 text-right text-sm font-semibold text-sidebar-foreground">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {tenants.data.map((tenant) => (
                                        <tr key={tenant.id} className="border-b border-sidebar-border hover:bg-sidebar-accent/30">
                                            <td className="px-6 py-4">
                                                <div className="font-medium text-sidebar-foreground">{tenant.data?.name || tenant.id}</div>
                                                <div className="text-sm text-sidebar-foreground/70">{tenant.id}</div>
                                            </td>
                                            <td className="px-6 py-4 text-sm text-sidebar-foreground">
                                                {tenant.domains[0]?.domain || '-'}
                                            </td>
                                            <td className="px-6 py-4">{getStatusBadge(tenant.status)}</td>
                                            <td className="px-6 py-4">{getProvisioningBadge(tenant.provisioning_status)}</td>
                                            <td className="px-6 py-4 text-sm text-sidebar-foreground/70">
                                                {new Date(tenant.created_at).toLocaleDateString()}
                                            </td>
                                            <td className="px-6 py-4 text-right">
                                                <div className="flex justify-end gap-2">
                                                    <Link href={`/tenants/${tenant.id}`}>
                                                        <Button variant="ghost" size="sm">
                                                            <Eye className="h-4 w-4" />
                                                        </Button>
                                                    </Link>
                                                    <Link href={`/tenants/${tenant.id}/edit`}>
                                                        <Button variant="ghost" size="sm">
                                                            <Edit className="h-4 w-4" />
                                                        </Button>
                                                    </Link>
                                                </div>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>

                        {tenants.last_page > 1 && (
                            <div className="flex items-center justify-between border-t border-sidebar-border px-6 py-4">
                                <div className="text-sm text-sidebar-foreground/70">
                                    Page {tenants.current_page} of {tenants.last_page}
                                </div>
                                <div className="flex gap-2">
                                    {tenants.current_page > 1 && (
                                        <Button
                                            variant="outline"
                                            size="sm"
                                            onClick={() => router.get('/tenants', { page: tenants.current_page - 1, search })}
                                        >
                                            Previous
                                        </Button>
                                    )}
                                    {tenants.current_page < tenants.last_page && (
                                        <Button
                                            variant="outline"
                                            size="sm"
                                            onClick={() => router.get('/tenants', { page: tenants.current_page + 1, search })}
                                        >
                                            Next
                                        </Button>
                                    )}
                                </div>
                            </div>
                        )}
                    </div>
                )}
            </PageContainer>
        </AppLayout>
    );
}
