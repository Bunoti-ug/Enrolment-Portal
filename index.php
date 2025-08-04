<?php
$page_title = 'Home';
start_secure_session();

// Check if user is already logged in and redirect appropriately
if (is_logged_in()) {
    // User is logged in, show user-specific navigation
    $user_logged_in = true;
} elseif (is_admin_logged_in()) {
    // Admin is logged in, redirect to admin dashboard
    header('Location: admin/dashboard.php');
    exit;
} else {
    $user_logged_in = false;
}

require_once 'includes/functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Buyunic Technologies - Enrollment Portal</title>
<link rel="icon" href="assets/img/logo.png" type="image/png" />

<script src="https://cdn.tailwindcss.com/3.4.16"></script>

<script>
tailwind.config = {
theme: {
extend: {
colors: {
primary: '#1e40af',
secondary: '#059669'
},
borderRadius: {
'none': '0px',
'sm': '4px',
DEFAULT: '8px',
'md': '12px',
'lg': '16px',
'xl': '20px',
'2xl': '24px',
'3xl': '32px',
'full': '9999px',
'button': '8px'
}
}
}
</script>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css" rel="stylesheet">
<style>
:where([class^="ri-"])::before {
content: "\f3c2";
}
body {
font-family: 'Inter', sans-serif;
}
.gradient-hero {
background: linear-gradient(135deg, #1e40af 0%, #3b82f6 50%, #ffffff 100%);
}
.gradient-card {
background: linear-gradient(145deg, #f8fafc 0%, #ffffff 100%);
}
</style>

</head>
<body class="bg-gray-50">
<header class="bg-primary shadow-lg sticky top-0 z-50">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
<div class="flex justify-between items-center h-16">
<div class="flex items-center">
<a href="index.php" class="flex items-center gap-2 text-white hover:text-blue-200 transition-colors duration-300 font-medium">
  <img src="assets/img/logo.png" alt="Buyunic Technologies Logo" class="h-8 w-8" onerror="this.style.display='none'" />
  <span class="text-lg font-semibold">Buyunic Technologies</span>
</a>
</div>
<nav class="hidden md:flex space-x-8">
<a href="#home" class="text-white hover:text-blue-200 transition-colors duration-300 font-medium">Home</a>
<a href="#courses" class="text-white hover:text-blue-200 transition-colors duration-300 font-medium">Courses</a>
<a href="#about" class="text-white hover:text-blue-200 transition-colors duration-300 font-medium">About</a>
<a href="#contact" class="text-white hover:text-blue-200 transition-colors duration-300 font-medium">Contact</a>
</nav>
<div class="flex items-center space-x-4">
<?php if ($user_logged_in): ?>
<div class="relative">
  <a href="user/dashboard.php" class="bg-secondary text-white px-6 py-2 !rounded-button font-medium hover:bg-green-700 transition-colors duration-300 whitespace-nowrap flex items-center gap-1">
    <i class="ri-dashboard-line"></i>
    Dashboard
  </a>
</div>
<div class="relative">
  <a href="auth/logout.php" class="text-white hover:text-blue-200 transition-colors duration-300 font-medium">
    <i class="ri-logout-box-line"></i>
    Logout
  </a>
</div>
<?php else: ?>
<div class="relative">
  <div class="flex items-center space-x-2">
    <a href="auth/login.php" class="text-white hover:text-blue-200 transition-colors duration-300 font-medium px-4 py-2 rounded-button border border-white/20 hover:bg-white/10">
      Login
    </a>
    <a href="auth/register.php" class="bg-secondary text-white px-6 py-2 !rounded-button font-medium hover:bg-green-700 transition-colors duration-300 whitespace-nowrap flex items-center gap-1">
      Register
    </a>
  </div>
</div>
<?php endif; ?>
</div>
<button class="md:hidden text-white w-6 h-6 flex items-center justify-center" id="mobile-menu-btn">
<i class="ri-menu-line text-xl"></i>
</button>
</div>
</div>
<div class="md:hidden bg-primary border-t border-blue-600 hidden" id="mobile-menu">
<div class="px-4 py-4 space-y-3">
<a href="#home" class="block text-white hover:text-blue-200 transition-colors duration-300 font-medium">Home</a>
<a href="#courses" class="block text-white hover:text-blue-200 transition-colors duration-300 font-medium">Courses</a>
<a href="#about" class="block text-white hover:text-blue-200 transition-colors duration-300 font-medium">About</a>
<a href="#contact" class="block text-white hover:text-blue-200 transition-colors duration-300 font-medium">Contact</a>
<?php if ($user_logged_in): ?>
<a href="user/dashboard.php" class="block text-white hover:text-blue-200 transition-colors duration-300 font-medium">Dashboard</a>
<a href="auth/logout.php" class="block text-white hover:text-blue-200 transition-colors duration-300 font-medium">Logout</a>
<?php else: ?>
<a href="auth/login.php" class="block text-white hover:text-blue-200 transition-colors duration-300 font-medium">Login</a>
<a href="auth/register.php" class="block text-white hover:text-blue-200 transition-colors duration-300 font-medium">Register</a>
<?php endif; ?>
</div>
</div>
</header>

<section id="home" class="gradient-hero min-h-screen flex items-center relative overflow-hidden" style="background-image: url('https://readdy.ai/api/search-image?query=modern%20professional%20technology%20training%20center%20with%20computers%20and%20students%20learning%20in%20bright%20clean%20environment%2C%20left%20side%20white%20space%20for%20text%20overlay%2C%20right%20side%20showing%20training%20facility%2C%20contemporary%20design%2C%20high-tech%20atmosphere%2C%20educational%20setting&width=1920&height=1080&seq=hero-bg-001&orientation=landscape'); background-size: cover; background-position: center;">
<div class="absolute inset-0 bg-gradient-to-r from-primary/90 via-primary/60 to-transparent"></div>

<div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
<div class="grid lg:grid-cols-2 gap-12 items-center">
<div class="text-white space-y-8">
<h1 class="text-5xl lg:text-6xl font-bold leading-tight">
Empowering ICT Skills
<span class="text-blue-200">for the Future</span>
</h1>
<p class="text-xl text-blue-100 leading-relaxed max-w-lg">
Transform your career with comprehensive ICT training programs designed for modern professionals. Join thousands who have advanced their skills with us.
</p>
<div class="grid grid-cols-3 gap-6 py-8">
<div class="text-center">
<div class="text-3xl font-bold text-white">500+</div>
<div class="text-blue-200 text-sm">Graduates</div>
</div>
<div class="text-center">
<div class="text-3xl font-bold text-white">25+</div>
<div class="text-blue-200 text-sm">Programs</div>
</div>
<div class="text-center">
<div class="text-3xl font-bold text-white">100%</div>
<div class="text-blue-200 text-sm">Certified Training</div>
</div>
</div>
<div class="flex flex-col sm:flex-row gap-4">
<?php if ($user_logged_in): ?>
<a href="user/dashboard.php">
  <button class="bg-secondary text-white px-8 py-4 !rounded-button font-semibold hover:bg-green-700 transition-all duration-300 transform hover:scale-105 whitespace-nowrap">
    Go to Dashboard
  </button>
</a>
<?php else: ?>
<a href="auth/register.php">
  <button class="bg-secondary text-white px-8 py-4 !rounded-button font-semibold hover:bg-green-700 transition-all duration-300 transform hover:scale-105 whitespace-nowrap">
    Apply Now
  </button>
</a>
<?php endif; ?>

<button class="border-2 border-white text-white px-8 py-4 !rounded-button font-semibold hover:bg-white hover:text-primary transition-all duration-300 whitespace-nowrap" onclick="document.getElementById('courses').scrollIntoView({behavior: 'smooth'})">
View Programs
</button>
</div>
</div>
</div>
</div>
</section>

<section id="courses" class="py-20 bg-white">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
<div class="text-center mb-16">
<h2 class="text-4xl font-bold text-gray-900 mb-4">Training Program Catalog</h2>
<p class="text-xl text-gray-600 max-w-3xl mx-auto">Choose from our comprehensive range of ICT training programs designed to boost your professional skills and career prospects.</p>
</div>
<div class="flex flex-wrap justify-center gap-4 mb-12">
<button class="category-filter bg-primary text-white px-6 py-3 !rounded-button font-medium transition-all duration-300 whitespace-nowrap" data-category="all">All Programs</button>
<button class="category-filter bg-gray-200 text-gray-700 px-6 py-3 !rounded-button font-medium hover:bg-gray-300 transition-all duration-300 whitespace-nowrap" data-category="microsoft">Microsoft Office</button>
<button class="category-filter bg-gray-200 text-gray-700 px-6 py-3 !rounded-button font-medium hover:bg-gray-300 transition-all duration-300 whitespace-nowrap" data-category="graphics">Graphics & Web</button>
<button class="category-filter bg-gray-200 text-gray-700 px-6 py-3 !rounded-button font-medium hover:bg-gray-300 transition-all duration-300 whitespace-nowrap" data-category="specialized">Specialized IT</button>
<button class="category-filter bg-gray-200 text-gray-700 px-6 py-3 !rounded-button font-medium hover:bg-gray-300 transition-all duration-300 whitespace-nowrap" data-category="internship">Internship</button>
</div>
<div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8" id="course-grid">
<!-- Microsoft Office Suite -->
<div class="course-card gradient-card p-6 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2" data-category="microsoft">
<div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
<i class="ri-file-word-line text-2xl text-blue-600"></i>
</div>
<h3 class="text-xl font-semibold text-gray-900 mb-2">General Intro + MS Word</h3>
<p class="text-gray-600 mb-4">Master document creation, formatting, and advanced features</p>
<div class="flex justify-between items-center mb-4">
<span class="text-2xl font-bold text-primary">120,000 UGX</span>
<span class="text-sm text-gray-500">3 weeks</span>
</div>
<a href="<?php echo $user_logged_in ? 'user/application.php' : 'auth/register.php'; ?>">
  <button class="w-full bg-secondary text-white py-3 !rounded-button font-medium hover:bg-green-700 transition-colors duration-300 whitespace-nowrap">
    Enroll Now
  </button>
</a>
</div>

<div class="course-card gradient-card p-6 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2" data-category="microsoft">
<div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center mb-4">
<i class="ri-slideshow-line text-2xl text-orange-600"></i>
</div>
<h3 class="text-xl font-semibold text-gray-900 mb-2">Microsoft PowerPoint</h3>
<p class="text-gray-600 mb-4">Create stunning presentations with advanced animations</p>
<div class="flex justify-between items-center mb-4">
<span class="text-2xl font-bold text-primary">50,000 UGX</span>
<span class="text-sm text-gray-500">2 weeks</span>
</div>
<a href="<?php echo $user_logged_in ? 'user/application.php' : 'auth/register.php'; ?>">
  <button class="w-full bg-secondary text-white py-3 !rounded-button font-medium hover:bg-green-700 transition-colors duration-300 whitespace-nowrap">
    Enroll Now
  </button>
</a>
</div>

<div class="course-card gradient-card p-6 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2" data-category="microsoft">
<div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-4">
<i class="ri-file-excel-line text-2xl text-green-600"></i>
</div>
<h3 class="text-xl font-semibold text-gray-900 mb-2">Microsoft Excel</h3>
<p class="text-gray-600 mb-4">Data analysis, formulas, pivot tables, and macros</p>
<div class="flex justify-between items-center mb-4">
<span class="text-2xl font-bold text-primary">60,000 UGX</span>
<span class="text-sm text-gray-500">2 weeks</span>
</div>
<a href="<?php echo $user_logged_in ? 'user/application.php' : 'auth/register.php'; ?>">
  <button class="w-full bg-secondary text-white py-3 !rounded-button font-medium hover:bg-green-700 transition-colors duration-300 whitespace-nowrap">
    Enroll Now
  </button>
</a>
</div>

<div class="course-card gradient-card p-6 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2" data-category="microsoft">
<div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-4">
<i class="ri-database-line text-2xl text-purple-600"></i>
</div>
<h3 class="text-xl font-semibold text-gray-900 mb-2">Microsoft Access/Database</h3>
<p class="text-gray-600 mb-4">Database design, queries, forms, and reports</p>
<div class="flex justify-between items-center mb-4">
<span class="text-2xl font-bold text-primary">60,000 UGX</span>
<span class="text-sm text-gray-500">2 weeks</span>
</div>
<a href="<?php echo $user_logged_in ? 'user/application.php' : 'auth/register.php'; ?>">
  <button class="w-full bg-secondary text-white py-3 !rounded-button font-medium hover:bg-green-700 transition-colors duration-300 whitespace-nowrap">
    Enroll Now
  </button>
</a>
</div>

<!-- Graphics & Web Development -->
<div class="course-card gradient-card p-6 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2" data-category="graphics">
<div class="w-12 h-12 bg-pink-100 rounded-lg flex items-center justify-center mb-4">
<i class="ri-image-edit-line text-2xl text-pink-600"></i>
</div>
<h3 class="text-xl font-semibold text-gray-900 mb-2">Adobe Photoshop</h3>
<p class="text-gray-600 mb-4">Professional photo editing and graphic design</p>
<div class="flex justify-between items-center mb-4">
<span class="text-2xl font-bold text-primary">550,000 UGX</span>
<span class="text-sm text-gray-500">4 weeks</span>
</div>
<a href="<?php echo $user_logged_in ? 'user/application.php' : 'auth/register.php'; ?>">
  <button class="w-full bg-secondary text-white py-3 !rounded-button font-medium hover:bg-green-700 transition-colors duration-300 whitespace-nowrap">
    Enroll Now
  </button>
</a>
</div>

<div class="course-card gradient-card p-6 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2" data-category="graphics">
<div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center mb-4">
<i class="ri-code-line text-2xl text-indigo-600"></i>
</div>
<h3 class="text-xl font-semibold text-gray-900 mb-2">HTML Language</h3>
<p class="text-gray-600 mb-4">Web development fundamentals and responsive design</p>
<div class="flex justify-between items-center mb-4">
<span class="text-2xl font-bold text-primary">600,000 UGX</span>
<span class="text-sm text-gray-500">4 weeks</span>
</div>
<a href="<?php echo $user_logged_in ? 'user/application.php' : 'auth/register.php'; ?>">
  <button class="w-full bg-secondary text-white py-3 !rounded-button font-medium hover:bg-green-700 transition-colors duration-300 whitespace-nowrap">
    Enroll Now
  </button>
</a>
</div>

<div class="course-card gradient-card p-6 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2" data-category="graphics">
<div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
<i class="ri-wordpress-line text-2xl text-blue-600"></i>
</div>
<h3 class="text-xl font-semibold text-gray-900 mb-2">WordPress CMS</h3>
<p class="text-gray-600 mb-4">Build professional websites with WordPress</p>
<div class="flex justify-between items-center mb-4">
<span class="text-2xl font-bold text-primary">500,000 UGX</span>
<span class="text-sm text-gray-500">4 weeks</span>
</div>
<a href="<?php echo $user_logged_in ? 'user/application.php' : 'auth/register.php'; ?>">
  <button class="w-full bg-secondary text-white py-3 !rounded-button font-medium hover:bg-green-700 transition-colors duration-300 whitespace-nowrap">
    Enroll Now
  </button>
</a>
</div>

<!-- Specialized IT Programs -->
<div class="course-card gradient-card p-6 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2" data-category="specialized">
<div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center mb-4">
<i class="ri-router-line text-2xl text-red-600"></i>
</div>
<h3 class="text-xl font-semibold text-gray-900 mb-2">Computer Networking</h3>
<p class="text-gray-600 mb-4">Network administration and troubleshooting</p>
<div class="flex justify-between items-center mb-4">
<span class="text-2xl font-bold text-primary">550,000 UGX</span>
<span class="text-sm text-gray-500">6 weeks</span>
</div>
<a href="<?php echo $user_logged_in ? 'user/application.php' : 'auth/register.php'; ?>">
  <button class="w-full bg-secondary text-white py-3 !rounded-button font-medium hover:bg-green-700 transition-colors duration-300 whitespace-nowrap">
    Enroll Now
  </button>
</a>
</div>

<div class="course-card gradient-card p-6 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2" data-category="specialized">
<div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center mb-4">
<i class="ri-camera-line text-2xl text-yellow-600"></i>
</div>
<h3 class="text-xl font-semibold text-gray-900 mb-2">CCTV Installation & Config</h3>
<p class="text-gray-600 mb-4">Security camera installation and monitoring</p>
<div class="flex justify-between items-center mb-4">
<span class="text-2xl font-bold text-primary">550,000 UGX</span>
<span class="text-sm text-gray-500">3 weeks</span>
</div>
<a href="<?php echo $user_logged_in ? 'user/application.php' : 'auth/register.php'; ?>">
  <button class="w-full bg-secondary text-white py-3 !rounded-button font-medium hover:bg-green-700 transition-colors duration-300 whitespace-nowrap">
    Enroll Now
  </button>
</a>
</div>

<div class="course-card gradient-card p-6 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2" data-category="specialized">
<div class="w-12 h-12 bg-teal-100 rounded-lg flex items-center justify-center mb-4">
<i class="ri-bar-chart-line text-2xl text-teal-600"></i>
</div>
<h3 class="text-xl font-semibold text-gray-900 mb-2">SPSS (Statistical Package)</h3>
<p class="text-gray-600 mb-4">Statistical analysis and data processing</p>
<div class="flex justify-between items-center mb-4">
<span class="text-2xl font-bold text-primary">550,000 UGX</span>
<span class="text-sm text-gray-500">6 weeks</span>
</div>
<a href="<?php echo $user_logged_in ? 'user/application.php' : 'auth/register.php'; ?>">
  <button class="w-full bg-secondary text-white py-3 !rounded-button font-medium hover:bg-green-700 transition-colors duration-300 whitespace-nowrap">
    Enroll Now
  </button>
</a>
</div>

<!-- Internship Program -->
<div class="course-card gradient-card p-6 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2" data-category="internship">
<div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-4">
<i class="ri-briefcase-line text-2xl text-green-600"></i>
</div>
<h3 class="text-xl font-semibold text-gray-900 mb-2">Internship Program</h3>
<p class="text-gray-600 mb-4">Hands-on experience with real-world projects</p>
<div class="flex justify-between items-center mb-4">
<span class="text-2xl font-bold text-primary">400,000 UGX</span>
<span class="text-sm text-gray-500">2 months</span>
</div>
<a href="<?php echo $user_logged_in ? 'user/application.php' : 'auth/register.php'; ?>">
  <button class="w-full bg-secondary text-white py-3 !rounded-button font-medium hover:bg-green-700 transition-colors duration-300 whitespace-nowrap">
    Enroll Now
  </button>
</a>
</div>
</div>
</div>
</section>

<section id="about" class="py-20 bg-gray-50">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
<div class="grid lg:grid-cols-2 gap-16 items-center">
<div class="space-y-8">
<h2 class="text-4xl font-bold text-gray-900">About Buyunic Technologies</h2>
<div class="space-y-6">
<div>
<h3 class="text-xl font-semibold text-gray-900 mb-3">Our Mission</h3>
<p class="text-gray-600 leading-relaxed">
To empower individuals and organizations with cutting-edge ICT skills that drive innovation, productivity, and career advancement in the digital age. We bridge the gap between traditional education and industry demands.
</p>
</div>
<div>
<h3 class="text-xl font-semibold text-gray-900 mb-3">Training Excellence</h3>
<p class="text-gray-600 leading-relaxed">
Our comprehensive programs combine theoretical knowledge with hands-on practical experience. Led by industry experts, we ensure every graduate is job-ready and equipped with relevant, marketable skills.
</p>
</div>
<div>
<h3 class="text-xl font-semibold text-gray-900 mb-3">Core Values</h3>
<ul class="text-gray-600 space-y-2">
<li class="flex items-center">
<i class="ri-check-line text-secondary mr-3"></i>
Quality education and practical training
</li>
<li class="flex items-center">
<i class="ri-check-line text-secondary mr-3"></i>
Industry-relevant curriculum
</li>
<li class="flex items-center">
<i class="ri-check-line text-secondary mr-3"></i>
Affordable and accessible learning
</li>
<li class="flex items-center">
<i class="ri-check-line text-secondary mr-3"></i>
Continuous support and mentorship
</li>
</ul>
</div>
</div>
</div>
<div class="relative">
<img src="https://readdy.ai/api/search-image?query=modern%20ICT%20training%20center%20with%20students%20working%20on%20computers%2C%20professional%20instructor%20teaching%20technology%20skills%2C%20bright%20classroom%20environment%2C%20diverse%20group%20of%20learners%2C%20contemporary%20educational%20setting%20with%20modern%20equipment&width=600&height=400&seq=about-img-001&orientation=landscape" alt="ICT Training Center" class="rounded-xl shadow-lg w-full h-96 object-cover object-top">
<div class="absolute inset-0 bg-gradient-to-t from-primary/20 to-transparent rounded-xl"></div>
</div>
</div>
</div>
</section>

<section class="py-20 bg-white">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
<div class="text-center mb-16">
<h2 class="text-4xl font-bold text-gray-900 mb-4">Payment Methods</h2>
<p class="text-xl text-gray-600">Simple and secure payment options for your convenience</p>
</div>
<div class="grid md:grid-cols-2 gap-12 items-center">
<div class="space-y-8">
<div class="flex items-center space-x-6">
<div class="w-16 h-16 bg-blue-100 rounded-lg flex items-center justify-center">
<i class="ri-smartphone-line text-3xl text-blue-600"></i>
</div>
<div>
<h3 class="text-xl font-semibold text-gray-900">Mobile Money</h3>
<p class="text-gray-600">Dial *165*3# > Pay Merchant > Use your Application ID as reference</p>
</div>
</div>
<div class="flex items-center space-x-6">
<div class="w-16 h-16 bg-green-100 rounded-lg flex items-center justify-center">
<i class="ri-bank-card-line text-3xl text-green-600"></i>
</div>
<div>
<h3 class="text-xl font-semibold text-gray-900">FlexiPay</h3>
<p class="text-gray-600">Dial *291# > Pay Merchant > ID: 236390 > Reference = Application ID</p>
</div>
</div>
<div class="flex items-center space-x-6">
<div class="w-16 h-16 bg-purple-100 rounded-lg flex items-center justify-center">
<i class="ri-secure-payment-line text-3xl text-purple-600"></i>
</div>
<div>
<h3 class="text-xl font-semibold text-gray-900">Pesapal Online</h3>
<p class="text-gray-600">Secure online payments with card and mobile wallet options</p>
</div>
</div>
</div>
<div class="bg-gray-50 p-8 rounded-xl">
<h3 class="text-2xl font-semibold text-gray-900 mb-6">Payment Process</h3>
<div class="space-y-6">
<div class="flex items-start space-x-4">
<div class="w-8 h-8 bg-primary text-white rounded-full flex items-center justify-center text-sm font-semibold">1</div>
<div>
<h4 class="font-semibold text-gray-900">Complete Application</h4>
<p class="text-gray-600 text-sm">Fill out your application form and receive your unique Application ID</p>
</div>
</div>
<div class="flex items-start space-x-4">
<div class="w-8 h-8 bg-primary text-white rounded-full flex items-center justify-center text-sm font-semibold">2</div>
<div>
<h4 class="font-semibold text-gray-900">Make Payment</h4>
<p class="text-gray-600 text-sm">Use your preferred payment method with your Application ID as reference</p>
</div>
</div>
<div class="flex items-start space-x-4">
<div class="w-8 h-8 bg-primary text-white rounded-full flex items-center justify-center text-sm font-semibold">3</div>
<div>
<h4 class="font-semibold text-gray-900">Confirm Enrollment</h4>
<p class="text-gray-600 text-sm">Receive confirmation and access your training materials</p>
</div>
</div>
</div>
<div class="mt-8 p-4 bg-blue-50 rounded-lg">
<h4 class="font-semibold text-gray-900 mb-2">Need Help?</h4>
<p class="text-sm text-gray-600">Contact our support team for payment assistance</p>
<div class="flex items-center space-x-4 mt-2">
<span class="text-sm text-gray-600">
<i class="ri-phone-line mr-1"></i>
+256 207 901 434
</span>
<span class="text-sm text-gray-600">
<i class="ri-whatsapp-line mr-1"></i>
WhatsApp Support
</span>
</div>
</div>
</div>
</div>
</div>
</section>

<section id="contact" class="py-20 bg-gray-50">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
<div class="text-center mb-16">
<h2 class="text-4xl font-bold text-gray-900 mb-4">Get In Touch</h2>
<p class="text-xl text-gray-600">Ready to start your ICT journey? Contact us today!</p>
</div>
<div class="grid md:grid-cols-2 gap-12">
<div class="space-y-8">
<div class="flex items-center space-x-4">
<div class="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center">
<i class="ri-map-pin-line text-2xl text-primary"></i>
</div>
<div>
<h3 class="text-lg font-semibold text-gray-900">Location</h3>
<p class="text-gray-600">Plot 28, North Road, Northern City Division, Mbale City</p>
</div>
</div>
<div class="flex items-center space-x-4">
<div class="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center">
<i class="ri-phone-line text-2xl text-primary"></i>
</div>
<div>
<h3 class="text-lg font-semibold text-gray-900">Phone</h3>
<p class="text-gray-600">+256 394 839 851</p>
</div>
</div>
<div class="flex items-center space-x-4">
<div class="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center">
<i class="ri-whatsapp-line text-2xl text-primary"></i>
</div>
<div>
<h3 class="text-lg font-semibold text-gray-900">WhatsApp</h3>
<p class="text-gray-600">+256 207 901 434</p>
</div>
</div>
<div class="flex items-center space-x-4">
<div class="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center">
<i class="ri-mail-line text-2xl text-primary"></i>
</div>
<div>
<h3 class="text-lg font-semibold text-gray-900">Email</h3>
<p class="text-gray-600">info@buyunic.ug / apply@buyunic.ug</p>
</div>
</div>
</div>
<div class="bg-white p-8 rounded-xl shadow-lg">
<h3 class="text-2xl font-semibold text-gray-900 mb-6">Quick Start</h3>
<div class="space-y-4">
<?php if (!$user_logged_in): ?>
<a href="auth/register.php" class="block w-full bg-primary text-white py-3 px-6 rounded-button font-medium hover:bg-blue-800 transition-colors duration-300 text-center">
Create Account
</a>
<a href="auth/login.php" class="block w-full border-2 border-primary text-primary py-3 px-6 rounded-button font-medium hover:bg-primary hover:text-white transition-colors duration-300 text-center">
Login to Existing Account
</a>
<?php else: ?>
<a href="user/application.php" class="block w-full bg-primary text-white py-3 px-6 rounded-button font-medium hover:bg-blue-800 transition-colors duration-300 text-center">
Start New Application
</a>
<a href="user/dashboard.php" class="block w-full border-2 border-primary text-primary py-3 px-6 rounded-button font-medium hover:bg-primary hover:text-white transition-colors duration-300 text-center">
Go to Dashboard
</a>
<?php endif; ?>
</div>
</div>
</div>
</div>
</section>

<footer class="bg-primary text-white py-16">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
<div class="grid md:grid-cols-3 gap-12">
<div class="space-y-6">
<div class="flex items-center gap-3">
  <img src="assets/img/logo.png" alt="Buyunic Logo" class="h-8 w-8" onerror="this.style.display='none'" />
  <h3 class="text-2xl font-bold">Buyunic Technologies</h3>
</div>
<p class="text-blue-200 leading-relaxed">
Empowering the next generation with essential ICT skills for a digital future. Join thousands who have transformed their careers with us.
</p>
<div class="flex space-x-4">
<a href="https://www.facebook.com/buyunicug/" target="_blank" rel="noopener noreferrer" class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center hover:bg-blue-700 transition-colors duration-300">
  <i class="ri-facebook-fill"></i>
</a>
<a href="https://x.com/buyunict" target="_blank" rel="noopener noreferrer" class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center hover:bg-blue-700 transition-colors duration-300">
  <i class="ri-twitter-fill"></i>
</a>
<a href="https://www.youtube.com/@buyunic" target="_blank" rel="noopener noreferrer" class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center hover:bg-blue-700 transition-colors duration-300">
  <i class="ri-youtube-fill"></i>
</a>
<a href="https://instagram.com/buyunic" target="_blank" rel="noopener noreferrer" class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center hover:bg-blue-700 transition-colors duration-300">
  <i class="ri-instagram-fill"></i>
</a>
</div>
</div>
<div class="space-y-6">
<h4 class="text-xl font-semibold">Quick Links</h4>
<ul class="space-y-3">
<li><a href="index.php" class="text-blue-200 hover:text-white transition-colors duration-300">Home</a></li>
<li><a href="#courses" class="text-blue-200 hover:text-white transition-colors duration-300">Training Programs</a></li>
<li><a href="#about" class="text-blue-200 hover:text-white transition-colors duration-300">About Us</a></li>
<li><a href="#contact" class="text-blue-200 hover:text-white transition-colors duration-300">Contact</a></li>
<?php if ($user_logged_in): ?>
<li><a href="user/dashboard.php" class="text-blue-200 hover:text-white transition-colors duration-300">Dashboard</a></li>
<?php else: ?>
<li><a href="auth/register.php" class="text-blue-200 hover:text-white transition-colors duration-300">Register</a></li>
<li><a href="auth/login.php" class="text-blue-200 hover:text-white transition-colors duration-300">Login</a></li>
<?php endif; ?>
<li><a href="terms.php" class="text-blue-200 hover:text-white transition-colors duration-300">Terms & Conditions</a></li>
<li><a href="privacy.php" class="text-blue-200 hover:text-white transition-colors duration-300">Privacy Policy</a></li>
</ul>
</div>
<div class="space-y-6">
<h4 class="text-xl font-semibold">Contact Info</h4>
<div class="space-y-4">
<div class="flex items-center space-x-3">
<i class="ri-phone-line text-blue-300"></i>
<span class="text-blue-200">+256 394 839 851</span>
</div>
<div class="flex items-center space-x-3">
<i class="ri-whatsapp-line text-blue-300"></i>
<span class="text-blue-200">+256 207 901 434</span>
</div>
<div class="flex items-center space-x-3">
<i class="ri-mail-line text-blue-300"></i>
<span class="text-blue-200">info@buyunic.ug</span>
</div>
<div class="flex items-center space-x-3">
<i class="ri-map-pin-line text-blue-300"></i>
<span class="text-blue-200">Plot 28, North Road, Mbale City</span>
</div>
</div>
</div>
</div>
<div class="border-t border-blue-600 mt-12 pt-8 text-center">
<p class="text-blue-200">&copy; <?php echo date('Y'); ?> Buyunic Technologies. All rights reserved.</p>
</div>
</div>
</footer>

<script>
document.addEventListener('DOMContentLoaded', function() {
// Mobile menu functionality
const mobileMenuBtn = document.getElementById('mobile-menu-btn');
const mobileMenu = document.getElementById('mobile-menu');

if (mobileMenuBtn && mobileMenu) {
mobileMenuBtn.addEventListener('click', function() {
mobileMenu.classList.toggle('hidden');
});
}

// Course filter functionality
const filterButtons = document.querySelectorAll('.category-filter');
const courseCards = document.querySelectorAll('.course-card');

filterButtons.forEach(button => {
button.addEventListener('click', function() {
const category = this.getAttribute('data-category');

// Update active filter button
filterButtons.forEach(btn => {
btn.classList.remove('bg-primary', 'text-white');
btn.classList.add('bg-gray-200', 'text-gray-700');
});
this.classList.remove('bg-gray-200', 'text-gray-700');
this.classList.add('bg-primary', 'text-white');

// Filter course cards
courseCards.forEach(card => {
if (category === 'all' || card.getAttribute('data-category') === category) {
card.style.display = 'block';
} else {
card.style.display = 'none';
}
});
});
});

// Smooth scrolling for anchor links
const links = document.querySelectorAll('a[href^="#"]');
links.forEach(link => {
link.addEventListener('click', function(e) {
e.preventDefault();
const targetId = this.getAttribute('href');
const targetElement = document.querySelector(targetId);
if (targetElement) {
targetElement.scrollIntoView({
behavior: 'smooth',
block: 'start'
});
}
});
});
});
</script>

</body>
</html>