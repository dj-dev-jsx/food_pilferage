<div class="modal fade" id="view-modal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="viewModalLabel">
                    <i class="bi bi-clipboard-data me-2"></i>Pilferage Report Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body p-4">
                <!-- Report Overview Section -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="info-group">
                                    <label class="text-muted">Report ID</label>
                                    <h6 class="mb-0" id="reportId"></h6>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-group">
                                    <label class="text-muted">Date Reported</label>
                                    <h6 class="mb-0" id="dateReported"></h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Item Details Section -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            <i class="bi bi-box me-2"></i>Item Information
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="info-group">
                                    <label class="text-muted">Item Name</label>
                                    <h6 class="mb-0" id="itemName"></h6>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-group">
                                    <label class="text-muted">Category</label>
                                    <h6 class="mb-0" id="itemCategory"></h6>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-group">
                                    <label class="text-muted">Quantity Missing</label>
                                    <h6 class="mb-0" id="quantityMissing"></h6>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-group">
                                    <label class="text-muted">Estimated Loss</label>
                                    <h6 class="mb-0" id="estimatedLoss"></h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reporter Details Section -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            <i class="bi bi-person me-2"></i>Reporter Information
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="info-group">
                                    <label class="text-muted">Reported By</label>
                                    <h6 class="mb-0" id="reporterName"></h6>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-group">
                                    <label class="text-muted">Department</label>
                                    <h6 class="mb-0" id="department"></h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Incident Details Section -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            <i class="bi bi-file-text me-2"></i>Incident Description
                        </h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-0" id="incidentDescription"></p>
                    </div>
                </div>

                <!-- Evidence Section -->
                <div class="card">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            <i class="bi bi-camera me-2"></i>Evidence Attachments
                        </h6>
                    </div>
                    <div class="card-body">
                        <div id="evidenceGallery" class="row g-3">
                            <!-- Evidence images/files will be loaded here -->
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-lg me-2"></i>Close
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.modal-content {
    border-radius: 15px;
    border: none;
    box-shadow: 0 0 20px rgba(0,0,0,0.1);
}

.modal-header {
    border-radius: 15px 15px 0 0;
}

.card {
    border-radius: 10px;
    border: 1px solid rgba(0,0,0,.125);
    box-shadow: 0 2px 4px rgba(0,0,0,.05);
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid rgba(0,0,0,.125);
}

.info-group {
    margin-bottom: 0.5rem;
}

.info-group label {
    font-size: 0.875rem;
    margin-bottom: 0.25rem;
    display: block;
}

.btn {
    border-radius: 10px;
    padding: 10px 20px;
}
</style>
