@extends('layouts.admin')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Table Posts</h4>
                    @if ($hasCreatePermission)
                        <a href="{{ route('posts.create') }}" class="btn btn-primary">Create Posts</a>
                    @endif
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="dataTable" class="table dt-responsive nowrap table-bordered align-middle"
                            style="width:100%">
                            <thead>
                                <tr class="fw-bolder">
                                    <th scope="col">No</th>
                                    <th scope="col" class="min-w-100px">Posts Name</th>
                                    <th scope="col" class="min-w-100px">Created By</th>
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
                    url: "{{ route('posts') }}",
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
                        data: 'judul',
                        name: 'judul',
                    },
                    {
                        data: 'nama_pembuat',
                        name: 'nama_pembuat',
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
                            url: "{{ route('posts.destroy') }}",
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
                                            title: res.text,
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
