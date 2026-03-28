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
                        <form class="" role="form" id="edit_form" action="{{ route('admin.coupons.update',$record->id) }}" method="post" enctype="multipart/form-data">

                            @else
                            <form class="" method="POST" id="add_form" action="{{ route('admin.coupons.store') }}" enctype="multipart/form-data">
                                @endif
                                @csrf
                                <div class="row">
                                    <div class="col-md-6 col-12 mb-3">
                                        <label class="form-label" for="name">Title</label>
                                        @if(isset($record->title))
                                        @php $title=$record->title @endphp
                                        @elseif(old('title'))
                                        @php $title=old('title') @endphp
                                        @else
                                        @php $title='' @endphp
                                        @endif
                                        <input type="text" class="form-control  @error('title') is-invalid  @enderror" id="title" name="title" placeholder="Title" value="{{ $title }}">

                                        @error('title')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 col-12 mb-3">
                                        <label>Code <small class="red">* </small></label>
                                        @if(old('code'))
                                        @php $code = old('code'); @endphp
                                        @elseif(isset($record->code) && $record->code != '')
                                        @php $code = $record->code; @endphp
                                        @else
                                        @php $code = ''; @endphp
                                        @endif
                                        <div class="row">
                                            <div class="col-10">
                                                <input required name="code" type="text" class="form-control code @error('code') is-invalid @enderror" placeholder="Code" value="{{ $code }}" {{$readonly}}>
                                            </div>
                                            <div class="col-2 m-auto">
                                                <i class="fas fa-random" id="generateCode" style="font-size: 18px;"></i>
                                            </div>
                                        </div>
                                        @error('code')
                                        <span class="error">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 col-12 mb-3">
                                        <label>Coupon For <small class="red">* </small></label>
                                        @if(old('type'))
                                        @php $type = old('type'); @endphp
                                        @elseif(isset($record->type) && $record->type != '')
                                        @php $type = $record->type; @endphp
                                        @else
                                        @php $type = ''; @endphp
                                        @endif
                                        <select required name="type" class="form-select @error('type') is-invalid @enderror" id="typeCouponSelect">
                                            <option value="A" {!! $type=='A' ? 'selected' : '' !!}>Admin</option>
                                            <option value="U" {!! $type=='U' ? 'selected' : '' !!}>Users</option>
                                        </select>
                                        @error('type')
                                        <span class="error">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 col-12 mb-3">
                                        <label>Type <small class="red">* </small></label>
                                        @if(old('discount_type'))
                                        @php $discount_type = old('discount_type'); @endphp
                                        @elseif(isset($record->discount_type) && $record->discount_type != '')
                                        @php $discount_type = $record->discount_type; @endphp
                                        @else
                                        @php $discount_type = ''; @endphp
                                        @endif
                                        <select required name="discount_type" class="form-select @error('discount_type') is-invalid @enderror" id="typeSelect">
                                            <option value="flat" {!! $discount_type=='flat' ? 'selected' : '' !!}>Flat</option>
                                            <option value="percent" {!! $discount_type=='percent' ? 'selected' : '' !!}>Percent</option>
                                            <option value="b1g1" {!! $discount_type=='b1g1' ? 'selected' : '' !!}>Buy One Get One</option>
                                        </select>
                                        @error('discount_type')
                                        <span class="error">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 col-12 mb-3">
                                        <label>Amount <small class="red">* </small></label>
                                        @if(old('amount'))
                                        @php $amount = old('amount'); @endphp
                                        @elseif(isset($record->amount) && $record->amount != '')
                                        @php $amount = $record->amount; @endphp
                                        @else
                                        @php $amount = ''; @endphp
                                        @endif
                                        <input type="number" id="amount" name="amount" value="{{ $amount }}" class="form-control @error('amount') is-invalid @enderror" placeholder="Enter Amount" {{$readonly}}>
                                        @error('amount')
                                        <span class="error">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 col-12 mb-3">
                                        <label>Limit</label>
                                        @if(old('limit'))
                                        @php $limit = old('limit'); @endphp
                                        @elseif(isset($record->limit) && $record->limit != '')
                                        @php $limit = $record->limit; @endphp
                                        @else
                                        @php $limit =''; @endphp
                                        @endif
                                        <input type="number" id="limit" name="limit" value="{{ $limit }}" class="form-control @error('limit') is-invalid @enderror" placeholder="" {{$readonly}}>
                                        @error('limit')
                                        <span class="error">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 col-12 mb-3">
                                        <label>Start Date </label>
                                        @if (isset($record) && $record->start_date!="")
                                        @php $start_date=date('Y-m-d',strtotime($record->start_date)) @endphp
                                        @elseif(old('start_date'))
                                        @php $start_date=old('start_date') @endphp
                                        @else
                                        @php $start_date='yyyy-mm-dd' @endphp
                                        @endif

                                        <input type="date" id="start_date" name="start_date" value="{{ $start_date }}" class="form-control @error('start_date') is-invalid @enderror" placeholder="" {{$readonly}}>
                                        @error('start_date')
                                        <span class="error">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 col-12 mb-3">
                                        <label>End Date</label>
                                        @if (isset($record) && $record->end_date!="")
                                        @php $end_date=date('Y-m-d',strtotime($record->end_date)) @endphp
                                        @elseif(old('end_date'))
                                        @php $end_date=old('end_date') @endphp
                                        @else
                                        @php $end_date='yyyy-mm-dd' @endphp
                                        @endif

                                        <input type="date" id="end_date" name="end_date" value="{{ $end_date }}" class="form-control @error('end_date') is-invalid @enderror" placeholder="" {{$readonly}}>
                                        @error('end_date')
                                        <span class="error">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 col-12 mb-3">
                                        <label>Description </label>
                                        @if (isset($record))
                                        @php $description=$record->description @endphp
                                        @elseif(old('description'))
                                        @php $description=old('description') @endphp
                                        @else
                                        @php $description='' @endphp
                                        @endif
                                        <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror" {{$readonly}}>{!! $description !!}</textarea>
                                        @error('description')
                                        <span class="error">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 col-12 mb-3">
                                        <div class="form-group row align-items-center">
                                            <label class="col-form-label">Active</label>
                                            <div class="col-sm-9">
                                                @if(isset($record->active))
                                                @if($record->active==1)
                                                @php $active='checked' @endphp
                                                @else
                                                @php $active='' @endphp
                                                @endif
                                                @else
                                                @php $active='checked' @endphp
                                                @endif
                                                <div class="form-check form-switch custom-switch-v1 form-check-inline">
                                                    <input type="checkbox" class="form-check-input input-primary" id="active" name="active" value=1 {{ $active }}>
                                                    <!-- <label class="form-check-label" for="customCheckinlh1">Inline 1</label> -->
                                                </div>
                                            </div>
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
<script>
  $(document).on('click', '#generateCode', function() {
    $.ajax({
      type: 'GET',
      url: "{{ route('admin.coupons.generateRandomCoupon') }}",
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      success: function(data) {
        $('.code').val(data);
      }
    });
  });
</script>
@endpush