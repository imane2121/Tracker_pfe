<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <title>{{ config('app.name', 'AquaScan') }}</title>
  <meta content="" name="description">
  <meta content="" name="keywords">
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

  <!-- Favicons -->
  <link href="{{ asset('/assets/img/favicon.ico') }}" rel="icon">
  <link href="{{ asset('/assets/img/apple-touch-icon.png') }}" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Raleway:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="{{ asset('/assets/vendor/aos/aos.css') }}" rel="stylesheet">
  <link href="{{ asset('/assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('/assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <link href="{{ asset('/assets/vendor/glightbox/css/glightbox.min.css') }}" rel="stylesheet">
  <link href="{{ asset('/assets/vendor/swiper/swiper-bundle.min.css') }}" rel="stylesheet">

  <!-- Leaflet CSS -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>

  <!-- Template Main CSS File -->
  <link href="{{ asset('/assets/css/main.css') }}" rel="stylesheet">
  
  <!-- Waste Signal Form CSS -->
  <link href="{{ asset('/assets/css/waste-signal-form.css') }}" rel="stylesheet">

  <!-- Your main CSS -->
  <link href="{{ asset('css/app.css') }}" rel="stylesheet">
  
  <!-- Stack for additional styles -->
  @stack('styles')

  <!-- =======================================================
  * Template Name: Squadfree
  * Updated: Aug 07 2024 with Bootstrap v5.3.3
  * Template URL: https://bootstrapmade.com/squadfree-free-bootstrap-template-creative/
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
</head>

