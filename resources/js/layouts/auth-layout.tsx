import AuthLayoutTemplate from '@/layouts/auth/auth-simple-layout';

interface AuthLayoutProps {
    children: React.ReactNode;
    title: string;
    description: string;
    subtitle?: string;
    badge?: string;
}

export default function AuthLayout({ children, title, description, subtitle, badge, ...props }: AuthLayoutProps) {
    return (
        <AuthLayoutTemplate title={title} description={description} subtitle={subtitle} badge={badge} {...props}>
            {children}
        </AuthLayoutTemplate>
    );
}
