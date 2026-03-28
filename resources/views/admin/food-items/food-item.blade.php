@extends('admin.layouts.admin')
@section('content')
<style>
    .addItem,
    .removeItem {
        padding: 6px;
        background: green;
        border-radius: 20px;
        color: #fff;
        margin-top: 16px;
        font-size: 10px;
    }

    #search_item {
        border: 1px solid #ccc;
        padding: 6px 10px;
        width: 100%;
    }

    #itemsList .food-item-img,
    #selectedItems .food-item-img {
        width: 80px;
    }

    .removeItem {
        background-color: red;
    }

    .selected-label {
        position: relative;
    }

    .selected-label label {
        bottom: 0;
        position: absolute;
        font-size: 16px;
        font-weight: 400;
    }
</style>
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
                            <li class="breadcrumb-item"><a href="{{ route('admin.food-items') }}">Food Items</a></li>
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
                      @php $id= $record->id; @endphp
                        <form class="" role="form" id="edit_form" action="{{ route('admin.food-items.update',$record->id) }}" method="post" enctype="multipart/form-data">
                            @else
                           @php $id= 0; @endphp
                            <form class="" method="POST" id="add_form" action="{{ route('admin.food-items.store') }}" enctype="multipart/form-data">
                                @endif
                                @csrf
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="name">Name</label>
                                        @if(isset($record->name))
                                        @php $name=$record->name @endphp
                                        @elseif(old('name'))
                                        @php $name=old('name') @endphp
                                        @else
                                        @php $name='' @endphp
                                        @endif
                                        <input type="text" class="form-control  @error('name') is-invalid  @enderror" id="name" name="name" placeholder="Name" value="{{ $name }}">

                                        @error('name')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="food_group_id">Food Group</label>
                                        @if(isset($record->food_group_id))
                                        @php $food_group_id=$record->food_group_id @endphp
                                        @elseif(old('food_group_id'))
                                        @php $food_group_id=old('food_group_id') @endphp
                                        @else
                                        @php $food_group_id=0 @endphp
                                        @endif
                                        <select class="form-control  @error('food_group_id') is-invalid  @enderror" id="food_group_id" name="food_group_id">
                                            <option value="">Select</option>
                                            @foreach($food_groups as $food_group)
                                            <option value="{{$food_group->id}}" {!! ($food_group_id==$food_group->id) ? 'selected' : '' !!}>{{$food_group->name}}</option>
                                            @endforeach
                                        </select>

                                        @error('food_group_id')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="name">Serving Size (grams)</label>
                                        @if(isset($record->serving_size))
                                        @php $serving_size=$record->serving_size @endphp
                                        @elseif(old('serving_size'))
                                        @php $serving_size=old('serving_size') @endphp
                                        @else
                                        @php $serving_size='' @endphp
                                        @endif
                                        <input type="text" class="form-control  @error('serving_size') is-invalid  @enderror" id="serving_size" name="serving_size" placeholder="Serving Size" value="{{ $serving_size }}">

                                        @error('serving_size')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="name">Serving Unit (grams, cups)</label>
                                        @if(isset($record->serving_unit))
                                        @php $serving_unit=$record->serving_unit @endphp
                                        @elseif(old('serving_unit'))
                                        @php $serving_unit=old('serving_unit') @endphp
                                        @else
                                        @php $serving_unit='' @endphp
                                        @endif
                                        <!-- <input type="text" class="form-control  @error('serving_unit') is-invalid  @enderror" id="serving_unit" name="serving_unit" placeholder="Serving Unit" value="{{ $serving_unit }}"> -->
                                        <select class="form-control  @error('serving_unit') is-invalid  @enderror" id="serving_unit" name="serving_unit">
                                            <option value="">-- Select --</option>
                                            @foreach($serving_units as $unit)
                                            <option value="{{ $unit->unit }}" {!! ($unit->unit==$serving_unit) ? 'selected' : '' !!}>{{ $unit->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('serving_unit')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="name">Calories (kcal)</label>
                                        @if(isset($record->calories))
                                        @php $calories=$record->calories @endphp
                                        @elseif(old('calories'))
                                        @php $calories=old('calories') @endphp
                                        @else
                                        @php $calories='' @endphp
                                        @endif
                                        <input type="number" class="form-control  @error('calories') is-invalid  @enderror" id="calories" name="calories" placeholder="Calories" value="{{ $calories }}">

                                        @error('calories')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="cholesterol">Cholesterol (milligrams)</label>
                                        @if(isset($record->cholesterol))
                                        @php $cholesterol=$record->cholesterol @endphp
                                        @elseif(old('cholesterol'))
                                        @php $cholesterol=old('cholesterol') @endphp
                                        @else
                                        @php $cholesterol='' @endphp
                                        @endif
                                        <input type="number" class="form-control  @error('cholesterol') is-invalid  @enderror" id="cholesterol" name="cholesterol" placeholder="Cholesterol" value="{{ $cholesterol }}">
                                        @error('cholesterol')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="total_carbohydrates">Total Carbohydrates (grams)</label>
                                        @if(isset($record->total_carbohydrates))
                                        @php $total_carbohydrates=$record->total_carbohydrates @endphp
                                        @elseif(old('total_carbohydrates'))
                                        @php $total_carbohydrates=old('total_carbohydrates') @endphp
                                        @else
                                        @php $total_carbohydrates='' @endphp
                                        @endif
                                        <input type="number" class="form-control  @error('total_carbohydrates') is-invalid  @enderror" id="total_carbohydrates" name="total_carbohydrates" placeholder="Total Carbohydrates" value="{{ $total_carbohydrates }}">
                                        @error('total_carbohydrates')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="dietary_fiber">Dietary Fiber (grams)</label>
                                        @if(isset($record->dietary_fiber))
                                        @php $dietary_fiber=$record->dietary_fiber @endphp
                                        @elseif(old('dietary_fiber'))
                                        @php $dietary_fiber=old('dietary_fiber') @endphp
                                        @else
                                        @php $dietary_fiber='' @endphp
                                        @endif
                                        <input type="number" class="form-control  @error('dietary_fiber') is-invalid  @enderror" id="dietary_fiber" name="dietary_fiber" placeholder="Dietary Fiber" value="{{ $dietary_fiber }}">
                                        @error('dietary_fiber')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="sugars">Sugars (grams)</label>
                                        @if(isset($record->sugars))
                                        @php $sugars=$record->sugars @endphp
                                        @elseif(old('sugars'))
                                        @php $sugars=old('sugars') @endphp
                                        @else
                                        @php $sugars='' @endphp
                                        @endif
                                        <input type="number" class="form-control  @error('sugars') is-invalid  @enderror" id="sugars" name="sugars" placeholder="Sugars" value="{{ $sugars }}">
                                        @error('sugars')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="added_sugars">Added Sugars (grams)</label>
                                        @if(isset($record->added_sugars))
                                        @php $added_sugars=$record->added_sugars @endphp
                                        @elseif(old('added_sugars'))
                                        @php $added_sugars=old('added_sugars') @endphp
                                        @else
                                        @php $added_sugars='' @endphp
                                        @endif
                                        <input type="number" class="form-control  @error('added_sugars') is-invalid  @enderror" id="added_sugars" name="added_sugars" placeholder="Added Sugars" value="{{ $added_sugars }}">
                                        @error('added_sugars')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="protein">Protein (grams)</label>
                                        @if(isset($record->protein))
                                        @php $protein=$record->protein @endphp
                                        @elseif(old('protein'))
                                        @php $protein=old('protein') @endphp
                                        @else
                                        @php $protein='' @endphp
                                        @endif
                                        <input type="number" class="form-control  @error('protein') is-invalid  @enderror" id="protein" name="protein" placeholder="Protein" value="{{ $protein }}">
                                        @error('protein')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>


                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="glycemic_index">Glycemic Index (if applicable)</label>
                                        @if(isset($record->glycemic_index))
                                        @php $glycemic_index=$record->glycemic_index @endphp
                                        @elseif(old('glycemic_index'))
                                        @php $glycemic_index=old('glycemic_index') @endphp
                                        @else
                                        @php $glycemic_index='' @endphp
                                        @endif
                                        <input type="number" class="form-control  @error('glycemic_index') is-invalid  @enderror" id="glycemic_index" name="glycemic_index" placeholder="Glycemic Index" value="{{ $glycemic_index }}">
                                        @error('glycemic_index')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>


                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="comments">Comments</label>
                                        @if(isset($record->comments))
                                        @php $comments=$record->comments @endphp
                                        @elseif(old('comments'))
                                        @php $comments=old('comments') @endphp
                                        @else
                                        @php $comments='' @endphp
                                        @endif
                                        <textarea class="form-control  @error('comments') is-invalid  @enderror" id="comments" name="comments" placeholder="Comments">{!! $comments !!}</textarea>
                                        @error('comments')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>


                                    <div class="col-md-4  mb-3">
                                        <label class="form-label" for="image">Image</label>
                                        <input type="file" class="form-control" id="image" name="image" aria-label="file example">
                                        @error('image')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                        @if(isset($record->image) && $record->image!="")
                                        @php $image=asset(Storage::url('public/food-items')).'/'.$record->image @endphp
                                        @else
                                        @php $image='' @endphp
                                        @endif
                                        <div id="image_display">
                                            @if($image!="")
                                            <img src="{{ $image }}">
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-4  mb-3">
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
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <div class="row">
                                    <h6 class="mt-3">Minerals</h6>
                                    <hr />

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="sodium">Sodium (milligrams)</label>
                                        @if(isset($record->sodium))
                                        @php $sodium=$record->sodium @endphp
                                        @elseif(old('sodium'))
                                        @php $sodium=old('sodium') @endphp
                                        @else
                                        @php $sodium='' @endphp
                                        @endif
                                        <input type="number" class="form-control  @error('sodium') is-invalid  @enderror" id="sodium" name="sodium" placeholder="Sodium" value="{{ $sodium }}">
                                        @error('sodium')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="calcium">Calcium (milligrams)</label>
                                        @if(isset($record->calcium))
                                        @php $calcium=$record->calcium @endphp
                                        @elseif(old('calcium'))
                                        @php $calcium=old('calcium') @endphp
                                        @else
                                        @php $calcium='' @endphp
                                        @endif
                                        <input type="number" class="form-control  @error('calcium') is-invalid  @enderror" id="calcium" name="calcium" placeholder="Calcium" value="{{ $calcium }}">
                                        @error('calcium')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="potassium">Potassium (milligrams)</label>
                                        @if(isset($record->potassium))
                                        @php $potassium=$record->potassium @endphp
                                        @elseif(old('potassium'))
                                        @php $potassium=old('potassium') @endphp
                                        @else
                                        @php $potassium='' @endphp
                                        @endif
                                        <input type="number" class="form-control  @error('potassium') is-invalid  @enderror" id="potassium" name="potassium" placeholder="Potassium" value="{{ $potassium }}">
                                        @error('potassium')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="iron">Iron (milligrams)</label>
                                        @if(isset($record->iron))
                                        @php $iron=$record->iron @endphp
                                        @elseif(old('iron'))
                                        @php $iron=old('iron') @endphp
                                        @else
                                        @php $iron='' @endphp
                                        @endif
                                        <input type="number" class="form-control  @error('iron') is-invalid  @enderror" id="iron" name="iron" placeholder="Iron" value="{{ $iron }}">
                                        @error('iron')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="magnesium">Magnesium (milligrams)</label>
                                        @if(isset($record->magnesium))
                                        @php $magnesium=$record->magnesium @endphp
                                        @elseif(old('magnesium'))
                                        @php $magnesium=old('magnesium') @endphp
                                        @else
                                        @php $magnesium='' @endphp
                                        @endif
                                        <input type="number" class="form-control  @error('magnesium') is-invalid  @enderror" id="magnesium" name="magnesium" placeholder="Magnesium" value="{{ $magnesium }}">
                                        @error('magnesium')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <h6 class="mt-3">Fats</h6>
                                    <hr />
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="name">Total Fat (grams)</label>
                                        @if(isset($record->total_fat))
                                        @php $total_fat=$record->total_fat @endphp
                                        @elseif(old('total_fat'))
                                        @php $total_fat=old('total_fat') @endphp
                                        @else
                                        @php $total_fat='' @endphp
                                        @endif
                                        <input type="number" class="form-control  @error('total_fat') is-invalid  @enderror" id="total_fat" name="total_fat" placeholder="Total Fat" value="{{ $total_fat }}">

                                        @error('total_fat')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="saturated_fat">Saturated Fat (grams)</label>
                                        @if(isset($record->saturated_fat))
                                        @php $saturated_fat=$record->saturated_fat @endphp
                                        @elseif(old('saturated_fat'))
                                        @php $saturated_fat=old('saturated_fat') @endphp
                                        @else
                                        @php $saturated_fat='' @endphp
                                        @endif
                                        <input type="number" class="form-control  @error('saturated_fat') is-invalid  @enderror" id="saturated_fat" name="saturated_fat" placeholder="Saturated Fat" value="{{ $saturated_fat }}">

                                        @error('saturated_fat')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="trans_fat">Trans Fat (grams)</label>
                                        @if(isset($record->trans_fat))
                                        @php $trans_fat=$record->trans_fat @endphp
                                        @elseif(old('trans_fat'))
                                        @php $trans_fat=old('trans_fat') @endphp
                                        @else
                                        @php $trans_fat='' @endphp
                                        @endif
                                        <input type="number" class="form-control  @error('trans_fat') is-invalid  @enderror" id="trans_fat" name="trans_fat" placeholder="Trans Fat" value="{{ $trans_fat }}">

                                        @error('trans_fat')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="monounsaturated_fat">Monounsaturated Fat (grams)</label>
                                        @if(isset($record->monounsaturated_fat))
                                        @php $monounsaturated_fat=$record->monounsaturated_fat @endphp
                                        @elseif(old('monounsaturated_fat'))
                                        @php $monounsaturated_fat=old('monounsaturated_fat') @endphp
                                        @else
                                        @php $monounsaturated_fat='' @endphp
                                        @endif
                                        <input type="number" class="form-control  @error('monounsaturated_fat') is-invalid  @enderror" id="monounsaturated_fat" name="monounsaturated_fat" placeholder="Monounsaturated Fat" value="{{ $monounsaturated_fat }}">

                                        @error('monounsaturated_fat')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="polyunsaturated_fat">Polyunsaturated Fat (grams)</label>
                                        @if(isset($record->polyunsaturated_fat))
                                        @php $polyunsaturated_fat=$record->polyunsaturated_fat @endphp
                                        @elseif(old('polyunsaturated_fat'))
                                        @php $polyunsaturated_fat=old('polyunsaturated_fat') @endphp
                                        @else
                                        @php $polyunsaturated_fat='' @endphp
                                        @endif
                                        <input type="number" class="form-control  @error('polyunsaturated_fat') is-invalid  @enderror" id="polyunsaturated_fat" name="polyunsaturated_fat" placeholder="Polyunsaturated Fat" value="{{ $polyunsaturated_fat }}">

                                        @error('polyunsaturated_fat')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row">
                                    <h6 class="mt-3">Vitamins</h6>
                                    <hr />
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="vitamin_a">Vitamin A (micrograms)</label>
                                        @if(isset($record->vitamin_a))
                                        @php $vitamin_a=$record->vitamin_a @endphp
                                        @elseif(old('vitamin_a'))
                                        @php $vitamin_a=old('vitamin_a') @endphp
                                        @else
                                        @php $vitamin_a='' @endphp
                                        @endif
                                        <input type="number" class="form-control  @error('vitamin_a') is-invalid  @enderror" id="vitamin_a" name="vitamin_a" placeholder="Vitamin A" value="{{ $vitamin_a }}">
                                        @error('vitamin_a')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="vitamin_c">Vitamin C (milligrams)</label>
                                        @if(isset($record->vitamin_c))
                                        @php $vitamin_c=$record->vitamin_c @endphp
                                        @elseif(old('vitamin_c'))
                                        @php $vitamin_c=old('vitamin_c') @endphp
                                        @else
                                        @php $vitamin_c='' @endphp
                                        @endif
                                        <input type="number" class="form-control  @error('vitamin_c') is-invalid  @enderror" id="vitamin_c" name="vitamin_c" placeholder="Vitamin C" value="{{ $vitamin_c }}">
                                        @error('vitamin_c')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>


                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="vitamin_d">Vitamin D (micrograms)</label>
                                        @if(isset($record->vitamin_d))
                                        @php $vitamin_d=$record->vitamin_d @endphp
                                        @elseif(old('vitamin_d'))
                                        @php $vitamin_d=old('vitamin_d') @endphp
                                        @else
                                        @php $vitamin_d='' @endphp
                                        @endif
                                        <input type="number" class="form-control  @error('vitamin_d') is-invalid  @enderror" id="vitamin_d" name="vitamin_d" placeholder="Vitamin D" value="{{ $vitamin_d }}">
                                        @error('vitamin_d')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="vitamin_b6">Vitamin B6 (milligrams)</label>
                                        @if(isset($record->vitamin_b6))
                                        @php $vitamin_b6=$record->vitamin_b6 @endphp
                                        @elseif(old('vitamin_b6'))
                                        @php $vitamin_b6=old('vitamin_b6') @endphp
                                        @else
                                        @php $vitamin_b6='' @endphp
                                        @endif
                                        <input type="number" class="form-control  @error('vitamin_b6') is-invalid  @enderror" id="vitamin_b6" name="vitamin_b6" placeholder="Vitamin B6" value="{{ $vitamin_b6 }}">
                                        @error('vitamin_b6')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="vitamin_b12">Vitamin B12 (micrograms)</label>
                                        @if(isset($record->vitamin_b12))
                                        @php $vitamin_b12=$record->vitamin_b12 @endphp
                                        @elseif(old('vitamin_b12'))
                                        @php $vitamin_b12=old('vitamin_b12') @endphp
                                        @else
                                        @php $vitamin_b12='' @endphp
                                        @endif
                                        <input type="number" class="form-control  @error('vitamin_b12') is-invalid  @enderror" id="vitamin_b12" name="vitamin_b12" placeholder="Vitamin B12" value="{{ $vitamin_b12 }}">
                                        @error('vitamin_b12')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <h6 class="mt-3">Similar Items</h6>
                                    <hr />
                                    <div class="col-md-12 mb-3">


                                        <div class="row">
                                            <div class="col-6 px-3">
                                                <label class="form-label">Search Food Items</label>
                                                <input type="text" placeholder="Search Item..." name="search_item" class="" id="search_item" />
                                            </div>
                                            <div class="col-6 px-3 selected-label">
                                                <label class="form-label">Selectd Food Items</label>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <!-- <input type="hidden" name="similar_ids" id="similar_ids" value="{{-- $similar_ids --}}" /> -->
                                            <div class="col-6 pt-0 p-3" id="itemsList">

                                            </div>
                                            <div class="col-6">

                                                <div class="border" id="selectedItems">
                                                    @foreach($similar_food_items as $fooditem)
                                                    @php 
                                                    $item=$fooditem; 
                                                    $html=view('admin.food-items.selected-items',compact('item'))->render(); @endphp
                                                    {!! $html !!}
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                        <!-- @if(isset($record->similar_food_items))
                                        @php $similar_item_ids=$record->similar_food_items->pluck('similar_item_id')->toArray();
                                        @endphp
                                        @elseif(old('similar_item_ids'))
                                        @php $similar_item_ids=old('similar_item_ids') @endphp
                                        @else
                                        @php $similar_item_ids=[] @endphp
                                        @endif
                                        <select class="form-control  @error('similar_item_ids') is-invalid  @enderror" id="similar_item_ids" name="similar_item_ids[]" multiple>
                                            <option value="">Select</option>
                                            @foreach($similar_food_items as $similar_food_item)
                                            <option value="{{$similar_food_item->id}}" {!! in_array($similar_food_item->id,$similar_item_ids) ? 'selected' : '' !!}>{{ $similar_food_item->name}}</option>
                                            @endforeach
                                        </select>

                                        @error('similar_item_ids')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror -->


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
<script type="text/javascript">
    $(document).ready(function() {
        // itemsList();
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

        $('#food_group_id').select2();

        // $("#similar_item_ids").select2({
        //     ajax: {
        //         url: "{{ route('admin.food-items.getSimilarItems') }}",
        //         type: "get",
        //         dataType: 'json',
        //         delay: 250,
        //         data: function(params) {
        //             return {
        //                 searchTerm: params.term // search term
        //             };
        //         },
        //         processResults: function(response) {
        //             return {
        //                 results: response
        //             };
        //         },
        //         cache: true
        //     }
        // });
    });

    $(document).on('keyup', '#search_item', function(e) {
        let selectedIds = $('.selected_ids').map(function() {
                return $(this).val();
            }).get();
        search = $(this).val();
        $.ajax({
            url: "{{ route('admin.food-items.getSimilarItems') }}",
            type: 'GET',
            data: {
                search: search,
                 item_id:"{{ $id }}",
                 selected_ids:selectedIds
            },
            success: function(res) {
                if (res.status == true) {
                    $('#itemsList').html(res.html);
                }
            }
        });
    });

    $(document).on('click', '.addItem', function() {
        id = $(this).attr('data-id');
        $.ajax({
            url: "{{ route('admin.food-items.addSimilarItem') }}",
            type: 'GET',
            data: {
                id: id,
                item_id:"{{ $id }}"
            },
            success: function(res) {
                if (res.status == true) {
                    $('#selectedItems').append(res.html);
                    itemsList();
                }
            }
        });
    });

    
    $(document).on('click', '.removeItem', function() {
        id = $(this).attr('data-id');
        $.ajax({
            url: "{{ route('admin.food-items.removeSimilarItem') }}",
            type: 'GET',
            data: {
                id: id,
                item_id:"{{ $id }}"
            },
            success: function(res) {
                if (res.status == true) {
                    $('.list-'+id).remove();
                    itemsList();
                    // $('#selectedItems').html(res.html);
                    
                }
            }
        });
    });


    $(document).on('keyup','.item_qty',function(){
        id=$(this).parent().attr('data-id');
        qty=$(this).val();
        $.ajax({
            url: "{{ route('admin.food-items.updateSimilarItemQty') }}",
            type: 'GET',
            data: {
                id: id,
                qty:qty,
                item_id:"{{ $id }}"
            },
            success: function(res) {
                if (res.status == true) {
                    // itemsList();
                    // $('#selectedItems').html(res.html);
                    
                }
            }
        });
    });

    function itemsList() {
        // similar_ids=$('#similar_ids').val();
        let selectedIds = $('.selected_ids').map(function() {
                return $(this).val();
            }).get();
        $.ajax({
            url: "{{ route('admin.food-items.getSimilarItems') }}",
            type: 'GET',
            data:{
                // ids:similar_ids,
                search:$('#search_item').val(),
                item_id:"{{ $id }}",
                selected_ids:selectedIds
            },
            success: function(res) {
                if (res.status == true) {
                    $('#itemsList').html(res.html);
                }
            }
        });
    }
</script>
@endpush