<body>
  <!-- ======= Header ======= -->
  <header id="header" class="header d-flex align-items-center fixed-top">
    <div class="container-fluid container-xl position-relative d-flex align-items-center justify-content-between">
      <a href="{{ route('overview') }}" class="logo d-flex align-items-center">
        <img src="{{ asset('/assets/img/apple-touch-icon.png') }}" alt="" class="logo-img">
        <span class="logo-text">AquaScan</span>
      </a>

      <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
      
      <nav id="navmenu" class="navmenu">
        <ul>
          <li><a href="{{ route('collecte.index') }}">Collections</a></li>
          <li><a href="{{ route('signal.create') }}">Report</a></li>
          @auth
            <li><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li><a href="{{ route('profile.show') }}"><i class="bi bi-person-circle"></i> Profile</a></li>
            <li>
              <form method="POST" action="{{ route('logout') }}" class="d-inline">
                @csrf
                <a href="#" onclick="event.preventDefault(); this.closest('form').submit();" class="nav-link">
                  <i class="bi bi-box-arrow-right"></i>Logout 
                </a>
              </form>
            </li>
          @else
            <li><a href="{{ route('login') }}">Sign In</a></li>
          @endauth
          @if(!auth()->check() || !auth()->user()->isAdmin())
            <li><a href="{{ route('contact.index') }}">Contact</a></li>
          @endif
          @auth
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('messaging.*') ? 'active' : '' }}" 
                   href="{{ route('messaging.index') }}">
                    <i class="bi bi-chat-dots"></i> Messages
                </a>
            </li>
          @endauth
        </ul>
      </nav>
    </div>
  </header>

  <style>
    /* Header and Logo Responsive Styles */
    .header {
      padding: 15px 0;
      transition: all 0.3s ease;
      background-color: rgba(14, 52, 106, 0.95);
    }

    .logo {
      text-decoration: none;
      gap: 10px;
    }

    .logo-img {
      height: 40px;
      width: auto;
      transition: all 0.3s ease;
    }

    .logo-text {
      font-size: 1.5rem;
      font-weight: 700;
      color: #fff;
      transition: all 0.3s ease;
    }

    .navmenu {
      width: auto;
      margin-left: auto;
    }

    .navmenu ul {
      margin: 0;
      padding: 0;
      list-style: none;
      display: flex;
      gap: 20px;
    }
    .fixed-top{
      position: relative;
    }

    .navmenu a {
      color: #fff !important;
      text-decoration: none;
      font-size: 1rem;
      font-weight: 500;
      transition: all 0.3s ease;
      padding: 8px 15px;
      border-radius: 5px;
    }

    .navmenu a:hover {
      color: #f8f9fa;
      background-color: rgba(255, 255, 255, 0.1);
    }

    /* Logout button styles */
    .navmenu form {
      margin: 0;
      padding: 0;
    }

    .navmenu .nav-link {
      display: flex;
      align-items: center;
      gap: 0.1rem !important;
      color: #fff !important;
      padding: 8px 15px;
      border-radius: 5px;
      transition: all 0.3s ease;
    }

    .navmenu .nav-link:hover {
      color: #f8f9fa !important;
      background-color: rgba(255, 255, 255, 0.1);
      text-decoration: none;
    }

    .navmenu .nav-link i {
      font-size: 1.1rem;
    }

    /* Mobile Navigation Toggle */
    .mobile-nav-toggle {
      display: none;
      color: #fff;
      font-size: 28px;
      cursor: pointer;
      line-height: 0;
      transition: 0.5s;
      position: absolute;
      right: 15px;
      top: 50%;
      transform: translateY(-50%);
    }

    

    
    /* Mobile Responsive Styles */
    @media (max-width: 991px) {
      .header {
        padding: 10px 0;
      }

      .logo-img {
        height: 35px;
      }

      .logo-text {
        font-size: 1.25rem;
      }

      .mobile-nav-toggle {
        display: block;
      }

      .navmenu {
        position: fixed;
        top: 0;
        right: -100%;
        width: 80%;
        max-width: 300px;
        height: 100vh;
        background: rgba(14, 52, 106, 0.95);
        padding: 60px 20px;
        transition: all 0.3s ease;
        z-index: 1000;
      }

      .navmenu.active {
        right: 0;
      }

      .navmenu ul {
        flex-direction: column;
        gap: 15px;
      }

      .navmenu a {
        font-size: 1.1rem;
        display: block;
        padding: 12px 20px;
      }

      .mobile-nav-toggle.active {
        position: fixed;
        right: 15px;
        top: 15px;
        z-index: 1001;
      }
    }

    @media (max-width: 576px) {
      .logo-img {
        height: 30px;
      }

      .logo-text {
        font-size: 1.1rem;
      }

      .header {
        padding: 8px 10px;
      }

      .navmenu {
        width: 100%;
        max-width: none;
      }

      
  </style>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const mobileNavToggle = document.querySelector('.mobile-nav-toggle');
      const navMenu = document.querySelector('.navmenu');
      
      if (mobileNavToggle && navMenu) {
        mobileNavToggle.addEventListener('click', function() {
          this.classList.toggle('active');
          navMenu.classList.toggle('active');
        });
      }
    });
  </script>

