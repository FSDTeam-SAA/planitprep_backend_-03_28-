@extends('admin.layouts.admin')

@section('content')
    <div class="pc-container">
        <div class="pc-content">
            <div class="page-header">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item" aria-current="page">{{ $title }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5>{{ $title }}</h5>
                                </div>
                                <div>
                                    <div class="d-flex justify-content-end">
                                        <div data-bs-toggle="tooltip" data-bs-original-title="Add new page.">
                                            <a href="{{ route('admin.pages.create') }}" role="button" class="btn btn-primary m-b-20 rounded-0">
                                                + Add Page
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div>
                                @if(Session::has('message'))
                                    <p class="alert {{ Session::get('alert-class', 'alert-info') }} flash-msg">{{ Session::get('message') }}</p>
                                @endif
                            </div>
                            <div class="dt-responsive table-responsive">
                                <table id="jsource-table" class="table  table-bordered nowrap"> </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')

    <script type="text/javascript">
        var table = $('#jsource-table').DataTable({
            ajax: "{{ route('admin.pages.index') }}",
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
                },
                {
                    title: 'Title',
                    data: 'title',
                    name: 'title',
                },
                {
                    title: 'Alias',
                    data: 'alias',
                    name: 'alias',
                },
                {
                    title: 'Created On',
                    data: 'created_at',
                    name: 'created_at',
                },
                {
                    title: 'Active',
                    data: 'active',
                    name: 'active',
                    searchable: false
                },
                {
                    title: 'Action',
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
            ],
            fixedHeader: true,
            fixedColumns: {
                rightColumns: 1
            }
        });

        table.on('draw', function() {
            $('[data-bs-toggle="tooltip"]').tooltip();
        });

        $(document).on('click', '.active_item', function() {
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
                            url: "{{ route('admin.pages.toggle') }}",
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
                            url: "{{ route('admin.pages.destroy') }}",
                            type: 'POST',
                            data: {
                                "id": ids
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
    </script>

    @endpush
@endsection
