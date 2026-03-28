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
                    <div class="card-header">
                        <h3 class="float-start">{{ $title }}</h3>
                        <a href="{{ route('admin.medical-issues.create') }}" class="btn btn-primary d-inline-flex align-item-center float-end">
                            <i class="ti ti-plus f-18"></i> Add Medical Issues
                        </a>
                    </div>
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
        ajax: "{{ route('admin.medical-issues') }}",
        processing: true,
        serverSide: true,
        deferRender: true,
        order: [
            [3, 'desc']
        ],
        columns: [{
                title: '#',
                data: 'id',
                name: 'id',
                visible: false,
            }, {
                title: 'Name',
                data: 'name',
                name: 'name',
            },
            {
                title: 'Active',
                data: 'active',
                name: 'active',
                orderable: false,
                searchable: false
            },
            {
                title: 'Date Time',
                data: 'created_at',
                name: 'created_at',
            },
            {
                title: 'Action',
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
            }
        ]
    });

    $(document).on('click', '.deleteSelSingle', function(e) {
        e.preventDefault();
        text = 'you want to delete ?';
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success',
                cancelButton: 'btn btn-danger'
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons
            .fire({
                title: 'Are you sure?',
                text: text,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes',
                cancelButtonText: 'No',
                reverseButtons: true
            })
            .then((result) => {
                if (result.isConfirmed) {
                    var favorite = [];
                    favorite.push($(this).attr('data-val'));
                    var ids = favorite.join(",");
                    $.ajax({
                        url: "{{ route('admin.medical-issues.destroy') }}",
                        type: 'POST',
                        data: {
                            "ids": ids
                        },
                        success: function(res) {
                            if (res.status == true) {
                                table.ajax.reload();
                            }
                        }
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    if (val == 0) {
                        $(this).prop('checked', false);
                    } else {
                        $(this).prop('checked', true);
                    }
                    swalWithBootstrapButtons.fire('Cancelled', '', 'error');
                }
            });
    });

    // active
    $(document).on('click', '.active', function() {
        val = $(this).attr('data-value');
        name = $(this).attr('data-name');
        id = $(this).attr('data-id');
        let temp = $(this);
        let text = '';

        if (val == 0) {
            text = 'Are you sure to activate this record?'
        } else {
            text = 'Are you sure to deactivate this record?'
        }

        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success',
                cancelButton: 'btn btn-danger'
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons
            .fire({
                title: 'Are you sure?',
                text: text,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes',
                cancelButtonText: 'No',
                reverseButtons: true
            })
            .then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('admin.medical-issues.updateActive') }}",
                        type: 'POST',
                        data: {
                            id: id,
                            value: val
                        },
                        success: function(res) {
                            if (res.status == true) {
                                swalWithBootstrapButtons.fire('Updated!', res.msg, 'success');

                                if (val == 1) {
                                    temp.attr('data-value', 0)
                                } else {
                                    temp.attr('data-value', 1)
                                }
                            }
                        }
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    if (val == 0) {
                        $(this).prop('checked', false);
                    } else {
                        $(this).prop('checked', true);
                    }
                    swalWithBootstrapButtons.fire('Cancelled', '', 'error');
                }
            });
    });
</script>
@endpush
