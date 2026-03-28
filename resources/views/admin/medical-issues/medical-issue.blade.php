@extends('admin.layouts.admin')
@section('content')
<!-- [ Main Content ] start -->
<div class="pc-container">
    <div class="pc-content">
        <!-- [ breadcrumb ] start -->
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.medical-issues') }}">Medical Issues</a></li>
                            <li class="breadcrumb-item" aria-current="page">{{ $title }}</li>
                        </ul>
                    </div>

                </div>
            </div>
        </div>
        <!-- [ breadcrumb ] end -->

        <!-- [ Main Content ] start -->
        <div class="row">
            <!-- [ sample-page ] start -->
            <div class="col-sm-6">
                <div class="card">
                    <div class="card-header">
                        <h5>{{ $title }}</h5>
                    </div>
                    <div class="card-body">
                        @if($action == 'Edit')
                        <form class="" role="form" id="edit_form" action="{{ route('admin.medical-issues.update',$record->id) }}" method="post" enctype="multipart/form-data">

                            @else
                            <form class="" method="POST" id="add_form" action="{{ route('admin.medical-issues.store') }}" enctype="multipart/form-data">
                                @endif
                                @csrf
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label" for="name">Name</label>
                                        @if(isset($record->name))
                                        @php $name=$record->name @endphp
                                        @elseif(old('name'))
                                        @php $name=old('name') @endphp
                                        @else
                                        @php $name='' @endphp
                                        @endif
                                        <input type="text" class="form-control  @error('name') is-invalid  @enderror" id="name" name="name" placeholder="Name" value="{{ $name }}">

                                        @error('name')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-12  mb-3">
                                        <div class="form-group row align-items-center">
                                            <label class="col-form-label">Active</label>
                                            <div class="col-sm-9">
                                                @if(isset($record->active))
                                                @if($record->active==1)
                                                @php $active='checked' @endphp
                                                @else
                                                @php $active='' @endphp
                                                @endif
                                                @else
                                                @php $active='checked' @endphp
                                                @endif
                                                <div class="form-check form-switch custom-switch-v1 form-check-inline">
                                                    <input type="checkbox" class="form-check-input input-primary" id="active" name="active" value=1 {{ $active }}>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <button class="btn btn-primary" type="submit">Submit</button>
                            </form>
                    </div>
                </div>
            </div>
            <!-- [ sample-page ] end -->
        </div>
        <!-- [ Main Content ] end -->
    </div>
</div>
@endsection
@push('scripts')
<script type="text/javascript">
    $(document).ready(function() {
        var fileInput = document.getElementById('image');
        var fileDisplayArea = document.getElementById('image_display');
        fileInput.addEventListener('change', function(e) {
            var file = fileInput.files[0];
            var imageType = /image.*/;
            if (file.type.match(imageType)) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    fileDisplayArea.innerHTML = "";
                    var images = new Image();
                    images.src = reader.result;
                    fileDisplayArea.appendChild(images);
                }

                reader.readAsDataURL(file);
            } else {
                fileDisplayArea.innerHTML = "File not supported!"
            }
        });
    });
</script>
@endpush
