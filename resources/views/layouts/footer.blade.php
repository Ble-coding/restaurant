<footer class="bg-dark text-white py-5">
    <div class="container">
        <div class="row">
            <!-- About Us -->
            <div class="col-md-4">
                <h5>{{ __('footer.about_title') }}</h5>
                <p><strong>Côte d'Ivoire Drinks & Foods</strong>, {{ __('footer.about_content') }}</p>
            </div>
    
            <!-- Contact Us -->
            <div class="col-md-4">
                <h5>{{ __('footer.contact_title') }}</h5>
                <p><strong>{{ __('footer.contact_phone') }}</strong> +44 7397 389224</p>
                <p><strong>{{ __('footer.contact_address') }}</strong> Deptford High Street</p>
                <p><strong>{{ __('footer.contact_email') }}</strong> <a href="mailto:sales@cifd.uk" class="text-white">sales@cifd.uk</a></p>
                <p><strong>{{ __('footer.contact_follow_us') }}</strong></p>
                <div class="mb-4 social-icons">
                    <a href="#" class="btn-custom-icon"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="btn-custom-icon"><i class="bi bi-instagram"></i></a>
                    <a href="#" class="btn-custom-icon"><i class="bi bi-twitter"></i></a>
                    <a href="#" class="btn-custom-icon"><i class="bi bi-behance"></i></a>
                </div>
            </div>
    
            <!-- Newsletter -->
            <div class="col-md-4">
                <h5>{{ __('footer.newsletter_title') }}</h5>
                <p>{{ __('footer.newsletter_content') }}</p>
                <form class="d-flex">
                    <input type="email" class="form-control form-custom me-2" placeholder="{{ __('footer.newsletter_placeholder') }}">
                    <button type="submit" class="btn btn-custom">
                        <img class="btn-arrow" src="{{ asset('assets/images/apropos/right_arrow_test.png') }}" width="30" height="30" alt="arrow_right" />
                    </button>
                </form>
            </div>
        </div>
    
        <hr class="bg-secondary">
    
        <div class="text-center">
            <p class="mb-0">{{ __('footer.opening_hours') }}</p>
            <p class="mb-0">{{ __('footer.opening_days') }}</p>
            <p class="mb-0">{{ __('footer.opening_sunday') }}</p>
            <p class="mt-3">{{ __('footer.copyright') }}</p>
        </div>
    </div>
    
</footer>
