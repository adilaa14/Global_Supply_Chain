import InputError from '@/Components/InputError';
import GuestLayout from '@/Layouts/GuestLayout';
import { Head, Link, useForm } from '@inertiajs/react';
import { FormEventHandler } from 'react';

export default function Login({ status, canResetPassword }: { status?: string, canResetPassword?: boolean }) {
    const { data, setData, post, processing, errors, reset } = useForm({
        email: '',
        password: '',
        remember: false,
    });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        post(route('login'), {
            onFinish: () => reset('password'),
        });
    };

    return (
        <GuestLayout>
            <Head title="Log in" />

            <div className="login-card fade-up">
                <div className="login-logo text-center mb-8">
                    <span className="material-symbols-outlined text-[52px] mb-4" style={{ background: 'linear-gradient(135deg, var(--primary), #ff6b8b)', WebkitBackgroundClip: 'text', WebkitTextFillColor: 'transparent', display: 'inline-block', fontWeight: 'bold' }}>hub</span>
                    <h4 className="font-bold mb-1 tracking-tight text-2xl" style={{ background: 'linear-gradient(135deg, var(--secondary), #4a5568)', WebkitBackgroundClip: 'text', WebkitTextFillColor: 'transparent' }}>Global Chain</h4>
                    <p className="text-[var(--text-muted)] text-sm">Enterprise Decision Support System</p>
                </div>

                {status && (
                    <div className="mb-4 text-sm font-medium text-green-600">
                        {status}
                    </div>
                )}

                <form onSubmit={submit}>
                    <div className="mb-4">
                        <label className="block font-medium text-[0.85rem] ml-2 mb-1" style={{ color: 'var(--text-main)' }}>
                            Work Email
                        </label>
                        <input
                            type="email"
                            name="email"
                            value={data.email}
                            onChange={(e) => setData('email', e.target.value)}
                            className="form-control-glass w-full"
                            placeholder="Enter your corporate email"
                            required
                            autoFocus
                        />
                        <InputError message={errors.email} className="mt-2" />
                    </div>

                    <div className="mb-4">
                        <label className="flex justify-between font-medium text-[0.85rem] ml-2 mb-1" style={{ color: 'var(--text-main)' }}>
                            <span>Password</span>
                            {canResetPassword && (
                                <Link
                                    href={route('password.request')}
                                    className="no-underline hover:underline"
                                    style={{ color: 'var(--primary)' }}
                                >
                                    Forgot?
                                </Link>
                            )}
                        </label>
                        <input
                            type="password"
                            name="password"
                            value={data.password}
                            onChange={(e) => setData('password', e.target.value)}
                            className="form-control-glass w-full"
                            placeholder="••••••••"
                            required
                        />
                        <InputError message={errors.password} className="mt-2" />
                    </div>

                    <div className="mb-4 flex items-center ml-2">
                        <input
                            type="checkbox"
                            id="remember"
                            name="remember"
                            checked={data.remember}
                            onChange={(e) => setData('remember', e.target.checked)}
                            className="rounded border-gray-300 shadow-sm focus:ring-[var(--primary)]"
                            style={{ borderColor: 'var(--primary)', color: 'var(--primary)' }}
                        />
                        <label htmlFor="remember" className="ml-2 text-[0.85rem]" style={{ color: 'var(--text-muted)' }}>
                            Stay signed in
                        </label>
                    </div>

                    <button
                        type="submit"
                        className={`btn-login ${processing ? 'opacity-25' : ''}`}
                        disabled={processing}
                    >
                        Secure Login
                    </button>

                    <div className="text-center mt-6">
                        <p className="text-[0.85rem]" style={{ color: 'var(--text-muted)' }}>
                            Is your company new here?{' '}
                            <Link
                                href={route('register')}
                                className="font-semibold no-underline hover:underline"
                                style={{ color: 'var(--primary)' }}
                            >
                                Register Company
                            </Link>
                        </p>
                    </div>
                </form>
            </div>
        </GuestLayout>
    );
}
