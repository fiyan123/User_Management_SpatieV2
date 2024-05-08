@extends('layouts.admin')
@section('content')
    <div class="card">
        <form id="form_data" enctype="multipart/form-data">
            @csrf
            <div class="card-header">
                <h5>{{ $users->id ? 'Edit' : 'Create' }} Users</h5>
            </div>
            <div class="card-body custom-flex">
                <input type="hidden" value="{{ $users->id }}" id="id">

                <div class="container">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" value="{{ old('name', $users->name) }}"
                                    id="name" placeholder="Input Name Users" name="name">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" value="{{ old('email', $users->email) }}"
                                    id="email" placeholder="Input Email Users" name="email">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" value="{{ old('password') }}" id="password"
                                    placeholder="Input Password Users" name="password">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

                                <x-text-input id="password_confirmation" class="form-control" type="password"
                                    name="password_confirmation" required autocomplete="new-password" />

                                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="roles" class="form-label">Roles</label>
                                <select name="roles" id="roles" class="form-control">
                                    <option value="">Select Role</option>
                                    @foreach ($roles as $item)
                                        <option value="{{ $item->name }}"
                                            {{ $users->hasRole($item->name) ? 'selected' : '' }}>
                                            {{ $item->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <center>
                <div class="card-footer d-flex justify-content-end py-6 px-9">
                    <a href="{{ route('users') }}" class="btn btn-light btn-active-light-primary me-2"><i
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

    <script>
        let button = document.querySelector("#simpan");
        button.addEventListener("click", function() {
            $('.indicator-progress').show();
            let id = $('#id').val();
            let url;
            let form_data = new FormData(document.getElementById('form_data'));
            form_data.append('_token', "{{ csrf_token() }}");

            if (id > 0) {
                url = "{{ route('users.update') }}";
                form_data.append('id', id);
            } else {
                url = "{{ route('users.store') }}";
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
                        window.location.href = "{{ route('users') }}";
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
@endsection
