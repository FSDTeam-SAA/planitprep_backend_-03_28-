@extends('admin.layouts.admin')
@section('content')
<div class="pc-container">
    <div class="pc-content">
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.users') }}">User</a></li>
                            <li class="breadcrumb-item" aria-current="page">List</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            @if ($message = Session::get('success'))
                <div class="col-sm-12">
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>{{ $message }}</strong>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            @endif

            <div class="col-sm-12">
                <div class="card ">
                    <div class="card-header">
                        <h3 class="float-start">{{ $title }}</h3>
                        <a href="{{ route('admin.users.create') }}" class="btn btn-primary d-inline-flex align-item-center float-end">
                            <i class="ti ti-plus f-18"></i> Add User
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

<div class="modal fade" id="membershipsModal" tabindex="-1" aria-labelledby="membershipsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="select-plan-modal-header modal-header pt-3 pb-3 border-0">
                <h5 class="modal-title" id="membershipsModalLabel">Select Plan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div id="modal-body" class="modal-body py-3">
                <div>
                    <i class="fa fa-clone d-none" aria-hidden="true" title="Copy"></i>
                    <i id="copy-status"></i>
                    <div id="url-div" class="input-group mb-3 d-none">
                        <div class="input-group mt-2">
                            <span class="input-group-text w-116px">Payment URL</span>
                            <input type="text" id="payment-url" class="form-control" readonly>
                            <button id="copy-icon" class="btn btn-primary" type="button">
                                <i class="fa fa-clone" aria-hidden="true" title="Copy"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div id="list-area"></div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script src="{{ asset('admin/js/plugins/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('admin/js/plugins/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('admin/js/plugins/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('admin/js/pages/ac-alert.js') }}"></script>
    <script type="text/javascript">
        var table = $('#jsource-table').DataTable({
            ajax: "{{ route('admin.users') }}",
            processing: true,
            serverSide: true,
            deferRender: true,
            order: [
                [5, 'desc']
            ],
            columns: [{
                    title: '#',
                    data: 'id',
                    name: 'id',
                    visible: false,
                }, {
                    title: 'Username',
                    data: 'username',
                    name: 'username',
                },
                {
                    title: 'Email',
                    data: 'email',
                    name: 'email',
                },
                {
                    title: 'Membership',
                    data: 'membership',
                    name: 'membership',
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
                    className: 'text-end',
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
                            url: "{{ route('admin.users.destroy') }}",
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
        $(document).on('click', '.form-check-input', function() {
            val = $(this).attr('data-value');
            name = $(this).attr('data-name');
            id = $(this).attr('data-id');
            text = name + '! You want to deactivate ?';

            if (val == 0) {
                text = name + '! You want to activate ?';
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
                            url: "{{ route('admin.users.updateActive') }}",
                            type: 'POST',
                            data: {
                                id: id,
                                value: val
                            },
                            success: function(res) {
                                if (res.status == true) {
                                    swalWithBootstrapButtons.fire('Updated!', res.msg, 'success');

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
    <script>
        function membershipsModal(id) {
            let url = '{{ route('admin.fetch-memberships') }}';

            $.post(url, {
                '_token': $('meta[name="csrf-token"]').attr('content'),
                'user_id': id,
            }, function(data, status) {
                $('#membershipsModal').find('#list-area').html(data);
                $('#payment-url').val('');
                $('#copy-icon').addClass('d-none');
                $('#copy-status').text('');
                $('#membershipsModal').modal('show');
            });
        }

        function generatePaymentLink(userId, amount, planId) {
            let url = '{{ route('admin.generate-payment-link') }}';

            $.post(url, {
                '_token': $('meta[name="csrf-token"]').attr('content'),
                'user_id': userId,
                'amount': amount,
                'plan_id': planId,
                'coupon': $('#coupon-select').val()
            }, function(data, status) {
                $('#url-div').removeClass('d-none');
                $('#membershipsModal').find('#payment-url').val(data);
                $('#copy-icon').removeClass('d-none');
                $('#copy-status').text('');
            });
        }


        var copyEle = document.getElementById('payment-url');
        var copyIconEle = document.getElementById('copy-icon');

        copyIconEle.addEventListener('click', function () {
            const ta = document.createElement('textarea');
            ta.value = copyEle.value;
            ta.setAttribute('readonly', '');
            ta.style.position = 'absolute';
            ta.style.left = '-9999px';
            document.body.appendChild(ta);

            ta.select();
            ta.focus();
            ta.setSelectionRange(0, ta.value.length);

            try {
                const successful = document.execCommand('copy');

                if (successful) {
                    document.getElementById('copy-status').innerText = 'Copied';
                    console.log('Text copied to clipboard:', ta.value);
                } else {
                    throw new Error('Copy command failed');
                }
            } catch (err) {
                console.error('Failed to copy text:', err);
            } finally {
                document.body.removeChild(ta);
            }
        });

        $(document).ready(function() {
            $('#membershipsModal').on('shown.bs.modal', function () {
                $('#coupon-select').change(function() {
                    if ($(this).val() == 'NULL') {
                        $('#url-div').addClass('d-none');
                        $('#membershipsModal').find('#payment-url').val('');
                        $('#copy-icon').addClass('d-none');
                        $('#copy-status').text('');
                    }
                });
            });
        });
    </script>
@endpush

@endsection
