<script src="{{ asset('admin/js/plugins/popper.min.js') }}"></script>
<script src="{{ asset('admin/js/plugins/simplebar.min.js') }}"></script>
<script src="{{ asset('admin/js/plugins/bootstrap.min.js') }}"></script>
<script src="{{ asset('admin/js/fonts/custom-font.js') }}"></script>
<script src="{{ asset('admin/js/pcoded.js') }}"></script>
<script src="{{ asset('admin/js/plugins/feather.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<script src="{{ asset('admin/js/plugins/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('admin/js/plugins/dataTables.bootstrap5.min.js') }}"></script>
<script src="{{ asset('admin/js/plugins/sweetalert2.all.min.js') }}"></script>
<script src="{{ asset('admin/js/pages/ac-alert.js') }}"></script>

<script src="{{ asset('admin/plugins/ckeditor/ckeditor.js') }}"></script>
<script src="{{ asset('admin/plugins/ckeditor/adapters/jquery.js') }}"></script>

<script src="{{ asset('admin/js/select2.min.js' ) }}"></script>
<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
        }
    });
    $(".alert-dismissible").delay(4000).slideUp(200, function() {
        $(this).alert('close');
    });
</script>
@stack('scripts')
