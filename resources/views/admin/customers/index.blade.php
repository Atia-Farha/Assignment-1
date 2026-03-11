@extends('layouts.admin')

@section('title', 'Customers')

@section('content')

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <span>Customers</span>
            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#customerCreateModal">
                <i class="bi bi-plus-lg me-1"></i> Add Customer
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="customerTable" class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 70px;">#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Mobile</th>
                            <th>Description</th>
                            <th class="text-end" style="width: 160px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="customersTableBody">
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Loading...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Create Modal -->
    <div class="modal fade" id="customerCreateModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="createName">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" id="createEmail">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mobile <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="createMobile">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" id="createDescription" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="createCustomerBtn">
                        <i class="bi bi-plus-lg me-1"></i> Create
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="customerEditModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="editCustomerId">
                    <div class="mb-3">
                        <label class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="editName">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" id="editEmail">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mobile <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="editMobile">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" id="editDescription" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="updateCustomerBtn">
                        <i class="bi bi-check-lg me-1"></i> Update
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" id="customerDeleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="deleteCustomerId">
                    <p>Are you sure you want to delete <strong id="deleteCustomerName"></strong>?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                        <i class="bi bi-trash me-1"></i> Delete
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            let customersUrl = '{{ url("/api/v1/customers") }}';
            let customersData = [];

            function getToken() {
                return localStorage.getItem('token') || '';
            }

            function authHeaders() {
                return { headers: { Authorization: 'Bearer ' + getToken() } };
            }

            function escapeHtml(text) {
                let div = document.createElement('div');
                div.textContent = text == null ? '' : text;
                return div.innerHTML;
            }

            getCustomers();

            async function getCustomers() {
                let tbody = document.getElementById('customersTableBody');
                try {
                    let response = await axios.get(customersUrl, authHeaders());
                    customersData = response.data['data'] || [];
                    tbody.innerHTML = '';

                    if (customersData.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4">No customers found.</td></tr>';
                        return;
                    }

                    customersData.forEach((item) => {
                        tbody.innerHTML += `
                                <tr>
                                    <td>${item['id']}</td>
                                    <td class="fw-semibold">${escapeHtml(item['name'])}</td>
                                    <td class="text-muted">${escapeHtml(item['email'] || '-')}</td>
                                    <td>${escapeHtml(item['mobile'])}</td>
                                    <td class="text-muted small">${escapeHtml(item['description'] || '-')}</td>
                                    <td class="text-end">
                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="openEditModal(${item['id']})" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="openDeleteModal(${item['id']})" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>`;
                    });

                    new DataTable('#customerTable');
                } catch (err) {
                    tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4">Failed to load customers.</td></tr>';
                    showErrorToast(getErrorMessage(err, 'Failed to load customers.'));
                }
            }

            // Create
            document.getElementById('createCustomerBtn').addEventListener('click', async function () {
                let btn = this;
                let originalHtml = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Creating...';

                try {
                    let payload = {
                        name: document.getElementById('createName').value,
                        email: document.getElementById('createEmail').value || null,
                        mobile: document.getElementById('createMobile').value,
                        description: document.getElementById('createDescription').value || null,
                    };

                    let response = await axios.post(customersUrl, payload, authHeaders());
                    if (response.data.success) {
                        showSuccessToast(response.data.message || 'Customer created successfully.');
                        bootstrap.Modal.getInstance(document.getElementById('customerCreateModal')).hide();
                        document.getElementById('createName').value = '';
                        document.getElementById('createEmail').value = '';
                        document.getElementById('createMobile').value = '';
                        document.getElementById('createDescription').value = '';
                        getCustomers();
                    } else {
                        showErrorToast(response.data.message || 'Failed to create customer.');
                    }
                } catch (err) {
                    showErrorToast(getErrorMessage(err, 'Failed to create customer.'));
                } finally {
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                }
            });

            // Edit
            function openEditModal(id) {
                let customer = customersData.find(c => c.id === id);
                if (!customer) return;

                document.getElementById('editCustomerId').value = customer.id;
                document.getElementById('editName').value = customer.name || '';
                document.getElementById('editEmail').value = customer.email || '';
                document.getElementById('editMobile').value = customer.mobile || '';
                document.getElementById('editDescription').value = customer.description || '';

                let modal = new bootstrap.Modal(document.getElementById('customerEditModal'));
                modal.show();
            }

            document.getElementById('updateCustomerBtn').addEventListener('click', async function () {
                let btn = this;
                let originalHtml = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Updating...';

                try {
                    let id = document.getElementById('editCustomerId').value;
                    let payload = {
                        name: document.getElementById('editName').value,
                        email: document.getElementById('editEmail').value || null,
                        mobile: document.getElementById('editMobile').value,
                        description: document.getElementById('editDescription').value || null,
                    };

                    let response = await axios.put(customersUrl + '/' + id, payload, authHeaders());
                    if (response.data.success) {
                        showSuccessToast(response.data.message || 'Customer updated successfully.');
                        bootstrap.Modal.getInstance(document.getElementById('customerEditModal')).hide();
                        getCustomers();
                    } else {
                        showErrorToast(response.data.message || 'Failed to update customer.');
                    }
                } catch (err) {
                    showErrorToast(getErrorMessage(err, 'Failed to update customer.'));
                } finally {
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                }
            });

            // Delete
            function openDeleteModal(id) {
                let customer = customersData.find(c => c.id === id);
                if (!customer) return;

                document.getElementById('deleteCustomerId').value = customer.id;
                document.getElementById('deleteCustomerName').textContent = customer.name;

                let modal = new bootstrap.Modal(document.getElementById('customerDeleteModal'));
                modal.show();
            }

            document.getElementById('confirmDeleteBtn').addEventListener('click', async function () {
                let btn = this;
                let originalHtml = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Deleting...';

                try {
                    let id = document.getElementById('deleteCustomerId').value;
                    let response = await axios.delete(customersUrl + '/' + id, authHeaders());
                    if (response.data.success) {
                        showSuccessToast(response.data.message || 'Customer deleted successfully.');
                        bootstrap.Modal.getInstance(document.getElementById('customerDeleteModal')).hide();
                        getCustomers();
                    } else {
                        showErrorToast(response.data.message || 'Failed to delete customer.');
                    }
                } catch (err) {
                    showErrorToast(getErrorMessage(err, 'Failed to delete customer.'));
                } finally {
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                }
            });
        </script>
    @endpush
@endsection