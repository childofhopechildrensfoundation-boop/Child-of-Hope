<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Options | Child of Hope Children's Foundation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Poppins', sans-serif; }
        body { background: #f8fafc; }
        .card { border-radius: 14px; box-shadow: 0 8px 24px rgba(0,0,0,0.08); }
        .btn-primary { background: linear-gradient(135deg, #F7941D, #E67E22); border: none; }
        
        /* Navigation */
        .navbar { background: white; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        .navbar-brand { font-weight: 700; font-size: 1.3rem; }
        
        /* Hero Section */
        .hero {
            background: linear-gradient(135deg, rgba(247, 148, 29, 0.85) 0%, rgba(230, 126, 34, 0.85) 100%), url('images/donate-meal.jpg') center/cover;
            padding: 120px 0 80px;
            color: white;
            text-align: center;
        }
        
        .hero h1 {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 1rem;
        }
        
        .hero p {
            font-size: 1.2rem;
            opacity: 0.95;
            line-height: 1.8;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container-fluid px-4">
            <a class="navbar-brand" href="index.html">
                <span style="margin-right: 0.5rem;">🤝</span> Child of Hope
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.html">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="about.html">About</a></li>
                    <li class="nav-item"><a class="nav-link" href="causes.html">Programs</a></li>
                    <li class="nav-item"><a class="nav-link" href="contact.html">Contact</a></li>
                    <li class="nav-item ms-2">
                        <a class="btn btn-primary" href="donate.html" style="color: white; font-weight: 600;">
                            <i class="fas fa-heart"></i> Donate
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero" style="margin-top: 60px;">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8 mx-auto">
                    <h1><i class="fas fa-money-bill-wave me-3"></i>Multiple Ways to Give</h1>
                    <p>Choose the payment method that works best for you. Every donation directly helps vulnerable children in Uganda.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card p-4 p-md-5">
                    <h1 class="mb-3" style="color: #0F5DA6;">Donation Payment Options</h1>
                    <p class="text-muted mb-4">Thank you for supporting Child of Hope Children's Foundation. You can donate through any of the options below.</p>

                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="card h-100 border-0" style="background: #F8FAFC;">
                                <div class="card-body">
                                    <h5 class="card-title"><i class="fas fa-mobile-alt me-2 text-primary"></i>WorldRemit</h5>
                                    <p class="card-text">Send money from anywhere in the world to our Uganda mobile account.</p>
                                    <p class="mb-2"><strong>Recipient Phone:</strong> +256 731 751 309</p>
                                    <p class="mb-2"><strong>Recipient Name:</strong> Bwira Moses</p>
                                    <p class="mb-3" style="font-size: 0.9rem; color: #666;"><em>Available worldwide • Funds arrive to Uganda mobile wallet</em></p>
                                    <a href="https://www.worldremit.com/" target="_blank" class="btn btn-primary mt-2">Open WorldRemit</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card h-100 border-0" style="background: #F8FAFC;">
                                <div class="card-body">
                                    <h5 class="card-title"><i class="fas fa-paper-plane me-2 text-warning"></i>SendWave</h5>
                                    <p class="card-text">Send money from anywhere in the world to our Uganda mobile account.</p>
                                    <p class="mb-2"><strong>Recipient Phone:</strong> +256 731 751 309</p>
                                    <p class="mb-2"><strong>Recipient Name:</strong> Bwira Moses</p>
                                    <p class="mb-3" style="font-size: 0.9rem; color: #666;"><em>Available worldwide • Funds arrive to Uganda mobile wallet</em></p>
                                    <a href="https://www.sendwave.com/" target="_blank" class="btn btn-primary mt-2">Open SendWave</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-4 border-0" style="background: linear-gradient(135deg, #0F5DA6, #2980B9); color: white;">
                        <div class="card-body">
                            <h5 class="card-title"><i class="fas fa-university me-2"></i>Bank Transfer</h5>
                            <p class="card-text">Please email us at <a href="mailto:childofhopechildrensfoundation@gmail.com" style="color: #fff; text-decoration: underline;">childofhopechildrensfoundation@gmail.com</a> to receive the bank account details securely after your request.</p>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <a href="donate.html" class="btn btn-outline-secondary">Back to Donate</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
