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

                        <form action="{{ route('admin.loginSettings.update') }}" method="post" enctype="multipart/form-data" class="form-data">
                            @csrf

                            <div class="row">
                                <div class="form-group col-12 align-items-center">
                                    <label class="col-form-label">Enable Email Login</label>
                                    <br />
                                    @if(isset($settings->email))
                                    @if($settings->email==1)
                                    @php $email='checked' @endphp
                                    @else
                                    @php $email='' @endphp
                                    @endif
                                    @else
                                    @php $email='' @endphp
                                    @endif
                                    <div class="form-check form-switch custom-switch-v1 form-check-inline">
                                        <input type="checkbox" class="form-check-input input-primary" id="email" name="email" value=1 {{ $email }}>
                                    </div>
                                </div>
                                <div class="form-group col-12 align-items-center">
                                    <label class="col-form-label">Enable Phone Number Login</label>
                                    <br />
                                    @if(isset($settings->phone))
                                    @if($settings->phone==1)
                                    @php $phone='checked' @endphp
                                    @else
                                    @php $phone='' @endphp
                                    @endif
                                    @else
                                    @php $phone='' @endphp
                                    @endif
                                    <div class="form-check form-switch custom-switch-v1 form-check-inline">
                                        <input type="checkbox" class="form-check-input input-primary" id="phone" name="phone" value=1 {{ $phone }}>
                                    </div>
                                </div>

                                <div class="form-group col-12 align-items-center">
                                    <label class="col-form-label">Enable Facebook Login</label>
                                    <br />
                                    @if(isset($settings->facebook))
                                    @if($settings->facebook==1)
                                    @php $facebook='checked' @endphp
                                    @else
                                    @php $facebook='' @endphp
                                    @endif
                                    @else
                                    @php $facebook='' @endphp
                                    @endif
                                    <div class="form-check form-switch custom-switch-v1 form-check-inline">
                                        <input type="checkbox" class="form-check-input input-primary" id="facebook" name="facebook" value=1 {{ $facebook }}>
                                    </div>
                                </div>

                                <div class="form-group col-12 align-items-center">
                                    <label class="col-form-label">Enable Google Login</label>
                                    <br />
                                    @if(isset($settings->google))
                                    @if($settings->google==1)
                                    @php $google='checked' @endphp
                                    @else
                                    @php $google='' @endphp
                                    @endif
                                    @else
                                    @php $google='' @endphp
                                    @endif
                                    <div class="form-check form-switch custom-switch-v1 form-check-inline">
                                        <input type="checkbox" class="form-check-input input-primary" id="google" name="google" value=1 {{ $google }}>
                                    </div>
                                </div>
                                
                                <div class="form-group col-12 align-items-center">
                                    <label class="col-form-label">Enable Apple Login</label>
                                    <br />
                                    @if(isset($settings->apple))
                                    @if($settings->apple==1)
                                    @php $apple='checked' @endphp
                                    @else
                                    @php $apple='' @endphp
                                    @endif
                                    @else
                                    @php $apple='' @endphp
                                    @endif
                                    <div class="form-check form-switch custom-switch-v1 form-check-inline">
                                        <input type="checkbox" class="form-check-input input-primary" id="apple" name="apple" value=1 {{ $apple }}>
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

@endsection