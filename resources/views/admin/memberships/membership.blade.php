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
                            <li class="breadcrumb-item"><a href="{{ route('admin.memberships') }}">Memberships</a></li>
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
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>{{ $title }}</h5>
                    </div>
                    <div class="card-body">
                        @if($action == 'Edit')
                        <form class="" role="form" id="edit_form" action="{{ route('admin.memberships.update',$record->id) }}" method="post">
                            @csrf
                            <input type="hidden" name="id" value="{{$record->id}}">
                            @else
                            <form class="" method="POST" id="add_form" action="{{ route('admin.memberships.store') }}">
                                @csrf
                                <input type="hidden" name="id" value="0">
                                @endif

                                <input type="hidden" name="form_type" value="{{$action}}">

                                <div class="row">
                                    <div class="col-md-12 form-group">
                                        @if(isset($record->title))
                                        @php $title=$record->title @endphp
                                        @elseif(old('title'))
                                        @php $title=old('title') @endphp
                                        @else
                                        @php $title='' @endphp
                                        @endif
                                        <label class="form-label">Title</label>
                                        <input type="text" name="title" class="form-control" placeholder="Name of membership" autofocus value="{{old('title', $title)}}">
                                        @error('title')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 form-group">
                                        @if(isset($record->price))
                                        @php $price=$record->price @endphp
                                        @elseif(old('price'))
                                        @php $price=old('price') @endphp
                                        @else
                                        @php $price='' @endphp
                                        @endif
                                        <label class="form-label">Price</label>
                                        <input type="text" name="price" class="form-control" placeholder="Price" autofocus value="{{old('price', $price)}}">
                                        @error('price')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 form-group">
                                        @if(isset($record->month))
                                        @php $month=$record->month @endphp
                                        @elseif(old('month'))
                                        @php $month=old('month') @endphp
                                        @else
                                        @php $month='' @endphp
                                        @endif
                                        <label class="form-label">Month</label>
                                        <input type="number" min="1" name="month" class="form-control" placeholder="Number of months" autofocus value="{{old('month', $month)}}">
                                        @error('month')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-12  mb-3">
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
                                                    <input type="checkbox" class="form-check-input input-primary" id="active" name="active" value="1" @checked(old('active', $active))>
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
