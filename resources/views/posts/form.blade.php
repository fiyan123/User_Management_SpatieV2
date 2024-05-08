@extends('layouts.admin')
@section('content')
    <div class="card">
        <form id="form_data" enctype="multipart/form-data">
            @csrf
            <div class="card-header">
                <h5>{{ $posts->id ? 'Edit' : 'Create' }} Posts</h5>
            </div>
            <div class="card-body custom-flex">
                <input type="hidden" value="{{ $posts->id }}" id="id">

                <div class="row">
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label for="judul" class="form-label">Posts Name</label>
                            <input type="text" class="form-control" value="{{ old('judul', $posts->judul) }}"
                                id="judul" placeholder="Masukkan Judul Posts" name="judul">
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label for="nama_pembuat" class="form-label">Created By</label>
                            <input type="text" class="form-control" value="{{ Auth::user()->name }}" id="nama_pembuat"
                                name="nama_pembuat" readonly>
                        </div>
                    </div>
                </div>
                <div class="row-lg-12 mx-1">
                    <div class="mb-3">
                        <label for="isi_posts" class="form-label">Posts Body</label>
                        <textarea name="isi_posts" id="isi_posts" class="form-control">{{ old('isi_posts', $posts->isi_posts) }}</textarea>
                    </div>
                </div>
            </div>
            <center>
                <div class="card-footer d-flex justify-content-end py-6 px-9">
                    <a href="{{ route('posts') }}" class="btn btn-light btn-active-light-primary me-2"><i
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
                url = "{{ route('posts.update') }}";
                form_data.append('id', id);
            } else {
                url = "{{ route('posts.store') }}";
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
                        window.location.href = "{{ route('posts') }}";
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

        $(document).ready(function() {
            ClassicEditor
                .create(document.querySelector('#isi_posts'))
                .then(editor => {
                    editor.model.document.on('change:data', () => {
                        const isi_posts = editor.getData();
                        document.querySelector('#isi_posts').value = isi_posts;
                    });
                })
                .catch(error => {
                    console.error(error);
                });
        });
    </script>
@endpush
