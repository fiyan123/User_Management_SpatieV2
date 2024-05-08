@extends('layouts.admin')
@section('content')
    <style>
        label {
            display: inline-block;
            margin-bottom: 10px;
        }

        input[type="checkbox"] {
            margin-right: 5px;
        }
    </style>

    <div class="card">
        <form id="form_data" enctype="multipart/form-data">
            @csrf
            <div class="card-header text-center">
                <h5>Role Name : {{ $role->name }}</h5>
            </div>
            <div class="card-body custom-flex">
                <input type="hidden" value="{{ $role->id }}" id="id">

                <div class="row">
                    @foreach ($permission as $item)
                        <div class="col-md-2">
                            <label>
                                <input type="checkbox" name="permission[]" value="{{ $item->name }}"
                                    {{ in_array($item->id, $rolePermission) ? 'checked' : '' }}>
                                <span>{{ $item->name }}</span>
                            </label>
                        </div>
                    @endforeach

                </div>
            </div>
            <center>
                <div class="card-footer d-flex justify-content-end py-6 px-9">
                    <a href="{{ route('roles') }}" class="btn btn-light btn-active-light-primary me-2"><i
                            class="ki-duotone ki-arrow-left fs-4 ms-1 me-0">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>Kembali</a>
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
            let form_data = new FormData(document.getElementById('form_data'));
            form_data.append('_token', "{{ csrf_token() }}");

            let url = "{{ route('roles.givePermissions', ['id' => ':id']) }}";
            url = url.replace(':id', id);

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
                        window.location.href = "{{ route('roles') }}";
                    })
                },
                error: function(xhr) {
                    $('.indicator-progress').hide();
                    var errorMessage = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON
                        .message : '';
                    swal({
                        icon: 'error',
                        title: 'Gagal',
                        text: errorMessage,
                    });
                }
            });
        });
    </script>
@endpush
