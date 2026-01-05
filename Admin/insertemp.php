<?php
define('TITLE', 'Add Technician');
define('PAGE', 'technician');
include('includes/header.php'); 
include('../dbConnection.php');
session_start();
if(isset($_SESSION['is_adminlogin'])){
  $aEmail = $_SESSION['aEmail'];
} else {
  echo "<script> location.href='login.php'; </script>";
}

// Handle form submission
if(isset($_REQUEST['empsubmit'])){
  // Check for empty fields
  if(($_REQUEST['empName'] == "") || ($_REQUEST['empCity'] == "") || 
     ($_REQUEST['empMobile'] == "") || ($_REQUEST['empEmail'] == "") || 
     ($_REQUEST['empPassword'] == "")){
    $msg = '<div class="alert alert-warning mt-2" role="alert">Fill All Fields</div>';
  } else {
    $empName = $conn->real_escape_string($_REQUEST['empName']);
    $empCity = $conn->real_escape_string($_REQUEST['empCity']);
    $empMobile = $conn->real_escape_string($_REQUEST['empMobile']);
    $empEmail = $conn->real_escape_string($_REQUEST['empEmail']);
    $empPassword = $conn->real_escape_string($_REQUEST['empPassword']);
    
    // Check if email already exists
    $checkEmail = "SELECT empid FROM technician_tb WHERE empEmail = '$empEmail'";
    $emailResult = $conn->query($checkEmail);
    
    if($emailResult->num_rows > 0){
      $msg = '<div class="alert alert-warning mt-2" role="alert">Email already exists! Please use a different email.</div>';
    } else {
      // Insert technician with password
      $sql = "INSERT INTO technician_tb (empName, empCity, empMobile, empEmail, t_password) 
              VALUES ('$empName', '$empCity', '$empMobile', '$empEmail', '$empPassword')";
      
      if($conn->query($sql) == TRUE){
        $msg = '<div class="alert alert-success mt-2" role="alert">Technician Added Successfully</div>';
      } else {
        $msg = '<div class="alert alert-danger mt-2" role="alert">Unable to Add Technician</div>';
      }
    }
  }
}
?>

<div class="col-sm-9 col-md-10 mt-5">
  <form class="mx-5" action="" method="POST">
    <div class="form-group">
      <label for="empName">Name *</label>
      <input type="text" class="form-control" id="empName" name="empName" required>
    </div>
    
    <div class="form-group">
      <label for="empCity">City *</label>
      <input type="text" class="form-control" id="empCity" name="empCity" required>
    </div>
    
    <div class="form-group">
      <label for="empMobile">Mobile Number *</label>
      <input type="text" class="form-control" id="empMobile" name="empMobile" 
             pattern="[0-9]{10,15}" title="Please enter valid mobile number" required>
    </div>
    
    <div class="form-group">
      <label for="empEmail">Email *</label>
      <input type="email" class="form-control" id="empEmail" name="empEmail" required>
      <small class="form-text text-muted">This will be used for technician login</small>
    </div>
    
    <div class="form-group">
      <label for="empPassword">Password *</label>
      <input type="password" class="form-control" id="empPassword" name="empPassword" 
             minlength="6" required>
      <small class="form-text text-muted">Minimum 6 characters</small>
    </div>
    
    <button type="submit" class="btn btn-success" name="empsubmit">
      <i class="fas fa-user-plus"></i> Add Technician
    </button>
    <a href="technician.php" class="btn btn-secondary">
      <i class="fas fa-times"></i> Cancel
    </a>
    
    <?php if(isset($msg)) {echo $msg;} ?>
  </form>
</div>
</div>

<?php
include('includes/footer.php'); 
?>