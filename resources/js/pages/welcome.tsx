import { Head, Link, usePage } from '@inertiajs/react';
import { dashboard, login } from '@/routes';
import AppLogo from '@/components/app-logo';
import { register } from '@/routes';

export default function Welcome() {
    const { auth, currentTeam } = usePage().props;
    const dashboardUrl = currentTeam ? dashboard(currentTeam.slug) : '/';

    return (
        <>
            <Head title="Influx | Container Hosting" />

            <div className="min-h-screen bg-gradient-to-b from-[#f7f8ff] via-white to-[#f5f8ff] px-6 py-6 text-[#1b1b18] dark:from-[#08090c] dark:via-[#090a0f] dark:to-[#121418] dark:text-[#edecea]">
                <div className="mx-auto flex max-w-[1320px] flex-col gap-8">
                    <header className="flex flex-col gap-4 rounded-3xl border border-[#e6e8f0] bg-white/80 px-6 py-5 shadow-[0_28px_90px_-50px_rgba(45,58,100,0.25)] backdrop-blur dark:border-[#26272d] dark:bg-[#121318]/90">
                        <div className="flex items-center justify-between gap-4">
                            <div className="flex items-center gap-3">
                                <div className="flex h-12 w-12 items-center justify-center rounded-3xl bg-[#1f1f1f] text-white shadow-lg shadow-[#00000021] dark:bg-white dark:text-[#1b1b18]">
                                    <AppLogo />
                                </div>
                                <span className="hidden text-sm uppercase tracking-[0.35em] text-[#4b4d57] dark:text-[#888b97] sm:block">
                                    Influx
                                </span>
                            </div>

                            <nav className="flex items-center gap-3 text-sm">
                                {auth.user ? (
                                    <Link
                                        href={dashboardUrl}
                                        className="rounded-full border border-[#1b1b18] bg-[#1b1b18] px-5 py-2 text-sm font-semibold text-white transition hover:bg-[#000] dark:border-[#e6e6e6] dark:bg-[#e6e6e6] dark:text-[#1b1b18] dark:hover:bg-[#d4d4d4]"
                                    >
                                        Dashboard
                                    </Link>
                                ) : (
                                    <>
                                        <Link
                                            href={login()}
                                            className="rounded-full px-5 py-2 text-sm font-semibold text-[#1b1b18] transition hover:text-[#000] dark:text-[#e7e7e7]"
                                        >
                                            Log in
                                        </Link>
                                        <Link
                                            href={register()}
                                            className="rounded-full border border-[#1b1b18] bg-[#1b1b18] px-5 py-2 text-sm font-semibold text-white transition hover:bg-[#000] dark:border-[#e6e6e6] dark:bg-[#e6e6e6] dark:text-[#1b1b18] dark:hover:bg-[#d4d4d4]"
                                        >
                                            Register
                                        </Link>
                                    </>
                                )}
                            </nav>
                        </div>
                    </header>

                    <main className="grid gap-10 lg:grid-cols-[1.2fr_0.9fr] lg:items-center">
                        <section className="space-y-8">
                            <div className="inline-flex rounded-full bg-[#eef0ff] px-4 py-2 text-xs font-semibold uppercase tracking-[0.35em] text-[#4e5acd] dark:bg-[#232736] dark:text-[#b9baf3]">
                                Container hosting, simplified
                            </div>

                            <div className="space-y-6">
                                <h1 className="text-4xl font-semibold tracking-tight text-[#12131a] dark:text-white sm:text-5xl">
                                    Build, deploy, and manage container services with a single unified dashboard.
                                </h1>
                                <p className="max-w-2xl text-lg leading-8 text-[#5b5d69] dark:text-[#c7c7d1]">
                                    Influx brings together pay-as-you-go billing, preset container types, automatic deployments, and one control plane to keep your apps, databases, and AI services running smoothly.
                                </p>
                            </div>

                            <div className="flex flex-col gap-3 sm:flex-row">
                                <Link
                                    href={auth.user ? dashboardUrl : register()}
                                    className="inline-flex items-center justify-center rounded-full bg-[#1b1b18] px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-[#1b1b1820] transition hover:bg-[#000] dark:bg-[#e8e8e8] dark:text-[#1b1b18] dark:hover:bg-[#d4d4d4]"
                                >
                                    Start free today
                                </Link>
                                <a
                                    href="#features"
                                    className="inline-flex items-center justify-center rounded-full border border-[#1b1b18] px-6 py-3 text-sm font-semibold text-[#1b1b18] transition hover:bg-[#f7f7f9] dark:border-[#e6e6e6] dark:text-[#e6e6e6] dark:hover:bg-[#181a1f]"
                                >
                                    Explore features
                                </a>
                            </div>

                            <div className="grid gap-4 sm:grid-cols-3">
                                <div className="rounded-[1.75rem] border border-[#e7e7ff] bg-white p-5 shadow-sm dark:border-[#272a34] dark:bg-[#141519]">
                                    <p className="text-sm font-semibold text-[#1b1b18] dark:text-white">Transparent pricing</p>
                                    <p className="mt-2 text-sm leading-6 text-[#5b5d69] dark:text-[#b8b9c4]">
                                        Usage-based billing for compute, storage, and bandwidth with no lock-ins.
                                    </p>
                                </div>
                                <div className="rounded-[1.75rem] border border-[#e7e7ff] bg-white p-5 shadow-sm dark:border-[#272a34] dark:bg-[#141519]">
                                    <p className="text-sm font-semibold text-[#1b1b18] dark:text-white">Fast deployment</p>
                                    <p className="mt-2 text-sm leading-6 text-[#5b5d69] dark:text-[#b8b9c4]">
                                        One-click deployments from Git, container registry, or preset service templates.
                                    </p>
                                </div>
                                <div className="rounded-[1.75rem] border border-[#e7e7ff] bg-white p-5 shadow-sm dark:border-[#272a34] dark:bg-[#141519]">
                                    <p className="text-sm font-semibold text-[#1b1b18] dark:text-white">Unified dashboard</p>
                                    <p className="mt-2 text-sm leading-6 text-[#5b5d69] dark:text-[#b8b9c4]">
                                        Monitor services, scale containers, and manage teams from one control plane.
                                    </p>
                                </div>
                            </div>
                        </section>

                        <section className="rounded-[2rem] border border-[#e7e7ff] bg-white p-8 shadow-[0_30px_90px_-45px_rgba(20,33,70,0.12)] dark:border-[#23272f] dark:bg-[#111316]">
                            <div className="space-y-6">
                                <div className="rounded-3xl bg-[#f5f6ff] p-5 dark:bg-[#171a23]">
                                    <p className="text-xs uppercase tracking-[0.35em] text-[#5b60f8] dark:text-[#9f9fff]">Dashboard preview</p>
                                    <p className="mt-4 text-lg font-semibold text-[#12131a] dark:text-white">Control every container from a single panel.</p>
                                </div>

                                <div className="grid gap-4">
                                    <div className="rounded-[1.5rem] border border-[#eef0ff] bg-white p-5 shadow-sm dark:border-[#23272f] dark:bg-[#111316]">
                                        <div className="flex items-center justify-between gap-4">
                                            <div>
                                                <p className="text-sm font-semibold text-[#1b1b18] dark:text-white">Service status</p>
                                                <p className="mt-1 text-sm text-[#6b6d79] dark:text-[#a9abb7]">Auto-deploy, health checks, and live logs.</p>
                                            </div>
                                            <span className="rounded-full bg-[#eef0ff] px-3 py-1 text-xs font-semibold uppercase text-[#4f5ccd] dark:bg-[#222634] dark:text-[#afb2ff]">
                                                Live
                                            </span>
                                        </div>
                                    </div>
                                    <div className="rounded-[1.5rem] border border-[#eef0ff] bg-white p-5 shadow-sm dark:border-[#23272f] dark:bg-[#111316]">
                                        <p className="text-sm font-semibold text-[#1b1b18] dark:text-white">Preset container types</p>
                                        <div className="mt-3 grid gap-2 text-sm text-[#5b5d69] dark:text-[#b8b9c4]">
                                            <span>• Web apps</span>
                                            <span>• Databases</span>
                                            <span>• Machine learning</span>
                                        </div>
                                    </div>
                                    <div className="rounded-[1.5rem] border border-[#eef0ff] bg-white p-5 shadow-sm dark:border-[#23272f] dark:bg-[#111316]">
                                        <p className="text-sm font-semibold text-[#1b1b18] dark:text-white">Quick actions</p>
                                        <div className="mt-3 grid gap-2 text-sm text-[#5b5d69] dark:text-[#b8b9c4]">
                                            <span>• Scale containers</span>
                                            <span>• Manage teams</span>
                                            <span>• Track spend</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </main>

                    <section id="features" className="grid gap-8 lg:grid-cols-3">
                        <div className="rounded-[2rem] border border-[#e7e7ff] bg-white p-8 shadow-sm dark:border-[#23272f] dark:bg-[#111316]">
                            <h2 className="text-xl font-semibold text-[#12131a] dark:text-white">Managed container types</h2>
                            <p className="mt-3 text-sm leading-6 text-[#5b5d69] dark:text-[#b8b9c4]">Choose from ready-made container stacks built for modern workloads without infrastructure setup.</p>
                            <ul className="mt-6 space-y-4 text-sm text-[#5b5d69] dark:text-[#b8b9c4]">
                                <li>• App containers with auto-deploy and HTTPS</li>
                                <li>• Database containers with backups and scaling</li>
                                <li>• GPU/AI containers for compute-heavy workloads</li>
                            </ul>
                        </div>
                        <div className="rounded-[2rem] border border-[#e7e7ff] bg-white p-8 shadow-sm dark:border-[#23272f] dark:bg-[#111316]">
                            <h2 className="text-xl font-semibold text-[#12131a] dark:text-white">Pay-as-you-go billing</h2>
                            <p className="mt-3 text-sm leading-6 text-[#5b5d69] dark:text-[#b8b9c4]">Only pay for active containers, network usage, and storage with daily cost visibility and team spend controls.</p>
                            <ul className="mt-6 space-y-4 text-sm text-[#5b5d69] dark:text-[#b8b9c4]">
                                <li>• Auto-scaling cost controls</li>
                                <li>• Usage insights per container</li>
                                <li>• Budget alerts and billing reports</li>
                            </ul>
                        </div>
                        <div className="rounded-[2rem] border border-[#e7e7ff] bg-white p-8 shadow-sm dark:border-[#23272f] dark:bg-[#111316]">
                            <h2 className="text-xl font-semibold text-[#12131a] dark:text-white">Automatic deployment</h2>
                            <p className="mt-3 text-sm leading-6 text-[#5b5d69] dark:text-[#b8b9c4]">Deploy from Git, container images, or presets in minutes with zero-touch build and release workflows.</p>
                            <ul className="mt-6 space-y-4 text-sm text-[#5b5d69] dark:text-[#b8b9c4]">
                                <li>• One-click Git sync</li>
                                <li>• Auto build and deploy</li>
                                <li>• Health checks and rollback</li>
                            </ul>
                        </div>
                    </section>

                    <section className="rounded-[2rem] border border-[#e9ebf4] bg-white p-8 shadow-[0_30px_60px_-35px_rgba(41,58,97,0.12)] dark:border-[#24272d] dark:bg-[#101214]">
                        <div className="grid gap-8 lg:grid-cols-2">
                            <div>
                                <p className="text-sm font-semibold uppercase tracking-[0.3em] text-[#4e5acd] dark:text-[#9d9fff]">Trusted by container-first teams</p>
                                <h2 className="mt-4 text-3xl font-semibold text-[#12131a] dark:text-white">A modern operations experience for every team.</h2>
                                <p className="mt-5 text-sm leading-7 text-[#5b5d69] dark:text-[#b8b9c4]">Securely manage service lifecycles, deploy updates automatically, and keep budgets under control with a platform built for distributed apps and microservices.</p>
                                <div className="mt-8 flex flex-col gap-3 sm:flex-row">
                                    <Link
                                        href={auth.user ? dashboardUrl : register()}
                                        className="inline-flex items-center justify-center rounded-full bg-[#1b1b18] px-6 py-3 text-sm font-semibold text-white transition hover:bg-[#000] dark:bg-[#e8e8e8] dark:text-[#1b1b18] dark:hover:bg-[#d4d4d4]"
                                    >
                                        Start your trial
                                    </Link>
                                    <a
                                        href="#features"
                                        className="inline-flex items-center justify-center rounded-full border border-[#1b1b18] px-6 py-3 text-sm font-semibold text-[#1b1b18] transition hover:bg-[#f7f7f9] dark:border-[#e6e6e6] dark:text-[#e6e6e6] dark:hover:bg-[#181a1f]"
                                    >
                                        View plans
                                    </a>
                                </div>
                            </div>
                            <div className="grid gap-4">
                                <div className="rounded-[1.75rem] bg-[#f6f7ff] p-6 dark:bg-[#161820]">
                                    <p className="text-sm font-semibold text-[#1b1b18] dark:text-white">Active services</p>
                                    <div className="mt-5 grid gap-4">
                                        <div className="rounded-3xl bg-white p-4 shadow-sm dark:bg-[#0d1116]">
                                            <p className="text-sm font-semibold text-[#1b1b18] dark:text-white">Web App Container</p>
                                            <p className="mt-2 text-sm text-[#5b5d69] dark:text-[#b8b9c4]">Running 14 containers with automatic HTTPS.</p>
                                        </div>
                                        <div className="rounded-3xl bg-white p-4 shadow-sm dark:bg-[#0d1116]">
                                            <p className="text-sm font-semibold text-[#1b1b18] dark:text-white">Database Cluster</p>
                                            <p className="mt-2 text-sm text-[#5b5d69] dark:text-[#b8b9c4]">Managed backups, monitoring, and smart scaling.</p>
                                        </div>
                                    </div>
                                </div>
                                <div className="rounded-[1.75rem] bg-[#eef1ff] p-6 dark:bg-[#161820]">
                                    <p className="text-sm font-semibold text-[#1b1b18] dark:text-white">Billing snapshot</p>
                                    <div className="mt-5 grid gap-3 text-sm text-[#5b5d69] dark:text-[#b8b9c4]">
                                        <span>• $0.43 / runtime hour</span>
                                        <span>• $0.08 / GB storage</span>
                                        <span>• $0.12 / GB bandwidth</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <footer className="border-t border-[#e6e8f0] py-6 text-sm text-[#5b5d69] dark:border-[#23272f] dark:text-[#9b9da7]">
                        <div className="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <p>© 2026 Influx. Container hosting for teams and modern services.</p>
                            <p>Secure deployment, usage-based billing, and one dashboard.</p>
                        </div>
                    </footer>
                </div>
            </div>
        </>
    );
}
