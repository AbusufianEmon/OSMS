<?php
session_start();

// Get success/error messages
$success_msg = isset($_SESSION['contact_success']) ? $_SESSION['contact_success'] : '';
$error_msg = isset($_SESSION['contact_error']) ? $_SESSION['contact_error'] : '';

// Clear messages after reading
unset($_SESSION['contact_success']);
unset($_SESSION['contact_error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>OSMS - Electronics Service Management</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap" rel="stylesheet">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Inter', sans-serif;
      color: #1a1a1a;
      overflow-x: hidden;
    }

    /* Navigation */
    nav {
      position: fixed;
      width: 100%;
      top: 0;
      z-index: 1000;
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(10px);
      box-shadow: 0 2px 20px rgba(0,0,0,0.05);
      transition: all 0.3s ease;
    }

    nav.scrolled {
      background: rgba(255, 255, 255, 0.98);
      box-shadow: 0 2px 30px rgba(0,0,0,0.1);
    }

    .nav-container {
      max-width: 1400px;
      margin: 0 auto;
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 1.2rem 3rem;
    }

    .logo {
      font-size: 1.8rem;
      font-weight: 800;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    .nav-links {
      display: flex;
      gap: 2.5rem;
      list-style: none;
    }

    .nav-links a {
      text-decoration: none;
      color: #333;
      font-weight: 500;
      transition: color 0.3s ease;
      position: relative;
    }

    .nav-links a:hover {
      color: #667eea;
    }

    .nav-links a::after {
      content: '';
      position: absolute;
      bottom: -5px;
      left: 0;
      width: 0;
      height: 2px;
      background: #667eea;
      transition: width 0.3s ease;
    }

    .nav-links a:hover::after {
      width: 100%;
    }

    .nav-cta {
      display: flex;
      gap: 1rem;
    }

    .btn {
      padding: 0.7rem 1.8rem;
      border-radius: 50px;
      text-decoration: none;
      font-weight: 600;
      transition: all 0.3s ease;
      border: none;
      cursor: pointer;
      font-size: 0.95rem;
    }

    .btn-primary {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    }

    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
    }

    .btn-outline {
      background: transparent;
      color: #667eea;
      border: 2px solid #667eea;
    }

    .btn-outline:hover {
      background: #667eea;
      color: white;
    }

    /* Hero Section */
    .hero {
      min-height: 100vh;
      display: flex;
      align-items: center;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      position: relative;
      overflow: hidden;
      padding-top: 80px;
    }

    .hero::before {
      content: '';
      position: absolute;
      width: 150%;
      height: 150%;
      background: radial-gradient(circle, rgba(255,255,255,0.1) 1px, transparent 1px);
      background-size: 50px 50px;
      animation: moveBackground 20s linear infinite;
    }

    @keyframes moveBackground {
      0% { transform: translate(0, 0); }
      100% { transform: translate(50px, 50px); }
    }

    .hero-container {
      max-width: 1400px;
      margin: 0 auto;
      padding: 0 3rem;
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 4rem;
      align-items: center;
      position: relative;
      z-index: 1;
    }

    .hero-content h1 {
      font-size: 3.5rem;
      font-weight: 800;
      color: white;
      line-height: 1.2;
      margin-bottom: 1.5rem;
    }

    .hero-content p {
      font-size: 1.2rem;
      color: rgba(255,255,255,0.9);
      margin-bottom: 2.5rem;
      line-height: 1.8;
    }

    .hero-buttons {
      display: flex;
      gap: 1.5rem;
    }

    .btn-hero {
      padding: 1rem 2.5rem;
      font-size: 1.1rem;
    }

    .btn-white {
      background: white;
      color: #667eea;
    }

    .btn-white:hover {
      transform: translateY(-3px);
      box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    }

    .hero-image {
      position: relative;
      animation: float 6s ease-in-out infinite;
    }

    @keyframes float {
      0%, 100% { transform: translateY(0px); }
      50% { transform: translateY(-20px); }
    }

    .hero-image img {
      width: 100%;
      filter: drop-shadow(0 20px 40px rgba(0,0,0,0.3));
    }

    /* About Section */
    .about {
      padding: 8rem 3rem;
      background: white;
    }

    .about-container {
      max-width: 1200px;
      margin: 0 auto;
      text-align: center;
    }

    .section-title {
      font-size: 2.8rem;
      font-weight: 800;
      margin-bottom: 1.5rem;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    .section-subtitle {
      font-size: 1.2rem;
      color: #666;
      margin-bottom: 3rem;
      line-height: 1.8;
      max-width: 800px;
      margin-left: auto;
      margin-right: auto;
    }

    /* Services Section */
    .services {
      padding: 8rem 3rem;
      background: linear-gradient(180deg, #f8f9ff 0%, #ffffff 100%);
    }

    .services-container {
      max-width: 1400px;
      margin: 0 auto;
    }

    .services-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
      gap: 2.5rem;
      margin-top: 4rem;
    }

    .service-card {
      background: white;
      padding: 3rem 2rem;
      border-radius: 20px;
      text-align: center;
      transition: all 0.3s ease;
      box-shadow: 0 10px 30px rgba(0,0,0,0.05);
      border: 1px solid rgba(102, 126, 234, 0.1);
    }

    .service-card:hover {
      transform: translateY(-10px);
      box-shadow: 0 20px 40px rgba(102, 126, 234, 0.2);
    }

    .service-icon {
      font-size: 4rem;
      margin-bottom: 1.5rem;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    .service-card h3 {
      font-size: 1.5rem;
      margin-bottom: 1rem;
      color: #1a1a1a;
    }

    .service-card p {
      color: #666;
      line-height: 1.8;
    }

    /* Testimonials */
    .testimonials {
      padding: 8rem 3rem;
      background: white;
    }

    .testimonials-container {
      max-width: 1400px;
      margin: 0 auto;
    }

    .testimonials-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 2rem;
      margin-top: 4rem;
    }

    .testimonial-card {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      padding: 2.5rem;
      border-radius: 20px;
      color: white;
      transition: transform 0.3s ease;
    }

    .testimonial-card:hover {
      transform: scale(1.05);
    }

    .testimonial-avatar {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      margin: 0 auto 1.5rem;
      border: 4px solid white;
      overflow: hidden;
    }

    .testimonial-avatar img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .testimonial-card h4 {
      font-size: 1.3rem;
      margin-bottom: 1rem;
    }

    .testimonial-card p {
      font-size: 0.95rem;
      opacity: 0.9;
      line-height: 1.6;
    }

    /* Contact Section */
    .contact {
      padding: 8rem 3rem;
      background: linear-gradient(180deg, #f8f9ff 0%, #ffffff 100%);
    }

    .contact-container {
      max-width: 1200px;
      margin: 0 auto;
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 4rem;
    }

    .contact-info {
      display: flex;
      flex-direction: column;
      gap: 2rem;
    }

    .info-card {
      background: white;
      padding: 2rem;
      border-radius: 15px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.05);
    }

    .info-card h4 {
      font-size: 1.3rem;
      margin-bottom: 1rem;
      color: #667eea;
    }

    .info-card p {
      color: #666;
      line-height: 1.8;
    }

    .contact-form {
      background: white;
      padding: 3rem;
      border-radius: 20px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.05);
    }

    .form-group {
      margin-bottom: 1.5rem;
    }

    .form-group label {
      display: block;
      margin-bottom: 0.5rem;
      font-weight: 600;
      color: #333;
    }

    .form-group input,
    .form-group textarea {
      width: 100%;
      padding: 1rem;
      border: 2px solid #e0e0e0;
      border-radius: 10px;
      font-family: 'Inter', sans-serif;
      transition: border-color 0.3s ease;
    }

    .form-group input:focus,
    .form-group textarea:focus {
      outline: none;
      border-color: #667eea;
    }

    .form-group textarea {
      resize: vertical;
      min-height: 120px;
    }

    /* Footer */
    footer {
      background: #1a1a1a;
      color: white;
      padding: 3rem 3rem 1.5rem;
    }

    .footer-container {
      max-width: 1400px;
      margin: 0 auto;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .social-links {
      display: flex;
      gap: 1.5rem;
    }

    .social-links a {
      color: white;
      font-size: 1.5rem;
      transition: color 0.3s ease;
    }

    .social-links a:hover {
      color: #667eea;
    }

    .footer-info {
      text-align: right;
    }

    .footer-info a {
      color: #667eea;
      text-decoration: none;
      margin-left: 1rem;
    }

    /* Alert Messages */
    .alert {
      padding: 1rem;
      border-radius: 10px;
      margin-bottom: 1.5rem;
      animation: slideIn 0.3s ease;
    }

    .alert-success {
      background-color: #d4edda;
      color: #155724;
      border: 1px solid #c3e6cb;
    }

    .alert-error {
      background-color: #f8d7da;
      color: #721c24;
      border: 1px solid #f5c6cb;
    }

    @keyframes slideIn {
      from {
        opacity: 0;
        transform: translateY(-10px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* Responsive */
    @media (max-width: 968px) {
      .hero-container {
        grid-template-columns: 1fr;
        text-align: center;
      }

      .hero-content h1 {
        font-size: 2.5rem;
      }

      .hero-buttons {
        justify-content: center;
      }

      .nav-links {
        display: none;
      }

      .contact-container {
        grid-template-columns: 1fr;
      }

      .footer-container {
        flex-direction: column;
        gap: 2rem;
        text-align: center;
      }

      .footer-info {
        text-align: center;
      }
    }
  </style>
</head>
<body>
  <!-- Navigation -->
  <nav id="navbar">
    <div class="nav-container">
      <div class="logo">OSMS</div>
      <ul class="nav-links">
        <li><a href="#home">Home</a></li>
        <li><a href="#about">About</a></li>
        <li><a href="#services">Services</a></li>
        <li><a href="#testimonials">Reviews</a></li>
        <li><a href="#contact">Contact</a></li>
      </ul>
      <div class="nav-cta">
        <a href="Requester/RequesterLogin.php" class="btn btn-outline">Login</a>
        <a href="UserRegistration.php" class="btn btn-primary">Sign Up</a>
      </div>
    </div>
  </nav>

  <!-- Hero Section -->
  <section class="hero" id="home">
    <div class="hero-container">
      <div class="hero-content">
        <h1>Professional Electronics Service Management</h1>
        <p>Bangladesh's leading electronics service provider. We bring expertise, reliability, and customer satisfaction to every repair.</p>
        <div class="hero-buttons">
          <a href="UserRegistration.php" class="btn btn-white btn-hero">Get Started</a>
          <a href="#services" class="btn btn-outline btn-hero" style="border-color: white; color: white;">Our Services</a>
        </div>
      </div>
      <div class="hero-image">
        <svg viewBox="0 0 500 500" xmlns="http://www.w3.org/2000/svg">
          <defs>
            <linearGradient id="grad1" x1="0%" y1="0%" x2="100%" y2="100%">
              <stop offset="0%" style="stop-color:rgba(255,255,255,0.3);stop-opacity:1" />
              <stop offset="100%" style="stop-color:rgba(255,255,255,0.1);stop-opacity:1" />
            </linearGradient>
          </defs>
          <rect x="100" y="100" width="300" height="200" rx="20" fill="url(#grad1)" />
          <circle cx="250" cy="200" r="40" fill="white" opacity="0.5" />
          <rect x="150" y="320" width="200" height="40" rx="10" fill="white" opacity="0.4" />
        </svg>
      </div>
    </div>
  </section>

  <!-- About Section -->
  <section class="about" id="about">
    <div class="about-container">
      <h2 class="section-title">About OSMS</h2>
      <p class="section-subtitle">
        OSMS Services is Bangladesh's leading chain of multi-brand electronics and electrical service workshops. 
        We focus on enhancing your experience by offering world-class electronic appliance maintenance services. 
        Our mission is to provide exceptional care services that keep your devices in perfect condition and our customers happy and satisfied.
      </p>
      <p class="section-subtitle">
        With state-of-the-art service centers and fully trained technicians across multiple cities, 
        we provide quality services with excellent packages designed to offer you great value. 
        Book your service online today through our convenient registration system.
      </p>
    </div>
  </section>

  <!-- Services Section -->
  <section class="services" id="services">
    <div class="services-container">
      <h2 class="section-title">Our Services</h2>
      <p class="section-subtitle">Comprehensive electronic appliance solutions tailored to your needs</p>
      
      <div class="services-grid">
        <div class="service-card">
          <div class="service-icon">📺</div>
          <h3>Electronic Appliances</h3>
          <p>Expert repair and maintenance for all your electronic devices including TVs, refrigerators, washing machines, and more.</p>
        </div>
        
        <div class="service-card">
          <div class="service-icon">🔧</div>
          <h3>Preventive Maintenance</h3>
          <p>Regular check-ups and maintenance services to prevent issues and extend the life of your appliances.</p>
        </div>
        
        <div class="service-card">
          <div class="service-icon">⚙️</div>
          <h3>Fault Repair</h3>
          <p>Quick and efficient diagnosis and repair of all types of electronic appliance faults and malfunctions.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Testimonials Section -->
  <section class="testimonials" id="testimonials">
    <div class="testimonials-container">
      <h2 class="section-title">What Our Customers Say</h2>
      <p class="section-subtitle">Customer satisfaction is our top priority</p>
      
      <div class="testimonials-grid">
        <div class="testimonial-card">
          <div class="testimonial-avatar">
            <img src="images/putin.png" alt="Customer">
          </div>
          <h4>Vladimir Putin</h4>
          <p>OSMS provided excellent service and quality repairs. Highly recommended!</p>
        </div>
        
        <div class="testimonial-card">
          <div class="testimonial-avatar">
            <img src="images/kim.png" alt="Customer">
          </div>
          <h4>Kim Jong Un</h4>
          <p>Professional team and outstanding results. My appliances work like new!</p>
        </div>
        
        <div class="testimonial-card">
          <div class="testimonial-avatar">
            <img src="images/trump.png" alt="Customer">
          </div>
          <h4>Donald Trump</h4>
          <p>Fast, reliable, and affordable. OSMS is simply the best in the business!</p>
        </div>
        
        <div class="testimonial-card">
          <div class="testimonial-avatar">
            <img src="images/Joffrey.png" alt="Customer">
          </div>
          <h4>Joffrey Baratheon</h4>
          <p>Exceptional service quality. They truly care about customer satisfaction!</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Contact Section -->
  <section class="contact" id="contact">
    <div class="contact-container">
      <div class="contact-info">
        <h2 class="section-title" style="text-align: left;">Get in Touch</h2>
        
        <div class="info-card">
          <h4>📍 Headquarters</h4>
          <p>
            OSMS Pvt Ltd<br>
            Temuki, Sylhet<br>
            Phone: 01640027997<br>
            <a href="#" style="color: #667eea;">www.osmspvt.com.bd</a>
          </p>
        </div>
        
        <div class="info-card">
          <h4>📍 Dhaka Branch</h4>
          <p>
            OSMS Pvt Ltd<br>
            Gulshan, Dhaka<br>
            Phone: 01640027997<br>
            <a href="#" style="color: #667eea;">www.osmspvt.com.bd</a>
          </p>
        </div>
      </div>
      
      <div class="contact-form">
        <h3 style="margin-bottom: 2rem;">Send us a Message</h3>
        
        <!-- Success/Error Messages -->
        <?php if (!empty($success_msg)): ?>
          <div class="alert alert-success">
            <?php echo htmlspecialchars($success_msg); ?>
          </div>
        <?php endif; ?>
        
        <?php if (!empty($error_msg)): ?>
          <div class="alert alert-error">
            <?php echo $error_msg; ?>
          </div>
        <?php endif; ?>
        
        <form action="contactform.php" method="POST" id="contactForm">
          <div class="form-group">
            <label>Name</label>
            <input type="text" name="name" required>
          </div>
          
          <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" required>
          </div>
          
          <div class="form-group">
            <label>Message</label>
            <textarea name="message" required></textarea>
          </div>
          
          <button type="submit" class="btn btn-primary" style="width: 100%; padding: 1rem;">Send Message</button>
        </form>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer>
    <div class="footer-container">
      <div class="social-links">
        <span style="margin-right: 1rem;">Follow Us:</span>
        <a href="#" target="_blank">📘</a>
        <a href="#" target="_blank">📷</a>
        <a href="#" target="_blank">💼</a>
      </div>
      
      <div class="footer-info">
        <small>© 2025 OSMS. Designed by Emon</small>
        <a href="Admin/login.php">Admin Login</a>
        <a href="Technician/TechnicianLogin.php">Technician Login</a>
      </div>
    </div>
  </footer>

  <script>
    // Navbar scroll effect
    window.addEventListener('scroll', function() {
      const navbar = document.getElementById('navbar');
      if (window.scrollY > 50) {
        navbar.classList.add('scrolled');
      } else {
        navbar.classList.remove('scrolled');
      }
    });

    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
          target.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
          });
        }
      });
    });

    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
      const alerts = document.querySelectorAll('.alert');
      alerts.forEach(function(alert) {
        alert.style.transition = 'opacity 0.5s';
        alert.style.opacity = '0';
        setTimeout(function() {
          alert.remove();
        }, 500);
      });
    }, 5000);
  </script>
</body>
</html>