import { type ReactNode } from 'react';

interface SectionCardProps {
    title: string;
    description?: string;
    children: ReactNode;
    className?: string;
}

export function SectionCard({ title, description, children, className = '' }: SectionCardProps) {
    return (
        <div className={`rounded-lg border border-sidebar-border bg-sidebar p-6 ${className}`}>
            <div className="mb-4">
                <h3 className="text-lg font-semibold text-sidebar-foreground">{title}</h3>
                {description && (
                    <p className="mt-1 text-sm text-sidebar-foreground/70">{description}</p>
                )}
            </div>
            {children}
        </div>
    );
}
