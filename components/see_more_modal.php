<!-- see_more_modal.php – Enchanted Dark Theme (standalone, no conflicts) -->
<style>
    /* ======================= ENCHANTED SEE-MORE MODAL ======================= */
    /* All styles are scoped under .sm-enchanted-modal to avoid conflicts */
    .sm-enchanted-modal .modal-content {
        border: none;
        border-radius: 28px;
        overflow: hidden;
        box-shadow: 0 30px 60px rgba(0,0,0,0.7), 0 0 0 1px rgba(139,92,246,0.3), 0 0 60px rgba(124,92,252,0.15);
        background: transparent;
    }

    /* Starry animated background inside modal (lightweight CSS stars) */
    .sm-enchanted-modal .modal-content::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background:
            radial-gradient(1px 1px at 10% 15%, rgba(255,255,255,0.4), transparent),
            radial-gradient(1px 1px at 30% 45%, rgba(255,255,255,0.3), transparent),
            radial-gradient(1.5px 1.5px at 70% 20%, rgba(255,255,255,0.5), transparent),
            radial-gradient(1px 1px at 90% 70%, rgba(255,255,255,0.4), transparent),
            radial-gradient(2px 2px at 50% 80%, rgba(255,255,255,0.3), transparent),
            radial-gradient(1px 1px at 20% 80%, rgba(255,255,255,0.4), transparent),
            radial-gradient(1px 1px at 80% 10%, rgba(255,255,255,0.3), transparent);
        pointer-events: none;
        z-index: 0;
        animation: sm-stars-drift 20s linear infinite;
    }

    @keyframes sm-stars-drift {
        0% { transform: translateY(0); }
        100% { transform: translateY(-10px); }
    }

    .sm-enchanted-modal .modal-header {
        background: linear-gradient(135deg, #0f0a2e 0%, #1e1b4b 50%, #2d1060 100%);
        border-bottom: none;
        padding: 1.4rem 2rem;
        position: relative;
        z-index: 1;
        overflow: hidden;
    }

    .sm-enchanted-modal .modal-header::after {
        content: '';
        position: absolute;
        top: -50%; left: -20%;
        width: 200%; height: 200%;
        background: radial-gradient(circle at 30% 50%, rgba(139,92,246,0.35) 0%, transparent 60%);
        pointer-events: none;
    }

    .sm-enchanted-modal .modal-header h5 {
        font-weight: 700;
        letter-spacing: -0.3px;
        color: #f1f5f9;
        position: relative;
        z-index: 2;
        display: flex;
        align-items: center;
        gap: 10px;
        text-shadow: 0 0 10px rgba(139,92,246,0.5);
    }

    .sm-enchanted-modal .btn-close {
        filter: invert(1) brightness(2);
        opacity: 0.8;
        transition: 0.3s;
        position: relative;
        z-index: 2;
    }

    .sm-enchanted-modal .btn-close:hover {
        opacity: 1;
        transform: rotate(90deg);
        filter: drop-shadow(0 0 6px #a78bfa);
    }

    .sm-enchanted-modal .modal-body {
        padding: 0;
        max-height: 80vh;
        overflow-y: auto;
        background: rgba(11, 15, 25, 0.9);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border-radius: 0 0 28px 28px;
        scrollbar-width: thin;
        scrollbar-color: #4b5563 #1e293b;
        position: relative;
        z-index: 1;
    }

    .sm-enchanted-modal .modal-body::-webkit-scrollbar {
        width: 6px;
    }
    .sm-enchanted-modal .modal-body::-webkit-scrollbar-track {
        background: #1e293b;
        border-radius: 0 0 20px 20px;
    }
    .sm-enchanted-modal .modal-body::-webkit-scrollbar-thumb {
        background: #4b5563;
        border-radius: 10px;
    }

    /* Info cards – glass with purple glow */
    .sm-info-card {
        background: rgba(17, 24, 39, 0.7);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        border-radius: 20px;
        margin: 1.2rem;
        padding: 1.2rem 1.5rem;
        border: 1px solid rgba(139,92,246,0.2);
        box-shadow: 0 4px 12px rgba(0,0,0,0.4);
        transition: all 0.3s ease;
    }

    .sm-info-card:hover {
        border-color: rgba(167,139,250,0.5);
        box-shadow: 0 8px 24px rgba(139,92,246,0.2), 0 0 0 1px rgba(139,92,246,0.4);
        transform: translateY(-2px);
        background: rgba(30, 41, 59, 0.7);
    }

    .sm-section-title {
        font-size: 1.05rem;
        font-weight: 700;
        color: #e2e8f0;
        border-left: 4px solid #8b5cf6;
        padding-left: 14px;
        margin-bottom: 1.2rem;
        display: flex;
        align-items: center;
        gap: 10px;
        letter-spacing: -0.2px;
        text-shadow: 0 0 8px rgba(139,92,246,0.3);
    }

    .sm-section-title i {
        color: #a78bfa;
        filter: drop-shadow(0 0 4px #7c5cfc);
    }

    .sm-detail-row {
        display: flex;
        flex-wrap: wrap;
        margin-bottom: 0.85rem;
        font-size: 0.92rem;
        line-height: 1.5;
    }

    .sm-detail-label {
        width: 150px;
        font-weight: 600;
        color: #94a3b8;
        display: flex;
        align-items: baseline;
        gap: 8px;
    }

    .sm-detail-label i {
        color: #6d28d9;
        width: 18px;
    }

    .sm-detail-value {
        flex: 1;
        color: #cbd5e1;
        word-break: break-word;
    }

    .sm-detail-value a {
        color: #a78bfa;
        text-decoration: none;
        font-weight: 500;
        border-bottom: 1px dashed transparent;
        transition: 0.2s;
    }

    .sm-detail-value a:hover {
        border-bottom-color: #a78bfa;
        color: #c4b5fd;
        text-shadow: 0 0 6px rgba(167,139,250,0.5);
    }

    /* Schedule items */
    .sm-schedule-item {
        background: rgba(30,41,59,0.6);
        backdrop-filter: blur(4px);
        border-radius: 12px;
        padding: 0.8rem 1.2rem;
        margin-bottom: 0.7rem;
        border: 1px solid rgba(139,92,246,0.25);
        transition: 0.2s;
    }

    .sm-schedule-item:hover {
        background: rgba(39, 52, 73, 0.8);
        border-color: #7c3aed;
        box-shadow: 0 0 12px rgba(124,92,252,0.2);
    }

    .sm-schedule-label {
        font-weight: 600;
        color: #c4b5fd;
    }

    .sm-schedule-item .sm-detail-label {
        width: 120px;
    }

    .sm-schedule-item .sm-detail-label i {
        color: #7c3aed;
    }

    /* Loading / error */
    .sm-enchanted-modal .modal-body .text-center .fa-spinner {
        color: #a78bfa;
        filter: drop-shadow(0 0 8px #7c5cfc);
    }

    .sm-enchanted-modal .modal-body .alert-danger {
        background: rgba(59, 13, 13, 0.8);
        border: 1px solid #7f1d1d;
        color: #fca5a5;
        border-radius: 12px;
        backdrop-filter: blur(4px);
    }

    /* ========== RESPONSIVE ========== */
    @media (max-width: 768px) {
        .sm-enchanted-modal .modal-header {
            padding: 1rem 1.5rem;
        }
        .sm-info-card {
            margin: 0.8rem;
            padding: 1rem 1.2rem;
            border-radius: 16px;
        }
        .sm-detail-label {
            width: 100%;
            margin-bottom: 2px;
            color: #a0aec0;
        }
        .sm-detail-value {
            margin-bottom: 14px;
        }
        .sm-schedule-item .sm-detail-label {
            width: 100%;
        }
        .sm-schedule-item .sm-detail-value {
            margin-bottom: 8px;
        }
        .sm-section-title {
            font-size: 0.95rem;
            padding-left: 10px;
        }
    }
</style>

<!-- Modal HTML with unique class -->
<div class="modal fade sm-enchanted-modal" id="viewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-info-circle me-2"></i>Service Information</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="viewDetails">
                <div class="text-center py-5">
                    <i class="fas fa-spinner fa-pulse fa-2x"></i>
                    <p class="mt-3" style="color: #94a3b8;">Loading details...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Global function to fetch and display service details (document images removed)
function viewDetails(serviceId) {
    const modalBody = document.getElementById('viewDetails');
    modalBody.innerHTML = '<div class="text-center py-5"><i class="fas fa-spinner fa-pulse fa-2x" style="color:#a78bfa;"></i><p class="mt-3" style="color:#94a3b8;">Loading details...</p></div>';
    
    fetch(`includes/fetch_services.php?service_id=${serviceId}`)
        .then(res => res.json())
        .then(data => {
            if (data.success && data.services.length) {
                const s = data.services[0];
                
                const formatArray = (arr) => (arr && arr.length) ? arr.join(', ') : 'N/A';
                const formatUrl = (url) => url ? `<a href="${escapeHtml(url)}" target="_blank">${escapeHtml(url)}</a>` : 'N/A';
                
                let html = `
                    <div class="sm-info-card">
                        <div class="sm-section-title"><i class="fas fa-tag"></i> Basic Information</div>
                        <div class="sm-detail-row"><div class="sm-detail-label"><i class="fas fa-bus"></i> Service Name:</div><div class="sm-detail-value">${escapeHtml(s.service_name)}</div></div>
                        <div class="sm-detail-row"><div class="sm-detail-label"><i class="fas fa-id-card"></i> Registration No:</div><div class="sm-detail-value">${escapeHtml(s.reg_no)}</div></div>
                    </div>
                    
                    <div class="sm-info-card">
                        <div class="sm-section-title"><i class="fas fa-truck"></i> Vehicle & Owner Details</div>
                        <div class="sm-detail-row"><div class="sm-detail-label"><i class="fas fa-car"></i> Vehicle Type:</div><div class="sm-detail-value">${escapeHtml(s.vehicle_type)}</div></div>
                        <div class="sm-detail-row"><div class="sm-detail-label"><i class="fas fa-tag"></i> Service Type:</div><div class="sm-detail-value">${escapeHtml(s.service_type)}</div></div>
                        <div class="sm-detail-row"><div class="sm-detail-label"><i class="fas fa-user"></i> Owner:</div><div class="sm-detail-value">${escapeHtml(s.owner)}</div></div>
                        <div class="sm-detail-row"><div class="sm-detail-label"><i class="fas fa-user-circle"></i> Driver:</div><div class="sm-detail-value">${escapeHtml(s.driver)} (${escapeHtml(s.driver_reg_no)})</div></div>
                        <div class="sm-detail-row"><div class="sm-detail-label"><i class="fas fa-users"></i> Assistants:</div><div class="sm-detail-value">${escapeHtml(formatArray(s.assistants))}</div></div>
                    </div>
                    
                    <div class="sm-info-card">
                        <div class="sm-section-title"><i class="fas fa-map-marked-alt"></i> Location & Coverage</div>
                        <div class="sm-detail-row"><div class="sm-detail-label"><i class="fas fa-map-marker-alt"></i> Province/District:</div><div class="sm-detail-value">${escapeHtml(s.province)}, ${escapeHtml(s.district)}</div></div>
                        <div class="sm-detail-row"><div class="sm-detail-label"><i class="fas fa-home"></i> Home Town:</div><div class="sm-detail-value">${escapeHtml(s.home_town)}</div></div>
                        <div class="sm-detail-row"><div class="sm-detail-label"><i class="fas fa-globe-asia"></i> Areas Covered:</div><div class="sm-detail-value">${escapeHtml(s.areas_covered)}</div></div>
                        <div class="sm-detail-row"><div class="sm-detail-label"><i class="fas fa-address-card"></i> Address:</div><div class="sm-detail-value">${escapeHtml(s.address)}</div></div>
                        ${s.road_description ? `<div class="sm-detail-row"><div class="sm-detail-label"><i class="fas fa-road"></i> Road Description:</div><div class="sm-detail-value">${escapeHtml(s.road_description)}</div></div>` : ''}
                    </div>
                    
                    <div class="sm-info-card">
                        <div class="sm-section-title"><i class="fas fa-school"></i> Schools / Institutes</div>
                        <div class="sm-detail-row"><div class="sm-detail-label"><i class="fas fa-graduation-cap"></i> Schools:</div><div class="sm-detail-value">${escapeHtml(formatArray(s.schools))}</div></div>
                    </div>`;
                
                // Dynamic schedules
                if (s.schedules && s.schedules.length) {
                    html += `<div class="sm-info-card">
                                <div class="sm-section-title"><i class="fas fa-clock"></i> Schedule</div>`;
                    s.schedules.forEach(sch => {
                        const label = sch.label ? `<span class="sm-schedule-label">${escapeHtml(sch.label)}:</span> ` : '';
                        html += `<div class="sm-schedule-item">
                                    <div class="sm-detail-row">
                                        <div class="sm-detail-label"><i class="fas fa-map-pin"></i> ${label}</div>
                                        <div class="sm-detail-value">${escapeHtml(sch.place)}</div>
                                    </div>
                                    <div class="sm-detail-row">
                                        <div class="sm-detail-label"><i class="fas fa-clock"></i> Time:</div>
                                        <div class="sm-detail-value">${escapeHtml(sch.time)}</div>
                                    </div>
                                </div>`;
                    });
                    html += `</div>`;
                }
                
                html += `<div class="sm-info-card">
                            <div class="sm-section-title"><i class="fas fa-phone-alt"></i> Contact Information</div>
                            <div class="sm-detail-row"><div class="sm-detail-label"><i class="fas fa-phone"></i> Telephones:</div><div class="sm-detail-value">${escapeHtml(formatArray(s.telephones))}</div></div>
                            <div class="sm-detail-row"><div class="sm-detail-label"><i class="fas fa-envelope"></i> Emails:</div><div class="sm-detail-value">${escapeHtml(formatArray(s.emails))}</div></div>
                            <div class="sm-detail-row"><div class="sm-detail-label"><i class="fas fa-globe"></i> Websites:</div><div class="sm-detail-value">${formatArray(s.websites).split(',').map(w => formatUrl(w.trim())).join(', ')}</div></div>
                        </div>`;
                
                if (s.description) {
                    html += `<div class="sm-info-card">
                                <div class="sm-section-title"><i class="fas fa-align-left"></i> Description</div>
                                <div class="sm-detail-row"><div class="sm-detail-value">${escapeHtml(s.description)}</div></div>
                            </div>`;
                }
                
                modalBody.innerHTML = html;
                new bootstrap.Modal(document.getElementById('viewModal')).show();
            } else {
                modalBody.innerHTML = '<div class="alert alert-danger m-4">Service details not found.</div>';
            }
        })
        .catch(err => {
            console.error(err);
            modalBody.innerHTML = '<div class="alert alert-danger m-4">Error loading details. Please try again.</div>';
        });
}

function escapeHtml(str) { 
    if (!str) return ''; 
    return String(str).replace(/[&<>]/g, function(m) {
        if (m === '&') return '&amp;';
        if (m === '<') return '&lt;';
        if (m === '>') return '&gt;';
        return m;
    });
}
</script>