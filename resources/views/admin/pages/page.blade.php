@extends('admin.layouts.admin')

@section('content')
    <section class="pc-container">
        <div class="pc-content">
            <div class="page-header">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item" aria-current="page"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                                <li class="breadcrumb-item">
                                    <a href="{{ route('admin.pages.index') }}">Pages</a>
                                </li>
                                <li class="breadcrumb-item" aria-current="page">{{ $action }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body mb-3">
                            <div class="row d-flex justify-content-between">
                                <div class="col-md-6">
                                    <h5>Page</h5>
                                </div>
                                <div class="col-md-6 text-end">
                                    <a href="{{ route('admin.pages.index') }}" data-bs-toggle="tooltip" title="Back">
                                        <i class="fa fa-arrow-left text-dark" aria-hidden="true"></i>
                                    </a>
                                </div>
                            </div>
                            <hr>

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

                            @if ($action == 'Create')
                                <form action="{{ route('admin.pages.store') }}" method="post" enctype="multipart/form-data">
                            @else
                                <form action="{{ route('admin.pages.update', ['page' => $page->id]) }}" method="post" enctype="multipart/form-data">
                                <input type="hidden" name="_method" value="PUT">
                            @endif
                                @csrf

                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label class="form-label">Title <span class="text-danger">*</span></label>
                                        @if ($action == 'Create')
                                            <input name="title" type="text" maxlength="250" class="form-control" placeholder="Page title" value="{{ old('title') }}" autofocus>
                                        @else
                                            <input name="title" type="text" maxlength="250" class="form-control" placeholder="Page title" value="{{ old('title', $page->title) }}">
                                        @endif
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label class="form-label">Alias</label>
                                        @if ($action == 'Create')
                                            <input id="alias-ele" name="alias" maxlength="250" type="text" class="form-control" placeholder="Will be auto generated if left blank" value="{{ old('alias') }}">
                                        @else
                                            <input id="alias-ele" name="alias" maxlength="250" type="text" class="form-control" placeholder="Will be auto generated if left blank" value="{{ old('alias', $page->alias) }}">
                                        @endif
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-12 tall-ck">
                                        <label class="form-label">Content <span class="text-danger">*</span></label>
                                        @if ($action == 'Create')
                                            <textarea id="content" name="content" class="form-control" placeholder="Content">{!! old('content') !!}</textarea>
                                        @else
                                            <textarea id="content" name="content" class="form-control" placeholder="Content">{!! old('content', $page->content) !!}</textarea>
                                        @endif
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-12">
                                        <label class="form-label">Meta Title</label>
                                        @if ($action == 'Create')
                                            <input type="text" id="meta_title" maxlength="250" name="meta_title" class="form-control" value="{{ old('meta_title') }}" />
                                        @else
                                            <input type="text" id="meta_title" maxlength="250" name="meta_title" class="form-control" value="{{ old('meta_title', $page->meta_title) }}" />
                                        @endif
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label class="form-label">Meta Description</label>
                                        @if ($action == 'Create')
                                            <textarea id="meta_description" maxlength="250" name="meta_description" class="form-control">{{ old('meta_description') }}</textarea>
                                        @else
                                            <textarea id="meta_description" maxlength="250" name="meta_description" class="form-control">{{ old('meta_description', $page->meta_description) }}</textarea>
                                        @endif
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-check form-switch custom-switch-v1 mt-3 mb-5">
                                            @if ($action == 'Create')
                                                <input name="active" type="checkbox" class="form-check-input input-primary" id="activeCheckbox" value='1' checked>
                                                <label class="form-check-label" for="activeCheckbox">Active</label>
                                            @else
                                                <input name="active" type="checkbox" class="form-check-input input-primary" id="activeCheckbox" value='1' @checked($page->active)>
                                                <label class="form-check-label" for="activeCheckbox">Active</label>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="row bottom-btn-bar">
                                    <div class="col-md-12">
                                        <input type="submit" value="Save" class="btn btn-primary m-t-20 m-b-20 rounded-0" data-bs-toggle="tooltip" data-bs-original-title="Save changes">
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
            $(document).ready(function() {
                CKEDITOR.replace('content', {
                    filebrowserUploadUrl: '{{ route('ckeditor.upload', ['_token' => csrf_token()]) }}',
                    filebrowserUploadMethod: 'form'
                });
            })
        </script>
    @endpush
@endsection
