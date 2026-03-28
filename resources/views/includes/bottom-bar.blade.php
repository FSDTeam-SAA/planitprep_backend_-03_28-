<section class="bottom-bar w-bg pic-frame mobile-hide">
    <div class="container">
        <div class="subscribe-main">
            <div class="row">
                <div class="col-4" data-aos="fade-right" data-aos-duration="1000" data-aos-delay="100">
                    <h3>Subscribe Newletter</h3>
                </div>
                <div class="col-6 p-0" data-aos="fade-left" data-aos-duration="1000" data-aos-delay="100">
                    <input id="subscriber-email" type="text" placeholder="Your email address">
                </div>
                <div class="col-2  p-0" data-aos="fade-right" data-aos-duration="1000" data-aos-delay="100">
                    <button type="button" class="btn btn-primary" onclick="subscribeNewsletter()" >Sign up</button>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xxl-4 col-xl-4 col-md-4 col-sm-12 col-12 mb-3" data-aos="fade-left" data-aos-duration="1000"
                data-aos-delay="200">
                <div class="bottom-left">
                    <img class="mb-3 w-50" src="{{ $logo }}" alt="Logo">
                    <br />
                    {!! $address !!}
                    <a href="{{ $facebook }}"><i class="fa fa-facebook"></i></a>
                    <a href="{{ $instagram }}"><i class="fa fa-instagram"></i></a>
                    <a href="{{ $youtube }}"><i class="fa fa-youtube-play"></i></a>
                    <a href="{{ $twitter }}"><i class="fa fa-twitter"></i></a>
                </div>
            </div>
            <div class="col-xxl-4 col-xl-4 col-md-4 col-sm-12 col-12 quick-links bottom-right" data-aos="fade-left"
                data-aos-duration="1000" data-aos-delay="500">
                <h3 class="mb-3">Quick Links</h3>
                <div class="quick-links">
                    <ul>
                        <li><a href="{{ route('home') }}"><i class="fa fa-angle-right"></i> Home</a></li>
                        <li><a href="#about-us"><i class="fa fa-angle-right"></i> About us</a></li>
                        @isset($pages)
                            @forelse ($pages as $page)
                                <li><a href="{{ $page->alias }}"><i class="fa fa-angle-right"></i> {{ $page->title }}</a></li>
                            @empty
                                {{--  --}}
                            @endforelse
                        @endisset
                        <!--<li><a href="#packages"><i class="fa fa-angle-right"></i> Packages</a></li>-->
                        <li><a href="#contact-us"><i class="fa fa-angle-right"></i> Contact us</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-xxl-4 col-xl-4 col-md-4 col-sm-12 col-12 bottom-right" data-aos="fade-left"
                data-aos-duration="1000" data-aos-delay="1000">
                <div class="contact-bottom">
                    <h3 class="mb-3">Contact us</h3>
                    <p><i class="fa fa-phone"></i> <a href="tel:+{{ $phone }}">{{ $phone }}</a></p>
                    <p><i class="fa fa-envelope"></i> <a href="mailto:{{ $email }}">{{ $email }}</a></p>
                    <p><i class="fa fa-globe"></i> <a href="{{ route('home') }}">{{ route('home') }}</a></p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="pic-frame w-bg mobile-hide">
    <div class="container-fluid copyright-main">
        <p>©Copyright {{ date('Y') }} Dietician® All Rights Reserved </p>
    </div>
</section>

@isset($data['slider_image_1'])
    <section class="mob-sec-main">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-10 text-center">
                    <img class="img-fluid  mb-3" src="{{ $mobile_logo }}" alt="Logo">
                    <img class="img-fluid" src="{{ $data['slider_image_1'] }}" alt="main image">
                </div>
                <div class="col-12 text-center">
                    <h2>{{ $data['slider_heading_2'] }}</h2>
                    <h3>{{ $data['slider_heading_3'] }}</h3>
                    <div class="row ios-button justify-content-center">
                        <div class="col-md-4 col-sm-4 col-6">
                            <a href="{{ $settings->apple_store_link }}">
                                <img class="img-fluid" src="{{ $settings->apple_store_image }}" alt="app image">
                            </a>
                        </div>
                        <div class="col-md-4 col-sm-4 col-6">
                            <a href="{{ $settings->android_store_link }}">
                                <img class="img-fluid" src="{{ $settings->android_store_image }}" alt="app image">
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endisset

@push('scripts')
    <script src="{{ asset('admin/js/plugins/sweetalert2.all.min.js') }}"></script>

    <script>
        function validateEmail(email) {
            var emailReg = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailReg.test(email);
        }

        function subscribeNewsletter() {
            let email = $('#subscriber-email').val();
            let timerInterval;
            const durationInSeconds = 10;
            const durationInMilliseconds = durationInSeconds * 1000;

            if (validateEmail(email)) {
                $.ajax({
                    url: '{{ route('subscribe-newsletter') }}',
                    type: 'POST',
                    data: {
                        'email' : email
                    },
                    success: function (res) {
                        if (res == 'done') {
                            Swal.fire({
                                icon: "success",
                                title: "<strong style='font-size:24px; color: #4B6F44;'>Subscription Successful</strong>",
                                html: `
                                    <span style='color: #4B6F44;'>Thank you for subscribing. Your subscription is active now.</span><br><br>
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
                            Swal.fire({
                                icon: "info",
                                title: "<strong style='font-size:24px; color: #0c88e0;'>Email Already Registered</strong>",
                                html: `
                                    <span style='color: #0a9205;'>Kindly re-check your email address.</span><br><br>
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
                        }
                    }
                });
            } else {
                Swal.fire({
                    icon: "error",
                    title: "<strong style='font-size:24px; color: #f44242;'>Invalid Email Address</strong>",
                    html: `
                        <span style='color: #099205;'>Kindly enter a valid email address.</span><br><br>
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
            }
        }

        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('.scroll-link').on('click', function(e) {
                e.preventDefault();

                let targetId = $(this).attr('href').substring(1);
                let targetElement = $('#' + targetId);

                $('html, body').animate({
                    scrollTop: targetElement.offset().top - 60
                }, 300);
            });
        });
    </script>
@endpush
