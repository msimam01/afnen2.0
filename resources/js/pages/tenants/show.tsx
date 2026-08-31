import { Building2, Database, Globe, User, Edit, ArrowLeft, Clock, CheckCircle, XCircle, Loader2, Copy } from 'lucide-react';
import { Head, Link } from '@inertiajs/react';

import { PageContainer } from '@/components/page-container';
import { PageHeader } from '@/components/page-header';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import { type BreadcrumbItem } from '@/types';
import AppLayout from '@/layouts/app-layout';

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
    activated_at?: string;
    deactivated_at?: string;
    deactivation_reason?: string;
    domains: Array<{
        domain: string;
    }>;
}

interface Props {
    tenant: Tenant;
    temp_password?: string;
}

export default function TenantShow({ tenant, temp_password }: Props) {
    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Dashboard', href: '/dashboard' },
        { title: 'Tenants', href: '/tenants' },
        { title: tenant.data?.name || tenant.id, href: `/tenants/${tenant.id}` },
    ];

    const getStatusBadge = (status: string) => {
        const variants: Record<string, 'default' | 'secondary' | 'destructive' | 'outline'> = {
            active: 'default',
            inactive: 'secondary',
            suspended: 'destructive',
        };
        const icons: Record<string, React.ReactNode> = {
            active: <CheckCircle className="h-3 w-3" />,
            inactive: <XCircle className="h-3 w-3" />,
            suspended: <XCircle className="h-3 w-3" />,
        };
        return (
            <Badge variant={variants[status] || 'outline'} className="gap-1">
                {icons[status]}
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
        const icons: Record<string, React.ReactNode> = {
            ready: <CheckCircle className="h-3 w-3" />,
            pending: <Loader2 className="h-3 w-3 animate-spin" />,
            provisioning: <Loader2 className="h-3 w-3 animate-spin" />,
            failed: <XCircle className="h-3 w-3" />,
        };
        return (
            <Badge variant={variants[status] || 'outline'} className="gap-1">
                {icons[status]}
                {status.charAt(0).toUpperCase() + status.slice(1)}
            </Badge>
        );
    };

    const formatDate = (dateString?: string) => {
        if (!dateString) return '-';
        return new Date(dateString).toLocaleString();
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={tenant.data?.name || tenant.id} />

            <PageContainer>
                <PageHeader
                    title={tenant.data?.name || tenant.id}
                    description="Tenant details and status"
                    backButton
                    backButtonHref="/tenants"
                    actions={
                        <Link href={`/tenants/${tenant.id}/edit`}>
                            <Button variant="outline">
                                <Edit className="mr-2 h-4 w-4" />
                                Edit Tenant
                            </Button>
                        </Link>
                    }
                />

                <div className="grid gap-6 lg:grid-cols-2">
                    <div className="space-y-6">
                        <Card>
                            <CardHeader>
                                <CardTitle className="flex items-center gap-2">
                                    <Building2 className="h-5 w-5" />
                                    Organization
                                </CardTitle>
                                <CardDescription>Basic tenant information</CardDescription>
                            </CardHeader>
                            <CardContent className="space-y-4">
                                <div>
                                    <p className="text-sm font-medium text-muted-foreground">Organization Name</p>
                                    <p className="text-base font-semibold">{tenant.data?.name || '-'}</p>
                                </div>
                                <Separator />
                                <div>
                                    <p className="text-sm font-medium text-muted-foreground">Tenant Slug</p>
                                    <p className="text-base font-semibold">{tenant.id}</p>
                                </div>
                                <Separator />
                                <div>
                                    <p className="text-sm font-medium text-muted-foreground">Description</p>
                                    <p className="text-base">{tenant.data?.description || '-'}</p>
                                </div>
                                <Separator />
                                <div>
                                    <p className="text-sm font-medium text-muted-foreground">Domain</p>
                                    <div className="flex items-center gap-2">
                                        <Globe className="h-4 w-4 text-muted-foreground" />
                                        <p className="text-base font-semibold">{tenant.domains[0]?.domain || '-'}</p>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>

                        <Card>
                            <CardHeader>
                                <CardTitle className="flex items-center gap-2">
                                    <Clock className="h-5 w-5" />
                                    Status Information
                                </CardTitle>
                                <CardDescription>Current tenant and provisioning status</CardDescription>
                            </CardHeader>
                            <CardContent className="space-y-4">
                                <div className="flex items-center justify-between">
                                    <p className="text-sm font-medium text-muted-foreground">Tenant Status</p>
                                    {getStatusBadge(tenant.status)}
                                </div>
                                <Separator />
                                <div className="flex items-center justify-between">
                                    <p className="text-sm font-medium text-muted-foreground">Provisioning Status</p>
                                    {getProvisioningBadge(tenant.provisioning_status)}
                                </div>
                                <Separator />
                                <div>
                                    <p className="text-sm font-medium text-muted-foreground">Created At</p>
                                    <p className="text-base">{formatDate(tenant.created_at)}</p>
                                </div>
                                <Separator />
                                <div>
                                    <p className="text-sm font-medium text-muted-foreground">Activated At</p>
                                    <p className="text-base">{formatDate(tenant.activated_at)}</p>
                                </div>
                                {tenant.deactivation_reason && (
                                    <>
                                        <Separator />
                                        <div>
                                            <p className="text-sm font-medium text-muted-foreground">Deactivation Reason</p>
                                            <p className="text-base text-destructive">{tenant.deactivation_reason}</p>
                                        </div>
                                    </>
                                )}
                            </CardContent>
                        </Card>
                    </div>

                    <div className="space-y-6">
                        <Card>
                            <CardHeader>
                                <CardTitle className="flex items-center gap-2">
                                    <User className="h-5 w-5" />
                                    Initial Administrator
                                </CardTitle>
                                <CardDescription>First administrator account for this tenant</CardDescription>
                            </CardHeader>
                            <CardContent className="space-y-4">
                                <div>
                                    <p className="text-sm font-medium text-muted-foreground">Administrator Name</p>
                                    <p className="text-base font-semibold">{tenant.data?.admin_name || '-'}</p>
                                </div>
                                <Separator />
                                <div>
                                    <p className="text-sm font-medium text-muted-foreground">Administrator Email</p>
                                    <p className="text-base font-semibold">{tenant.data?.admin_email || '-'}</p>
                                </div>
                                <Separator />
                                <div>
                                    <p className="text-sm font-medium text-muted-foreground">Role</p>
                                    <p className="text-base font-semibold">Tenant Administrator</p>
                                </div>
                            </CardContent>
                        </Card>

                        {temp_password && (
                            <Card className="border-primary bg-primary/5">
                                <CardHeader>
                                    <CardTitle className="flex items-center gap-2 text-primary">
                                        <User className="h-5 w-5" />
                                        Temporary Administrator Credentials
                                    </CardTitle>
                                    <CardDescription>Save these credentials - they will not be shown again</CardDescription>
                                </CardHeader>
                                <CardContent className="space-y-4">
                                    <div className="space-y-2">
                                        <p className="text-sm font-medium text-muted-foreground">Administrator Email</p>
                                        <p className="text-base font-semibold">{tenant.data?.admin_email || '-'}</p>
                                    </div>
                                    <Separator />
                                    <div className="space-y-2">
                                        <p className="text-sm font-medium text-muted-foreground">Temporary Password</p>
                                        <div className="flex items-center gap-2">
                                            <code className="flex-1 rounded bg-muted px-3 py-2 text-sm font-mono">
                                                {temp_password}
                                            </code>
                                            <Button
                                                variant="outline"
                                                size="sm"
                                                onClick={() => {
                                                    navigator.clipboard.writeText(temp_password);
                                                }}
                                            >
                                                <Copy className="h-4 w-4" />
                                            </Button>
                                        </div>
                                    </div>
                                    <Separator />
                                    <div className="rounded-lg bg-yellow-50 dark:bg-yellow-900/20 p-3">
                                        <p className="text-xs text-yellow-800 dark:text-yellow-200">
                                            <strong>Important:</strong> This is a temporary password. The administrator should change it immediately after first login. This password will not be shown again.
                                        </p>
                                    </div>
                                </CardContent>
                            </Card>
                        )}

                        <Card>
                            <CardHeader>
                                <CardTitle className="flex items-center gap-2">
                                    <Database className="h-5 w-5" />
                                    Database Information
                                </CardTitle>
                                <CardDescription>Tenant database status</CardDescription>
                            </CardHeader>
                            <CardContent className="space-y-4">
                                <div className="flex items-center justify-between">
                                    <p className="text-sm font-medium text-muted-foreground">Database Status</p>
                                    {tenant.provisioning_status === 'ready' ? (
                                        <Badge variant="default" className="gap-1">
                                            <CheckCircle className="h-3 w-3" />
                                            Provisioned
                                        </Badge>
                                    ) : tenant.provisioning_status === 'failed' ? (
                                        <Badge variant="destructive" className="gap-1">
                                            <XCircle className="h-3 w-3" />
                                            Failed
                                        </Badge>
                                    ) : (
                                        <Badge variant="secondary" className="gap-1">
                                            <Loader2 className="h-3 w-3 animate-spin" />
                                            {tenant.provisioning_status === 'pending' ? 'Pending' : 'Provisioning'}
                                        </Badge>
                                    )}
                                </div>
                                <Separator />
                                <div>
                                    <p className="text-sm font-medium text-muted-foreground">Database Name</p>
                                    <p className="text-base font-mono text-sm">tenant{tenant.id}</p>
                                </div>
                                <p className="text-xs text-muted-foreground">
                                    Database credentials are managed securely and are not displayed for security reasons.
                                </p>
                            </CardContent>
                        </Card>

                        {tenant.provisioning_status === 'provisioning' && (
                            <Card className="border-primary">
                                <CardHeader>
                                    <CardTitle className="flex items-center gap-2 text-primary">
                                        <Loader2 className="h-5 w-5 animate-spin" />
                                        Provisioning in Progress
                                    </CardTitle>
                                    <CardDescription>Your tenant is being set up. This may take a few moments.</CardDescription>
                                </CardHeader>
                                <CardContent className="space-y-3 text-sm">
                                    <div className="flex items-center gap-2">
                                        <div className="h-2 w-2 rounded-full bg-primary" />
                                        <p>Creating tenant database...</p>
                                    </div>
                                    <div className="flex items-center gap-2">
                                        <div className="h-2 w-2 rounded-full bg-primary/50" />
                                        <p>Running database migrations...</p>
                                    </div>
                                    <div className="flex items-center gap-2">
                                        <div className="h-2 w-2 rounded-full bg-primary/30" />
                                        <p>Configuring roles and permissions...</p>
                                    </div>
                                    <div className="flex items-center gap-2">
                                        <div className="h-2 w-2 rounded-full bg-primary/20" />
                                        <p>Creating administrator account...</p>
                                    </div>
                                </CardContent>
                            </Card>
                        )}

                        {tenant.provisioning_status === 'failed' && (
                            <Card className="border-destructive">
                                <CardHeader>
                                    <CardTitle className="flex items-center gap-2 text-destructive">
                                        <XCircle className="h-5 w-5" />
                                        Provisioning Failed
                                    </CardTitle>
                                    <CardDescription>Tenant provisioning encountered an error</CardDescription>
                                </CardHeader>
                                <CardContent>
                                    <p className="text-sm text-destructive">{tenant.deactivation_reason || 'An unknown error occurred during provisioning.'}</p>
                                    <p className="mt-2 text-xs text-muted-foreground">
                                        Please contact system administrator for assistance.
                                    </p>
                                </CardContent>
                            </Card>
                        )}
                    </div>
                </div>
            </PageContainer>
        </AppLayout>
    );
}
