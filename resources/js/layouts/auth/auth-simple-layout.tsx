import AppLogoIcon from '@/components/app-logo-icon';
import { Link } from '@inertiajs/react';

interface AuthLayoutProps {
    children: React.ReactNode;
    name?: string;
    title?: string;
    description?: string;
    subtitle?: string;
    badge?: string;
}

export default function AuthSimpleLayout({ children, title, description, subtitle, badge }: AuthLayoutProps) {
    // Check if home route exists, otherwise render logo without link
    const hasHomeRoute = (window as any).route?.has?.('home') || false;

    return (
        <div className="bg-background flex min-h-svh flex-col items-center justify-center gap-6 p-6 md:p-10">
            <div className="w-full max-w-sm">
                <div className="flex flex-col gap-8">
                    <div className="flex flex-col items-center gap-4">
                        {hasHomeRoute ? (
                            <Link href={(window as any).route('home')} className="flex flex-col items-center gap-2 font-medium">
                                <div className="mb-1 flex h-9 w-9 items-center justify-center rounded-md">
                                    <AppLogoIcon className="size-9 fill-current text-[var(--foreground)] dark:text-white" />
                                </div>
                                <span className="sr-only">{title}</span>
                            </Link>
                        ) : (
                            <div className="flex flex-col items-center gap-2 font-medium">
                                <div className="mb-1 flex h-9 w-9 items-center justify-center rounded-md">
                                    <AppLogoIcon className="size-9 fill-current text-[var(--foreground)] dark:text-white" />
                                </div>
                                <span className="sr-only">{title}</span>
                            </div>
                        )}

                        <div className="space-y-2 text-center">
                            <h1 className="text-xl font-medium">{title}</h1>
                            {subtitle && <p className="text-sm text-gray-600 dark:text-gray-400">{subtitle}</p>}
                            {badge && (
                                <div className="mt-2 rounded-md bg-blue-50 dark:bg-blue-900/20 px-4 py-2">
                                    <p className="text-sm font-medium text-blue-700 dark:text-blue-300">{badge}</p>
                                </div>
                            )}
                            <p className="text-muted-foreground text-center text-sm">{description}</p>
                        </div>
                    </div>
                    {children}
                </div>
            </div>
        </div>
    );
}
