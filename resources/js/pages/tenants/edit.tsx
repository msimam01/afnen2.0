import { Building2, Globe, Loader2, Lock } from 'lucide-react';
import { Head, Link, useForm } from '@inertiajs/react';

import { PageContainer } from '@/components/page-container';
import { PageHeader } from '@/components/page-header';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import InputError from '@/components/input-error';
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
        admin_name?: string;
        admin_email?: string;
    };
    status: string;
    provisioning_status: string;
    created_at: string;
    domains: Array<{
        domain: string;
    }>;
}

interface Props {
    tenant: Tenant;
}

export default function TenantEdit({ tenant }: Props) {
    const { data, setData, put, processing, errors } = useForm({
        name: tenant.data?.name || '',
        description: tenant.data?.description || '',
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        put(`/tenants/${tenant.id}`);
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`Edit ${tenant.data?.name || tenant.id}`} />

            <PageContainer>
                <PageHeader
                    title={`Edit ${tenant.data?.name || tenant.id}`}
                    description="Update tenant information"
                    backButton
                    backButtonHref={`/tenants/${tenant.id}`}
                />

                <form onSubmit={handleSubmit}>
                    <div className="grid gap-6 lg:grid-cols-2">
                        <div className="space-y-6">
                            <Card>
                                <CardHeader>
                                    <CardTitle>Organization Details</CardTitle>
                                    <CardDescription>Update basic organization information</CardDescription>
                                </CardHeader>
                                <CardContent className="space-y-4">
                                    <div className="space-y-2">
                                        <Label htmlFor="name">Organization Name *</Label>
                                        <Input
                                            id="name"
                                            type="text"
                                            value={data.name}
                                            onChange={(e) => setData('name', e.target.value)}
                                            placeholder="e.g., Gombe State Farmers Association"
                                            required
                                        />
                                        <InputError message={errors.name} />
                                    </div>

                                    <div className="space-y-2">
                                        <Label htmlFor="description">Description</Label>
                                        <Input
                                            id="description"
                                            type="text"
                                            value={data.description}
                                            onChange={(e) => setData('description', e.target.value)}
                                            placeholder="Brief description of the organization"
                                        />
                                        <InputError message={errors.description} />
                                    </div>
                                </CardContent>
                            </Card>
                        </div>

                        <div className="space-y-6">
                            <Card>
                                <CardHeader>
                                    <CardTitle className="flex items-center gap-2">
                                        <Lock className="h-5 w-5 text-muted-foreground" />
                                        Read-Only Information
                                    </CardTitle>
                                    <CardDescription>These fields cannot be changed after tenant creation</CardDescription>
                                </CardHeader>
                                <CardContent className="space-y-4">
                                    <div className="space-y-2">
                                        <Label htmlFor="slug">Tenant Slug</Label>
                                        <div className="relative">
                                            <Input
                                                id="slug"
                                                type="text"
                                                value={tenant.id}
                                                disabled
                                                className="bg-muted"
                                            />
                                            <Lock className="absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                                        </div>
                                        <p className="text-xs text-muted-foreground">
                                            Tenant slug cannot be changed after creation
                                        </p>
                                    </div>

                                    <div className="space-y-2">
                                        <Label htmlFor="domain">Domain</Label>
                                        <div className="relative">
                                            <Input
                                                id="domain"
                                                type="text"
                                                value={tenant.domains[0]?.domain || ''}
                                                disabled
                                                className="bg-muted"
                                            />
                                            <Globe className="absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                                        </div>
                                        <p className="text-xs text-muted-foreground">
                                            Domain cannot be changed after creation
                                        </p>
                                    </div>

                                    <div className="space-y-2">
                                        <Label htmlFor="admin_email">Administrator Email</Label>
                                        <div className="relative">
                                            <Input
                                                id="admin_email"
                                                type="email"
                                                value={tenant.data?.admin_email || ''}
                                                disabled
                                                className="bg-muted"
                                            />
                                            <Lock className="absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                                        </div>
                                        <p className="text-xs text-muted-foreground">
                                            Administrator email cannot be changed
                                        </p>
                                    </div>
                                </CardContent>
                            </Card>

                            <Card>
                                <CardHeader>
                                    <CardTitle>Important Notes</CardTitle>
                                    <CardDescription>Considerations when editing tenants</CardDescription>
                                </CardHeader>
                                <CardContent className="space-y-3 text-sm">
                                    <div className="flex items-start gap-3">
                                        <Building2 className="mt-0.5 h-4 w-4 text-muted-foreground" />
                                        <div>
                                            <p className="font-medium">Organization Impact</p>
                                            <p className="text-muted-foreground">
                                                Changes to organization name will be reflected across the platform
                                            </p>
                                        </div>
                                    </div>
                                    <div className="flex items-start gap-3">
                                        <Lock className="mt-0.5 h-4 w-4 text-muted-foreground" />
                                        <div>
                                            <p className="font-medium">Slug and Domain</p>
                                            <p className="text-muted-foreground">
                                                These identifiers are tied to database structure and cannot be safely changed
                                            </p>
                                        </div>
                                    </div>
                                </CardContent>
                            </Card>
                        </div>
                    </div>

                    <div className="mt-6 flex items-center justify-end gap-4">
                        <Link href={`/tenants/${tenant.id}`}>
                            <Button variant="outline" type="button" disabled={processing}>
                                Cancel
                            </Button>
                        </Link>
                        <Button type="submit" disabled={processing}>
                            {processing ? (
                                <>
                                    <Loader2 className="mr-2 h-4 w-4 animate-spin" />
                                    Saving changes...
                                </>
                            ) : (
                                'Save Changes'
                            )}
                        </Button>
                    </div>
                </form>
            </PageContainer>
        </AppLayout>
    );
}
