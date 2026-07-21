import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, router } from '@inertiajs/react';
import { useState, useEffect } from 'react';
import axios from 'axios';
import Select from 'react-select';

export default function ShipmentCreate() {
    const [step, setStep] = useState(1);
    const [loading, setLoading] = useState(false);
    const [ports, setPorts] = useState([]);
    const [vessels, setVessels] = useState([]);
    const [commodities, setCommodities] = useState<any[]>([]);
    const [formData, setFormData] = useState({
        shipment_number: '',
        shipment_type: 'Export',
        origin_port_id: '',
        destination_port_id: '',
        priority: 'Normal',
        vessel_id: '',
        commodity_id: ''
    });
    const [cargoData, setCargoData] = useState({
        quantity: '',
        weight: '',
        estimated_value: ''
    });
    const [containerData, setContainerData] = useState({
        container_number: '',
        container_type: 'Dry',
        container_size: '20ft'
    });

    useEffect(() => {
        axios.get('/api/tracking/ports/list').then(res => setPorts(res.data));
        axios.get('/api/tracking/vessels').then(res => setVessels(res.data));
        axios.get('/api/commodities/list').then(res => setCommodities(res.data));
    }, []);

    const handleChange = (e: any) => {
        setFormData({ ...formData, [e.target.name]: e.target.value });
    };

    const handleCargoChange = (e: any) => {
        setCargoData({ ...cargoData, [e.target.name]: e.target.value });
    };

    const handleContainerChange = (e: any) => {
        setContainerData({ ...containerData, [e.target.name]: e.target.value });
    };

    const handleNext = () => {
        if (step < 6) setStep(step + 1);
    };

    const handleBack = () => {
        if (step > 1) setStep(step - 1);
    };

    const handleSubmit = async (e: any) => {
        e.preventDefault();
        setLoading(true);
        try {
            // 1. Create Core Shipment
            const res = await axios.post('/api/shipments', formData);
            const shipmentId = res.data.data.id;

            // 2. Update Cargo Details
            await axios.put(`/api/shipments/${shipmentId}`, cargoData);

            // 3. Add Container (if container number is provided)
            if (containerData.container_number.trim() !== '') {
                await axios.post('/api/shipments/containers', { ...containerData, shipment_id: shipmentId });
            }

            router.visit(`/shipments/${shipmentId}`);
        } catch (error: any) {
            console.error('Error creating shipment', error);
            if (error.response && error.response.data && error.response.data.errors) {
                const errorMessages = Object.values(error.response.data.errors).flat().join('\n');
                alert(`Validation Error:\n${errorMessages}`);
            } else if (error.response && error.response.data && error.response.data.message) {
                alert(`Error: ${error.response.data.message}`);
            } else {
                alert('An unexpected error occurred while creating the shipment.');
            }
            setLoading(false);
        }
    };

    const getPortName = (id: string) => {
        const port: any = ports.find((p: any) => p.id === id);
        return port ? `${port.port_name}, ${port.country?.country_name}` : 'Unknown';
    };

    const getVesselName = (id: string) => {
        const vessel: any = vessels.find((v: any) => v.id === id);
        return vessel ? vessel.name : 'Not Assigned';
    };

    const portOptions = ports.map((p: any) => ({
        value: p.id,
        label: `${p.port_name} (${p.country?.country_name})`
    }));

    const selectStyles = {
        control: (base: any) => ({
            ...base,
            background: 'rgba(255, 255, 255, 0.7)',
            backdropFilter: 'blur(10px)',
            border: '1px solid rgba(255, 255, 255, 0.3)',
            borderRadius: '0.5rem',
            padding: '2px',
            boxShadow: 'none',
            '&:hover': {
                border: '1px solid rgba(255, 255, 255, 0.5)'
            }
        }),
        menu: (base: any) => ({
            ...base,
            background: 'rgba(255, 255, 255, 0.95)',
            backdropFilter: 'blur(10px)',
            zIndex: 100
        }),
        option: (base: any, state: any) => ({
            ...base,
            background: state.isSelected ? '#0d6efd' : state.isFocused ? 'rgba(13, 110, 253, 0.1)' : 'transparent',
            color: state.isSelected ? 'white' : 'black',
            cursor: 'pointer'
        })
    };

    return (
        <AuthenticatedLayout>
            <Head title="Create Shipment" />

            <div className="container-fluid p-0">
                <div className="d-flex justify-content-between align-items-center mb-4 pb-2 fade-up">
                    <div>
                        <h2 className="fw-bold mb-1" style={{ color: 'var(--secondary)' }}>Create New Shipment</h2>
                        <p className="text-muted mb-0">Step {step} of 6</p>
                    </div>
                    <Link href="/shipments" className="btn btn-outline-secondary rounded-pill">
                        Cancel
                    </Link>
                </div>

                <div className="panel-card fade-up" style={{ animationDelay: '0.2s', maxWidth: '800px', margin: '0 auto' }}>
                    <form onSubmit={step === 6 ? handleSubmit : (e) => { e.preventDefault(); handleNext(); }}>
                        
                        {step === 1 && (
                            <div className="wizard-step">
                                <h5 className="mb-4 fw-bold">Step 1: Basic Information</h5>
                                <div className="mb-3">
                                    <label className="form-label text-muted small fw-bold">Shipment Number</label>
                                    <input type="text" className="form-control-glass" name="shipment_number" value={formData.shipment_number} onChange={handleChange} required placeholder="e.g. SHP-2026-001" />
                                </div>
                                <div className="mb-3">
                                    <label className="form-label text-muted small fw-bold">Shipment Type</label>
                                    <select className="form-control-glass" name="shipment_type" value={formData.shipment_type} onChange={handleChange}>
                                        <option value="Export">Export</option>
                                        <option value="Import">Import</option>
                                    </select>
                                </div>
                                <div className="mb-3">
                                    <label className="form-label text-muted small fw-bold">Priority</label>
                                    <select className="form-control-glass" name="priority" value={formData.priority} onChange={handleChange}>
                                        <option value="Normal">Normal</option>
                                        <option value="High">High</option>
                                        <option value="Critical">Critical</option>
                                    </select>
                                </div>
                            </div>
                        )}

                        {step === 2 && (
                            <div className="wizard-step">
                                <h5 className="mb-4 fw-bold">Step 2: Routing</h5>
                                <div className="row">
                                    <div className="col-md-6 mb-3">
                                        <label className="form-label text-muted small fw-bold">Origin Port</label>
                                        <Select 
                                            options={portOptions}
                                            styles={selectStyles}
                                            value={portOptions.find(o => o.value === formData.origin_port_id) || null}
                                            onChange={(selected: any) => setFormData({ ...formData, origin_port_id: selected ? selected.value : '' })}
                                            placeholder="Select Origin Port"
                                            isClearable
                                            isSearchable
                                        />
                                    </div>
                                    <div className="col-md-6 mb-3">
                                        <label className="form-label text-muted small fw-bold">Destination Port</label>
                                        <Select 
                                            options={portOptions}
                                            styles={selectStyles}
                                            value={portOptions.find(o => o.value === formData.destination_port_id) || null}
                                            onChange={(selected: any) => setFormData({ ...formData, destination_port_id: selected ? selected.value : '' })}
                                            placeholder="Select Destination Port"
                                            isClearable
                                            isSearchable
                                        />
                                    </div>
                                </div>
                            </div>
                        )}

                        {step === 3 && (
                            <div className="wizard-step">
                                <h5 className="mb-4 fw-bold">Step 3: Vessel Assignment</h5>
                                <div className="mb-3">
                                    <label className="form-label text-muted small fw-bold">Assign to Active Fleet Vessel</label>
                                    <select className="form-control-glass" name="vessel_id" value={formData.vessel_id} onChange={handleChange} required>
                                        <option value="">Select a Vessel (Kapal)</option>
                                        {vessels.map((v: any) => (
                                            <option key={v.id} value={v.id}>{v.name} ({v.vessel_type})</option>
                                        ))}
                                    </select>
                                    <small className="text-muted d-block mt-2">Selecting a vessel will instruct the global fleet engine to redirect this vessel to your destination port automatically.</small>
                                </div>
                            </div>
                        )}

                        {step === 4 && (
                            <div className="wizard-step">
                                <h5 className="mb-4 fw-bold">Step 4: Cargo Details</h5>
                                <div className="mb-3">
                                    <label className="form-label text-muted small fw-bold">Commodity</label>
                                    <select className="form-control-glass" name="commodity_id" value={formData.commodity_id} onChange={handleChange} required>
                                        <option value="">Select a Commodity</option>
                                        {commodities.map((c: any) => (
                                            <option key={c.id} value={c.id}>{c.commodity_name} ({c.commodity_code})</option>
                                        ))}
                                    </select>
                                </div>
                                <div className="mb-3">
                                    <label className="form-label text-muted small fw-bold">Total Quantity</label>
                                    <input type="number" className="form-control-glass" name="quantity" value={cargoData.quantity} onChange={handleCargoChange} placeholder="e.g. 1000" />
                                </div>
                                <div className="mb-3">
                                    <label className="form-label text-muted small fw-bold">Total Weight (KG)</label>
                                    <input type="number" className="form-control-glass" name="weight" value={cargoData.weight} onChange={handleCargoChange} placeholder="e.g. 5000" />
                                </div>
                                <div className="mb-3">
                                    <label className="form-label text-muted small fw-bold">Estimated Value (USD)</label>
                                    <input type="number" className="form-control-glass" name="estimated_value" value={cargoData.estimated_value} onChange={handleCargoChange} placeholder="e.g. 250000" />
                                </div>
                            </div>
                        )}

                        {step === 5 && (
                            <div className="wizard-step">
                                <h5 className="mb-4 fw-bold">Step 5: Add Primary Container (Optional)</h5>
                                <div className="mb-3">
                                    <label className="form-label text-muted small fw-bold">Container Number</label>
                                    <input type="text" className="form-control-glass" name="container_number" value={containerData.container_number} onChange={handleContainerChange} placeholder="e.g. MSKU1234567" />
                                </div>
                                <div className="row">
                                    <div className="col-md-6 mb-3">
                                        <label className="form-label text-muted small fw-bold">Container Type</label>
                                        <select className="form-control-glass" name="container_type" value={containerData.container_type} onChange={handleContainerChange}>
                                            <option value="Dry">Dry</option>
                                            <option value="Reefer">Reefer</option>
                                            <option value="Flat Rack">Flat Rack</option>
                                        </select>
                                    </div>
                                    <div className="col-md-6 mb-3">
                                        <label className="form-label text-muted small fw-bold">Container Size</label>
                                        <select className="form-control-glass" name="container_size" value={containerData.container_size} onChange={handleContainerChange}>
                                            <option value="20ft">20ft</option>
                                            <option value="40ft">40ft</option>
                                            <option value="40ft HC">40ft HC</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        )}

                        {step === 6 && (
                            <div className="wizard-step">
                                <h5 className="mb-4 fw-bold">Step 6: Review & Submit</h5>
                                <div className="p-4 rounded" style={{ background: 'rgba(255,255,255,0.4)', border: '1px solid var(--glass-border)' }}>
                                    <h6 className="fw-bold">Summary</h6>
                                    <hr />
                                    <p className="mb-1"><strong>Shipment Number:</strong> {formData.shipment_number}</p>
                                    <p className="mb-1"><strong>Type:</strong> {formData.shipment_type} ({formData.priority} Priority)</p>
                                    <p className="mb-1"><strong>Route:</strong> {getPortName(formData.origin_port_id)} &rarr; {getPortName(formData.destination_port_id)}</p>
                                    <p className="mb-1"><strong>Assigned Vessel:</strong> {getVesselName(formData.vessel_id)}</p>
                                    <hr />
                                    <p className="mb-1"><strong>Cargo Weight:</strong> {cargoData.weight ? `${cargoData.weight} KG` : 'Not Set'}</p>
                                    <p className="mb-1"><strong>Commodity:</strong> {commodities.find((c: any) => c.id === formData.commodity_id)?.commodity_name || 'Not Set'}</p>
                                    <p className="mb-1"><strong>Container:</strong> {containerData.container_number || 'None assigned yet'}</p>
                                </div>
                            </div>
                        )}

                        <div className="d-flex justify-content-between mt-5 pt-3 border-top">
                            <button type="button" className="btn btn-outline-secondary rounded-pill px-4" onClick={handleBack} disabled={step === 1}>
                                Back
                            </button>
                            {step < 6 ? (
                                <button type="button" className="btn-primary-custom px-4" onClick={handleNext}>
                                    Next Step
                                </button>
                            ) : (
                                <button type="submit" className="btn-primary-custom px-4 d-flex align-items-center gap-2" disabled={loading}>
                                    {loading ? 'Submitting...' : 'Confirm & Create'} 
                                    {!loading && <span className="material-symbols-outlined" style={{ fontSize: '18px' }}>check_circle</span>}
                                </button>
                            )}
                        </div>
                    </form>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
