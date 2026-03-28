@extends('layouts.blank')

@section('content')
    <section class="login-main">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xxl-10 col-xl-10 col-lg-12 col-md-12 col-sm-12 col-md-12">
                    <div class="card-main login-card step12 shadow-n">
                        <form action="{{ route('store-step12') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <h3>Get Started</h3>
                            <h4>Step 12 of 12</h4>
                            <div class="step-loading">
                                <div class="loading12"></div>
                            </div>
                            <h5 class="login-lable">What clinical test you have?</h5>
                            <div class="medical">
                                <div class="row mb-4">
                                    <div class="col-md-12 text-end">
                                        <button type="button" class="btn btn-success rounded-0" onclick="addTestBlockDiv()" data-bs-toggle="tooltip" data-bs-original-title="Add Test">+</button>
                                    </div>
                                </div>
                                <div id="test-block">
                                    <div class="row mx-1 rounded-0 border mb-3 pb-3 test-block-div">
                                        <div class="d-flex justify-content-end px-0">
                                            <button type="button" class="remove-test-btn btn btn-sm rounded-0 py-0" onclick="removeTestBlock(this)" data-bs-toggle="tooltip" data-bs-original-title="Remove this test">&times;</button>
                                        </div>
                                        <div class="py-0 px-3">
                                            <div class="row">
                                                <div class="col-md-5">
                                                    <div class="form-group">
                                                        <label class="form-label text-start">Test</label>
                                                        <select name="test[]" class="form-select test-ele">
                                                            <option selected disabled>Select Test</option>
                                                            @foreach ($clinical_tests as $item)
                                                                <option value="{{ $item }}">{{ $item }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label class="form-label">Date</label>
                                                        <input type="date" name="date[]" class="form-control" max="{{ date('Y-m-d') }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="form-label">File</label>
                                                        <input name="file[]" type="file" class="form-control">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="clearfix"></div>

                            <div>
                                @if ($errors->step12_error_bag->any())
                                    <div class="alert alert-danger pb-0 text-start">
                                        @foreach ($errors->step12_error_bag->all() as $error)
                                            <p>{{ $error }}</p>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <div class="d-flex login-b-buttons">
                                <a class="back-button" href="{{ route('step11') }}">Back</a>
                                <button class="btn continue-button mx-auto" type="submit">Continue</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @push('scripts')
        <script>
            function addTestBlockDiv() {
                let html = '\
                    <div class="row mx-1 rounded-0 border mb-3 pb-3 test-block-div">\
                        <div class="d-flex justify-content-end px-0">\
                            <button type="button" class="btn btn-sm rounded-0 py-0" onclick="removeTestBlock(this)" data-bs-toggle="tooltip" data-bs-original-title="Remove this test">&times;</button>\
                        </div>\
                        <div class="py-0 px-3">\
                            <div class="row">\
                                <div class="col-md-5">\
                                    <div class="form-group">\
                                        <label class="form-label text-start">Test</label>\
                                        <select name="test[]" class="form-select test-ele">\
                                            @foreach ($clinical_tests as $item)\
                                                <option value="{{ $item }}">{{ $item }}</option>\
                                            @endforeach\
                                        </select>\
                                    </div>\
                                </div>\
                                <div class="col-md-3">\
                                    <div class="form-group">\
                                        <label class="form-label">Date</label>\
                                        <input type="date" name="date[]" class="form-control" max="{{ date('Y-m-d') }}">\
                                    </div>\
                                </div>\
                                <div class="col-md-4">\
                                    <div class="form-group">\
                                        <label class="form-label">File</label>\
                                        <input name="file[]" type="file" class="form-control">\
                                    </div>\
                                </div>\
                            </div>\
                        </div>\
                    </div>';


                $('#test-block').prepend(html);
                $('.test-ele:first').focus();

                $('.test-block-div:first').css('background-color','lightgray');

                setTimeout(() => {
                    $('.test-block-div:first').css('background-color','transparent');
                }, 300);

                $('html, body').animate({
                    scrollTop: $(document).height()
                }, 40);
            }

            function removeTestBlock(ele) {
                $(ele).closest('.test-block-div').remove();
            }
        </script>
    @endpush
@endsection
