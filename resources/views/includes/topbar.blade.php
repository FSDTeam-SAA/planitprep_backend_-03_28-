<section class="topbar mobile-hide">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 text-center">
                <marquee>
                    <p>{{ $marquee }}</p>
                </marquee>
            </div>
        </div>
    </div>
</section>
<section class="mobile-hide">
    <div class="nav">
        <nav class="navbar navbar-expand-lg navbg">
            <div class="container-fluid">
                <div class="topbar">

                </div>
                <a class="navbar-brand" href="{{ route('home') }}"><img class="navbar-logo" src="{{ $logo }}" alt="Logo"> </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"><i class="fa fa-bars" aria-hidden="true"></i></span>
                </button>
                <div class="collapse navbar-collapse " id="navbarSupportedContent">
                    <div class="mainnav-links">
                        <a href="{{ route('home') }}">Home</a>
                        <a href="#about-us" class="scroll-link">About us</a>
                        <!--<a href="#packages" class="scroll-link">Packages</a>-->
                        <a href="#contact-us" class="scroll-link">Contact us</a>
                    </div>
                    <!--<div class="d-xxl-flex d-xl-flex d-lg-flex login-button">-->
                    <!--    @auth-->
                    <!--        <a href="{{ route('sign-out') }}" class="btn btn-outline-success login-b">-->
                    <!--            <i class="fa fa-sign-in"></i> Logout-->
                    <!--        </a>-->
                    <!--    @else-->
                    <!--        <a href="{{ route('sign-in') }}" class="btn btn-outline-success login-b">-->
                    <!--            <i class="fa fa-sign-in"></i> Login-->
                    <!--        </a>-->
                    <!--    @endauth-->
                    <!--</div>-->
                </div>
            </div>
        </nav>
    </div>
</section>
