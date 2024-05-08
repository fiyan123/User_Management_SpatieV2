@extends('layouts.admin')
@section('content')
    <div class="card">
        <form id="form_data" enctype="multipart/form-data">
            @csrf
            <div class="card-header">
                <h5>Detail Posts : {{ $posts->judul }}</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label for="judul" class="form-label">Posts Name</label>
                            <input type="text" class="form-control" value="{{ old('judul', $posts->judul) }}"
                                id="judul" placeholder="Masukkan Judul Posts" name="judul" readonly>
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
                    <a href="{{ route('posts') }}" class="btn btn-primary btn-light-primary me-2"><i
                            class="ki-duotone ki-arrow-left fs-4 ms-1 me-0">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>Back</a>
                </div>
            </center>
        </form>
    </div>
@endsection
@push('script_processing')
    <script>
        $(document).ready(function() {
            ClassicEditor
                .create(document.querySelector('#isi_posts'), {
                    readOnly: true,
                })
                .catch(error => {
                    console.error(error);
                });
        });
    </script>
@endpush
