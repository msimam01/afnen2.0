import { type LucideIcon } from 'lucide-react';

interface StatCardProps {
    title: string;
    value: string | number;
    icon: LucideIcon;
    description?: string;
    trend?: {
        value: string;
        positive: boolean;
    };
}

export function StatCard({ title, value, icon: Icon, description, trend }: StatCardProps) {
    return (
        <div className="rounded-lg border border-sidebar-border bg-sidebar p-6">
            <div className="flex items-center justify-between">
                <div>
                    <p className="text-sm font-medium text-sidebar-foreground/70">{title}</p>
                    <p className="mt-2 text-3xl font-semibold text-sidebar-foreground">{value}</p>
                </div>
                <div className="rounded-full bg-sidebar-accent p-3 text-sidebar-accent-foreground">
                    <Icon />
                </div>
            </div>
            {description && (
                <p className="mt-4 text-sm text-sidebar-foreground/70">{description}</p>
            )}
            {trend && (
                <p className={`mt-2 text-sm ${trend.positive ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'}`}>
                    {trend.value}
                </p>
            )}
        </div>
    );
}
