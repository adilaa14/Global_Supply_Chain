import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageProps } from '@/types';
import { Head } from '@inertiajs/react';
import DeleteUserForm from './Partials/DeleteUserForm';
import UpdatePasswordForm from './Partials/UpdatePasswordForm';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm';

export default function Edit({
    mustVerifyEmail,
    status,
}: PageProps<{ mustVerifyEmail: boolean; status?: string }>) {
    return (
        <AuthenticatedLayout
            header={
                <div className="d-flex justify-content-between align-items-center mb-4 pb-2 fade-up">
                    <h2 className="fw-bold mb-1" style={{ color: 'var(--secondary)' }}>
                        Profile Settings
                    </h2>
                </div>
            }
        >
            <Head title="Profile" />

            <div className="container-fluid p-0">
                <div className="row g-4 mb-4 fade-up">
                    <div className="col-12">
                        <div className="panel-card p-4 sm:p-8">
                            <UpdateProfileInformationForm
                                mustVerifyEmail={mustVerifyEmail}
                                status={status}
                                className="max-w-xl"
                            />
                        </div>
                    </div>

                    <div className="col-12">
                        <div className="panel-card p-4 sm:p-8">
                            <UpdatePasswordForm className="max-w-xl" />
                        </div>
                    </div>

                    <div className="col-12">
                        <div className="panel-card p-4 sm:p-8">
                            <DeleteUserForm className="max-w-xl" />
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
