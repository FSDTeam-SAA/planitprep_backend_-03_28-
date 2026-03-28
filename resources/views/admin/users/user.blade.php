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
                        @if($action == 'Edit')
                        <form class="" role="form" id="edit_form" action="{{ route('admin.users.update',$record->id) }}" method="post" enctype="multipart/form-data">

                            @else
                            <form class="" method="POST" id="add_form" action="{{ route('admin.users.store') }}" enctype="multipart/form-data">
                                @endif
                                @csrf
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="name">Username</label>
                                        @if(isset($record->username))
                                        @php $username=$record->username @endphp
                                        @elseif(old('username'))
                                        @php $username=old('username') @endphp
                                        @else
                                        @php $username='' @endphp
                                        @endif
                                        <input type="text" class="form-control  @error('username') is-invalid  @enderror" id="username" name="username" placeholder="Username" value="{{ $username }}">

                                        @error('username')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="full_name">Full Name</label>
                                        @if(isset($record->full_name))
                                        @php $full_name=$record->full_name @endphp
                                        @elseif(old('full_name'))
                                        @php $full_name=old('full_name') @endphp
                                        @else
                                        @php $full_name='' @endphp
                                        @endif
                                        <input type="text" class="form-control  @error('full_name') is-invalid  @enderror" id="full_name" name="full_name" placeholder="Full Name" value="{{ $full_name }}">

                                        @error('full_name')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="phone">Phone Number</label>
                                        @if(isset($record->phone))
                                        @php $phone=$record->phone @endphp
                                        @elseif(old('phone'))
                                        @php $phone=old('phone') @endphp
                                        @else
                                        @php $phone='' @endphp
                                        @endif
                                        <input type="text" class="form-control  @error('phone') is-invalid  @enderror" id="phone" name="phone" placeholder="Phone Number" value="{{ $phone }}">

                                        @error('phone')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="email">Email</label>
                                        @if(isset($record->email))
                                        @php $email=$record->email @endphp
                                        @elseif(old('email'))
                                        @php $email=old('email') @endphp
                                        @else
                                        @php $email='' @endphp
                                        @endif
                                        <input type="email" class="form-control  @error('email') is-invalid  @enderror" id="email" name="email" placeholder="Email" value="{{ $email }}">

                                        @error('email')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="age">Age</label>
                                        @if(isset($record->age))
                                        @php $age=$record->age @endphp
                                        @elseif(old('age'))
                                        @php $age=old('age') @endphp
                                        @else
                                        @php $age='' @endphp
                                        @endif
                                        <input type="number" class="form-control  @error('age') is-invalid  @enderror" id="age" name="age" placeholder="Age" value="{{ $age }}">

                                        @error('age')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="gender">Gender</label>
                                        @if(isset($record->gender))
                                        @php $gender=$record->gender @endphp
                                        @elseif(old('gender'))
                                        @php $gender=old('gender') @endphp
                                        @else
                                        @php $gender='' @endphp
                                        @endif
                                        <select class="form-control  @error('gender') is-invalid  @enderror" id="gender" name="gender">
                                            <option value="">Select</option>
                                            <option value="M" {!! ($gender=='M' ) ? 'selected' : '' !!}>Male</option>
                                            <option value="F" {!! ($gender=='F' ) ? 'selected' : '' !!}>Female</option>
                                            <option value="O" {!! ($gender=='O' ) ? 'selected' : '' !!}>Others</option>
                                        </select>

                                        @error('age')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="country_id">Country</label>
                                        @if(old('country_id'))
                                        @php $country_id = old('country_id'); @endphp
                                        @elseif(isset($record->country_id) && $record->country_id != '')
                                        @php $country_id = $record->country_id; @endphp
                                        @else
                                        @php $country_id = ''; @endphp
                                        @endif
                                        <select id="country_id" name="country_id" class="form-control">
                                            <option value="" disabled>Select</option>
                                            @foreach($countries as $item)
                                                <option value="{{$item->id}}">{{$item->name}}</option>
                                            @endforeach
                                        </select>
                                        @error('country_id')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="state_id">State</label>
                                        @if(old('state_id'))
                                        @php $state_id = old('state_id'); @endphp
                                        @elseif(isset($record->state_id) && $record->state_id != '')
                                        @php $state_id = $record->state_id; @endphp
                                        @else
                                        @php $state_id = ''; @endphp
                                        @endif
                                        <select id="state_id" name="state_id" class="form-control">
                                            <option value="" disabled>Select</option>
                                            @foreach($states as $state)
                                            <option value="{{$state->id}}" {!! ($state->id==$state_id) ? 'selected' : '' !!}>{{$state->name}}</option>
                                            @endforeach
                                        </select>
                                        @error('state_id')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="city_id">City</label>
                                        @if(old('city_id'))
                                        @php $city_id = old('city_id'); @endphp
                                        @elseif(isset($record->city_id) && $record->city_id != '')
                                        @php $city_id = $record->city_id; @endphp
                                        @else
                                        @php $city_id = ''; @endphp
                                        @endif
                                        <select id="city_id" name="city_id" class="form-control">
                                            <option value="" disabled>Select</option>
                                            @foreach($cities as $city)
                                            <option value="{{$city->id}}" {!! ($city->id==$city_id) ? 'selected' : '' !!}>{{$city->name}}</option>
                                            @endforeach
                                        </select>
                                        @error('city_id')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-8 col-12">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label" for="height">Height (in cm) </label>
                                                @if(isset($record->height))
                                                @php $height=$record->height @endphp
                                                @elseif(old('height'))
                                                @php $height=old('height') @endphp
                                                @else
                                                @php $height='' @endphp
                                                @endif
                                                <input type="number" class="form-control  @error('height') is-invalid  @enderror" id="height" name="height" placeholder="Height" value="{{ $height }}">

                                                @error('height')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="form-label" for="weight">Weight (in kg) </label>
                                                @if(isset($record->weight))
                                                @php $weight=$record->weight @endphp
                                                @elseif(old('weight'))
                                                @php $weight=old('weight') @endphp
                                                @else
                                                @php $weight='' @endphp
                                                @endif
                                                <input type="number" class="form-control  @error('weight') is-invalid  @enderror" id="weight" name="weight" placeholder="Weight" value="{{ $weight }}">

                                                @error('weight')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6  mb-3">
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
                                    </div>
                                    <div class="col-md-4 col-12">
                                        <div class="row">
                                            <div class="col-md-12  mb-3">
                                                <label class="form-label" for="image">Image</label>
                                                <input type="file" class="form-control" id="image" name="image" aria-label="file example">
                                                @error('image')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror

                                                @if(isset($record->image) && $record->image!="")
                                                @php $image=asset(Storage::url('public/users')).'/'.$record->id.'/'.$record->image @endphp
                                                @else
                                                @php $image='' @endphp
                                                @endif
                                                <div id="image_display">
                                                    @if($image!="")
                                                    <img src="{{ $image }}">
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <div class="row mb-4">
                                    <div class="col-4">
                                        <h6>Activity Levels</h6>

                                        @php $activity_levels=[] @endphp
                                        @if(isset($record) && $record->activity_levels)
                                        @php $activity_levels=$record->activity_levels->pluck('id')->toArray() @endphp
                                        @endif
                                        <select class="form-control actiivity_level" name="actiivity_level[]" id="actiivity_level" multiple>
                                            <option value="">Select</option>
                                            @foreach($activityLevels as $activity)
                                            <option value="{{$activity->id}}" {!! in_array($activity->id,$activity_levels) ? 'selected' : '' !!}>{{$activity->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <!-- </div>


                                <div class="row mb-4"> -->
                                    <div class="col-4">
                                        <h6>Medical Issues</h6>
                                        <!-- </div>
                                    <div class="col-12"> -->
                                        @php $medical_issues=[] @endphp
                                        @if(isset($record) && $record->medical_issues)
                                        @php $medical_issues=$record->medical_issues->pluck('id')->toArray() @endphp
                                        @endif
                                        <select class="form-control medical_issues" name="medical_issues[]" id="medical_issues" multiple>
                                            <option value="">Select</option>
                                            @foreach($medicalIssues as $issue)
                                            <option value="{{$issue->id}}" {!! in_array($issue->id,$medical_issues) ? 'selected' : '' !!}>{{$issue->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <!-- </div>

                                <div class="row mb-4"> -->
                                    <div class="col-4">
                                        <h6>Preferences</h6>
                                        <!-- </div>
                                    <div class="col-12"> -->
                                        @php $preferences=[] @endphp
                                        @if(isset($record) && $record->preferences)
                                        @php $preferences=$record->preferences->pluck('id')->toArray() @endphp
                                        @endif
                                        <select class="form-control preferences" name="preferences[]" id="preferences" multiple>
                                            <option value="">Select</option>
                                            @foreach($dietTypes as $diet)
                                            <option value="{{$diet->id}}" {!! in_array($diet->id,$preferences) ? 'selected' : '' !!}>{{$diet->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-4 mb-4">
                                    <h6>Meal Plan</h6>
                                    <select id="meal_plan" @error('meal_plan') is-invalid  @enderror" class="form-select" name="meal_plan">
                                        <option selected disabled>Not Selected</option>
                                        @foreach ($mealPlans as $item)
                                            <option value="{{$item->id}}" {{ $item->id == $userMealPlanId ? 'selected' : '' }}>{{ $item->name }}</option>
                                        @endforeach
                                    </select>


                                    @error('meal_plan')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror


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

@push('scripts')
<script type="text/javascript">
    $(document).ready(function() {
        // select countries
        $("#country_id").select2({
            ajax: {
                url: "{{ route('getCountries') }}",
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

        // select states
        $("#state_id").select2({
            ajax: {
                url: "{{ route('getStates') }}",
                type: "get",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    country_id = $("#country_id").val();
                    return {
                        searchTerm: params.term, // search term
                        country_id: country_id
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

        // select cities
        $("#city_id").select2({
            ajax: {
                url: "{{ route('getCities') }}",
                type: "get",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    state_id = $("#state_id").val();
                    return {
                        searchTerm: params.term, // search term
                        state_id: state_id
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

        var fileInput = document.getElementById('image');
        var fileDisplayArea = document.getElementById('image_display');
        fileInput.addEventListener('change', function(e) {
            var file = fileInput.files[0];
            var imageType = /image.*/;
            if (file.type.match(imageType)) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    fileDisplayArea.innerHTML = "";
                    var images = new Image();
                    images.src = reader.result;
                    fileDisplayArea.appendChild(images);
                }

                reader.readAsDataURL(file);
            } else {
                fileDisplayArea.innerHTML = "File not supported!"
            }
        });

        $('#actiivity_level,#medical_issues,#preferences').select2();

    });
</script>
@endpush
@endsection