<!-- Page Title -->
   <!-- <div class="page-title accent-background">-->
      <div class="container position-relative">
        <nav class="breadcrumbs">
        </nav>
      <!--</div>-->
    </div><!-- End Page Title -->
  <!-- Main Content -->
  <main class="main">
    <!-- Add this right after your navbar or at the top of your main content area -->
    <div class="container mt-3">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    </div>
    @yield('content')
  </main>

  <!-- Footer -->
  <footer id="footer" class="footer dark-background">
    <div class="container footer-top">
      <div class="row gy-4">
        <div class="col-lg-4 col-md-6 footer-about">
          <a href="{{ route('overview') }}" class="logo d-flex align-items-center">
            <span class="sitename">AquaScan</span>
          </a>
          <div class="footer-contact pt-3">
            <p>A012 ABD Street</p>
            <p>City, AB 012345</p>
            <p class="mt-3"><strong>Phone:</strong> <span>+0123456789</span></p>
            <p><strong>Email:</strong> <span>info@AquaScan.com</span></p>
          </div>
          <div class="social-links d-flex mt-4">
            <a href=""><i class="bi bi-twitter-x"></i></a>
            <a href=""><i class="bi bi-facebook"></i></a>
            <a href=""><i class="bi bi-instagram"></i></a>
            <a href=""><i class="bi bi-linkedin"></i></a>
          </div>
        </div>

      

        <div class="col-lg-4 col-md-6 footer-newsletter">
          <h4>Our Newsletter</h4>
          <p>Subscribe to our newsletter to receive updates and news.</p>
          <form action="" method="post">
            <input type="email" name="email"><input type="submit" value="Subscribe">
          </form>
        </div>
      </div>
    </div>

    <div class="container copyright text-center mt-4">
      <p>&copy; <span>Copyright</span> <strong class="px-1">AquaScan</strong> <span>All Rights Reserved</span></p>
      <div class="credits">
        <!-- All the links in the footer should remain intact. -->
        <!-- You can delete the links only if you purchased the pro version. -->
        <!-- Licensing information: https://bootstrapmade.com/license/ -->
        <!-- Purchase the pro version with working PHP/AJAX contact form: https://bootstrapmade.com/squadfree-free-bootstrap-template-creative/ -->
        Designed by <a href="https://bootstrapmade.com/">BootstrapMade</a>
      </div>
    </div>
  </footer>

  <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Preloader -->
  <div id="preloader"></div>

  <!-- Vendor JS Files -->
  <script src="{{ asset('/assets/vendor/aos/aos.js') }}"></script>
  <script src="{{ asset('/assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('/assets/vendor/glightbox/js/glightbox.min.js') }}"></script>
  <script src="{{ asset('/assets/vendor/isotope-layout/isotope.pkgd.min.js') }}"></script>
  <script src="{{ asset('/assets/vendor/swiper/swiper-bundle.min.js') }}"></script>
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
  <script src="https://unpkg.com/leaflet.heat@0.2.0/dist/leaflet-heat.js"></script>

  <!-- Template Main JS File -->
  <script src="{{ asset('/assets/js/main.js') }}"></script>

  <!-- Initialize Scripts -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Initialize AOS
      AOS.init({
        duration: 1000,
        easing: 'ease-in-out',
        once: true,
        mirror: false
      });

      // Initialize Swiper
      if (typeof Swiper !== 'undefined' && document.querySelector('.collectesSwiper')) {
        var collectesSwiper = new Swiper('.collectesSwiper', {
          slidesPerView: 1,
          spaceBetween: 30,
          pagination: {
            el: '.swiper-pagination',
            clickable: true
          },
          breakpoints: {
            640: {
              slidesPerView: 1,
            },
            768: {
              slidesPerView: 2,
            },
            1024: {
              slidesPerView: 3,
            }
          }
        });
      }

      // Initialize GLightbox
      if (typeof GLightbox !== 'undefined') {
        GLightbox({
          selector: '.glightbox'
        });
      }
    });
  </script>

  <!-- Scripts -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  
  <!-- Waste Signal Form JavaScript -->
  <script src="{{ asset('/assets/js/waste-signal-form.js') }}"></script>
  
  <!-- Add CSRF token to all AJAX requests -->
  <script>
      document.addEventListener('DOMContentLoaded', function() {
          // Add CSRF token to all fetch requests
          let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
          
          let originalFetch = window.fetch;
          window.fetch = function() {
              let args = Array.prototype.slice.call(arguments);
              if(args[1] && args[1].method && ['POST', 'PUT', 'DELETE'].includes(args[1].method.toUpperCase())) {
                  if(args[1].headers) {
                      args[1].headers['X-CSRF-TOKEN'] = token;
                  } else {
                      args[1].headers = {
                          'X-CSRF-TOKEN': token
                      };
                  }
              }
              return originalFetch.apply(window, args);
          };
      });
  </script>

  @push('scripts')
  <script>
      // Auto-dismiss alerts after 5 seconds
      document.addEventListener('DOMContentLoaded', function() {
          setTimeout(function() {
              const alerts = document.querySelectorAll('.alert');
              alerts.forEach(function(alert) {
                  const dismissButton = alert.querySelector('.btn-close');
                  if (dismissButton) {
                      dismissButton.click();
                  }
              });
          }, 5000);
      });
  </script>
  @endpush

  @stack('scripts')
</body>

</html>