import { Building2, Globe, Loader2 } from 'lucide-react';
import { Head, Link, useForm } from '@inertiajs/react';
import { useState } from 'react';

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
    { title: 'Create Tenant', href: '/tenants/create' },
];

export default function TenantCreate() {
    const [slugPreview, setSlugPreview] = useState('');
    const { data, setData, post, processing, errors, reset } = useForm({
        name: '',
        description: '',
        slug: '',
        admin_name: '',
        admin_email: '',
    });

    const handleSlugChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const value = e.target.value.toLowerCase().replace(/[^a-z0-9-]/g, '-').replace(/-+/g, '-');
        setData('slug', value);
        setSlugPreview(value);
    };

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post('/tenants', {
            onSuccess: () => {
                reset();
            },
        });
    };

    const baseDomain = 'afnen.com';

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Create Tenant" />

            <PageContainer>
                <PageHeader
                    title="Create Tenant"
                    description="Add a new organization to the AFNEN platform"
                    backButton
                />

                <form onSubmit={handleSubmit}>
                    <div className="grid gap-6 lg:grid-cols-2">
                        <div className="space-y-6">
                            <Card>
                                <CardHeader>
                                    <CardTitle>Organization Details</CardTitle>
                                    <CardDescription>Basic information about the tenant organization</CardDescription>
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

                                    <div className="space-y-2">
                                        <Label htmlFor="slug">Tenant Slug *</Label>
                                        <Input
                                            id="slug"
                                            type="text"
                                            value={data.slug}
                                            onChange={handleSlugChange}
                                            placeholder="e.g., gombe-farmers"
                                            required
                                        />
                                        <p className="text-xs text-muted-foreground">
                                            Lowercase, URL-safe identifier (letters, numbers, dashes only)
                                        </p>
                                        <InputError message={errors.slug} />
                                    </div>
                                </CardContent>
                            </Card>

                            <Card>
                                <CardHeader>
                                    <CardTitle>Initial Administrator</CardTitle>
                                    <CardDescription>The first administrator for this tenant</CardDescription>
                                </CardHeader>
                                <CardContent className="space-y-4">
                                    <div className="space-y-2">
                                        <Label htmlFor="admin_name">Administrator Name *</Label>
                                        <Input
                                            id="admin_name"
                                            type="text"
                                            value={data.admin_name}
                                            onChange={(e) => setData('admin_name', e.target.value)}
                                            placeholder="e.g., John Doe"
                                            required
                                        />
                                        <InputError message={errors.admin_name} />
                                    </div>

                                    <div className="space-y-2">
                                        <Label htmlFor="admin_email">Administrator Email *</Label>
                                        <Input
                                            id="admin_email"
                                            type="email"
                                            value={data.admin_email}
                                            onChange={(e) => setData('admin_email', e.target.value)}
                                            placeholder="e.g., admin@gombe-farmers.afnen.com"
                                            required
                                        />
                                        <p className="text-xs text-muted-foreground">
                                            A secure temporary password will be generated and the administrator will need to set their password on first login
                                        </p>
                                        <InputError message={errors.admin_email} />
                                    </div>
                                </CardContent>
                            </Card>
                        </div>

                        <div className="space-y-6">
                            <Card>
                                <CardHeader>
                                    <CardTitle className="flex items-center gap-2">
                                        <Globe className="h-5 w-5" />
                                        Domain Preview
                                    </CardTitle>
                                    <CardDescription>Your tenant will be accessible at this domain</CardDescription>
                                </CardHeader>
                                <CardContent>
                                    {slugPreview ? (
                                        <div className="rounded-lg bg-sidebar-accent p-6 text-center">
                                            <div className="text-2xl font-semibold text-sidebar-foreground">
                                                {slugPreview}.{baseDomain}
                                            </div>
                                            <p className="mt-2 text-sm text-sidebar-foreground/70">
                                                This is a preview. The server will create the actual domain.
                                            </p>
                                        </div>
                                    ) : (
                                        <div className="rounded-lg border border-sidebar-border bg-sidebar p-6 text-center">
                                            <Building2 className="mx-auto h-12 w-12 text-sidebar-foreground/30" />
                                            <p className="mt-4 text-sm text-sidebar-foreground/70">
                                                Enter a tenant slug to see the domain preview
                                            </p>
                                        </div>
                                    )}
                                </CardContent>
                            </Card>

                            <Card>
                                <CardHeader>
                                    <CardTitle>Provisioning Information</CardTitle>
                                    <CardDescription>What happens after creation</CardDescription>
                                </CardHeader>
                                <CardContent className="space-y-3 text-sm">
                                    <div className="flex items-start gap-3">
                                        <div className="mt-1 h-2 w-2 rounded-full bg-primary" />
                                        <div>
                                            <p className="font-medium">Tenant database created</p>
                                            <p className="text-muted-foreground">A dedicated database will be provisioned</p>
                                        </div>
                                    </div>
                                    <div className="flex items-start gap-3">
                                        <div className="mt-1 h-2 w-2 rounded-full bg-primary" />
                                        <div>
                                            <p className="font-medium">Migrations run automatically</p>
                                            <p className="text-muted-foreground">Database schema will be initialized</p>
                                        </div>
                                    </div>
                                    <div className="flex items-start gap-3">
                                        <div className="mt-1 h-2 w-2 rounded-full bg-primary" />
                                        <div>
                                            <p className="font-medium">Roles & permissions configured</p>
                                            <p className="text-muted-foreground">Standard AFNEN roles will be created</p>
                                        </div>
                                    </div>
                                    <div className="flex items-start gap-3">
                                        <div className="mt-1 h-2 w-2 rounded-full bg-primary" />
                                        <div>
                                            <p className="font-medium">Administrator account created</p>
                                            <p className="text-muted-foreground">Initial admin will receive secure credentials</p>
                                        </div>
                                    </div>
                                    <div className="flex items-start gap-3">
                                        <div className="mt-1 h-2 w-2 rounded-full bg-primary" />
                                        <div>
                                            <p className="font-medium">Tenant activated</p>
                                            <p className="text-muted-foreground">Organization can access the platform</p>
                                        </div>
                                    </div>
                                </CardContent>
                            </Card>
                        </div>
                    </div>

                    <div className="mt-6 flex items-center justify-end gap-4">
                        <Link href="/tenants">
                            <Button variant="outline" type="button" disabled={processing}>
                                Cancel
                            </Button>
                        </Link>
                        <Button type="submit" disabled={processing}>
                            {processing ? (
                                <>
                                    <Loader2 className="mr-2 h-4 w-4 animate-spin" />
                                    Creating tenant...
                                </>
                            ) : (
                                'Create Tenant'
                            )}
                        </Button>
                    </div>
                </form>
            </PageContainer>
        </AppLayout>
    );
}
