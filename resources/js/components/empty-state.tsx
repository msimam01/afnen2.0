import { type LucideIcon } from 'lucide-react';
import { type ReactNode } from 'react';

interface EmptyStateProps {
    icon?: LucideIcon;
    title: string;
    description: string;
    action?: ReactNode;
}

export function EmptyState({ icon: Icon, title, description, action }: EmptyStateProps) {
    return (
        <div className="flex flex-col items-center justify-center rounded-lg border border-sidebar-border bg-sidebar p-12 text-center">
            {Icon && (
                <div className="mb-4 rounded-full bg-sidebar-accent p-4 text-sidebar-accent-foreground">
                    <Icon />
                </div>
            )}
            <h3 className="text-lg font-semibold text-sidebar-foreground">{title}</h3>
            <p className="mt-2 text-sm text-sidebar-foreground/70 max-w-md">{description}</p>
            {action && <div className="mt-6">{action}</div>}
        </div>
    );
}
