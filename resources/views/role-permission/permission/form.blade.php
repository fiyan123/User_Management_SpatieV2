@extends('layouts.admin')
@section('content')
    <div class="card">
        <form id="form_data" enctype="multipart/form-data">
            @csrf
            <div class="card-header">
                <h5>{{ $permission->id ? 'Edit' : 'Create' }} Permission</h5>
            </div>
            <div class="card-body custom-flex">
                <input type="hidden" value="{{ $permission->id }}" id="id">

                <div class="row">
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label for="permissions" class="form-label">Permission Name</label>
                            <input type="text" class="form-control" value="{{ old('name', $permission->name) }}"
                                id="name" placeholder="Masukkan Nama Permission" name="name">
                        </div>
                    </div>
                </div>
            </div>
            <center>
                <div class="card-footer d-flex justify-content-end py-6 px-9">
                    <a href="{{ route('permissions') }}" class="btn btn-light btn-active-light-primary me-2"><i
                            class="ki-duotone ki-arrow-left fs-4 ms-1 me-0">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>Back</a>
                    <button type="button" class="btn btn-primary me-10" id="simpan">
                        <span class="indicator-label">
                            Submit
                        </span>
                        <span class="indicator-progress" style="display: none;">
                            Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                        </span>
                    </button>
                </div>
            </center>
        </form>
    </div>
@endsection
@push('script_processing')
    <script>
        let button = document.querySelector("#simpan");
        button.addEventListener("click", function() {
            $('.indicator-progress').show();
            let id = $('#id').val();
            let url;
            let form_data = new FormData(document.getElementById('form_data'));
            form_data.append('_token', "{{ csrf_token() }}");

            if (id > 0) {
                url = "{{ route('permissions.update') }}";
                form_data.append('id', id);
            } else {
                url = "{{ route('permissions.store') }}";
            }

            $.ajax({
                type: "POST",
                url: url,
                data: form_data,
                contentType: false,
                processData: false,
                success: function(res) {
                    $('.indicator-progress').hide();
                    Swal.fire({
                        icon: 'success',
                        title: res.text,
                        showConfirmButton: false,
                        timer: 1500
                    }).then((res) => {
                        window.location.href = "{{ route('permissions') }}";
                    })
                },
                error: function(xhr) {
                    $('.indicator-progress').hide();
                    swal({
                        icon: 'error',
                        title: 'Error',
                        text: xhr.responseJSON.text,
                    });
                }
            });
        });
    </script>
@endpush
