@extends('admin.layouts.admin')

@section('content')
<div class="auth-main">
    <div class="auth-wrapper v1">
        <div class="auth-form">
            <div class="card my-5">
                <div class="card-body">
                    <div class="text-center mb-5">
                        <a href="javascript:void(0)">
                            <img src="{{ $logo }}" alt="logo">
                        </a>
                    </div>
                    <div>
                        <p class="text-center text-success fw-medium f-20">
                            Your password is updated successfully.
                        </p>
                        <p class="text-center fw-medium">
                            You will be shortly logged out. Kindly login again.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    setTimeout(() => {
        location.href = '{{ route("admin.login") }}';
    }, 3000);
</script>
