import InputError from '@/components/input-error';
import { Head, useForm } from '@inertiajs/react';
import { FormEventHandler, useRef } from 'react';
import toast from 'react-hot-toast';

import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthLayout from '@/layouts/auth-layout';

interface PasswordPageProps {
    mustChangePassword?: boolean;
    tenantName?: string;
}

export default function TenantPasswordChange({ mustChangePassword = false, tenantName }: PasswordPageProps) {
    const passwordInput = useRef<HTMLInputElement>(null);
    const currentPasswordInput = useRef<HTMLInputElement>(null);

    const { data, setData, errors, put, reset, processing, recentlySuccessful } = useForm({
        current_password: '',
        password: '',
        password_confirmation: '',
    });

    const updatePassword: FormEventHandler = (e) => {
        e.preventDefault();

        put(route('tenant.settings.password.update'), {
            preserveScroll: true,
            onSuccess: () => {
                reset();
                toast.success('Password changed successfully');

                // If this was a forced password change, redirect to dashboard
                if (mustChangePassword) {
                    window.location.href = route('tenant.dashboard');
                }
            },
            onError: (errors) => {
                toast.error('Failed to change password. Please check your input.');

                if (errors.password) {
                    reset('password', 'password_confirmation');
                    passwordInput.current?.focus();
                }

                if (errors.current_password) {
                    reset('current_password');
                    currentPasswordInput.current?.focus();
                }
            },
        });
    };

    return (
        <AuthLayout
            title="AFNEN"
            subtitle="Agricultural Finance Network"
            badge={tenantName}
            description={mustChangePassword 
                ? "For your security, you must change your temporary password before continuing."
                : "Update your password to keep your account secure."
            }
        >
            <Head title={mustChangePassword ? "Change Password" : "Password Settings"} />

            <form onSubmit={updatePassword} className="flex flex-col gap-6">
                <div className="grid gap-6">
                    {mustChangePassword && (
                        <div className="rounded-md bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 p-4">
                            <p className="text-sm text-amber-800 dark:text-amber-200">
                                <strong>Security Required:</strong> You are using a temporary password. 
                                Please create a new password to continue accessing your account.
                            </p>
                        </div>
                    )}

                    <div className="grid gap-2">
                        <Label htmlFor="current_password">
                            {mustChangePassword ? "Temporary password" : "Current password"}
                        </Label>

                        <Input
                            id="current_password"
                            ref={currentPasswordInput}
                            value={data.current_password}
                            onChange={(e) => setData('current_password', e.target.value)}
                            type="password"
                            required
                            autoFocus
                            tabIndex={1}
                            autoComplete="current-password"
                            placeholder="••••••••"
                        />

                        <InputError message={errors.current_password} />
                    </div>

                    <div className="grid gap-2">
                        <Label htmlFor="password">New password</Label>

                        <Input
                            id="password"
                            ref={passwordInput}
                            value={data.password}
                            onChange={(e) => setData('password', e.target.value)}
                            type="password"
                            required
                            tabIndex={2}
                            autoComplete="new-password"
                            placeholder="••••••••"
                        />

                        <InputError message={errors.password} />
                    </div>

                    <div className="grid gap-2">
                        <Label htmlFor="password_confirmation">Confirm new password</Label>

                        <Input
                            id="password_confirmation"
                            value={data.password_confirmation}
                            onChange={(e) => setData('password_confirmation', e.target.value)}
                            type="password"
                            required
                            tabIndex={3}
                            autoComplete="new-password"
                            placeholder="••••••••"
                        />

                        <InputError message={errors.password_confirmation} />
                    </div>

                    <Button type="submit" className="mt-4 w-full" tabIndex={4} disabled={processing}>
                        {processing ? 'Changing...' : (mustChangePassword ? 'Set New Password' : 'Update Password')}
                    </Button>

                    {recentlySuccessful && !mustChangePassword && (
                        <p className="text-center text-sm text-green-600">Password updated successfully</p>
                    )}
                </div>
            </form>
        </AuthLayout>
    );
}