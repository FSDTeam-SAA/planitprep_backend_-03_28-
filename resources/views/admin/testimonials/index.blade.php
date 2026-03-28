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
                            <li class="breadcrumb-item"><a href="{{ route('admin.testimonials') }}">Testimonials</a></li>
                            <li class="breadcrumb-item" aria-current="page">List</li>
                        </ul>
                    </div>

                </div>
            </div>
        </div>

        <div class="row pt-0">
            @if ($message = Session::pull('success'))
                <div class="col-sm-12">
                    <div class="alert alert-success alert-dismissible fade show flash-msg" role="alert">
                        <strong>{{ $message }}</strong>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            @endif

            <div class="col-sm-12">
                <div class="card ">
                    <div class="card-header">
                        <h3 class="float-start">{{ $title }}</h3>
                        <a href="{{ route('admin.add-testimonial') }}" class="btn btn-primary d-inline-flex align-item-center float-end" data-bs-original-title="Add new testimonial." data-bs-toggle="tooltip">
                            <i class="ti ti-plus f-18"></i> Add Testimonial
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="dt-responsive table-responsive">
                            <table id="jsource-table" class="table  table-bordered nowrap"> </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')

<script type="text/javascript">
    var table = $('#jsource-table').DataTable({
        ajax: "{{ route('admin.testimonials') }}",
        processing: true,
        serverSide: true,
        deferRender: true,
        order: [
            [6, 'desc']
        ],
        columns: [{
                title: '#',
                data: 'id',
                name: 'id',
                visible:false,
            },
            {
                title: 'Image',
                data: 'image',
                name: 'image',
                searchable: false,
                orderable: false,
            },
            {
                title: 'Name',
                data: 'name',
                name: 'name',
            },
            {
                title: 'Designation',
                data: 'designation',
                name: 'designation',
            },
            {
                title: 'Rating',
                data: 'rating',
                name: 'rating',
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

    table.on('draw', function() {
        $('[data-bs-toggle="tooltip"]').tooltip();
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
                        url: "{{ route('admin.testimonial.destroy') }}",
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
    $(document).on('click', '.active_item', function() {
        val = $(this).attr('data-value');
        name = $(this).attr('data-name');
        id = $(this).attr('data-id');
        let temp = $(this);
        text = name + 'You want to deactivate ?';

        if (val == 0) {
            text = name + 'You want to activate ?';
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
                        url: "{{ route('admin.testimonial.updateActive') }}",
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
