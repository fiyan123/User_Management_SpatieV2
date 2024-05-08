@extends('layouts.admin')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Table Users</h4>
                    <a href="{{ route('users.create') }}" class="btn btn-primary">Create Users</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="dataTable" class="table dt-responsive nowrap table-bordered align-middle"
                            style="width:100%">
                            <thead>
                                <tr class="fw-bolder">
                                    <th scope="col">No</th>
                                    <th scope="col" class="min-w-100px">Users Name</th>
                                    <th scope="col" class="min-w-100px">Email</th>
                                    <th scope="col" class="min-w-100px">Role Name</th>
                                    <th scope="col" class="min-w-80px">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script_processing')
    <script>
        function loadData() {
            table = $('#dataTable').DataTable({
                pageLength: 10,
                searching: true,
                serverSide: true,
                processing: true,
                responsive: true,
                ajax: {
                    url: "{{ route('users') }}",
                    type: 'GET',
                },

                columnDefs: [{
                    "defaultContent": "-",
                    "targets": "_all"
                }],
                columns: [{
                        data: 'DT_RowIndex',
                    },
                    {
                        data: 'name',
                        name: 'name',
                    },
                    {
                        data: 'email',
                        name: 'email',
                    },
                    {
                        data: 'roles',
                        name: 'roles',
                    },
                    {
                        data: 'action',
                        name: 'action',
                        className: "text-center",
                        orderable: false,
                        searchable: false
                    },
                ],
            });
        }

        $(document).ready(function() {
            loadData();

            $(document).on('click', '.delete', function() {
                let id = $(this).attr('id')
                swal({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    buttons: true,
                }).then((willDelete) => {
                    if (willDelete) {
                        $.ajax({
                            url: "{{ route('users.destroy') }}",
                            type: 'post',
                            data: {
                                id: id,
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(res, status) {
                                if (status = '200') {
                                    setTimeout(() => {
                                        Swal.fire({
                                            icon: 'success',
                                            title: res.message,
                                            showConfirmButton: false,
                                            timer: 1500
                                        }).then((res) => {
                                            $('#dataTable').DataTable()
                                                .ajax.reload()
                                        })
                                    });
                                }
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: xhr.responseJSON.text,
                                })
                            }
                        })
                    }
                })
            });
        });
    </script>
@endpush
