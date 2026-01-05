<?php
include('../dbConnection.php');
session_start();

// Redirect if already logged in
if(isset($_SESSION['is_tech_login'])){
  header("Location: TechDashboard.php");
  exit();
}

$msg = '';

if(isset($_POST['tEmail'])){
  $tEmail = mysqli_real_escape_string($conn, trim($_POST['tEmail']));
  $tPassword = mysqli_real_escape_string($conn, trim($_POST['tPassword']));
  
  if(empty($tEmail) || empty($tPassword)){
    $msg = '<div class="alert alert-warning mt-2" role="alert">Please fill all fields</div>';
  } else {
    // Prepared statement to prevent SQL injection
    $sql = "SELECT * FROM technician_tb WHERE empEmail = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $tEmail);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows == 1){
      $row = $result->fetch_assoc();
      
      // Simple password check
      if($tPassword === $row['t_password']){
        $_SESSION['is_tech_login'] = true;
        $_SESSION['tEmail'] = $tEmail;
        $_SESSION['tId'] = $row['empid'];
        $_SESSION['tName'] = $row['empName'];
        
        echo "<script> location.href='TechDashboard.php'; </script>";
        exit();
      } else {
        $msg = '<div class="alert alert-warning mt-2" role="alert">Enter Valid Email and Password</div>';
      }
    } else {
      $msg = '<div class="alert alert-warning mt-2" role="alert">Enter Valid Email and Password</div>';
    }
    $stmt->close();
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <link rel="stylesheet" href="../css/bootstrap.min.css">
  <link rel="stylesheet" href="../css/all.min.css">
  <style>
    .custom-margin {
      margin-top: 8vh;
    }
  </style>
  <title>Technician Login</title>
</head>
<body>
  <div class="mb-3 text-center mt-5" style="font-size: 30px;">
    <i class="fas fa-stethoscope"></i>
    <span>Online Service Management System</span>
  </div>
  <p class="text-center" style="font-size: 20px;">
    <i class="fas fa-user-cog text-dark"></i> 
    <span>Technician Area</span>
  </p>
  <div class="container-fluid mb-5">
    <div class="row justify-content-center custom-margin">
      <div class="col-sm-6 col-md-4">
        <form action="" class="shadow-lg p-4" method="POST">
          <div class="form-group">
            <i class="fas fa-user"></i>
            <label for="email" class="pl-2 font-weight-bold">Email</label>
            <input type="email" class="form-control" placeholder="Email" name="tEmail" required>
          </div>
          <div class="form-group">
            <i class="fas fa-key"></i>
            <label for="pass" class="pl-2 font-weight-bold">Password</label>
            <input type="password" class="form-control" placeholder="Password" name="tPassword" required>
          </div>
          <button type="submit" class="btn btn-outline-dark mt-3 btn-block shadow-sm font-weight-bold">Login</button>
          <?php if(isset($msg)) {echo $msg;} ?>
        </form>
        <div class="text-center">
          <a class="btn btn-info mt-3 shadow-sm font-weight-bold" href="../index.php">Back to Home</a>
        </div>
      </div>
    </div>
  </div>
  <script src="../js/jquery.min.js"></script>
  <script src="../js/popper.min.js"></script>
  <script src="../js/bootstrap.min.js"></script>
  <script src="../js/all.min.js"></script>
</body>
</html>

<?php $conn->close(); ?>