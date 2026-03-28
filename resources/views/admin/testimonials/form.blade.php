@extends('admin.layouts.admin')

@section('content')
    <section class="pc-container">
        <div class="pc-content">
            <div class="testimonial-header">
                <div class="testimonial-block">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('admin.testimonials') }}">Testimonials</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row pt-0">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div>
                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li>
                                                    {{ $error }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </div>

                            @if ($action == 'create')
                                <form action="{{ route('admin.store-testimonial') }}" method="post" enctype="multipart/form-data" class="form-data">
                            @else
                                <form action="{{ route('admin.update-testimonial') }}" method="post" enctype="multipart/form-data" class="form-data">
                                    <input type="hidden" name="id" value="{{ $testimonial->id }}">
                            @endif

                                @csrf

                                <div class="row">
                                    <div class="col-md-12 mb-4">
                                        <h6>{{ Str::ucfirst($action) }}</h6>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12 col-xl-8">
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <label class="form-label">Name <span class="text-danger">*</span></label>

                                                @if ($action == 'create')
                                                    <input required name="name" maxlength="50" type="text" class="form-control" maxlength="191" placeholder="User name" value="{{ old('name') }}">
                                                @else
                                                    <input name="name" type="text" maxlength="50" class="form-control" maxlength="191" placeholder="User name" value="{{ old('name', $testimonial->name) }}">
                                                @endif
                                            </div>

                                            <div class="form-group col-md-6">
                                                <label class="form-label">Designation <span class="text-danger">*</span></label>

                                                @if ($action == 'create')
                                                    <input name="designation" type="text" maxlength="50" class="form-control" maxlength="191" placeholder="Designation" value="{{ old('designation') }}">
                                                @else
                                                    <input name="designation" type="text" maxlength="50" class="form-control" maxlength="191" placeholder="Designation" value="{{ old('designation', $testimonial->designation) }}">
                                                @endif
                                            </div>

                                            <div class="col-md-12 form-group">
                                                <label class="form-label">Review <span class="text-danger">*</span></label>

                                                @if ($action == 'create')
                                                    <textarea id="content" name="review" class="form-control" placeholder="Meta review">{!! old('review') !!}</textarea>
                                                @else
                                                    <textarea id="content" name="review" class="form-control" placeholder="Meta review">{!! old('review', $testimonial->review) !!}</textarea>
                                                @endif
                                            </div>

                                            <div class="form-group col-md-3">
                                                <label class="form-label">Rating <span class="text-danger">*</span></label>

                                                @if ($action == 'create')
                                                    <input required name="rating" type="number" min="1" max="5" class="form-control" maxlength="191" placeholder="Rating" value="{{ old('rating', 5) }}">
                                                @else
                                                    <input name="rating" type="number" min="1" max="5" class="form-control" maxlength="191" placeholder="Rating" value="{{ old('rating', $testimonial->rating) }}">
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 col-xl-4">
                                        <div class="row">
                                            <div class="form-group col-md-12">
                                                <label class="form-label">Image</label>

                                                @if ($action == 'create')
                                                    <input name="image" class="form-control" type="file" accept="image/jpg, image/jpeg, image/png, image/webp" onchange="loadFile_1(event)" data-bs-original-title="Upload main image." data-bs-toggle="tooltip">
                                                @else
                                                    <input name="image" class="form-control" type="file" accept="image/jpg, image/jpeg, image/png, image/webp" onchange="loadFile_1(event)" data-bs-original-title="Upload main image." data-bs-toggle="tooltip">
                                                @endif
                                            </div>

                                            <div class="col-md-12">
                                                <div class="d-flex">
                                                    @if ($action == 'create')
                                                        <img class="img-fluid image-preview" src="{{ asset('admin/images/imgpreview-lg.jpg') }}" alt="" id="output-1">
                                                    @else
                                                        <img class="img-fluid image-preview" src="{{ $testimonial->image }}" alt="" id="output-1">
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-md-12">
                                                <div class="form-check form-switch custom-switch-v1 mt-3">
                                                    @if ($action == 'create')
                                                        <input name="active" type="checkbox" class="form-check-input input-primary" id="activeCheckbox" value='1' checked data-bs-original-title="Enable/disable this testimonial." data-bs-toggle="tooltip">
                                                    @else
                                                        <input name="active" type="checkbox" class="form-check-input input-primary" id="activeCheckbox" value='1' @checked(old('checked', $testimonial->active)) data-bs-original-title="Enable/disable this testimonial." data-bs-toggle="tooltip">
                                                    @endif

                                                    <label class="form-check-label" for="activeCheckbox" data-bs-original-title="Enable/disable this testimonial." data-bs-toggle="tooltip">Active</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <input type="submit" value="Save" class="btn btn-primary m-t-20 m-b-20" data-bs-toggle="tooltip" data-bs-original-title="Save changes">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
    </section>

    @push('scripts')

        <script>
            var loadFile_1 = function(event) {
                var output_1 = document.getElementById('output-1');
                output_1.src = URL.createObjectURL(event.target.files[0]);
                output_1.onload = function() {
                URL.revokeObjectURL(output_1.src) // free memory
                }
            };

            $(document).ready(function() {
                CKEDITOR.replace('content', {
                    filebrowserUploadUrl: '{{ route('ckeditor.upload', ['_token' => csrf_token()]) }}',
                    filebrowserUploadMethod: 'form'
                });
            })
        </script>

    @endpush

@endsection
