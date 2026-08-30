import { Separator } from '@/components/ui/separator';
import { SidebarTrigger } from '@/components/ui/sidebar';
import { type BreadcrumbItem } from '@/types';
import { Breadcrumbs } from './breadcrumbs';
import { usePage } from '@inertiajs/react';

interface TenantSidebarHeaderProps {
    breadcrumbs?: BreadcrumbItem[];
}

export function TenantSidebarHeader({ breadcrumbs = [] }: TenantSidebarHeaderProps) {
    const { tenant } = usePage().props as any;

    return (
        <header className="flex h-16 shrink-0 items-center gap-2 border-b px-4">
            <div className="flex items-center gap-2">
                <SidebarTrigger className="-ml-1" />
                <Separator orientation="vertical" className="h-4 mr-2" />
                <div className="flex items-center gap-2 text-sm font-medium">
                    <span className="text-sidebar-foreground">AFNEN</span>
                    <Separator orientation="vertical" className="h-4" />
                    <span className="text-sidebar-foreground/70">{tenant?.name || 'Tenant'}</span>
                </div>
            </div>
            {breadcrumbs.length > 0 && <Breadcrumbs breadcrumbs={breadcrumbs} />}
        </header>
    );
}
