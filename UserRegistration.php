<?php
include('dbConnection.php');

// Initialize message variable
$regmsg = '';

if (isset($_POST['rSignup'])) {
  // Trim user input to avoid whitespace issues
  $rFirstName = trim($_POST['rFirstName']);
  $rLastName = trim($_POST['rLastName']);
  $rEmail = trim($_POST['rEmail']);
  $rPassword = trim($_POST['rPassword']);
  
  // Combine first and last name
  $rName = $rFirstName . ' ' . $rLastName;
  
  // Validate empty fields
  if ($rFirstName == "" || $rLastName == "" || $rEmail == "" || $rPassword == "") {
    $regmsg = '<div class="alert alert-warning mt-3 text-center" role="alert">
                ⚠️ All fields are required!
              </div>';
  }

  elseif (!preg_match("/^[a-zA-Z]+( [a-zA-Z]+)*$/", $rFirstName)) {
    $regmsg = '<div class="alert alert-warning mt-3 text-center" role="alert">
                ⚠️ First name should only contain letters!
              </div>';
  }
  // Validate last name contains only letters
  elseif (!preg_match("/^[a-zA-Z]+$/", $rLastName)) {
    $regmsg = '<div class="alert alert-warning mt-3 text-center" role="alert">
                ⚠️ Last name should only contain letters!
              </div>';
  }
  // Validate email format
  elseif (!filter_var($rEmail, FILTER_VALIDATE_EMAIL)) {
    $regmsg = '<div class="alert alert-warning mt-3 text-center" role="alert">
                ⚠️ Please enter a valid email address!
              </div>';
  }
  // Additional email validation - must have proper domain with dot and at least 2 letter TLD
  elseif (!preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9-]+\.[a-zA-Z]{2,}$/", $rEmail)) {
    $regmsg = '<div class="alert alert-warning mt-3 text-center" role="alert">
                ⚠️ Please enter a valid email address with proper domain (e.g., user@example.com)!
              </div>';
  }
  // Validate password length
  elseif (strlen($rPassword) < 6) {
    $regmsg = '<div class="alert alert-warning mt-3 text-center" role="alert">
                ⚠️ Password must be at least 6 characters long!
              </div>';
  }
  else {
    // Check if email already exists
    $sql = "SELECT r_email FROM requesterlogin_tb WHERE r_email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $rEmail);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
      $regmsg = '<div class="alert alert-warning mt-3 text-center" role="alert">
                  ❗ Email already registered. Try another.
                </div>';
    } else {
      // Insert data securely
      $sql = "INSERT INTO requesterlogin_tb (r_name, r_email, r_password) VALUES (?, ?, ?)";
      $stmt = $conn->prepare($sql);
      $stmt->bind_param("sss", $rName, $rEmail, $rPassword);
      if ($stmt->execute()) {
        // Clear all session data
        if (session_status() === PHP_SESSION_ACTIVE) {
          session_unset();
          session_destroy();
        }
        
        $regmsg = '<div class="alert alert-success mt-3 text-center" role="alert">
                    ✅ Registration successful! Please log in to continue.
                  </div>';
        // Use meta refresh to show message for 3 seconds then redirect
        echo '<meta http-equiv="refresh" content="3;url=index.php#login">';
      } else {
        $regmsg = '<div class="alert alert-danger mt-3 text-center" role="alert">
                    ❌ Unable to create account. Please try again.
                  </div>';
      }
    }
    $stmt->close();
  }
}
?>
<!-- ===== Signup Form UI ===== -->
<div class="container py-5" id="registration">
  <div class="row justify-content-center">
    <div class="col-lg-6 col-md-8">
      <div class="card shadow-lg border-0 rounded-4">
        <div class="card-body p-5">
          <h2 class="text-center mb-4 text-danger fw-bold">
            <i class="fas fa-user-plus"></i> Create an Account
          </h2>
          <form action="" method="POST" id="registrationForm">
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="rFirstName" class="form-label fw-semibold">
                  <i class="fas fa-user me-2"></i>First Name
                </label>
                <input type="text" class="form-control" name="rFirstName" id="rFirstName" placeholder="First name" value="<?php echo isset($_POST['rFirstName']) ? htmlspecialchars($_POST['rFirstName']) : ''; ?>" required />
              </div>
              <div class="col-md-6 mb-3">
                <label for="rLastName" class="form-label fw-semibold">
                  <i class="fas fa-user me-2"></i>Last Name
                </label>
                <input type="text" class="form-control" name="rLastName" id="rLastName" placeholder="Last name" value="<?php echo isset($_POST['rLastName']) ? htmlspecialchars($_POST['rLastName']) : ''; ?>" required />
              </div>
            </div>
            <div class="mb-3">
              <label for="rEmail" class="form-label fw-semibold">
                <i class="fas fa-envelope me-2"></i>Email
              </label>
              <input type="email" class="form-control" name="rEmail" id="rEmail" placeholder="user@example.com" value="<?php echo isset($_POST['rEmail']) ? htmlspecialchars($_POST['rEmail']) : ''; ?>" required pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9-]+\.[a-zA-Z]{2,}" title="Please enter a valid email address" />
              <small class="text-muted">Must be a valid email (e.g., user@example.com)</small>
            </div>
            <div class="mb-4">
              <label for="rPassword" class="form-label fw-semibold">
                <i class="fas fa-lock me-2"></i>Password
              </label>
              <input type="password" class="form-control" name="rPassword" id="rPassword" placeholder="At least 6 characters" minlength="6" required />
              <small class="text-muted">Password must be at least 6 characters long</small>
            </div>
            <button type="submit" class="btn btn-danger w-100 py-2 fw-bold shadow-sm" name="rSignup">
              <i class="fas fa-user-check me-2"></i>Sign Up
            </button>
            <p class="text-center mt-3 small text-muted">
              By clicking <strong>Sign Up</strong>, you agree to our <br>
              <a href="#" class="text-decoration-none">Terms</a>, 
              <a href="#" class="text-decoration-none">Data Policy</a>, and 
              <a href="#" class="text-decoration-none">Cookie Policy</a>.
            </p>
            <?php if (isset($regmsg) && $regmsg != '') echo $regmsg; ?>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
// Clear form resubmission on page refresh
if (window.history.replaceState) {
  window.history.replaceState(null, null, window.location.href);
}

// Auto-hide alert messages after 5 seconds
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

<!-- Bootstrap & FontAwesome (for icons) -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://kit.fontawesome.com/a2d9d5c6b1.js" crossorigin="anonymous"></script>