import InputError from '@/Components/InputError';
import GuestLayout from '@/Layouts/GuestLayout';
import { Head, Link, useForm } from '@inertiajs/react';
import { FormEventHandler } from 'react';

export default function Register() {
    const { data, setData, post, processing, errors, reset } = useForm({
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
    });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();

        post(route('register'), {
            onFinish: () => reset('password', 'password_confirmation'),
        });
    };

    return (
        <GuestLayout>
            <Head title="Register Company" />

            <div className="login-card fade-up">
                <div className="login-logo text-center mb-6">
                    <span className="material-symbols-outlined text-[42px] text-[var(--primary)] mb-3">domain_add</span>
                    <h4 className="font-bold text-[var(--secondary)] mb-1 tracking-tight text-xl">Company Registration</h4>
                    <p className="text-[var(--text-muted)] text-sm">Join the G-SCRI Enterprise Network</p>
                </div>

                <form onSubmit={submit}>
                    <div className="mb-4">
                        <label className="block font-medium text-[0.85rem] ml-2 mb-1" style={{ color: 'var(--text-main)' }}>
                            Full Name
                        </label>
                        <input
                            type="text"
                            name="name"
                            value={data.name}
                            onChange={(e) => setData('name', e.target.value)}
                            className="form-control-glass w-full"
                            placeholder="John Doe"
                            required
                            autoFocus
                        />
                        <InputError message={errors.name} className="mt-2" />
                    </div>

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
                            placeholder="you@company.com"
                            required
                        />
                        <InputError message={errors.email} className="mt-2" />
                    </div>

                    <div className="mb-4">
                        <label className="block font-medium text-[0.85rem] ml-2 mb-1" style={{ color: 'var(--text-main)' }}>
                            Password
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

                    <div className="mb-5">
                        <label className="block font-medium text-[0.85rem] ml-2 mb-1" style={{ color: 'var(--text-main)' }}>
                            Confirm Password
                        </label>
                        <input
                            type="password"
                            name="password_confirmation"
                            value={data.password_confirmation}
                            onChange={(e) => setData('password_confirmation', e.target.value)}
                            className="form-control-glass w-full"
                            placeholder="••••••••"
                            required
                        />
                        <InputError message={errors.password_confirmation} className="mt-2" />
                    </div>

                    <button
                        type="submit"
                        className={`btn-login ${processing ? 'opacity-25' : ''}`}
                        disabled={processing}
                    >
                        Register Account
                    </button>

                    <div className="text-center mt-6">
                        <p className="text-[0.85rem]" style={{ color: 'var(--text-muted)' }}>
                            Already registered?{' '}
                            <Link
                                href={route('login')}
                                className="font-semibold no-underline hover:underline"
                                style={{ color: 'var(--primary)' }}
                            >
                                Log in
                            </Link>
                        </p>
                    </div>
                </form>
            </div>
        </GuestLayout>
    );
}
