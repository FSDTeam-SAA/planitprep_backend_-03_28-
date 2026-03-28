@extends('admin.layouts.admin')

@section('content')
    <section class="pc-container">
        <div class="pc-content">
            <div class="page-header">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item" aria-current="page">Newsletter</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5>Newsletter Template</h5>
                                    <br>
                                </div>
                            </div>
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
                            <form action="{{ route('admin.update-newsletter') }}" method="post" enctype="multipart/form-data">
                                @csrf

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group tall-ck-600">
                                            <textarea id="content" name="description" class="form-control tall-ck" placeholder="Content">{!! old('description', $newsletter->description) !!}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <input type="submit" value="Save" class="btn btn-primary rounded-0 m-t-20 m-b-20" data-bs-toggle="tooltip" data-bs-original-title="Save changes">
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
            });
        </script>
    @endpush
@endsection
