import { Head, usePage } from '@inertiajs/react';
import type { Auth } from '@/types';

type PageProps = {
    auth: Auth;
};

export default function AdminIndex() {
    const { auth } = usePage<PageProps>().props;

    return (
        <>
            <Head title="Admin" />

            <div className="space-y-6 p-4">
                <section className="rounded-3xl border border-sidebar-border/70 bg-card p-6 shadow-sm dark:border-sidebar-border">
                    <div className="space-y-3">
                        <div>
                            <p className="text-sm font-semibold uppercase tracking-[0.12em] text-muted-foreground">
                                Admin area
                            </p>
                            <h1 className="mt-2 text-3xl font-semibold">Administration</h1>
                        </div>

                        <p className="max-w-2xl text-base text-muted-foreground">
                            You have administrator access because your account is assigned an admin role.
                            Use this section for admin-only tools, user management, and permissions.
                        </p>

                        <div className="rounded-2xl border border-muted/30 bg-muted/5 p-4 text-sm text-muted-foreground">
                            <p>
                                Logged in as <span className="font-semibold text-foreground">{auth.user.name}</span>.
                            </p>
                            <div className="mt-2">
                                <p className="mb-1">Permissions:</p>
                                <div className="flex flex-wrap gap-2">
                                    {auth.permissions?.length ? (
                                        auth.permissions.map(perm => (
                                            <span key={perm} className="rounded bg-emerald-500/10 px-2 py-1 text-emerald-600 dark:text-emerald-400">
                                                {perm}
                                            </span>
                                        ))
                                    ) : (
                                        <span className="text-muted-foreground">No permissions assigned</span>
                                    )}
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </>
    );
}

AdminIndex.layout = () => ({
    breadcrumbs: [
        {
            title: 'Admin',
            href: '/admin',
        },
    ],
});
