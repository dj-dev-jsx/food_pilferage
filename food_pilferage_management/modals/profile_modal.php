<div class="modal fade" id="profile-modal" tabindex="-1" aria-labelledby="profileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="profileModalLabel">
                    <i class="bi bi-person-circle me-2"></i>User Profile
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body p-4">
                <form id="profileForm">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="profileUsername" name="username" placeholder="Username" readonly>
                                <label for="profileUsername">Username</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="email" class="form-control" id="profileEmail" name="email" placeholder="Email">
                                <label for="profileEmail">Email Address</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="profileFirstName" name="first_name" placeholder="First Name">
                                <label for="profileFirstName">First Name</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="profileLastName" name="last_name" placeholder="Last Name">
                                <label for="profileLastName">Last Name</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="profileMiddleName" name="middle_name" placeholder="Middle Name">
                                <label for="profileMiddleName">Middle Name</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="profileContact" name="contact_number" placeholder="Contact Number">
                                <label for="profileContact">Contact Number</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="profileRole" name="role" placeholder="Role" readonly>
                                <label for="profileRole">Role</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="password" class="form-control" id="profileNewPassword" name="new_password" placeholder="New Password">
                                <label for="profileNewPassword">New Password (Optional)</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-lg me-2"></i>Cancel
                </button>
                <button type="submit" form="profileForm" class="btn btn-success">
                    <i class="bi bi-check-lg me-2"></i>Save Changes
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

.form-floating > .form-control,
.form-floating > .form-select {
    border-radius: 10px;
    border: 1px solid #ced4da;
}

.form-floating > .form-control:focus {
    border-color: #28a745;
    box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.25);
}

.btn {
    border-radius: 10px;
    padding: 10px 20px;
}
</style>
