<?php
define('TITLE', 'Requester Profile');
define('PAGE', 'RequesterProfile');
include('includes/header.php'); 
include('../dbConnection.php');
 session_start();
 if($_SESSION['is_login']){
  $rEmail = $_SESSION['rEmail'];
 } else {
  echo "<script> location.href='RequesterLogin.php'; </script>";
 }

 $sql = "SELECT * FROM requesterlogin_tb WHERE r_email='$rEmail'";
 $result = $conn->query($sql);
 if($result->num_rows == 1){
 $row = $result->fetch_assoc();
 $rName = $row["r_name"]; }

 if(isset($_REQUEST['nameupdate'])){
  if(($_REQUEST['rName'] == "")){
  
   $passmsg = '<div class="alert alert-warning col-sm-6 ml-5 mt-2" role="alert"> Fill All Fileds </div>';
  } else {
   $rName = $_REQUEST["rName"];
   $sql = "UPDATE requesterlogin_tb SET r_name = '$rName' WHERE r_email = '$rEmail'";
   if($conn->query($sql) == TRUE){
 
   $passmsg = '<div class="alert alert-success col-sm-6 ml-5 mt-2" role="alert"> Updated Successfully </div>';
   } else {

   $passmsg = '<div class="alert alert-danger col-sm-6 ml-5 mt-2" role="alert"> Unable to Update </div>';
      }
    }
   }
?>
<div class="col-sm-8 mt-5">

  <h3 class="mb-3">Welcome, <?php echo $rName; ?> 👋</h3>
  <p class="text-muted">
    This is your Requester Profile panel.  
    You can update your details, view your service requests, or create a new service request anytime.
  </p>

  <div class="card shadow-sm mb-4">
    <div class="card-header bg-dark text-white">
      Your Profile Information
    </div>
    <div class="card-body">
      <p><strong>Email:</strong> <?php echo $rEmail; ?></p>
      <p><strong>Name:</strong> <?php echo $rName; ?></p>
    </div>
  </div>

  <hr class="my-4">


  <div class="list-group">
    <a href="SubmitRequest.php" class="list-group-item list-group-item-action">
      ➤ Raise a New Service Request
    </a>
    <a href="CheckStatus.php" class="list-group-item list-group-item-action">
      ➤ Check Your Service Status
    </a>
    <a href="Requesterchangepass.php" class="list-group-item list-group-item-action">
      ➤ Change your password
    </a>
  </div>

</div>

<?php
include('includes/footer.php'); 
?>