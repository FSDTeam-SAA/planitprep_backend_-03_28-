@extends('admin.layouts.admin')
@section('content')
@php $readonly=''; @endphp
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
                            <li class="breadcrumb-item"><a href="{{ route('admin.coupons') }}">Coupons</a></li>
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
                        @if($action == 'Edit')
                        <form class="" role="form" id="edit_form" action="{{ route('admin.coupon-users.update',$record->id) }}" method="post" enctype="multipart/form-data">

                            @else
                            <form class="" method="POST" id="add_form" action="{{ route('admin.coupon-users.store') }}" enctype="multipart/form-data">
                                @endif
                                @csrf
                                <div class="row">
                                    <div class="col-md-12 col-12 mb-3">
                                        <label>Coupon <small class="red">* </small></label>
                                        @if(old('coupon_id'))
                                        @php $coupon_id = old('coupon_id'); @endphp
                                        @elseif(isset($record->coupon_id) && $record->coupon_id != '')
                                        @php $coupon_id = $record->coupon_id; @endphp
                                        @else
                                        @php $coupon_id = ''; @endphp
                                        @endif
                                        <select name="coupon_id" id="coupon_id" class="form-control  @error('coupon_id') is-invalid @enderror">
                                            <option value="">Select</option>
                                            @foreach($coupons as $coupon)
                                            <option value="{{ $coupon->id }}" {!! ($coupon->id==$coupon_id) ? 'selected' : '' !!}>{{ $coupon->code }}</option>
                                            @endforeach
                                        </select>
                                        @error('coupon_id')
                                        <span class="error">{{ $message }}</span>
                                        @enderror
                                    </div>


                                    <div class="col-md-12 col-12 mb-3">
                                        <label>Assign To <small class="red">* </small></label>
                                        @if(old('assign_to'))
                                        @php $assign_to = old('assign_to'); @endphp
                                        @elseif(isset($record->assign_to) && $record->assign_to != '')
                                        @php $assign_to = $record->assign_to; @endphp
                                        @else
                                        @php $assign_to = ''; @endphp
                                        @endif
                                        <select required name="assign_to" class="form-control @error('assign_to') is-invalid @enderror" id="assign_to">
                                            <option value="all" {!! ($assign_to=='all' ) ? 'selected' : '' !!}>All</option>
                                            <option value="individual" {!! ($assign_to=='individual' ) ? 'selected' : '' !!}>Individuals</option>
                                        </select>
                                        @error('assign_to')
                                        <span class="error">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-md-12 form-group users-div mb-3">
                                        <label>Users <span class="text-danger">*</span></label>
                                        <select class="form-control @error('user_ids') is-invalid @enderror" name="user_ids[]" id="user_ids" multiple>
                                        </select>
                                    </div>

                                    <div class="col-md-12 col-12 mb-3">
                                        <label>Expiry Date <small class="red">* </small></label>
                                        @if (isset($record))
                                        @php $expiry_date=date('Y-m-d',strtotime($record->expiry_date)) @endphp
                                        @elseif(old('expiry_date'))
                                        @php $expiry_date=old('expiry_date') @endphp
                                        @else
                                        @php $expiry_date='' @endphp
                                        @endif
                                        <input type="date" id="expiry_date" name="expiry_date" value="{{ $expiry_date }}" class="form-control @error('expiry_date') is-invalid @enderror" placeholder="" {{$readonly}}>
                                        @error('expiry_date')
                                        <span class="error">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 col-12 mb-3">
                                        @if(isset($record->active))
                                        @php $active = $record->active; @endphp
                                        @else
                                        @php $active = 1; @endphp
                                        @endif
                                        <label for="activeToggle" class="control-label"> Active</label>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" role="switch" name="active" id="activeToggle" <?php echo ($active == 1) ? "checked" : ""; ?> value=1>
                                        </div>
                                    </div>
                                 
                                </div>

                                <button class="btn btn-primary" type="submit">Submit</button>
                            </form>
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
<script src="{{ asset('js/moment.min.js') }}"></script>
<script>
  $('.users-div').hide();
  $("#assign_to").select2();
  $("#coupon_id").select2({
    ajax: {
      url: "{{ route('getCoupons') }}",
      type: "get",
      dataType: 'json',
      delay: 250,
      data: function(params) {
        return {
          searchTerm: params.term // search term
        };
      },
      processResults: function(response) {
        return {
          results: response
        };
      },
      cache: true
    }
  });

  $('#assign_to').change(function() {
    let selectedOption = $(this).val();
    if (selectedOption == 'individual') {
      $('.users-div').show();
      $("#user_ids").select2({
        ajax: {
          url: "{{ route('getUsers') }}",
          type: "get",
          dataType: 'json',
          delay: 250,
          data: function(params) {
            return {
              searchTerm: params.term // search term
            };
          },
          processResults: function(response) {
            return {
              results: response
            };
          },
          cache: true
        }
      });
    } else {
      $('.users-div').hide();
    }
  })

  $(document).on('change', '#coupon_id', function() {
    id = $(this).val();
    var url = "{{ route('admin.coupons.getCoupon', ':id') }}";
    url = url.replace(':id', id);
    $.ajax({
      type: 'GET',
      url: url,
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      success: function(data) {
        data = JSON.parse(data);
        console.log(data.end_date);
        date = moment(data.end_date).format("YYYY-MM-DD");
        //    date=new Date(data.end_date).format("YYYY-MM-DD"); 
        $('#expiry_date').val(date);
      }
    });
  })
</script>
@endpush