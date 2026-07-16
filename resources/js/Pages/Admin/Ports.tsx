import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, router, useForm } from '@inertiajs/react';
import { useState, useEffect } from 'react';
import { MapContainer, TileLayer, Marker, useMapEvents } from 'react-leaflet';
import L from 'leaflet';

// Fix for default Leaflet markers in React
delete (L.Icon.Default.prototype as any)._getIconUrl;
L.Icon.Default.mergeOptions({
    iconRetinaUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-icon-2x.png',
    iconUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-icon.png',
    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png',
});

function MapClickHandler({ onLocationSelect }: { onLocationSelect: (lat: number, lng: number) => void }) {
    useMapEvents({
        click(e) {
            onLocationSelect(e.latlng.lat, e.latlng.lng);
        },
    });
    return null;
}

export default function Ports({ ports, countries = [] }: any) {
    const [editingPort, setEditingPort] = useState<any>(null);
    const [isAddingPort, setIsAddingPort] = useState(false);
    const [mapCenter, setMapCenter] = useState<[number, number]>([0, 0]);

    const { data, setData, put, post, processing, reset, errors } = useForm({
        port_code: '',
        port_name: '',
        country_id: '',
        latitude: '',
        longitude: '',
        status: 'active'
    });

    const openAddModal = () => {
        reset();
        setMapCenter([0, 0]); // Default center for new ports
        setIsAddingPort(true);
    };

    const openEditModal = (port: any) => {
        setEditingPort(port);
        setMapCenter([parseFloat(port.latitude) || 0, parseFloat(port.longitude) || 0]);
        setData({
            port_code: port.port_code,
            port_name: port.port_name,
            country_id: port.country_id || '',
            latitude: port.latitude,
            longitude: port.longitude,
            status: port.status
        });
    };

    const closeModal = () => {
        setEditingPort(null);
        setIsAddingPort(false);
        reset();
    };

    const submitEdit = (e: React.FormEvent) => {
        e.preventDefault();
        put(`/admin/ports/${editingPort.id}`, {
            onSuccess: () => closeModal(),
        });
    };

    const submitAdd = (e: React.FormEvent) => {
        e.preventDefault();
        post(`/admin/ports`, {
            onSuccess: () => closeModal(),
        });
    };

    const deletePort = (id: string) => {
        if (confirm('Apakah Anda yakin ingin menghapus pelabuhan ini?')) {
            router.delete(`/admin/ports/${id}`);
        }
    };

    return (
        <AuthenticatedLayout>
            <Head title="Manage Ports" />
            
            <div className="container-fluid p-0">
                <div className="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <Link href="/admin/dashboard" className="text-decoration-none mb-2 d-inline-block">&larr; Back to Admin Dashboard</Link>
                        <h2 className="fw-bold mb-0">Ports Dataset</h2>
                    </div>
                    <button onClick={openAddModal} className="btn btn-success">Add New Port</button>
                </div>

                <div className="panel-card">
                    <div className="table-responsive">
                        <table className="table table-hover align-middle">
                            <thead className="table-light">
                                <tr>
                                    <th>Port Code</th>
                                    <th>Port Name</th>
                                    <th>Country</th>
                                    <th>Coordinates</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                {ports.data.map((port: any) => (
                                    <tr key={port.id}>
                                        <td className="fw-bold">{port.port_code}</td>
                                        <td>{port.port_name}</td>
                                        <td>{port.country?.country_name || 'Unknown'}</td>
                                        <td>{port.latitude}, {port.longitude}</td>
                                        <td>
                                            <span className={`badge ${port.status === 'active' ? 'bg-success' : 'bg-secondary'}`}>
                                                {port.status}
                                            </span>
                                        </td>
                                        <td>
                                            <button onClick={() => openEditModal(port)} className="btn btn-sm btn-outline-primary me-2">Edit</button>
                                            <button onClick={() => deletePort(port.id)} className="btn btn-sm btn-outline-danger">Delete</button>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                    {ports.links && ports.links.length > 3 && (
                        <div className="d-flex justify-content-center mt-3 pb-3">
                            {ports.links.map((link: any, idx: number) => (
                                link.url ? (
                                    <Link 
                                        key={idx} 
                                        href={link.url} 
                                        className={`btn btn-sm mx-1 ${link.active ? 'btn-primary' : 'btn-outline-secondary'}`}
                                        dangerouslySetInnerHTML={{ __html: link.label }}
                                    />
                                ) : (
                                    <span 
                                        key={idx} 
                                        className="btn btn-sm mx-1 btn-outline-secondary disabled"
                                        dangerouslySetInnerHTML={{ __html: link.label }}
                                    />
                                )
                            ))}
                        </div>
                    )}
                </div>

                {/* Modal Overlay for Add/Edit */}
                {(isAddingPort || editingPort) && (
                    <div className="modal show d-block" tabIndex={-1} style={{ backgroundColor: 'rgba(0,0,0,0.5)' }}>
                        <div className="modal-dialog modal-dialog-centered">
                            <div className="modal-content">
                                <div className="modal-header">
                                    <h5 className="modal-title">
                                        {isAddingPort ? 'Add New Port' : `Edit Port: ${editingPort?.port_code}`}
                                    </h5>
                                    <button type="button" className="btn-close" onClick={closeModal}></button>
                                </div>
                                <form onSubmit={isAddingPort ? submitAdd : submitEdit}>
                                    <div className="modal-body">
                                        <div className="mb-3">
                                            <label className="form-label">Port Code</label>
                                            <input type="text" className="form-control" value={data.port_code} onChange={e => setData('port_code', e.target.value)} />
                                            {errors.port_code && <div className="text-danger small">{errors.port_code}</div>}
                                        </div>
                                        <div className="mb-3">
                                            <label className="form-label">Port Name</label>
                                            <input type="text" className="form-control" value={data.port_name} onChange={e => setData('port_name', e.target.value)} />
                                            {errors.port_name && <div className="text-danger small">{errors.port_name}</div>}
                                        </div>
                                        <div className="mb-3">
                                            <label className="form-label">Country</label>
                                            <select className="form-select" value={data.country_id} onChange={e => setData('country_id', e.target.value)}>
                                                <option value="">-- Select Country --</option>
                                                {countries.map((c: any) => (
                                                    <option key={c.id} value={c.id}>{c.country_name}</option>
                                                ))}
                                            </select>
                                            {errors.country_id && <div className="text-danger small">{errors.country_id}</div>}
                                        </div>
                                        <div className="row">
                                            <div className="col-md-6 mb-3">
                                                <label className="form-label">Latitude</label>
                                                <input type="number" step="0.0001" className="form-control" value={data.latitude} onChange={e => setData('latitude', e.target.value)} />
                                            </div>
                                            <div className="col-md-6 mb-3">
                                                <label className="form-label">Longitude</label>
                                                <input type="number" step="0.0001" className="form-control" value={data.longitude} onChange={e => setData('longitude', e.target.value)} />
                                            </div>
                                        </div>

                                        <div className="mb-3">
                                            <label className="form-label text-primary" style={{ fontSize: '0.85rem' }}>
                                                <span className="material-symbols-outlined align-middle me-1" style={{ fontSize: '16px' }}>location_on</span>
                                                Click on the map to automatically set coordinates
                                            </label>
                                            <div style={{ height: '250px', borderRadius: '8px', overflow: 'hidden', border: '1px solid var(--border-color)' }}>
                                                <MapContainer center={mapCenter} zoom={2} style={{ height: '100%', width: '100%' }}>
                                                    <TileLayer url="https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png" />
                                                    <MapClickHandler onLocationSelect={(lat, lng) => {
                                                        setData(prev => ({ ...prev, latitude: lat.toFixed(4) as any, longitude: lng.toFixed(4) as any }));
                                                    }} />
                                                    {data.latitude && data.longitude && (
                                                        <Marker position={[parseFloat(data.latitude), parseFloat(data.longitude)]} />
                                                    )}
                                                </MapContainer>
                                            </div>
                                        </div>

                                        <div className="mb-3">
                                            <label className="form-label">Status</label>
                                            <select className="form-select" value={data.status} onChange={e => setData('status', e.target.value)}>
                                                <option value="active">Active</option>
                                                <option value="inactive">Inactive</option>
                                                <option value="maintenance">Maintenance</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div className="modal-footer">
                                        <button type="button" className="btn btn-secondary" onClick={closeModal}>Cancel</button>
                                        <button type="submit" className="btn btn-primary" disabled={processing}>
                                            {processing ? 'Saving...' : 'Save Data'}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                )}
            </div>
        </AuthenticatedLayout>
    );
}
