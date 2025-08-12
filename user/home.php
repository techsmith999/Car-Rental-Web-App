<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DriveNow Car Rentals</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <!-- AOS Scroll Animations -->
  <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">

  <style>
    body {
      color:white;
  font-family: 'Poppins', sans-serif;
  margin: 0;
  background: url('images/home5.jpg') no-repeat center center fixed;
  background-size: cover;
  position: relative; /* Needed for overlay positioning */
}

body::before {
  content: "";
  position: fixed; /* Stays fixed even when scrolling */
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5); /* 0.5 = 50% dark, adjust as needed */
  z-index: -1; /* Keeps it behind everything */
}
    .navbar {
      background: rgba(0, 0, 0, 0.6);
      backdrop-filter: blur(10px);
    }
    /* Hero section */
#hero {
      height: 100vh;
      background: url('https://images.unsplash.com/photo-1552519507-da3b142c6e3d') no-repeat center center/cover;
      display: flex;
      align-items: center;
      justify-content: center;
      text-align: center;
      color: white;
    }
    #hero h1 {
      font-size: 3rem;
      font-weight: bold;
      background: linear-gradient(90deg, #ff6a00, #ee0979);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }
    #hero::before {
      content: '';
      position: absolute;
      top:0;
      left:0;
      width:100%;
      height:100%;
      background: rgba(0,0,0,0.5);
    }

   .py-5{
    backdrop-filter: blur(10px);
   }
    /* Glassy sections */
    .glassy {
      background: rgba(255, 255, 255, 0.05);
      backdrop-filter: blur(10px);
      border-radius: 10px;
      padding: 2rem;
    }
   .car-card {
  position: relative;
  overflow: hidden;
  border-radius: 10px;
  cursor: pointer;
}

.car-card img {
  width: 100%;
  height: 250px;
  object-fit: cover;
  border-radius: 10px;
  transition: transform 0.3s ease;
}

.car-card:hover img {
  transform: scale(1.05);
}

/* Glassy overlay */
.car-card::after {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(255, 255, 255, 0.08);
  backdrop-filter: blur(6px);
  opacity: 0;
  transition: opacity 0.3s ease;
  border-radius: 10px;
}

.car-card:hover::after {
  opacity: 1;
}

/* Text/Button on hover */
.car-card .hover-content {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  color:  rgba(20, 12, 254, 1);
  font-weight: bolder;
  text-align: center;
  opacity: 0;
  transition: opacity 0.3s ease;
  z-index: 1;
}

.car-card:hover .hover-content {
  opacity: 1;
}
    /* CTA strip */
    .cta-strip {
      background: url('https://images.unsplash.com/photo-1503736334956-4c8f8e92946d') center/cover no-repeat;
      position: relative;
      color: white;
      text-align: center;
      padding: 3rem 1rem;
    }
    .cta-strip::before {
      content: '';
      position: absolute;
      top:0;
      left:0;
      width:100%;
      height:100%;
      background: rgba(0,0,0,0.6);
    }
    .cta-strip-content {
      position: relative;
      z-index: 1;
    }

   /* Adjust glassy look for contact */
#contact .glassy {
  background: rgba(255, 255, 255, 0.08);
  backdrop-filter: blur(12px);
  border: 1px solid rgba(255, 255, 255, 0.1);
}

/* Transparent inputs */
#contact input,
#contact textarea {
  background: rgba(255, 255, 255, 0.05) !important;
  color: white;
}

#contact input::placeholder,
#contact textarea::placeholder {
  color: rgba(255, 255, 255, 0.7);
}

