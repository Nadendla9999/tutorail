<?php
session_start();

// Redirect to dashboard if already logged in
if(isset($_SESSION['user_id'])) {
    switch($_SESSION['role']) {
        case 'admin':
            header("Location: admin/dashboard.php");
            break;
        case 'manager':
            header("Location: manager/dashboard.php");
            break;
        case 'user':
            header("Location: user/dashboard.php");
            break;
    }
    exit();
}

$title = "Event Manager - Home";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Manager</title>

    <!-- Bootstrap CSS from CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons from CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    <!-- Popper.js from CDN (needed for Bootstrap tooltips, popovers, etc.) -->

    <!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

<!-- Your custom CSS -->
<link href="/assets/css/style.css" rel="stylesheet">
</head>

<body>

<!-- Custom CSS for spectacular effects -->
<style>
    /* Particle animation */
    .particles-js {
        position: absolute;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        z-index: 1;
    }
    
    /* Hero section styling */
    .hero-section {
        position: relative;
        background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.7)), url('https://cdnjs.cloudflare.com/ajax/libs/placeholder-images/1.2.2/images/banner1.jpg');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        padding: 150px 0;
        margin-bottom: 50px;
        overflow: hidden;
        z-index: 1;
    }
    
    .hero-content {
        position: relative;
        z-index: 2;
    }
    
    /* Animated text with gradient */
    .animated-heading {
        background: linear-gradient(90deg, #ff6b6b, #4e73df, #1cc88a);
        background-size: 200% auto;
        background-clip: text;
        -webkit-background-clip: text;
        color: transparent;
        animation: shine 3s linear infinite;
    }
    
    @keyframes shine {
        to {
            background-position: 200% center;
        }
    }
    
    /* Glowing button effect */
    .btn-glow {
        position: relative;
        z-index: 1;
        overflow: hidden;
        transition: all 0.5s ease;
    }
    
    .btn-glow:before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.1);
        z-index: -1;
        transform: translateX(-100%);
        transition: all 0.5s ease;
    }
    
    .btn-glow:hover:before {
        transform: translateX(0);
    }
    
    .btn-glow:after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: -2;
        border-radius: inherit;
        box-shadow: 0 0 20px rgba(255, 255, 255, 0.5);
        opacity: 0;
        transition: all 0.5s ease;
    }
    
    .btn-glow:hover:after {
        opacity: 1;
    }
    
    /* Feature cards */
    .feature-card {
        border: none;
        border-radius: 15px;
        overflow: hidden;
        transition: all 0.5s ease;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }
    
    .feature-card:hover {
        transform: translateY(-15px);
        box-shadow: 0 15px 35px rgba(78, 115, 223, 0.3);
    }
    
    .icon-wrapper {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        position: relative;
        transition: all 0.5s ease;
    }
    
    .icon-wrapper:before {
        content: '';
        position: absolute;
        top: -5px;
        left: -5px;
        right: -5px;
        bottom: -5px;
        border-radius: 50%;
        background: linear-gradient(90deg, #4e73df, #1cc88a, #4e73df);
        background-size: 200% auto;
        z-index: -1;
        animation: rotate 3s linear infinite;
    }
    
    @keyframes rotate {
        to {
            transform: rotate(360deg);
        }
    }
    
    .feature-card:hover .icon-wrapper {
        transform: scale(1.1);
    }
    
    .feature-icon {
        font-size: 2.5rem;
        color: white;
    }
    
    /* Event counter section */
    .counter-section {
        padding: 80px 0;
        background: linear-gradient(135deg, #f8f9fc 0%, #e8eaf6 100%);
    }
    
    .counter-box {
        text-align: center;
        padding: 30px 20px;
        border-radius: 10px;
        background-color: white;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
        transition: all 0.5s ease;
    }
    
    .counter-box:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 30px rgba(78, 115, 223, 0.2);
    }
    
    .counter-number {
        font-size: 3rem;
        font-weight: 700;
        background: linear-gradient(90deg, #4e73df, #224abe);
        background-clip: text;
        -webkit-background-clip: text;
        color: transparent;
    }
    
    /* Testimonial section */
    .testimonial-section {
        background-color: #f8f9fc;
        padding: 80px 0;
    }
    
    .testimonial-card {
        background-color: white;
        border-radius: 15px;
        padding: 30px;
        margin: 20px 10px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
        position: relative;
        transition: all 0.5s ease;
    }
    
    .testimonial-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 30px rgba(78, 115, 223, 0.2);
    }
    
    .testimonial-card:before {
        content: '\201C';
        position: absolute;
        top: 20px;
        left: 20px;
        font-size: 5rem;
        color: rgba(78, 115, 223, 0.1);
        font-family: Georgia, serif;
    }
    
    /* Call to action section */
    .cta-section {
        padding: 100px 0;
        background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('https://cdnjs.cloudflare.com/ajax/libs/placeholder-images/1.2.2/images/banner2.jpg');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        position: relative;
        overflow: hidden;
    }
    
    .cta-content {
        position: relative;
        z-index: 2;
    }
    
    /* Floating animation */
    @keyframes float {
        0% { transform: translateY(0px); }
        50% { transform: translateY(-15px); }
        100% { transform: translateY(0px); }
    }
    
    .float-animation {
        animation: float 4s ease-in-out infinite;
    }
    
    /* Sparkle effect */
    .sparkle {
        position: absolute;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background-color: white;
        opacity: 0;
        pointer-events: none;
    }
    
    @keyframes sparkle {
        0% { transform: scale(0); opacity: 0; }
        50% { transform: scale(1); opacity: 0.8; }
        100% { transform: scale(0); opacity: 0; }
    }
</style>

<!-- Particles.js container for background animation -->
<div id="particles-js" class="particles-js"></div>

<!-- Hero Section with animated elements -->
<div class="hero-section">
    <div class="container hero-content text-center" data-aos="fade-up">
        <h1 class="display-3 fw-bold mb-4 animated-heading">Welcome to Event Manager</h1>
        <p class="lead fs-3 mb-5">Transform your special occasions into unforgettable memories</p>
        <div class="mt-5">
            <a href="register.php" class="btn btn-light btn-lg mx-2 px-4 py-3 btn-glow">
                <i class="bi bi-person-plus me-2"></i>Register Now
            </a>
            <a href="login.php" class="btn btn-outline-light btn-lg mx-2 px-4 py-3 btn-glow">
                <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
            </a>
        </div>
    </div>
</div>

<!-- Services/Features Section -->
<div class="container my-5">
    <div class="text-center mb-5" data-aos="fade-up">
        <h2 class="fw-bold">Our Premium Services</h2>
        <p class="lead text-muted">Everything you need for your perfect event in one place</p>
    </div>
    
    <div class="row g-4">
        <div class="col-lg-3 col-md-6">
            <div class="feature-card h-100" data-aos="zoom-in" data-aos-delay="100">
                <div class="card-body text-center p-4">
                    <div class="icon-wrapper mb-4">
                        <i class="bi bi-building feature-icon"></i>
                    </div>
                    <h3 class="card-title">Function Halls</h3>
                    <p class="card-text">Elegant and spacious venues for your events with modern amenities.</p>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
            <div class="feature-card h-100" data-aos="zoom-in" data-aos-delay="200">
                <div class="card-body text-center p-4">
                    <div class="icon-wrapper mb-4">
                        <i class="bi bi-cup-hot feature-icon"></i>
                    </div>
                    <h3 class="card-title">Catering</h3>
                    <p class="card-text">Exquisite cuisine options to delight your guests with various cuisines.</p>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
            <div class="feature-card h-100" data-aos="zoom-in" data-aos-delay="300">
                <div class="card-body text-center p-4">
                    <div class="icon-wrapper mb-4">
                        <i class="bi bi-stars feature-icon"></i>
                    </div>
                    <h3 class="card-title">Decorations</h3>
                    <p class="card-text">Beautiful decorations that transform your venue into a magical space.</p>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
            <div class="feature-card h-100" data-aos="zoom-in" data-aos-delay="400">
                <div class="card-body text-center p-4">
                    <div class="icon-wrapper mb-4">
                        <i class="bi bi-music-note-beamed feature-icon"></i>
                    </div>
                    <h3 class="card-title">DJ Sets</h3>
                    <p class="card-text">Professional DJs with top-notch equipment to keep the party going.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Event Counter Section -->
<div class="counter-section">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-3 col-6">
                <div class="counter-box" data-aos="fade-up" data-aos-delay="100">
                    <div class="counter-number" data-count="150">0</div>
                    <h4>Events Completed</h4>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="counter-box" data-aos="fade-up" data-aos-delay="200">
                    <div class="counter-number" data-count="25">0</div>
                    <h4>Venues Available</h4>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="counter-box" data-aos="fade-up" data-aos-delay="300">
                    <div class="counter-number" data-count="15">0</div>
                    <h4>Professional DJs</h4>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="counter-box" data-aos="fade-up" data-aos-delay="400">
                    <div class="counter-number" data-count="98">0</div>
                    <h4>Happy Clients</h4>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- How It Works Section -->
<div class="container my-5 py-5">
    <div class="text-center mb-5" data-aos="fade-up">
        <h2 class="fw-bold">How It Works</h2>
        <p class="lead text-muted">Simple steps to plan your perfect event</p>
    </div>
    
    <div class="row g-4">
        <div class="col-lg-3 col-md-6">
            <div class="card h-100 border-0 shadow-sm" data-aos="fade-right" data-aos-delay="100">
                <div class="card-body text-center p-4">
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-4 float-animation" style="width: 80px; height: 80px; font-size: 2rem; font-weight: bold;">1</div>
                    <h4>Register Account</h4>
                    <p class="text-muted">Create your account to access all features.</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card h-100 border-0 shadow-sm" data-aos="fade-right" data-aos-delay="200">
                <div class="card-body text-center p-4">
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-4 float-animation" style="width: 80px; height: 80px; font-size: 2rem; font-weight: bold;">2</div>
                    <h4>Create Event</h4>
                    <p class="text-muted">Define your event details and requirements.</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card h-100 border-0 shadow-sm" data-aos="fade-right" data-aos-delay="300">
                <div class="card-body text-center p-4">
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-4 float-animation" style="width: 80px; height: 80px; font-size: 2rem; font-weight: bold;">3</div>
                    <h4>Select Services</h4>
                    <p class="text-muted">Choose the perfect venue, catering, and more.</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card h-100 border-0 shadow-sm" data-aos="fade-right" data-aos-delay="400">
                <div class="card-body text-center p-4">
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-4 float-animation" style="width: 80px; height: 80px; font-size: 2rem; font-weight: bold;">4</div>
                    <h4>Enjoy Event</h4>
                    <p class="text-muted">Relax and enjoy your perfectly managed event.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Testimonials Section -->
<div class="testimonial-section">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="fw-bold">What Our Clients Say</h2>
            <p class="lead text-muted">Hear from our satisfied customers</p>
        </div>
        
        <div class="row">
            <div class="col-md-4">
                <div class="testimonial-card" data-aos="fade-up" data-aos-delay="100">
                    <p class="mb-4">"The event management team did an exceptional job with my wedding. Everything was perfect and exceeded my expectations!"</p>
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                            <i class="bi bi-person-fill"></i>
                        </div>
                        <div>
                            <h5 class="mb-0">Raghava</h5>
                            <small class="text-muted">Wedding Event</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="testimonial-card" data-aos="fade-up" data-aos-delay="200">
                    <p class="mb-4">"The attention to detail was impressive. Our corporate event was managed professionally from start to finish."</p>
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                            <i class="bi bi-person-fill"></i>
                        </div>
                        <div>
                            <h5 class="mb-0">Manoj</h5>
                            <small class="text-muted">Corporate Event</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="testimonial-card" data-aos="fade-up" data-aos-delay="300">
                    <p class="mb-4">"I was amazed by how easy it was to plan my birthday party. The venue, decorations, and catering were all perfect!"</p>
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                            <i class="bi bi-person-fill"></i>
                        </div>
                        <div>
                            <h5 class="mb-0">Srikanth</h5>
                            <small class="text-muted">Birthday Celebration</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Call to Action Section -->
<div class="cta-section">
    <div class="container cta-content text-center text-white">
        <h2 class="display-4 fw-bold mb-4" data-aos="fade-up">Ready to Plan Your Event?</h2>
        <p class="lead fs-4 mb-5" data-aos="fade-up" data-aos-delay="100">Join us today and experience the difference of professional event management.</p>
        <a href="register.php" class="btn btn-primary btn-lg px-5 py-3 btn-glow" data-aos="fade-up" data-aos-delay="200">
            <i class="bi bi-calendar-event me-2"></i>Get Started Now
        </a>
    </div>
</div>

<!-- JavaScript for animations and effects -->
<script>
  
    // Animated counters
    document.addEventListener('DOMContentLoaded', function() {
        const counters = document.querySelectorAll('.counter-number');
        const speed = 200;
        
        const animateCounters = () => {
            counters.forEach(counter => {
                const target = +counter.getAttribute('data-count');
                const count = +counter.innerText;
                const increment = target / speed;
                
                if (count < target) {
                    counter.innerText = Math.ceil(count + increment);
                    setTimeout(animateCounters, 1);
                } else {
                    counter.innerText = target;
                }
            });
        };
        
        // Start animation when elements come into view
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    animateCounters();
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });
        
        counters.forEach(counter => {
            observer.observe(counter.parentElement);
        });
    });

    // Sparkle effect on hover
    document.addEventListener('DOMContentLoaded', function() {
        const heroSection = document.querySelector('.hero-section');
        
        heroSection.addEventListener('mousemove', function(e) {
            const sparkle = document.createElement('div');
            sparkle.classList.add('sparkle');
            sparkle.style.left = `${e.pageX}px`;
            sparkle.style.top = `${e.pageY}px`;
            document.body.appendChild(sparkle);
            
            sparkle.style.animation = `sparkle ${Math.random() * 1 + 0.5}s ease-in-out`;
            
            sparkle.addEventListener('animationend', function() {
                sparkle.remove();
            });
        });
    });
    
    // Float animation for specific elements
    document.addEventListener('DOMContentLoaded', function() {
        const floatElements = document.querySelectorAll('.float-animation');
        
        floatElements.forEach(element => {
            element.style.animation = `float ${Math.random() * 2 + 3}s ease-in-out infinite`;
            element.style.animationDelay = `${Math.random() * 2}s`;
        });
    });
</script>

<?php require_once 'includes/footer.php'; ?>