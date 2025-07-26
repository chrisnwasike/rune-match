<?php
// index.php - Main entry point
    header('Location: home.php');
    exit;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rune Match - Nordic Puzzle Game</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 text-center">
                <h1 class="display-4 mb-4">Rune Match</h1>
                <p class="lead mb-5">A Nordic-themed puzzle game to earn resources for your garden</p>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="card bg-dark border-light mb-4">
                            <div class="card-header">Login</div>
                            <div class="card-body">
                                <form id="login-form" method="post">
                                    <div class="mb-3">
                                        <input type="text" class="form-control" id="login-username" placeholder="Username" required>
                                    </div>
                                    <div class="mb-3">
                                        <input type="password" class="form-control" id="login-password" placeholder="Password" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100">Login</button>
                                </form>
                                <div id="login-message" class="mt-3 text-danger"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card bg-dark border-light">
                            <div class="card-header">Register</div>
                            <div class="card-body">
                                <form id="register-form" method="post">
                                    <div class="mb-3">
                                        <input type="text" class="form-control" id="register-username" placeholder="Username" required>
                                    </div>
                                    <div class="mb-3">
                                        <input type="email" class="form-control" id="register-email" placeholder="Email" required>
                                    </div>
                                    <div class="mb-3">
                                        <input type="password" class="form-control" id="register-password" placeholder="Password" required>
                                    </div>
                                    <button type="submit" class="btn btn-success w-100">Register</button>
                                </form>
                                <div id="register-message" class="mt-3 text-danger"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/api.js"></script>
    <script src="assets/js/auth.js"></script>
</body>
</html>
