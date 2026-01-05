<?php
define('TITLE', 'Edit Technician');
define('PAGE', 'technician');
include('includes/header.php'); 
include('../dbConnection.php');
session_start();
if(isset($_SESSION['is_adminlogin'])){
  $aEmail = $_SESSION['aEmail'];
} else {
  echo "<script> location.href='login.php'; </script>";
}

// Handle update
if(isset($_REQUEST['empupdate'])){
  // Check for empty fields
  if(($_REQUEST['empName'] == "") || ($_REQUEST['empCity'] == "") || 
     ($_REQUEST['empMobile'] == "") || ($_REQUEST['empEmail'] == "")){
    $msg = '<div class="alert alert-warning mt-2" role="alert">Fill All Fields</div>';
  } else {
    $empId = (int)$_REQUEST['empId'];
    $empName = $conn->real_escape_string($_REQUEST['empName']);
    $empCity = $conn->real_escape_string($_REQUEST['empCity']);
    $empMobile = $conn->real_escape_string($_REQUEST['empMobile']);
    $empEmail = $conn->real_escape_string($_REQUEST['empEmail']);
    
    // Check if password is being updated
    if(!empty($_REQUEST['empPassword'])){
      $empPassword = $conn->real_escape_string($_REQUEST['empPassword']);
      $sql = "UPDATE technician_tb SET 
              empName = '$empName', 
              empCity = '$empCity', 
              empMobile = '$empMobile', 
              empEmail = '$empEmail',
              t_password = '$empPassword'
              WHERE empid = $empId";
    } else {
      // Update without changing password
      $sql = "UPDATE technician_tb SET 
              empName = '$empName', 
              empCity = '$empCity', 
              empMobile = '$empMobile', 
              empEmail = '$empEmail'
              WHERE empid = $empId";
    }
    
    if($conn->query($sql) == TRUE){
      $msg = '<div class="alert alert-success mt-2" role="alert">Updated Successfully</div>';
    } else {
      $msg = '<div class="alert alert-danger mt-2" role="alert">Unable to Update</div>';
    }
  }
}

// Fetch technician data
if(isset($_REQUEST['view'])){
  $sql = "SELECT * FROM technician_tb WHERE empid = {$_REQUEST['id']}";
  $result = $conn->query($sql);
  $row = $result->fetch_assoc();
}
?>

<div class="col-sm-9 col-md-10 mt-5">
  <form class="mx-5" action="" method="POST">
    <div class="form-group">
      <label for="empId">Emp ID</label>
      <input type="text" class="form-control" id="empId" name="empId" 
             value="<?php if(isset($row['empid'])) {echo $row['empid'];} ?>" readonly>
    </div>
    
    <div class="form-group">
      <label for="empName">Name *</label>
      <input type="text" class="form-control" id="empName" name="empName" 
             value="<?php if(isset($row['empName'])) {echo $row['empName'];} ?>" required>
    </div>
    
    <div class="form-group">
      <label for="empCity">City *</label>
      <input type="text" class="form-control" id="empCity" name="empCity" 
             value="<?php if(isset($row['empCity'])) {echo $row['empCity'];} ?>" required>
    </div>
    
    <div class="form-group">
      <label for="empMobile">Mobile Number *</label>
      <input type="text" class="form-control" id="empMobile" name="empMobile" 
             value="<?php if(isset($row['empMobile'])) {echo $row['empMobile'];} ?>" 
             pattern="[0-9]{10,15}" required>
    </div>
    
    <div class="form-group">
      <label for="empEmail">Email *</label>
      <input type="email" class="form-control" id="empEmail" name="empEmail" 
             value="<?php if(isset($row['empEmail'])) {echo $row['empEmail'];} ?>" required>
    </div>
    
    <div class="form-group">
      <label for="empPassword">Password</label>
      <input type="password" class="form-control" id="empPassword" name="empPassword" 
             minlength="6" placeholder="Leave blank to keep current password">
      <small class="form-text text-muted">
        Only fill this if you want to change the password (minimum 6 characters)
      </small>
    </div>
    
    <button type="submit" class="btn btn-success" name="empupdate">
      <i class="fas fa-save"></i> Update
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