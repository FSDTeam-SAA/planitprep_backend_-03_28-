@extends('admin.layouts.admin')

@section('content')
    <section class="pc-container">
        <div class="pc-content">
            <div class="page-header">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                                <li class="breadcrumb-item">Manage Homepage</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row pt-0">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body pt-4">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5>Manage Homepage</h5>
                                    <br>
                                </div>
                            </div>
                            <div>
                                @if(Session::has('message'))
                                    <p class="alert {{ Session::get('alert-class', 'alert-info') }} flash-msg">{{ Session::get('message') }}</p>
                                @endif
                            </div>

                            <div>
                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li>
                                                    {{ $error }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </div>

                            <ul class="nav nav-tabs mb-3" id="myTab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link text-uppercase active" id="tab-0" data-bs-toggle="tab" href="#tab0" role="tab" aria-controls="tab0" aria-selected="true">
                                        Main Slider
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link text-uppercase" id="tab-1" data-bs-toggle="tab" href="#tab1" role="tab" aria-controls="tab1" aria-selected="true">
                                        About
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link text-uppercase" id="tab-4" data-bs-toggle="tab" href="#tab4" role="tab" aria-controls="tab4" aria-selected="true">
                                        Services
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link text-uppercase" id="tab-7" data-bs-toggle="tab" href="#tab7" role="tab" aria-controls="tab7" aria-selected="true">
                                        Our Packages
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link text-uppercase" id="tab-3" data-bs-toggle="tab" href="#tab3" role="tab" aria-controls="tab3" aria-selected="true">
                                        Intro App
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link text-uppercase" id="tab-2" data-bs-toggle="tab" href="#tab2" role="tab" aria-controls="tab2" aria-selected="true">
                                        Contact
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link text-uppercase" id="tab-6" data-bs-toggle="tab" href="#tab6" role="tab" aria-controls="tab6" aria-selected="true">
                                        Our Clients
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link text-uppercase" id="tab-5" data-bs-toggle="tab" href="#tab5" role="tab" aria-controls="tab5" aria-selected="true">
                                        Testimonials
                                    </a>
                                </li>
                            </ul>

                            <form action="{{ route('admin.update-manage-homepage') }}" method="post" enctype="multipart/form-data">
                                @csrf

                                <div class="tab-content" id="myTabContent">
                                    <div class="tab-pane show active" id="tab0" role="tabpanel" aria-labelledby="tab-0">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div>
                                                    <label class="form-label">Heading 1<span class="text-danger">*</span></label>
                                                    <input name="slider_heading_1" type="text" class="form-control" value="{{ old('slider_heading_1', $data->slider_heading_1) }}">
                                                </div>
                                                <div class="mt-3">
                                                    <div class="form-group">
                                                        <label class="form-label">Main Image 1<span class="text-danger">*</span></label>
                                                        <input name="slider_image_1" class="form-control" type="file" accept="image/jpg, image/jpeg, image/png, image/webp, image/svg+xml" onchange="loadFile_7(event)">
                                                    </div>
                                                    <div class="d-flex">
                                                        <img class="img-fluid image-preview bg-gray-300" src="{{ $data->slider_image_1 }}" alt="" id="output-7">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-8">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <label class="form-label">Heading 2<span class="text-danger">*</span></label>
                                                        <input name="slider_heading_2" type="text" class="form-control" value="{{ old('slider_heading_2', $data->slider_heading_2) }}">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">Heading 3<span class="text-danger">*</span></label>
                                                        <input name="slider_heading_3" type="text" class="form-control" value="{{ old('slider_heading_3', $data->slider_heading_3) }}">
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-4 mt-3">
                                                        <div class="form-group">
                                                            <label class="form-label">Slide Image 1<span class="text-danger">*</span></label>
                                                            <input name="slide_image_1" class="form-control" type="file" accept="image/jpg, image/jpeg, image/png, image/webp, image/svg+xml" onchange="loadFile_9(event)">
                                                        </div>
                                                        <div class="d-flex">
                                                            <img class="img-fluid image-preview bg-gray-300" src="{{ $data->slide_image_1 }}" alt="" id="output-9">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 mt-3">
                                                        <div class="form-group">
                                                            <label class="form-label">Slide Image 2<span class="text-danger">*</span></label>
                                                            <input name="slide_image_2" class="form-control" type="file" accept="image/jpg, image/jpeg, image/png, image/webp, image/svg+xml" onchange="loadFile_10(event)">
                                                        </div>
                                                        <div class="d-flex">
                                                            <img class="img-fluid image-preview bg-gray-300" src="{{ $data->slide_image_2 }}" alt="" id="output-10">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 mt-3">
                                                        <div class="form-group">
                                                            <label class="form-label">Slide Image 3<span class="text-danger">*</span></label>
                                                            <input name="slide_image_3" class="form-control" type="file" accept="image/jpg, image/jpeg, image/png, image/webp, image/svg+xml" onchange="loadFile_11(event)">
                                                        </div>
                                                        <div class="d-flex">
                                                            <img class="img-fluid image-preview bg-gray-300" src="{{ $data->slide_image_3 }}" alt="" id="output-11">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 mt-3">
                                                        <div class="form-group">
                                                            <label class="form-label">Slide Image 4<span class="text-danger">*</span></label>
                                                            <input name="slide_image_4" class="form-control" type="file" accept="image/jpg, image/jpeg, image/png, image/webp, image/svg+xml" onchange="loadFile_12(event)">
                                                        </div>
                                                        <div class="d-flex">
                                                            <img class="img-fluid image-preview bg-gray-300" src="{{ $data->slide_image_4 }}" alt="" id="output-12">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 mt-3">
                                                        <div class="form-group">
                                                            <label class="form-label">Slide Image 5<span class="text-danger">*</span></label>
                                                            <input name="slide_image_5" class="form-control" type="file" accept="image/jpg, image/jpeg, image/png, image/webp, image/svg+xml" onchange="loadFile_13(event)">
                                                        </div>
                                                        <div class="d-flex">
                                                            <img class="img-fluid image-preview bg-gray-300" src="{{ $data->slide_image_5 }}" alt="" id="output-13">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 mt-3">
                                                        <div class="form-group">
                                                            <label class="form-label">Slide Image 6<span class="text-danger">*</span></label>
                                                            <input name="slide_image_6" class="form-control" type="file" accept="image/jpg, image/jpeg, image/png, image/webp, image/svg+xml" onchange="loadFile_14(event)">
                                                        </div>
                                                        <div class="d-flex">
                                                            <img class="img-fluid image-preview bg-gray-300" src="{{ $data->slide_image_6 }}" alt="" id="output-14">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="tab-pane fade" id="tab1" role="tabpanel" aria-labelledby="tab-1">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <label class="form-label">Title<span class="text-danger">*</span></label>
                                                        <input name="about_title" type="text" class="form-control" value="{{ old('about_title', $data->about_title) }}">
                                                    </div>
                                                    <div class="col-md-12 mt-3">
                                                        <label class="form-label">Description<span class="text-danger">*</span></label>
                                                        <textarea id="content1" name="about_description" class="form-control">{!! old('about_description', $data->about_description) !!}</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <label class="form-label">Button Text<span class="text-danger">*</span></label>
                                                        <input name="about_button_text" type="text" class="form-control" value="{{ old('about_button_text', $data->about_button_text) }}">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">Button URL<span class="text-danger">*</span></label>
                                                        <input name="about_button_url" type="text" class="form-control" value="{{ old('about_button_text', $data->about_button_url) }}">
                                                    </div>
                                                </div>

                                                <div class="row mt-3">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="form-label">Image<span class="text-danger">*</span></label>
                                                            <input name="about_image_1" class="form-control" type="file" accept="image/jpg, image/jpeg, image/png, image/webp, image/svg+xml" onchange="loadFile(event)">
                                                        </div>
                                                        <div class="d-flex">
                                                            <img class="img-fluid image-preview bg-gray-300" src="{{ $data->about_image_1 }}" alt="" id="output-1">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="tab-pane fade" id="tab2" role="tabpanel" aria-labelledby="tab-2">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label">Image<span class="text-danger">*</span></label>
                                                    <input name="contact_image_1" class="form-control" type="file" accept="image/jpg, image/jpeg, image/png, image/webp, image/svg+xml" onchange="loadFile_3(event)">
                                                </div>
                                                <div class="d-flex">
                                                    <img class="img-fluid image-preview bg-gray-300" src="{{ $data->contact_image_1 }}" alt="" id="output-3">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <label class="form-label">Title<span class="text-danger">*</span></label>
                                                        <input name="contact_title" type="text" class="form-control" value="{{ old('contact_title', $data->contact_title) }}">
                                                    </div>
                                                    <div class="col-md-12 mt-3">
                                                        <label class="form-label">Description<span class="text-danger">*</span></label>
                                                        <textarea id="content2" name="contact_description" class="form-contro">{!! old('contact_description', $data->contact_description) !!}</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="tab-pane fade" id="tab3" role="tabpanel" aria-labelledby="tab-3">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="form-label">Image 1<span class="text-danger">*</span></label>
                                                            <input name="intro_app_image_1" class="form-control" type="file" accept="image/jpg, image/jpeg, image/png, image/webp, image/svg+xml" onchange="loadFile_4(event)">
                                                        </div>
                                                        <div class="d-flex">
                                                            <img class="img-fluid image-preview bg-gray-300" src="{{ $data->intro_app_image_1 }}" alt="" id="output-4">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="form-label">Image 2<span class="text-danger">*</span></label>
                                                            <input name="intro_app_image_2" class="form-control" type="file" accept="image/jpg, image/jpeg, image/png, image/webp, image/svg+xml" onchange="loadFile_5(event)">
                                                        </div>
                                                        <div class="d-flex">
                                                            <img class="img-fluid image-preview bg-gray-300" src="{{ $data->intro_app_image_2 }}" alt="" id="output-5">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="form-label">Image 3<span class="text-danger">*</span></label>
                                                            <input name="intro_app_image_3" class="form-control" type="file" accept="image/jpg, image/jpeg, image/png, image/webp, image/svg+xml" onchange="loadFile_6(event)">
                                                        </div>
                                                        <div class="d-flex">
                                                            <img class="img-fluid image-preview bg-gray-300" src="{{ $data->intro_app_image_3 }}" alt="" id="output-6">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <label class="form-label">Title<span class="text-danger">*</span></label>
                                                        <input name="intro_app_title" type="text" class="form-control" value="{{ old('intro_app_title', $data->intro_app_title) }}">
                                                    </div>
                                                    <div class="col-md-12 mt-3">
                                                        <label class="form-label">Description<span class="text-danger">*</span></label>
                                                        <textarea id="content3" name="intro_app_description" class="form-control">{!! old('intro_app_description', $data->intro_app_description) !!}</textarea>
                                                    </div>
                                                </div>
                                                <div class="row mt-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label">Button Text<span class="text-danger">*</span></label>
                                                        <input name="intro_app_button_text" type="text" class="form-control" value="{{ old('intro_app_button_text', $data->intro_app_button_text) }}">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">Button URL<span class="text-danger">*</span></label>
                                                        <input name="intro_app_button_url" type="text" class="form-control" value="{{ old('intro_app_button_text', $data->intro_app_button_url) }}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="tab-pane fade" id="tab4" role="tabpanel" aria-labelledby="tab-4">
                                        <div class="row">
                                            <ul class="list-group">
                                                @forelse ($services as $item)
                                                    <li class="list-group-item">
                                                        <div class="form-check">
                                                            <input name="service[]" class="form-check-input" type="checkbox" value="{{ $item->id }}" id="service-{{ $loop->iteration }}" {{ in_array($item->id, $data['service']) ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="service-{{ $loop->iteration }}">
                                                                {{ $item->title }}
                                                            </label>
                                                        </div>
                                                    </li>
                                                @empty
                                                    {{--  --}}
                                                @endforelse
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="tab5" role="tabpanel" aria-labelledby="tab-5">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <ul class="list-group">
                                                    @forelse ($testimonials as $item)
                                                        <li class="list-group-item">
                                                            <div class="form-check">
                                                                <input name="testimonial[]" class="form-check-input" type="checkbox" value="{{ $item->id }}" id="testimonial-{{ $loop->iteration }}" {{ in_array($item->id, $data['testimonial']) ? 'checked' : '' }}>
                                                                <label class="form-check-label" for="testimonial-{{ $loop->iteration }}">
                                                                    @while ($item->rating--)
                                                                        <i class="fa fa-star text-yellow-400" aria-hidden="true"></i>
                                                                    @endwhile
                                                                    <span class="fw-medium">{{ $item->name }}</span> <span>({{ $item->designation }})</span>

                                                                    {!! Str::limit($item->review, 300) !!}
                                                                </label>
                                                            </div>
                                                        </li>
                                                    @empty
                                                        {{--  --}}
                                                    @endforelse
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="tab6" role="tabpanel" aria-labelledby="tab-6">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label class="form-label">Our Clients Text</label>
                                                <textarea name="our_clients_text" class="form-control">{{ old('our_clients_text', $data->our_clients_text) }}</textarea>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="row">
                                                    <div class="col-md-4 col-xxl-3">
                                                        <div class="d-flex my-2">
                                                            <img class="img-fluid image-preview-sm bg-gray-300" src="" alt="" id="output-15">
                                                            <button id="photo-upload-btn" class="btn btn-primary rounded-0 d-none mx-1" type="button">Upload</button>
                                                        </div>
                                                        <div id="upload-status"></div>
                                                        <input class="form-control my-2" type="file" id="photo" name="image" accept="image/jpg, image/jpeg, image/png, image/webp, image/svg+xml" onchange="loadFile_15(event)">
                                                    </div>
                                                    <div class="col-md-8 col-xxl-9">
                                                        <div id="our-clients-images" class="">
                                                            @forelse ($our_clients_image_urls as $url)
                                                                <div class="our-clients-image-container m-3">
                                                                    <i class="fa fa-times" aria-hidden="true" onclick="deleteClientImage(this, '{{ $url }}')"></i>
                                                                    <img src="{{ asset(Storage::url($url)) }}" alt="" class="thumb-2 shadow-lg">
                                                                </div>
                                                            @empty
                                                                {{--  --}}
                                                            @endforelse
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="tab7" role="tabpanel" aria-labelledby="tab-7">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label class="form-label">Our Packages Text</label>
                                                <textarea name="our_packages_text" class="form-control">{{ old('our_packages_text', $data->our_packages_text) }}</textarea>
                                            </div>
                                            <div class="col-md-6 mt-3">
                                                <label class="form-label">Premium Features<span class="text-danger">*</span></label>
                                                <ul class="list-group">
                                                    @forelse ($features as $item)
                                                        <li class="list-group-item border-0 py-2">
                                                            <div class="form-check">
                                                                <input name="feature[]" class="form-check-input" type="checkbox" value="{{ $item->id }}" id="feature-{{ $loop->iteration }}" {{ in_array($item->id, $data['features']) ? 'checked' : '' }}>
                                                                <label class="form-check-label" for="feature-{{ $loop->iteration }}">
                                                                    {!! $item->title !!}
                                                                </label>
                                                            </div>
                                                        </li>
                                                    @empty
                                                        {{--  --}}
                                                    @endforelse
                                                </ul>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mt-3">
                                                    <label class="form-label">Select Packages:</label>
                                                </div>
                                                <div class="border-top pt-2">
                                                    <ul class="list-group">
                                                        @forelse ($packages as $item)
                                                            <li class="list-group-item ps-0 py-2 border-0">
                                                                <div class="form-check">
                                                                    <input name="package[]" class="form-check-input" type="checkbox" value="{{ $item->id }}" id="package-{{ $loop->iteration }}" {{ in_array($item->id, $data['packages']) ? 'checked' : '' }}>
                                                                    <label class="form-check-label" for="package-{{ $loop->iteration }}">{{ $item->title }}</label>
                                                                </div>
                                                            </li>
                                                        @empty
                                                            {{--  --}}
                                                        @endforelse
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row bottom-btn-bar">
                                    <div class="col-md-12">
                                        <input type="submit" value="Save" class="btn btn-primary m-t-20 m-b-20 rounded-0" data-bs-toggle="tooltip" data-bs-original-title="Save changes">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
    </section>


    @push('scripts')
        <script>
            let loadFile = function(event) {
                let output = document.getElementById('output-1');
                output.src = URL.createObjectURL(event.target.files[0]);
                output.onload = function() {
                    URL.revokeObjectURL(output.src)
                }
            };

            let loadFile_2 = function(event) {
                let output = document.getElementById('output-2');
                output.src = URL.createObjectURL(event.target.files[0]);
                output.onload = function() {
                    URL.revokeObjectURL(output.src)
                }
            };

            let loadFile_3 = function(event) {
                let output = document.getElementById('output-3');
                output.src = URL.createObjectURL(event.target.files[0]);
                output.onload = function() {
                    URL.revokeObjectURL(output.src)
                }
            };
            let loadFile_4 = function(event) {
                let output = document.getElementById('output-4');
                output.src = URL.createObjectURL(event.target.files[0]);
                output.onload = function() {
                    URL.revokeObjectURL(output.src)
                }
            };
            let loadFile_5 = function(event) {
                let output = document.getElementById('output-5');
                output.src = URL.createObjectURL(event.target.files[0]);
                output.onload = function() {
                    URL.revokeObjectURL(output.src)
                }
            };
            let loadFile_6 = function(event) {
                let output = document.getElementById('output-6');
                output.src = URL.createObjectURL(event.target.files[0]);
                output.onload = function() {
                    URL.revokeObjectURL(output.src)
                }
            };
            let loadFile_7 = function(event) {
                let output = document.getElementById('output-7');
                output.src = URL.createObjectURL(event.target.files[0]);
                output.onload = function() {
                    URL.revokeObjectURL(output.src)
                }
            };
            let loadFile_8 = function(event) {
                let output = document.getElementById('output-8');
                output.src = URL.createObjectURL(event.target.files[0]);
                output.onload = function() {
                    URL.revokeObjectURL(output.src)
                }
            };
            let loadFile_9 = function(event) {
                let output = document.getElementById('output-9');
                output.src = URL.createObjectURL(event.target.files[0]);
                output.onload = function() {
                    URL.revokeObjectURL(output.src)
                }
            };
            let loadFile_10 = function(event) {
                let output = document.getElementById('output-10');
                output.src = URL.createObjectURL(event.target.files[0]);
                output.onload = function() {
                    URL.revokeObjectURL(output.src)
                }
            };
            let loadFile_11 = function(event) {
                let output = document.getElementById('output-11');
                output.src = URL.createObjectURL(event.target.files[0]);
                output.onload = function() {
                    URL.revokeObjectURL(output.src)
                }
            };
            let loadFile_12 = function(event) {
                let output = document.getElementById('output-12');
                output.src = URL.createObjectURL(event.target.files[0]);
                output.onload = function() {
                    URL.revokeObjectURL(output.src)
                }
            };
            let loadFile_13 = function(event) {
                let output = document.getElementById('output-13');
                output.src = URL.createObjectURL(event.target.files[0]);
                output.onload = function() {
                    URL.revokeObjectURL(output.src)
                }
            };
            let loadFile_14 = function(event) {
                let output = document.getElementById('output-14');
                output.src = URL.createObjectURL(event.target.files[0]);
                output.onload = function() {
                    URL.revokeObjectURL(output.src)
                }
            };
            let loadFile_15 = function(event) {
                let output = document.getElementById('output-15');
                output.src = URL.createObjectURL(event.target.files[0]);
                output.onload = function() {
                    URL.revokeObjectURL(output.src)
                }
            };

            $('#photo-upload-btn').on('click', function () {
                const fileInput = document.getElementById('photo');
                const formData = new FormData();

                formData.append('photo', fileInput.files[0]);

                $.ajax({
                    url: '{{ route('admin.upload-photo') }}',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function (res) {
                        if (res.status == 'true') {
                            $('#upload-status').html('<p>Image uploaded successfully!</p>');
                            $('#our-clients-images').empty();
                            $('#our-clients-images').append(res.html);
                            $('#photo-upload-btn').addClass('d-none');
                        } else {
                            $('#upload-status').html('Error uploading image.');
                        }
                    },
                    error: function (xhr, status, error) {
                        $('#upload-status').html('<p>An error occurred while uploading the image.</p>');
                    }
                });
            });

            function deleteClientImage(ele, url) {
                let x = url.replace("http://dietician.test/storage/", "");

                let confirmAction = confirm("Are you sure you want to delete this photo?");

                if (confirmAction) {
                    $.ajax({
                        url: '{{ route('admin.delete-our-clients-image') }}',
                        type: 'POST',
                        data: {
                            'url' : x
                        },
                        success: function (res) {
                            if (res == 'done') {
                                $(ele).parent().remove();
                            }
                        }
                    });
                }
            }

            $(document).ready(function() {
                $('#photo').change(function() {
                    if ($(this).src != '') {
                        $('#photo-upload-btn').removeClass('d-none');
                    } else {
                        $('#photo-upload-btn').addClass('d-none');
                    }
                });

                CKEDITOR.replace('content1', {
                    filebrowserUploadUrl: '{{ route('ckeditor.upload', ['_token' => csrf_token()]) }}',
                    filebrowserUploadMethod: 'form'
                });
                CKEDITOR.replace('content2', {
                    filebrowserUploadUrl: '{{ route('ckeditor.upload', ['_token' => csrf_token()]) }}',
                    filebrowserUploadMethod: 'form'
                });
                CKEDITOR.replace('content3', {
                    filebrowserUploadUrl: '{{ route('ckeditor.upload', ['_token' => csrf_token()]) }}',
                    filebrowserUploadMethod: 'form'
                });
            });
        </script>
    @endpush

@endsection
