import { type ReactNode } from 'react';

interface PageContainerProps {
    children: ReactNode;
    className?: string;
}

export function PageContainer({ children, className = '' }: PageContainerProps) {
    return (
        <div className={`space-y-6 p-6 ${className}`}>
            {children}
        </div>
    );
}
