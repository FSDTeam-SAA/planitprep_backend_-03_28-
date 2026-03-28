@extends('admin.layouts.admin')

@section('content')
    <section class="pc-container">
        <div class="pc-content">
            <div class="page-header">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('admin.services') }}">Services</a></li>
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
                                <form action="{{ route('admin.store-service') }}" method="post" enctype="multipart/form-data" class="form-data">
                            @else
                                <form action="{{ route('admin.update-service') }}" method="post" enctype="multipart/form-data" class="form-data">
                                    <input type="hidden" name="id" value="{{ $service->id }}">
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
                                                <label class="form-label">Title <span class="text-danger">*</span></label>

                                                @if ($action == 'create')
                                                    <input required name="title" type="text" class="form-control" maxlength="191" placeholder="Title" value="{{ old('title') }}">
                                                @else
                                                    <input name="title" type="text" class="form-control" maxlength="191" placeholder="Title" value="{{ old('title', $service->title) }}">
                                                @endif
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label class="form-label">Alias</label>

                                                @if ($action == 'create')
                                                    <input name="alias" type="text" class="form-control" maxlength="191" placeholder="Will be auto generated if left blank." value="{{ old('alias') }}">
                                                @else
                                                    <input name="alias" type="text" class="form-control" maxlength="191" placeholder="Will be auto generated if left blank." value="{{ old('alias', $service->alias) }}">
                                                @endif
                                            </div>

                                            <div class="col-md-12 form-group">
                                                <label class="form-label">Short Description <span class="text-danger">*</span></label>

                                                @if ($action == 'create')
                                                    <textarea required name="short_description" class="form-control">{!! old('short_description') !!}</textarea>
                                                @else
                                                    <textarea name="short_description" class="form-control">{!! old('short_description', $service->short_description) !!}</textarea>
                                                @endif
                                            </div>
                                            <div class="col-md-12 form-group">
                                                <label class="form-label">Description <span class="text-danger">*</span></label>

                                                @if ($action == 'create')
                                                    <textarea required name="description" class="form-control ckeditor">{!! old('description') !!}</textarea>
                                                @else
                                                    <textarea name="description" class="form-control ckeditor">{!! old('description', $service->description) !!}</textarea>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 col-xl-4">
                                        <div class="row">
                                            <div class="form-group col-md-12">
                                                <label class="form-label">Image <span class="text-danger">*</span></label>

                                                @if ($action == 'create')
                                                    <input required name="image" class="form-control" type="file" accept="image/jpg, image/jpeg, image/png, image/webp" onchange="loadFile_1(event)" data-bs-original-title="Upload main image." data-bs-toggle="tooltip">
                                                @else
                                                    <input name="image" class="form-control" type="file" accept="image/jpg, image/jpeg, image/png, image/webp" onchange="loadFile_1(event)" data-bs-original-title="Upload main image." data-bs-toggle="tooltip">
                                                @endif
                                            </div>

                                            <div class="col-md-12">
                                                <div class="d-flex">
                                                    @if ($action == 'create')
                                                        <img class="img-fluid image-preview service-preview-bg" src="{{ asset('admin/images/imgpreview-lg.jpg') }}" alt="" id="output-1">
                                                    @else
                                                        <img class="img-fluid image-preview service-preview-bg" src="{{ $service->image }}" alt="" id="output-1">
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="form-group col-md-6">
                                                <label class="form-label">Rank</label>
                                                @if ($action == 'create')
                                                    <input name="rank" type="number" min="0" class="form-control" placeholder="Rank" value="{{ old('rank') }}">
                                                @else
                                                    <input name="rank" type="number" min="0" class="form-control" placeholder="Rank" value="{{ old('rank', $service->rank) }}">
                                                @endif
                                            </div>

                                            <div class="row mt-5">
                                                <div class="col-md-6">
                                                    <div class="form-check form-switch custom-switch-v1">
                                                        @if ($action == 'create')
                                                            <input name="active" type="checkbox" class="form-check-input input-primary" id="activeCheckbox" value='1' checked>
                                                        @else
                                                            <input name="active" type="checkbox" class="form-check-input input-primary" id="activeCheckbox" value='1' @checked(old('checked', $service->active))>
                                                        @endif

                                                        <label class="form-check-label" for="activeCheckbox" data-bs-original-title="Enable/disable this service." data-bs-toggle="tooltip">Active</label>
                                                    </div>
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

    <script>
        var loadFile_1 = function(event) {
            var output_1 = document.getElementById('output-1');
            output_1.src = URL.createObjectURL(event.target.files[0]);
            output_1.onload = function() {
              URL.revokeObjectURL(output_1.src) // free memory
            }
        };
    </script>

@endsection
