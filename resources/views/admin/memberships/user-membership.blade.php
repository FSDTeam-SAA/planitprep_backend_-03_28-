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
                            <li class="breadcrumb-item"><a href="{{ route('admin.users') }}">Users</a></li>
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
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5>{{ $title }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                @if ($userMembership != NULL)
                                    <div class="table-responsive">
                                        <table class="table mb-5">
                                            <tr>
                                                <td>User</td>
                                                <td>{{ $userMembership->user->full_name }}</td>
                                            </tr>
                                            <tr>
                                                <td>Membership</td>
                                                <td>{{ $userMembership->membership->title }}</td>
                                            </tr>
                                            <tr>
                                                <td>Start Date</td>
                                                <td>{{ $userMembership->start_date }}</td>
                                            </tr>
                                            <tr>
                                                <td>End Date</td>
                                                <td>{{ $userMembership->end_date }}</td>
                                            </tr>
                                            <tr>
                                                <td>Price</td>
                                                <td>&#x20B9;{{ $userMembership->membership->price }}</td>
                                            </tr>
                                            <tr>
                                                <td>Month</td>
                                                <td>{{ $userMembership->membership->month }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                @else
                                    <form role="form" id="edit_form" action="{{ route('admin.memberships.store-user-membership') }}" method="post">
                                        @csrf
                                        <input type="hidden" name="id" value="{{$user->id}}">
                                        <div class="form-group">
                                            <label for="">Select Membership</label>
                                            <select id="membership-select" name="membership" class="form-select">
                                                <option disabled selected>Not Selected</option>
                                                @foreach ($memberships as $item)
                                                    <option value="{{$item->id}}">{{$item->title}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group mt-3">
                                            <input class="btn btn-primary" type="submit" value="Save">
                                        </div>
                                    </form>
                                @endif
                            </div>
                        </div>
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
<script>
    $(document).ready(function() {
        let x = @json($userMembership);

        if (x != null) {
            let y = x.membership_id;

            $('#membership-select').val(y);
        }
    });
</script>
@endpush
