@extends('layouts.front')

@section('content')
    <section class="slider-main mobile-hide">
        <div class="container">
            <div class="row">
                <div class="col-xxl-6 col-xl-6 col-lg-6 col-md-6 col-sm-6 col-6" data-aos="fade-right" data-aos-duration="1000"
                    data-aos-delay="10">
                    <div class="main-pic-text">
                        <h1>{{ $data->slider_heading_1 }}</h1>
                        <h3>{{ $data->slider_heading_2 }}</h3>
                        <h4>{{ $data->slider_heading_3 }}</h4>
                        <div class="row play-stor-b">
                            <div class="col-4 p-0 ">
                                <a href="{{ $settings->apple_store_link }}">
                                    <img class="img-fluid" src="{{ $settings->apple_store_image }}" alt="app image">
                                </a>
                            </div>
                            <div class="col-4 p-0 ">
                                <a href="{{ $settings->android_store_link }}">
                                    <img class="img-fluid" src="{{ $settings->android_store_image }}" alt="app image">
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-4 col-xl-4 col-lg-3 col-md-3 col-sm-6 col-6" data-aos="fade-left"
                    data-aos-duration="1000" data-aos-delay="10">
                    <img class="img-fluid main-pic" src="{{ $data->slider_image_1 }}" alt="">
                </div>
                <div class="col-xxl-2 col-xl-2 col-lg-3 col-md-3 col-sm-6 col-6 p-0" data-aos="fade-left"
                    data-aos-duration="1000" data-aos-delay="10">

                    <section id="hero">
                        <div class="swiper mySwiper auto-slider">
                            <div class="swiper-wrapper">
                                <div class="swiper-slide">
                                    <img src="{{ $data->slide_image_1 }}" class="img-fluid">
                                </div>
                                <div class="swiper-slide">
                                    <img src="{{ $data->slide_image_2 }}" class="img-fluid">
                                </div>
                                <div class="swiper-slide">
                                    <img src="{{ $data->slide_image_3 }}" class="img-fluid">
                                </div>
                                <div class="swiper-slide">
                                    <img src="{{ $data->slide_image_4 }}" class="img-fluid">
                                </div>
                                <div class="swiper-slide">
                                    <img src="{{ $data->slide_image_5 }}" class="img-fluid">
                                </div>
                                <div class="swiper-slide">
                                    <img src="{{ $data->slide_image_6 }}" class="img-fluid">
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </section>

    <div class="clearfix"></div>

    <div id="about-us" class="mobile-hide">
        <section class="sec2-main fixs-pic pb-4"  style="background-color:#f8f8f8">
            <div class="pic100"><img src="{{ asset('imgs/100.png') }}" alt=""></div>
            <div class="container">
                <div class="row">
                    <div class="col-xxl-6 col-xl-4 col-lg-6 col-md-6 col-sm-12 col-12" data-aos="fade-right"
                        data-aos-duration="1000" data-aos-delay="10">
                        <img class="img-fluid" src="{{ asset('imgs/about-img2.png') }}" alt="">
                    </div>
                    <div class="col-xxl-6 col-xl-8 col-lg-6 col-md-6 col-sm-12 col-12" data-aos="fade-left"
                        data-aos-duration="1000" data-aos-delay="10">
                        <div class="sec2-text">
                            <h5>Get To Know</h5>
                            <h2>{{ $data->about_title }}</h2>
                            {!! $data->about_description !!}
                            <a class="read-more" href="{{ $data->about_button_url }}">{{ $data->about_button_text }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        @if (is_countable($data->service) && count($data->service))
            <section class="sec3-text">
                <div class="container">
                    <div class="row">
                        <div class="col-12 text-center">
                            <h4 data-aos="fade-left" data-aos-duration="1000" data-aos-delay="100">What We Offer</h4>
                            <h2 data-aos="fade-right" data-aos-duration="1000" data-aos-delay="100">Our Diet & Nutrition Servies
                            </h2>
                        </div>
                        @forelse ($data->service as $item)
                            <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-6 col-sm-6 col-12">
                                <div class="services-box" data-aos="fade-down" data-aos-duration="1000" data-aos-delay="200">
                                    <img src="{{ $item->image }}" alt="service">
                                    <h3>{{ $item->title }}</h3>
                                    <p>
                                        {!! $item->short_description !!}
                                    </p>
                                    <a href="#">Read more</a>
                                </div>
                            </div>
                        @empty
                            {{--  --}}
                        @endforelse
                    </div>
                </div>
            </section>
        @endif

        <section class="sec3-text sec4-text text-center fixs-pic d-none" style="background-color:#F8F8F8">
            <div class="left-ani-pic"><img src="{{ asset('imgs/2-4-180x300.png') }}" alt=""></div>
            <div class="container">
                <div class="row">
                    <div class="col-12 text-center">
                        <h4 data-aos="fade-right" data-aos-duration="1000" data-aos-delay="100">How To Start</h4>
                        <h2 data-aos="fade-left" data-aos-duration="1000" data-aos-delay="100">It's Easy To Start Today!
                        </h2>
                    </div>
                    <div class="col-xxl-3" data-aos="fade-right" data-aos-duration="1000" data-aos-delay="500">
                        <img class="img-fluid" src="{{ asset('imgs/our-diet1.jpg') }}" alt="">
                        <h5>01</h5>
                        <h3>Explore Services</h3>
                        <p>Browse our wide range of personalized nutrition services tailored to your health needs</p>
                    </div>
                    <div class="col-xxl-3" data-aos="fade-right" data-aos-duration="1000" data-aos-delay="200">
                        <img class="img-fluid" src="{{ asset('imgs/our-diet2.jpg') }}" alt="">
                        <h5>02</h5>
                        <h3>Get Brief</h3>
                        <p>Get a quick overview of our offerings and see how we can help you achieve your health goals.</p>
                    </div>
                    <div class="col-xxl-3 " data-aos="fade-left" data-aos-duration="1000" data-aos-delay="200">
                        <img class="img-fluid" src="{{ asset('imgs/our-diet3.jpg') }}" alt="">
                        <h5>03</h5>
                        <h3>Schedule Meeting</h3>
                        <p>Book a one-on-one consultation with our expert dieticians to start your wellness journey.</p>
                    </div>
                    <div class="col-xxl-3" data-aos="fade-left" data-aos-duration="1000" data-aos-delay="500">
                        <img class="img-fluid" src="{{ asset('imgs/our-diet4.jpg') }}" alt="">
                        <h5>04</h5>
                        <h3>Celebrate Health</h3>
                        <p>Join us in celebrating your health milestones with expert support every step of the way.</p>
                    </div>
                </div>
            </div>
        </section>

        <!--<section id="packages" class="our-packages ">-->
        <!--    <div class="container-fluid">-->
        <!--        <div class="row justify-content-center">-->
        <!--            <div class="col-12">-->
        <!--                <h3 data-aos="fade-down" data-aos-duration="1000" data-aos-delay="10">Our Packages</h3>-->
        <!--                <p data-aos="fade-up" data-aos-duration="1000" data-aos-delay="10">{{ $data['our_packages_text'] }}</p>-->
        <!--            </div>-->
        <!--            <div class="col-9">-->
        <!--                <div class="row">-->
        <!--                    <div class="col-xxl-6 col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12 mb-3" data-aos="fade-right"-->
        <!--                        data-aos-duration="1000" data-aos-delay="30">-->
        <!--                        <div class="premium-features">-->
        <!--                            <h4>Premium Features</h4>-->
        <!--                            <ul>-->
        <!--                                @forelse ($data['features'] as $item)-->
        <!--                                    <li>-->
        <!--                                        <i class="fa fa-angle-right"></i>-->
        <!--                                        {{ $item->title }}-->
        <!--                                    </li>-->
        <!--                                @empty-->
        <!--                                    {{--  --}}-->
        <!--                                @endforelse-->
        <!--                            </ul>-->
        <!--                        </div>-->
        <!--                    </div>-->
        <!--                    <div class="col-xxl-6 col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12 mb-3" data-aos="fade-left" data-aos-duration="1000" data-aos-delay="40">-->
        <!--                        @forelse ($data['packages'] as $item)-->
        <!--                            <div class="pack-main ">-->
        <!--                                <div class="row">-->
        <!--                                    <div class="col-9">-->
        <!--                                        <h4>{{ $item->title }}</h4>-->
        <!--                                        <p><span>₹{{ $item->price }}</span>₹{{ $item->discounted_price }}</p>-->
        <!--                                    </div>-->
        <!--                                    <div class="col-2 text-center">-->
        <!--                                        <i class="fa fa-shopping-cart p-cart-icon"></i>-->
        <!--                                        <div class="save20">Save {{ 100 - (($item->price / $item->price) * 100) }}%</div>-->
        <!--                                    </div>-->
        <!--                                    <div class="col-1 ">-->
        <!--                                        <div class="buy-icon"><a href="#"><i class="fa fa-angle-right"></i></a>-->
        <!--                                        </div>-->
        <!--                                    </div>-->
        <!--                                </div>-->
        <!--                            </div>-->
        <!--                        @empty-->
        <!--                            {{--  --}}-->
        <!--                        @endforelse-->
        <!--                    </div>-->
        <!--                </div>-->
        <!--            </div>-->
        <!--        </div>-->
        <!--    </div>-->
        <!--</section>-->

        <section class="sec5-main w-bg">
            <div class="pic100 a-mrg-pic"><img src="{{ asset('imgs/3-3-1-204x300.png') }}" alt=""></div>
            <div class="container">
                <div class="row">
                    <div class="col-xxl-6 col-xl-4 col-lg-6 col-md-6 col-sm-12 col-12" data-aos="fade-right"
                        data-aos-duration="800" data-aos-delay="500">
                        <div class="hover-ani-main">
                            <div class="hover-pic">
                                <div class="sc1"><a href="#"><img src="{{ $data->intro_app_image_1 }}" alt=""></a></div>
                                <div class="sc2"><a href="#"><img src="{{ $data->intro_app_image_2 }}" alt=""></a></div>
                                <div class="sc3"><a href="#"><img src="{{ $data->intro_app_image_3 }}" alt=""></a></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-6 col-xl-8 col-lg-6 col-md-6 col-sm-12 col-12" data-aos="fade-left"
                        data-aos-duration="800" data-aos-delay="500">
                        <div class="sec2-text app-screen">
                            <h2>{{ $data->intro_app_title }}</h2>
                            {!! $data->intro_app_description !!}
                            <a class="get-app" href="{{ $data->intro_app_button_url }}">{{ $data->intro_app_button_text }} <i class="fa fa-angle-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="contact-us" class="sec5-main w-bg enquiry-form">
            <div class="container">
                <div class="row">
                    <div class="col-xxl-6 col-xl-4 col-lg-6 col-md-6 col-sm-12 col-12" data-aos="fade-right"
                        data-aos-duration="1000" data-aos-delay="1000">
                        <img class="img-fluid" src="{{ $data->contact_image_1 }}" alt="image">
                    </div>
                    <div class="col-xxl-6 col-xl-8 col-lg-6 col-md-6 col-sm-12 col-12" data-aos="fade-left"
                        data-aos-duration="1000" data-aos-delay="1000">
                        <div class="enquiry-form-box">
                            <h4>Let's Talk</h4>
                            <h2>{{ $data->contact_title }}</h2>
                            {!! $data->contact_description !!}

                            <form id="contact-form" action="{{ route('store-contact-form') }}" method="post">
                                @csrf
                                <div class="row">
                                    <div class="col-xxl-6">
                                        <input name="name" type="text" class="form-control" placeholder="You Name">
                                    </div>
                                    <div class="col-xxl-6">
                                        <input name="phone" type="text" class="form-control" placeholder="You Phone">
                                    </div>
                                    <div class="col-xxl-12">
                                        <input id="enquiry-email" name="email" type="text" class="form-control" placeholder="Email Address">
                                    </div>
                                    <div class="col-xxl-12">
                                        <input name="subject" type="text" class="form-control" placeholder="Subject">
                                    </div>
                                    <div class="col-xxl-12">
                                        <textarea name="message" class="form-control" placeholder="Write Your Message" rows="4"></textarea>
                                    </div>
                                    <div class="col-xxl-12">
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class=" about-me w-bg">
            <div class="container-fluid our-status-bar" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="10">
                <div class="row">
                    <div class="col-xxl-3 col-xl-3 col-lg-3 col-md-6 col-sm-6 col-12 mb-3" data-aos="fade-right"
                        data-aos-duration="1000" data-aos-delay="500">
                        <img src="{{ asset('imgs/icon1.png') }}" alt="">
                        <h3>46+</h3>
                        <h4>Dietician Program</h4>
                    </div>
                    <div class="col-xxl-3 col-xl-3 col-lg-3 col-md-6 col-sm-6 col-12 mb-3" data-aos="fade-right"
                        data-aos-duration="1000" data-aos-delay="200">
                        <img src="{{ asset('imgs/icon2.png') }}" alt="">
                        <h3>89+</h3>
                        <h4>Health Diet Session</h4>
                    </div>
                    <div class="col-xxl-3 col-xl-3 col-lg-3 col-md-6 col-sm-6 col-12 mb-3" data-aos="fade-left"
                        data-aos-duration="1000" data-aos-delay="200">
                        <img class="icon3" src="{{ asset('imgs/icon3.png') }}" alt="">
                        <h3>250+</h3>
                        <h4>Happy Customer</h4>
                    </div>
                    <div class="col-xxl-3 col-xl-3 col-lg-3 col-md-6 col-sm-6 col-12 mb-3" data-aos="fade-left"
                        data-aos-duration="1000" data-aos-delay="500">
                        <img src="{{ asset('imgs/icon4.png') }}" alt="">
                        <h3>25+</h3>
                        <h4>Dietician Certificate</h4>
                    </div>
                </div>
            </div>
        </section>


        <!--Slider1-->
        <!--<section id="demos" class="our-packages befor-after">-->
        <!--    <div class="left-ani-pic"><img class="o-pic" src="{{ asset('imgs/1-3.png') }}" alt=""></div>-->
        <!--    <div class="container">-->
        <!--        <div class="row justify-content-center">-->
        <!--            <div class="col-12">-->
        <!--                <h3 data-aos="fade-up" data-aos-duration="1000" data-aos-delay="100">Our Clients</h3>-->
        <!--                <p data-aos="fade-up" data-aos-duration="800" data-aos-delay="300">{{ $data['our_clients_text'] }}</p>-->
        <!--            </div>-->
        <!--            <div class="large-12 columns" data-aos="fade-up" data-aos-duration="800" data-aos-delay="500">-->
        <!--                <div id="befor-after" class="owl-carousel  owl-theme">-->
        <!--                    @forelse ($data['our_clients_image'] as $item)-->
        <!--                        <div class="item video-text">-->
        <!--                            <a href="javascript:void(0)">-->
        <!--                                <img class="img-fluid" src="{{ $item }}">-->
        <!--                            </a>-->
        <!--                        </div>-->
        <!--                    @empty-->
        <!--                        {{--  --}}-->
        <!--                    @endforelse-->
        <!--                </div>-->
        <!--            </div>-->
        <!--        </div>-->
        <!--    </div>-->
        <!--</section>-->
        <!--Slider1-->
        <!--Slider2-->
        <!--<section id="demos" class="customer-main">-->
        <!--    <div class="container-fluid">-->
                <!-- <div class="customer-act"></div>      -->
        <!--        <div class="row justify-content-center">-->
        <!--            <div class="col-12">-->
        <!--                <h4 data-aos="fade-left" data-aos-duration="1000" data-aos-delay="100">Testimonials</h4>-->
        <!--                <h3 data-aos="fade-right" data-aos-duration="1000" data-aos-delay="100">What Our Customer Say-->
        <!--                </h3>-->

        <!--            </div>-->
        <!--            <div class="large-12 columns" data-aos="fade-up" data-aos-duration="800" data-aos-delay="500">-->
        <!--                <div id="customer" class="owl-carousel  owl-theme">-->
        <!--                    @forelse ($data->testimonial as $item)-->
        <!--                        <div class="item review-box">-->
        <!--                            <img class="comme-icon" src="{{ asset('imgs/comme.png') }}" alt="">-->
        <!--                            <p>{!! Str::limit($item->review, 300) !!}</p>-->
        <!--                            <div class="customer-detial">-->
        <!--                                <img src="{{ $item->image }}" alt="customer">-->
        <!--                                <h2>{{ $item->name }}</h2>-->
        <!--                                <h6>{{ $item->designation }}</h6>-->
        <!--                            </div>-->
        <!--                        </div>-->
        <!--                    @empty-->
        <!--                        {{--  --}}-->
        <!--                    @endforelse-->
        <!--                </div>-->
        <!--            </div>-->
        <!--        </div>-->
        <!--    </div>-->
        <!--</section>-->
        <!--Slider2-->
    </div>

    @push('scripts')
        <script src="{{ asset('js/popper.min.js') }}"></script>
        <script src="{{ asset('js/swiper-bundle.min.js') }}"></script>

        <script type="text/javascript">
            var swiper = new Swiper(".mySwiper", {
                direction: "vertical",
                slidesPerView: 1,
                spaceBetween: 30,
                loop: true,
                grabCursor: true,
                autoplay: {
                    delay: 1,
                    disableOnInteraction: true
                },
                breakpoints: {
                    640: {
                        slidesPerView: 3,
                        spaceBetween: 20,
                    },
                    768: {
                        slidesPerView: 4,
                        spaceBetween: 40,
                    },
                    1399: {
                        slidesPerView: 2.5,
                        spaceBetween: 40,
                    },
                    1600: {
                        slidesPerView: 2.5,
                        spaceBetween: 20,
                    },
                },
                freeMode: true,
                speed: 5000,
                freeModeMomentum: false
            });

            swiper.on('slideChange', function() {
                var currentActiveSlide = $('.swiper-slide-active img').attr('src');

                $(".main-pic").fadeTo(1000, 0.30, function() {
                    $(".main-pic").attr("src", currentActiveSlide);
                }).fadeTo(500, 1);
            });


            $(document).ready(function() {
                var owl1 = $('#befor-after');
                owl1.owlCarousel({
                    margin: 10,
                    nav: true,
                    loop: true,
                    autoplay: true,
                    autoplayTimeout: 4000,
                    autoplayHoverPause: true,
                    responsive: {
                        0: {
                            items: 1
                        },
                        600: {
                            items: 1
                        },
                        1000: {
                            items: 3
                        }
                    }
                });

                var owl2 = $('#customer');
                owl2.owlCarousel({
                    margin: 10,
                    nav: true,
                    loop: true,
                    autoplay: true,
                    autoplayTimeout: 3000,
                    autoplayHoverPause: true,
                    responsive: {
                        0: {
                            items: 1
                        },
                        600: {
                            items: 1
                        },
                        1000: {
                            items: 3
                        }
                    }
                });

                var owl3 = $('#v-slider');
                owl3.owlCarousel({
                    margin: 10,
                    nav: false,
                    loop: true,
                    autoplay: true,
                    autoplayHoverPause: true,
                    animateOut: 'slideOutUp',
                    animateIn: 'slideInUp',
                    autoplayTimeout: 2000,
                    responsive: {
                        0: {
                            items: 1
                        },
                        600: {
                            items: 1
                        },
                        1000: {
                            items: 2
                        }
                    }
                });

                $(".pack-main").on("mouseover", function() {
                    $(this).addClass("pack-main-h");
                });
                $(".pack-main").on("mouseout", function() {
                    $(this).removeClass("pack-main-h");
                })
            });
        </script>

        <script>
            $(document).ready(function() {
                $('#contact-form').on('submit', function (e) {
                    e.preventDefault(); // Prevent form submission

                    // Flag to track form validity
                    let isValid = true;

                    // Check required fields (You can add more field selectors as needed)
                    $('#contact-form input, #contact-form textarea').each(function() {
                        // Check if the field is empty and is required
                        if ($(this).prop('required') && $(this).val().trim() === '') {
                            isValid = false; // Set form validity to false
                            $(this).css('border', '1px solid red'); // Optionally, highlight empty fields
                        } else {
                            $(this).css('border', ''); // Reset field style if it's not empty
                        }
                    });

                    // If form is not valid, show SweetAlert
                    if (!isValid) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Please fill in all required fields.',
                        });
                        return; // Stop form submission if fields are missing
                    }

                    // If form is valid, submit the form via AJAX
                    let formData = new FormData(this);

                    $.ajax({
                        url: $(this).attr('action'),
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(data) {
                            if (data == 'done') {
                                let timerInterval;
                                const durationInSeconds = 10;
                                const durationInMilliseconds = durationInSeconds * 1000;
                                Swal.fire({
                                    icon: "success",
                                    title: "<strong style='font-size:24px; color: #4B6F44;'>Thank You</strong>",
                                    html: `
                                        <span style='color: #4B6F44;'>Your enquiry is successfully received.</span><br><br>
                                        <b>10</b>
                                    `,
                                    showConfirmButton: false,
                                    timer: durationInMilliseconds,
                                    didOpen: () => {
                                        const timer = Swal.getPopup().querySelector("b");
                                        timerInterval = setInterval(() => {
                                            const remainingTime = Swal.getTimerLeft();
                                            const remainingSeconds = Math.ceil(remainingTime / 1000);
                                            timer.textContent = remainingSeconds;
                                        }, 1000);
                                    },
                                    willClose: () => {
                                        clearInterval(timerInterval);
                                    },
                                    showClass: {
                                        popup: `
                                            animate__animated
                                            animate__fadeInUp
                                            animate__faster
                                        `
                                    },
                                    hideClass: {
                                        popup: `
                                            animate__animated
                                            animate__fadeOutDown
                                            animate__faster
                                        `
                                    }
                                });
                            } else {
                                // Handle error or failure response from the server
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Submission Failed',
                                    text: 'Please check if you have filled form correctly.',
                                });
                            }
                        },
                    });
                });
            });
        </script>

    @endpush
@endsection
