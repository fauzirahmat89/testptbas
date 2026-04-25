@extends('layouts.master')

@section('title', 'Customer Management')

@push('css')
    <!--datatable css-->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />
    <!--datatable responsive css-->
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" />
@endpush

@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Customer Management</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Master</a></li>
                        <li class="breadcrumb-item active">Customer</li>
                    </ol>
                </div>

            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header border-0">
                    <div class="d-flex align-items-center">
                        <h5 class="card-title mb-0 flex-grow-1">Customer List</h5>
                        <div class="flex-shrink-0">
                            <button class="btn btn-primary add-btn" data-bs-toggle="modal" data-bs-target="#addCustomerModal">
                                <i class="ri-add-line align-bottom me-1"></i> Add Customer
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <table id="customer-table" class="table nowrap align-middle" style="width:100%">
                        <thead>
                            <tr>
                                <th>User ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Created At</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Customer Modal -->
    <div class="modal fade" id="addCustomerModal" tabindex="-1" aria-labelledby="addCustomerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-light p-3">
                    <h5 class="modal-title" id="addCustomerModalLabel">Add New Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="close-modal"></button>
                </div>
                <form id="addCustomerForm">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" id="name" name="name" class="form-control" placeholder="Enter name" required />
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" id="email" name="email" class="form-control" placeholder="Enter email" required />
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="">Select Status</option>
                                <option value="NEW CUSTOMER">NEW CUSTOMER</option>
                                <option value="LOYAL CUSTOMER">LOYAL CUSTOMER</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="hstack gap-2 justify-content-end">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-success" id="add-btn">Add Customer</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <!--datatable js-->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>

    <!-- Choices.js js -->
    <script src="{{ asset('assets/libs/choices.js/public/assets/scripts/choices.min.js') }}"></script>

    <!-- Sweet Alerts js -->
    <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.all.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            var statusChoices;
            
            // Initialize Choices.js when modal is shown to fix width calculation issues
            $('#addCustomerModal').on('shown.bs.modal', function () {
                if (!statusChoices) {
                    var element = document.getElementById('status');
                    statusChoices = new Choices(element, {
                        searchEnabled: false,
                        shouldSort: false,
                    });
                }
            });

            var table = $('#customer-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('customers.data') }}",
                columns: [
                    { data: 'user_id', name: 'user_id' },
                    { data: 'name', name: 'name' },
                    { data: 'email', name: 'email' },
                    { data: 'status', name: 'status' },
                    { data: 'created_at', name: 'created_at' }
                ],
                order: [[4, 'desc']]
            });

            $('#addCustomerForm').on('submit', function(e) {
                e.preventDefault();
                
                var formData = $(this).serialize();
                var submitBtn = $('#add-btn');
                
                submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Adding...');

                $.ajax({
                    url: "{{ route('customers.store') }}",
                    type: "POST",
                    data: formData,
                    success: function(response) {
                        submitBtn.prop('disabled', false).text('Add Customer');
                        if (response.success) {
                            $('#addCustomerModal').modal('hide');
                            $('#addCustomerForm')[0].reset();
                            if (statusChoices) {
                                statusChoices.destroy();
                                statusChoices = null;
                            }
                            table.ajax.reload();
                            
                            // Re-initialize choices if needed, but usually reset is enough
                            
                            Swal.fire({
                                title: 'Success!',
                                text: response.message,
                                icon: 'success',
                                confirmButtonClass: 'btn btn-primary w-xs mt-2',
                                buttonsStyling: false
                            });
                        }
                    },
                    error: function(xhr) {
                        submitBtn.prop('disabled', false).text('Add Customer');
                        var errors = xhr.responseJSON.errors;
                        var errorMessage = xhr.responseJSON.message || 'Something went wrong';
                        
                        if (errors) {
                            errorMessage = Object.values(errors).flat().join('<br>');
                        }

                        Swal.fire({
                            title: 'Error!',
                            html: errorMessage,
                            icon: 'error',
                            confirmButtonClass: 'btn btn-primary w-xs mt-2',
                            buttonsStyling: false
                        });
                    }
                });
            });
        });
    </script>
@endpush
