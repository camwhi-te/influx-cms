import type { Auth } from '@/types/auth';
import type { Group } from '@/types/groups';

declare module 'react' {
    // eslint-disable-next-line @typescript-eslint/no-unused-vars
    interface InputHTMLAttributes<T> {
        passwordrules?: string;
    }
}

declare module '@inertiajs/core' {
    export interface InertiaConfig {
        sharedPageProps: {
            name: string;
            auth: Auth;
            sidebarOpen: boolean;
            currentGroup: Group | null;
            groups: Group[];
            [key: string]: unknown;
        };
    }
}
