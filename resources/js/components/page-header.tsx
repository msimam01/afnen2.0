import { ArrowLeft } from 'lucide-react';
import { type ReactNode } from 'react';
import { Link } from '@inertiajs/react';

interface PageHeaderProps {
    title: string;
    description?: string;
    actions?: ReactNode;
    backButton?: boolean;
    backButtonHref?: string;
}

export function PageHeader({ title, description, actions, backButton, backButtonHref }: PageHeaderProps) {
    return (
        <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div className="flex items-center gap-4">
                {backButton && backButtonHref && (
                    <Link href={backButtonHref}>
                        <button className="rounded-md p-2 hover:bg-gray-100 dark:hover:bg-gray-800">
                            <ArrowLeft className="h-5 w-5 text-gray-600 dark:text-gray-400" />
                        </button>
                    </Link>
                )}
                <div>
                    <h1 className="text-3xl font-semibold text-gray-900 dark:text-gray-100">{title}</h1>
                    {description && (
                        <p className="mt-2 text-gray-600 dark:text-gray-400">{description}</p>
                    )}
                </div>
            </div>
            {actions && <div className="flex gap-2">{actions}</div>}
        </div>
    );
}
