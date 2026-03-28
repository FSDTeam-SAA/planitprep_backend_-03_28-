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
                            <li class="breadcrumb-item"><a href="{{ route('admin.expiring-memberships') }}">Expiring Memberships</a></li>
                            <li class="breadcrumb-item" aria-current="page">List</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- [ breadcrumb ] end -->

        <!-- [ Main Content ] start -->
        <div class="row">
            @if ($message = Session::get('success'))
                <div class="col-sm-12">
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>{{ $message }}</strong>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            @endif

            <!-- [ sample-page ] start -->
            <div class="col-sm-12">
                <div class="card ">
                    <div class="card-body">
                        <div class="dt-responsive table-responsive">
                            <table id="jsource-table" class="table  table-bordered nowrap"> </table>
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
<script src="{{ asset('admin/js/plugins/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('admin/js/plugins/dataTables.bootstrap5.min.js') }}"></script>
<script src="{{ asset('admin/js/plugins/sweetalert2.all.min.js') }}"></script>
<script src="{{ asset('admin/js/pages/ac-alert.js') }}"></script>
<script type="text/javascript">
    var table = $('#jsource-table').DataTable({
        ajax: "{{ route('admin.expiring-memberships') }}",
        processing: true,
        serverSide: true,
        deferRender: true,
        order: [
            [4, 'asc']
        ],
        columns: [{
                title: '#',
                data: 'id',
                name: 'id',
                visible: false,
            },
            {
                title: 'User',
                data: 'user',
                name: 'user',
            },
            {
                title: 'Membership',
                data: 'membership',
                name: 'membership',
            },
            {
                title: 'Start Date',
                data: 'start_date',
                name: 'start_date',
            },
            {
                title: 'End Date',
                data: 'end_date',
                name: 'end_date',
            },
            {
                title: 'Price',
                data: 'price',
                name: 'price',
            },
            {
                title: 'Month',
                data: 'month',
                name: 'month',
            },
        ]
    });

</script>
@endpush
