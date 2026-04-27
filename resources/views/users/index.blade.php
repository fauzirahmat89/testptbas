@extends('layouts.master')

@section('title', 'User Management')

@push('css')
<!--datatable css-->
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />
<!--datatable responsive css-->
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" />
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">User Management</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Users</a></li>
                    <li class="breadcrumb-item active">Index</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header border-bottom-dashed">
                <div class="row g-4 align-items-center">
                    <div class="col-sm">
                        <div>
                            <h5 class="card-title mb-0">User List</h5>
                        </div>
                    </div>
                    <div class="col-sm-auto">
                        <div class="d-flex flex-wrap align-items-start gap-2">
                            <button type="button" class="btn btn-success add-btn" data-bs-toggle="modal" id="create-btn" data-bs-target="#userModal"><i class="ri-add-line align-bottom me-1"></i> Add User</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <table id="userTable" class="table table-bordered dt-responsive nowrap table-striped align-middle" style="width:100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Created At</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="userModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-light p-3">
                <h5 class="modal-title" id="modalTitle">Add User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="close-modal"></button>
            </div>
            <form id="userForm" autocomplete="off">
                @csrf
                <input type="hidden" id="user_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name-field" class="form-label">Name</label>
                        <input type="text" id="name-field" name="name" class="form-control" placeholder="Enter name" required />
                    </div>

                    <div class="mb-3">
                        <label for="email-field" class="form-label">Email</label>
                        <input type="email" id="email-field" name="email" class="form-control" placeholder="Enter email" required />
                    </div>

                    <div class="mb-3">
                        <label for="password-field" class="form-label">Password</label>
                        <input type="password" id="password-field" name="password" class="form-control" placeholder="Enter password" />
                        <small class="text-muted" id="password-help">Leave blank if you don't want to change password (only for edit).</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="hstack gap-2 justify-content-end">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success" id="save-btn">Add User</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('js')
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>

<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    let table = $('#userTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('users.index') }}",
        columns: [
            {data: 'id', name: 'id'},
            {data: 'name', name: 'name'},
            {data: 'email', name: 'email'},
            {data: 'created_at', name: 'created_at'},
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ]
    });

    $('#create-btn').click(function() {
        $('#userForm')[0].reset();
        $('#user_id').val('');
        $('#modalTitle').text('Add User');
        $('#save-btn').text('Add User');
        $('#password-field').attr('required', true);
        $('#password-help').hide();
    });

    function editUser(id) {
        $.get("{{ url('users') }}/" + id + "/edit", function(data) {
            $('#modalTitle').text('Edit User');
            $('#save-btn').text('Update User');
            $('#userModal').modal('show');
            $('#user_id').val(data.id);
            $('#name-field').val(data.name);
            $('#email-field').val(data.email);
            $('#password-field').val('');
            $('#password-field').attr('required', false);
            $('#password-help').show();
        });
    }

    $('#userForm').submit(function(e) {
        e.preventDefault();
        let id = $('#user_id').val();
        let url = id ? "{{ url('users') }}/" + id : "{{ route('users.store') }}";
        let method = id ? "PUT" : "POST";

        $.ajax({
            url: url,
            method: method,
            data: $(this).serialize(),
            success: function(response) {
                $('#userModal').modal('hide');
                table.draw();
            },
            error: function(response) {
                let errors = response.responseJSON.errors;
                let errorMsg = '';
                $.each(errors, function(key, value) {
                    errorMsg += value + '\n';
                });
                alert(errorMsg);
            }
        });
    });

    function deleteUser(id) {
        if (confirm('Are you sure you want to delete this user?')) {
            $.ajax({
                url: "{{ url('users') }}/" + id,
                method: "DELETE",
                success: function(response) {
                    table.draw();
                },
                error: function(response) {
                    alert('Error deleting user');
                }
            });
        }
    }
</script>
@endpush