#contact label {
  color: #fff;
}

    .py-4{
      backdrop-filter:blur(20px);
      text-align: center;
    }
  .btn {
      font-weight: light;
      background: linear-gradient(90deg, #00c3ffff, #ee09eeff);
      -webkit-background-clip: ;
      -webkit-text-fill-color: ;
      
  }
      

  </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark fixed-top">
  <div class="container">
    <a class="navbar-brand" href="#">SAFARI-RENTALS</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navMenu">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="#fleet">Fleet</a></li>
        <li class="nav-item"><a class="nav-link" href="#how">Guide</a></li>
        <li class="nav-item"><a class="nav-link" href="#why">Why Us</a></li>
        <li class="nav-item"><a class="nav-link" href="#testimonials">Reviews</a></li>
        <li class="nav-item"><a class="nav-link" href="#about">About</a></li>
        <li class="nav-item"><a class="nav-link" href="#contact">Contact</a></li>
        <li class="nav-item"><a class="btn btn-success ms-2" href="login.php">Book Now</a></li>
      </ul>
    </div>
  </div>
</nav>

<!-- Hero Section -->
<section id="hero">
  <div>
    <h1 data-aos="fade-up">Drive Your Dream Car Today</h1>
    <p data-aos="fade-up" data-aos-delay="200">Luxury & Affordable Rentals at Your Fingertips</p>
    <a href="login.php" class="btn btn-primary btn-lg mt-3" data-aos="zoom-in" data-aos-delay="400">Book Now</a>
  </div>
</section>

<!-- How It Works -->
<section id="how" class="py-5">
  <div class="container text-center">
    <h2 class="mb-4" data-aos="fade-up">How It Works</h2>
    <div class="row g-4">
      <div class="col-md-3" data-aos="fade-up" data-aos-delay="100">
        <div class="glassy">
          <h4>1. Browse Fleet</h4>
          <p>Choose from our range of vehicles.</p>
        </div>
      </div>
      <div class="col-md-3" data-aos="fade-up" data-aos-delay="200">
        <div class="glassy">
          <h4>2. Select Car</h4>
          <p>Pick the one that fits your style & budget.</p>
        </div>
      </div>
      <div class="col-md-3" data-aos="fade-up" data-aos-delay="300">
        <div class="glassy">
          <h4>3. Book Online</h4>
          <p>Reserve in minutes from any device.</p>
        </div>
      </div>
      <div class="col-md-3" data-aos="fade-up" data-aos-delay="400">
        <div class="glassy">
          <h4>4. Drive & Enjoy</h4>
          <p>Pick up & hit the road hassle-free.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Featured Fleet -->
<section id="fleet" class="py-5" data-aos="fade-up">
  <div class="container">
    <h2 class="text-center mb-4">Featured Fleet</h2>
    <div class="row g-4">
      <div class="col-md-4" data-aos="fade-right">
        <div class="car-card">
          <img src="images/toyota_corolla.jpg" class="w-100" alt="">
          <div class= "hover-content">
          <h5 class="mt-5">Toyota Corolla 2023</h5>
          <p>$40/day</p>
          <a href="login.php" class="btn btn-primary btn-sm mt-2">Book</a>
         </div> 
        </div>
      </div>
      <div class="col-md-4" data-aos="fade-up" >
        <div class="car-card">
          <img src="images/ford_escape.jpg" class="w-100" alt="">
          <div class= "hover-content">
          <h5 class="mt-2">Ford Escape</h5> 
           <p>$80/day</p>
           <a href="login.php" class="btn btn-primary btn-sm mt-2">Book</a>
         </div>
        </div>
      </div>
      <div class="col-md-4" data-aos="fade-left">
        <div class="car-card">
          <img src="images/honda.jpg" class="w-100" alt="">
          <div class= "hover-content">
          <h5 class="mt-2">Honda Civic</h5>
          <p>$90/day</p>
          <a href="login.php" class="btn btn-primary btn-sm mt-2">Book</a>
         </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Why Choose Us -->
<section id="why" class="py-5">
  <div class="container text-center">
    <h2 class="mb-4" data-aos="fade-up">Why Choose Us?</h2>
    <div class="row g-4">
      <div class="col-md-3" data-aos="fade-up" data-aos-delay="100">
        <div class="glassy">
          <h4>Affordable Prices</h4>
        </div>
      </div>
      <div class="col-md-3" data-aos="fade-up" data-aos-delay="200">
        <div class="glassy">
          <h4>24/7 Support</h4>
        </div>
      </div>
      <div class="col-md-3" data-aos="fade-up" data-aos-delay="300">
        <div class="glassy">
          <h4>Well-Maintained Cars</h4>
        </div>
      </div>
      <div class="col-md-3" data-aos="fade-up" data-aos-delay="400">
        <div class="glassy">
          <h4>Flexible Booking</h4>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Testimonials -->
<section id="testimonials" class="py-5">
  <div class="container">
    <h2 class="text-center mb-4" data-aos="fade-up">What Our Customers Say</h2>
    <div class="row">
      <div class="col-md-4" data-aos="fade-right">
        <div class="p-3 glassy">
          <p>"Great service, friendly staff, and clean cars!"</p>
          <small>- Sarah J.</small>
        </div>
      </div>
      <div class="col-md-4" data-aos="fade-up">
        <div class="p-3 glassy">
          <p>"Affordable and easy booking process."</p>
          <small>- Mark L.</small>
        </div>
      </div>
      <div class="col-md-4" data-aos="fade-left">
        <div class="p-3 glassy">
          <p>"Will definitely rent again, highly recommend."</p>
          <small>- Emma W.</small>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- About Section -->
<section id="about" class="py-5">
  <div class="container">
    <h2 class="text-center mb-4" data-aos="fade-up">About Safari Rentals</h2>
    <div class="row align-items-center">
      
      <!-- About Image -->
      <div class="col-md-6 mb-4 mb-md-0" data-aos="fade-right">
        <img src="https://images.unsplash.com/photo-1552519507-da3b142c6e3d" 
             alt="About Safari Rentals" 
             class="img-fluid rounded shadow">
      </div>

      <!-- About Content -->
      <div class="col-md-6" data-aos="fade-left">
        <div class="glassy">
          <h3 class="mb-3">Your Journey, Our Passion</h3>
          <p>
            At <strong>Safari Rentals</strong>, we believe that every trip should be a seamless blend 
            of comfort, style, and reliability. Founded with a vision to make car rentals simple and 
            stress-free, we have proudly served thousands of satisfied customers across the country.
          </p>
          <p>
            Whether you’re traveling for business, leisure, or adventure, our diverse fleet ensures you 
            always find the perfect ride.
          </p>

          <!-- Stats -->
          <div class="row text-center mt-4">
            <div class="col-4">
              <h4 class="fw-bold">500+</h4>
              <small>Cars Rented</small>
            </div>
            <div class="col-4">
              <h4 class="fw-bold">4.9★</h4>
              <small>Average Rating</small>
            </div>
            <div class="col-4">
              <h4 class="fw-bold">20+</h4>
              <small>Cities Served</small>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>


<!-- CTA Strip -->
<section class="cta-strip">
  <div class="cta-strip-content">
    <h2>Ready to Hit the Road?</h2>
    <a href="login.php" class="btn btn-success btn-lg mt-3">Book Your Car Now</a>
  </div>
</section>

<!-- Contact Section -->
<section id="contact" class="py-5">
  <div class="container">
    <h2 class="text-center mb-4" data-aos="fade-up">Get in Touch</h2>
    <div class="row g-4">
      <!-- Contact Info -->
      <div class="col-md-4" data-aos="fade-right">
        <div class="glassy p-4 text-white">
          <h5>Our Office</h5>
          <p>123 Safari Street<br>Nakuru, Kenya</p>
          <h5>Email</h5>
          <p>info@safarirentals.com</p>
          <h5>Phone</h5>
          <p>+254 718 366 047</p>
        </div>
      </div>

      <!-- Contact Form -->
      <div class="col-md-8" data-aos="fade-left">
        <div class="glassy p-4 text-white">
          <form>
            <div class="mb-3">
              <label class="form-label">Name</label>
              <input type="text" class="form-control bg-transparent text-white border-light" placeholder="Your Name">
            </div>
            <div class="mb-3">
              <label class="form-label">Email</label>
              <input type="email" class="form-control bg-transparent text-white border-light" placeholder="Your Email">
            </div>
            <div class="mb-3">
              <label class="form-label">Message</label>
              <textarea class="form-control bg-transparent text-white border-light" rows="4" placeholder="Your Message"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Send Message</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>


<!-- Footer -->
<footer id="contact" class="py-4">
  <p>&copy; 2025 DriveNow Car Rentals | All Rights Reserved</p>
</footer>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
<script>
  AOS.init();
</script>
</body>
</html>