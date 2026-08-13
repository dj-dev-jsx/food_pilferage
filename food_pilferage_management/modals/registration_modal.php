<div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="registerModalLabel">
                    <i class="bi bi-person-plus-fill me-2"></i>Add User
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="registerForm" class="needs-validation" novalidate>
                <div class="modal-body p-4">
                    <div id="errorMessage" class="alert alert-danger d-none"></div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="regUsername" name="username" placeholder="Username" required>
                                <label for="regUsername">Username</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="email" class="form-control" id="regEmail" name="email" placeholder="Email" required>
                                <label for="regEmail">Email Address</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="regFirstName" name="first_name" placeholder="First Name" required>
                                <label for="regFirstName">First Name</label>
                            </div>
                            
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="regMidName" name="middle_name" placeholder="Middle Name" required>
                                <label for="regMidName">Middle Name</label>
                            </div>
                            
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="regLastName" name="last_name" placeholder="Last Name" required>
                                <label for="regLastName">Last Name</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="regContactNumber" name="contact_number" placeholder="Contact Number" required>
                                <label for="regContactNumber">Contact Number</label>
                            </div>
                            <div class="form-floating mb-3">
                                <select class="form-select" id="regRole" name="role" required onchange="toggleVerifyForm(this.value)">
                                    <option value="" disabled selected>Select role</option>
                                    <option value="Inventory Staff">Inventory Staff</option>
                                    <option value="Kitchen Staff">Kitchen Staff</option>
        
                                </select>
                                <label for="regRole">Select Role</label>
                            </div>

                            <div class="form-floating mb-3">
                                <label for="access_code" class="form-label">Access Code</label>
                                <input type="text" class="form-control" id="access_code" name="access_code">
                            </div>

                            <div class="form-floating mb-3 d-none" id="adminVerificationCode">
                                <input type="text" class="form-control" id="regAdminCode" name="input_code" placeholder="Admin Code">
                                <label for="regAdminCode">Admin Verification Code</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-person-plus me-2"></i>Register
                    </button>
                </div>
            </form>
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

.form-floating > .form-control:focus,
.form-floating > .form-select:focus {
    border-color: #28a745;
    box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.25);
}

.password-requirements {
    border-radius: 10px;
    background-color: #f8f9fa;
}

.btn-success {
    border-radius: 10px;
    padding: 10px 20px;
}
</style>
