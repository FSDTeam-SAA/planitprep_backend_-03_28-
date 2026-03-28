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
                            <li class="breadcrumb-item"><a href="{{ route('admin.settings') }}">Basic Settings</a></li>
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

                        @if ($message = Session::pull('success'))
                        <div class="col-sm-12">
                            <div class="alert alert-success alert-dismissible fade show flash-msg" role="alert">
                                <strong>{{ $message }}</strong>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        </div>
                        @endif

                        <form action="{{ route('admin.settings.update') }}" method="post" enctype="multipart/form-data" class="form-data">
                            @csrf

                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label class="form-label">Phone</label>
                                    <input name="phone" type="text" maxlength="20" class="form-control" placeholder="Phone number" value="{{ old('phone', $settings->phone) }}">
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="form-label">Email</label>
                                    <input name="email" type="email" maxlength="50" class="form-control" placeholder="Email address" value="{{ old('email', $settings->email) }}">
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="form-label">Facebook</label>
                                    <input name="facebook" type="text" maxlength="250" class="form-control" placeholder="" value="{{ old('facebook', $settings->facebook) }}">
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="form-label">Instagram</label>
                                    <input name="instagram" type="text" maxlength="250" class="form-control" placeholder="" value="{{ old('instagram', $settings->instagram) }}">
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="form-label">X</label>
                                    <input name="x" type="text" maxlength="250" class="form-control" placeholder="" value="{{ old('x', $settings->x) }}">
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="form-label">Youtube</label>
                                    <input name="youtube" type="text" maxlength="250" class="form-control" placeholder="" value="{{ old('youtube', $settings->youtube) }}">
                                </div>
                                <div class="form-group col-md-8">
                                    <label class="form-label">Marquee</label>
                                    <input name="marquee" type="text" maxlength="250" class="form-control" placeholder="" value="{{ old('marquee', $settings->marquee) }}">
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="form-label">
                                        Contact No.
                                        <small class="text-muted">
                                            (<b>Note:</b> Please add the country code prefix (e.g., +1, +91))
                                        </small>
                                    </label>
                                    <input name="contact_no" type="text" maxlength="250" class="form-control" placeholder="" value="{{ old('contact_no', $settings->contact_no) }}">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="row">
                                                <div class="form-group col-md-12">
                                                    <label class="form-label">Logo</label>
                                                    <input name="logo" class="form-control" type="file" accept="image/jpg, image/jpeg, image/png, image/webp" onchange="loadFile_1(event)" data-bs-original-title="Upload logo." data-bs-toggle="tooltip">
                                                </div>

                                                <div class="col-md-12">
                                                    <label class="form-label">&nbsp;</label>
                                                    <div class="d-flex">
                                                        <img class="img-fluid image-preview bg-gray-100" src="{{ $settings->logo }}" alt="" id="output-1">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="row">
                                                <div class="form-group col-md-12">
                                                    <label class="form-label">Mobile Logo</label>
                                                    <input name="mobile_logo" class="form-control" type="file" accept="image/jpg, image/jpeg, image/png, image/webp" onchange="loadFile_2(event)" data-bs-original-title="Upload logo." data-bs-toggle="tooltip">
                                                </div>

                                                <div class="col-md-12">
                                                    <label class="form-label">&nbsp;</label>
                                                    <div class="d-flex">
                                                        <img class="img-fluid image-preview bg-gray-100" src="{{ $settings->mobile_logo }}" alt="" id="output-2">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Address</label>
                                        <textarea name="address" maxlength="250" rows="4" class="form-control ckeditor" placeholder="">{{ old('address', $settings->address) }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <hr>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="row">
                                                <div class="form-group col-md-12">
                                                    <label class="form-label">Apple Store Image</label>
                                                    <input name="apple_store_image" class="form-control" type="file" accept="image/jpg, image/jpeg, image/png, image/webp" onchange="loadFile_3(event)" data-bs-original-title="Upload logo." data-bs-toggle="tooltip">
                                                </div>

                                                <div class="col-md-12">
                                                    <label class="form-label">&nbsp;</label>
                                                    <div class="d-flex">
                                                        <img class="img-fluid image-preview bg-gray-100" src="{{ $settings->apple_store_image }}" alt="" id="output-3">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="row">
                                                <div class="form-group col-md-12">
                                                    <label class="form-label">Android Store Image</label>
                                                    <input name="android_store_image" class="form-control" type="file" accept="image/jpg, image/jpeg, image/png, image/webp" onchange="loadFile_4(event)" data-bs-original-title="Upload logo." data-bs-toggle="tooltip">
                                                </div>

                                                <div class="col-md-12">
                                                    <label class="form-label">&nbsp;</label>
                                                    <div class="d-flex">
                                                        <img class="img-fluid image-preview bg-gray-100" src="{{ $settings->android_store_image }}" alt="" id="output-4">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="form-group col-md-12">
                                            <label class="form-label">Apple Store Link</label>
                                            <input name="apple_store_link" type="text" maxlength="250" class="form-control" placeholder="" value="{{ old('apple_store_link', $settings->apple_store_link) }}">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-12">
                                            <label class="form-label">Android Store Link</label>
                                            <input name="android_store_link" type="text" maxlength="250" class="form-control" placeholder="" value="{{ old('android_store_link', $settings->android_store_link) }}">
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="row">
                                <div class="col-md-12">
                                    <hr>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-3 align-items-center">
                                    <label class="col-form-label">Free Membership</label>
                                <br />
                                        @if(isset($settings->enable_free_membership))
                                        @if($settings->enable_free_membership==1)
                                        @php $enable_free_membership='checked' @endphp
                                        @else
                                        @php $enable_free_membership='' @endphp
                                        @endif
                                        @else
                                        @php $enable_free_membership='' @endphp
                                        @endif
                                        <div class="form-check form-switch custom-switch-v1 form-check-inline">
                                            <input type="checkbox" class="form-check-input input-primary" id="enable_free_membership" name="enable_free_membership" value=1 {{ $enable_free_membership }}>
                                            <!-- <label class="form-check-label" for="customCheckinlh1">Inline 1</label> -->
                                        </div>
                                </div>

                                <div class="form-group col-3">
                                @if(isset($settings->free_upto))
                                        @if($settings->free_upto=="")
                                        @php $free_upto=$settings->free_upto @endphp
                                        @else
                                        @php $free_upto='' @endphp
                                        @endif
                                        @else
                                        @php $free_upto='' @endphp
                                        @endif
                                    <label class="form-label">Free Upto</label>
                                    <input name="free_upto" type="date" class="form-control" placeholder="" value="{{ $settings->free_upto }}">
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
