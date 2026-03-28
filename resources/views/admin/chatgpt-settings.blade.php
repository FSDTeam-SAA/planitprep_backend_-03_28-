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
                            <li class="breadcrumb-item">Open AI Settings</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="row pt-0">
            <div class="col-md-6">
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

                        @if ($message = Session::pull('success'))
                        <div class="col-sm-12">
                            <div class="alert alert-success alert-dismissible fade show flash-msg" role="alert">
                                <strong>{{ $message }}</strong>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        </div>
                        @endif

                        <form action="{{ route('admin.chatgptSettings.update') }}" method="post" enctype="multipart/form-data" class="form-data">
                            @csrf

                            <div class="row">
                                <div class="form-group col-md-12">
                                    <label class="form-label">API Key</label>
                                    <input name="api_key" type="text" class="form-control" placeholder="API Key" value="{{ old('api_key', $settings->api_key) }}">
                                </div>
                                <div class="form-group col-md-12">
                                    <label class="form-label">Model</label>
                                    <input name="model" type="text" class="form-control" placeholder="Model" value="{{ old('model', $settings->model) }}">
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
        let output_1 = document.getElementById('output-1');
        output_1.src = URL.createObjectURL(event.target.files[0]);
        output_1.onload = function() {
            URL.revokeObjectURL(output_1.src) // free memory
        }
    };
    var loadFile_2 = function(event) {
        let output_2 = document.getElementById('output-2');
        output_2.src = URL.createObjectURL(event.target.files[0]);
        output_2.onload = function() {
            URL.revokeObjectURL(output_2.src) // free memory
        }
    };
    var loadFile_3 = function(event) {
        let output_3 = document.getElementById('output-3');
        output_3.src = URL.createObjectURL(event.target.files[0]);
        output_3.onload = function() {
            URL.revokeObjectURL(output_3.src) // free memory
        }
    };
    var loadFile_4 = function(event) {
        let output_4 = document.getElementById('output-4');
        output_4.src = URL.createObjectURL(event.target.files[0]);
        output_4.onload = function() {
            URL.revokeObjectURL(output_4.src) // free memory
        }
    };
</script>

@endsection