@extends('admin.layouts.admin')

@section('content')
    <section class="pc-container">
        <div class="pc-content">
            <div class="page-header">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item">Change Password</li>
                            </ul>
                        </div>
                        <div class="col-md-12">
                            <div class="page-header-title">
                                <h3 class="mb-0">Change Password</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row pt-0">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body pt-3">
                            <div>
                                @if(Session::has('message'))
                                    <p class="alert {{ Session::get('alert-class', 'alert-info') }} flash-msg">{{ Session::get('message') }}</p>
                                @endif
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

                            <form action="{{ route('admin.update-password') }}" method="post">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6 col-xl-4">
                                        <div class="form-group">
                                            <label class="form-label">Current Password <span class="text-danger">*</span></label>
                                            <input required name="current_password" type="password" min="8" class="form-control" placeholder="Current password" value="{{ old('current_password') }}" autofocus>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">New Password <span class="text-danger">*</span></label>
                                            <input id="new-password" required name="new_password" type="password" min="8" class="form-control" maxlength="20" placeholder="New password ( minimum 8 characters )" value="{{ old('new_password') }}">
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Confirm New Password <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input id="confirm-password" required name="confirm_password" type="text" min="8" class="form-control round" maxlength="20" placeholder="Re ente new password" value="{{ old('confirm_password') }}">
                                                <span class="input-group-text">
                                                    <i id="password-match-tick" class="ti ti-check f-20 text-red-100"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <input type="submit" value="Update password" class="btn btn-primary m-t-20 m-b-20" data-bs-toggle="tooltip" data-bs-original-title="Update password">
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
                $('#confirm-password, #new-password').keyup(function() {
                    if ($('#confirm-password').val() == $('#new-password').val()) {
                        if ($('#confirm-password').val().length >= 8) {
                            $('#password-match-tick').removeClass('text-red-100').addClass('text-green-900').addClass('fw-bolder');;
                        }
                    } else {
                        $('#password-match-tick').addClass('text-red-100').removeClass('text-green-900').removeClass('fw-bolder');;
                    }
                })
            });
        </script>
    @endpush

@endsection
