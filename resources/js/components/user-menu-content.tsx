import { DropdownMenuGroup, DropdownMenuItem, DropdownMenuLabel, DropdownMenuSeparator } from '@/components/ui/dropdown-menu';
import { UserInfo } from '@/components/user-info';
import { useMobileNavigation } from '@/hooks/use-mobile-navigation';
import { type User } from '@/types';
import { Link } from '@inertiajs/react';
import { LogOut, Settings } from 'lucide-react';

interface UserMenuContentProps {
    user: User;
}

export function UserMenuContent({ user }: UserMenuContentProps) {
    const cleanup = useMobileNavigation();

    // Check which logout route is available (central or tenant)
    // Tenant routes are named with 'tenant.' prefix
    const hasTenantLogout = (window as any).route?.has?.('tenant.logout');
    const hasCentralLogout = (window as any).route?.has?.('logout');

    // Debug: log available routes
    console.log('Available routes:', (window as any).route?.list?.());
    console.log('Has tenant.logout:', hasTenantLogout);
    console.log('Has logout:', hasCentralLogout);

    const logoutRoute = hasTenantLogout
        ? (window as any).route('tenant.logout')
        : hasCentralLogout
        ? (window as any).route('logout')
        : '/logout';

    console.log('Logout route:', logoutRoute);

    // Check if profile.edit route exists
    const hasProfileRoute = (window as any).route?.has?.('profile.edit');
    const profileRoute = hasProfileRoute ? (window as any).route('profile.edit') : '#';

    return (
        <>
            <DropdownMenuLabel className="p-0 font-normal">
                <div className="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
                    <UserInfo user={user} showEmail={true} />
                </div>
            </DropdownMenuLabel>
            <DropdownMenuSeparator />
            <DropdownMenuGroup>
                {hasProfileRoute && (
                    <DropdownMenuItem asChild>
                        <Link className="block w-full" href={profileRoute} as="button" prefetch onClick={cleanup}>
                            <Settings className="mr-2" />
                            Settings
                        </Link>
                    </DropdownMenuItem>
                )}
            </DropdownMenuGroup>
            <DropdownMenuSeparator />
            <DropdownMenuItem asChild>
                <Link className="block w-full" method="post" href={logoutRoute} as="button" onClick={cleanup}>
                    <LogOut className="mr-2" />
                    Log out
                </Link>
            </DropdownMenuItem>
        </>
    );
}
