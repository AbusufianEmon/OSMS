<?php
define('TITLE', 'Requests');
define('PAGE', 'request');
include('includes/header.php'); 
include('../dbConnection.php');
session_start();
 if(isset($_SESSION['is_adminlogin'])){
  $aEmail = $_SESSION['aEmail'];
 } else {
  echo "<script> location.href='login.php'; </script>";
 }

// Get current date
$currentDate = date('Y-m-d');
?>
<div class="col-sm-9 col-md-10">
  <div class="row">
<?php 
 // Fetch all requests from submitrequest_tb
 $sql = "SELECT sr.*, aw.assign_tech, aw.assign_date, aw.tech_status 
         FROM submitrequest_tb sr
         LEFT JOIN assignwork_tb aw ON sr.request_id = aw.request_id
         ORDER BY sr.request_id DESC";
 $result = $conn->query($sql);
 
 if($result->num_rows > 0){
  while($row = $result->fetch_assoc()){
   // Skip if already completed
   if($row['tech_status'] == 'completed'){
     continue;
   }
   
   echo '<div class="col-sm-6 col-md-4 mb-4">';
   echo '<div class="card">';
   
   // Change header color based on status
   $headerClass = 'bg-primary';
   if($row['tech_status'] == 'rejected'){
     $headerClass = 'bg-danger';
   } elseif($row['tech_status'] == 'accepted'){
     $headerClass = 'bg-success';
   } elseif($row['tech_status'] == 'pending'){
     $headerClass = 'bg-info';
   }
   
   echo '<div class="card-header '.$headerClass.' text-white">';
   echo '<strong>Request ID: '. $row['request_id'] . '</strong>';
   echo '</div>';
   echo '<div class="card-body">';
   echo '<h6 class="card-title"><strong>Issue:</strong> ' . htmlspecialchars($row['request_info']) . '</h6>';
   echo '<p class="card-text"><small>' . htmlspecialchars(substr($row['request_desc'], 0, 80)) . '...</small></p>';
   
   echo '<hr>';
   echo '<p class="mb-1"><strong>Requester:</strong> ' . htmlspecialchars($row['requester_name']) . '</p>';
   echo '<p class="mb-1"><strong>Email:</strong> ' . htmlspecialchars($row['requester_email']) . '</p>';
   echo '<p class="mb-1"><strong>Mobile:</strong> ' . htmlspecialchars($row['requester_mobile']) . '</p>';
   echo '<p class="mb-1"><strong>Location:</strong> ' . htmlspecialchars($row['requester_city']) . ', ' . htmlspecialchars($row['requester_state']) . '</p>';
   
   echo '<hr>';
   
   // Check if already assigned
   if(!empty($row['assign_tech'])){
     $alertClass = 'alert-info';
     $statusText = $row['tech_status'] ?: 'pending';
     
     if($row['tech_status'] == 'rejected'){
       $alertClass = 'alert-danger';
       $statusText = 'REJECTED - Needs Reassignment';
     } elseif($row['tech_status'] == 'accepted'){
       $alertClass = 'alert-success';
       $statusText = 'Accepted - In Progress';
     } elseif($row['tech_status'] == 'pending'){
       $alertClass = 'alert-warning';
       $statusText = 'Pending Technician Response';
     }
     
     echo '<div class="alert '.$alertClass.' p-2 mb-2">';
     echo '<small><strong>Assigned to:</strong> ' . htmlspecialchars($row['assign_tech']) . '</small><br>';
     echo '<small><strong>Assignment Date:</strong> ' . htmlspecialchars($row['assign_date']) . '</small><br>';
     echo '<small><strong>Status:</strong> <strong>' . htmlspecialchars($statusText) . '</strong></small>';
     echo '</div>';
   }
   
   // Check if request is accepted - if yes, disable reassignment
   $isAccepted = ($row['tech_status'] == 'accepted');
   
   if($isAccepted){
     // Show read-only information for accepted requests
     echo '<div class="alert alert-info p-2 mb-2">';
     echo '<i class="fas fa-info-circle"></i> <small>This request has been accepted by the technician and cannot be reassigned.</small>';
     echo '</div>';
     
     echo '<div class="d-flex justify-content-between">';
     echo '<a href="requestdetails.php?id='.$row['request_id'].'" class="btn btn-info btn-sm">View Details</a>';
     echo '</div>';
   } else {
     // Show assignment form for non-accepted requests
     // Fetch technicians for dropdown
     $techSql = "SELECT empName FROM technician_tb";
     $techResult = $conn->query($techSql);
     
     echo '<form action="" method="POST" class="mt-2">';
     echo '<input type="hidden" name="request_id" value="'. $row['request_id'] .'">';
     
     // Technician dropdown
     echo '<div class="form-group">';
     echo '<label for="technician_'.$row['request_id'].'"><small>';
     echo ($row['tech_status'] == 'rejected') ? 'Reassign to:' : 'Assign Technician:';
     echo '</small></label>';
     echo '<select name="technician" id="technician_'.$row['request_id'].'" class="form-control form-control-sm" required>';
     echo '<option value="">Select Technician</option>';
     if($techResult->num_rows > 0){
       while($techRow = $techResult->fetch_assoc()){
         $selected = ($row['assign_tech'] == $techRow['empName']) ? 'selected' : '';
         echo '<option value="'. htmlspecialchars($techRow['empName']) .'" '.$selected.'>'. htmlspecialchars($techRow['empName']) .'</option>';
       }
     }
     echo '</select>';
     echo '</div>';
     
     // Assignment date input
     echo '<div class="form-group">';
     echo '<label for="assign_date_'.$row['request_id'].'"><small>Assignment Date:</small></label>';
     $assignDateValue = (!empty($row['assign_date'])) ? $row['assign_date'] : $currentDate;
     echo '<input type="date" name="assign_date" id="assign_date_'.$row['request_id'].'" class="form-control form-control-sm" min="'.$currentDate.'" value="'.$assignDateValue.'" required>';
     echo '</div>';
     
     echo '<div class="d-flex justify-content-between">';
     $buttonText = ($row['tech_status'] == 'rejected') ? 'Reassign Work' : 'Assign Work';
     echo '<button type="submit" name="assign" class="btn btn-success btn-sm">'.$buttonText.'</button>';
     echo '<a href="requestdetails.php?id='.$row['request_id'].'" class="btn btn-info btn-sm">View Details</a>';
     echo '</div>';
     echo '</form>';
   }
   
   echo '</div>'; // card-body
   echo '</div>'; // card
   echo '</div>'; // col
  }
 } else {
  echo '<div class="col-12">';
  echo '<div class="alert alert-success mt-5" role="alert">';
  echo '<h4 class="alert-heading">Well done!</h4>';
  echo '<p>Aww yeah, you successfully processed all Requests.</p>';
  echo '<hr>';
  echo '<h5 class="mb-0">No Pending Requests</h5>';
  echo '</div>';
  echo '</div>';
 }
?>
  </div>
</div>

<?php
// Handle assignment
if(isset($_POST['assign']) && isset($_POST['technician']) && isset($_POST['request_id']) && isset($_POST['assign_date'])){
  $techName = $conn->real_escape_string($_POST['technician']);
  $requestId = (int)$_POST['request_id'];
  $assignDate = $conn->real_escape_string($_POST['assign_date']);
  
  // Check if request is already accepted - prevent reassignment
  $statusCheckSql = "SELECT tech_status FROM assignwork_tb WHERE request_id = $requestId";
  $statusCheckResult = $conn->query($statusCheckSql);
  
  if($statusCheckResult && $statusCheckResult->num_rows > 0){
    $statusRow = $statusCheckResult->fetch_assoc();
    if($statusRow['tech_status'] == 'accepted'){
      echo '<script>
              alert("Cannot reassign! This request has already been accepted by the technician.");
              window.location.href="request.php";
            </script>';
      exit();
    }
  }
  
  // Validate assignment date
  if($assignDate >= $currentDate){
    // Check if already exists in assignwork_tb
    $checkSql = "SELECT rno FROM assignwork_tb WHERE request_id = $requestId";
    $checkResult = $conn->query($checkSql);
    
    if($checkResult->num_rows > 0){
      // Update existing assignment (reset to pending for reassignment)
      $updateSql = "UPDATE assignwork_tb 
                   SET assign_tech = '$techName',
                       assign_date = '$assignDate',
                       tech_status = 'pending'
                   WHERE request_id = $requestId";
      
      if($conn->query($updateSql) === TRUE){
        echo '<script>
                alert("Work reassigned successfully to ' . $techName . '!");
                window.location.href="request.php";
              </script>';
      } else {
        echo '<script>alert("Unable to reassign work: ' . $conn->error . '");</script>';
      }
    } else {
      // Get request details from submitrequest_tb
      $reqSql = "SELECT * FROM submitrequest_tb WHERE request_id = $requestId";
      $reqResult = $conn->query($reqSql);
      
      if($reqResult->num_rows > 0){
        $reqData = $reqResult->fetch_assoc();
        
        // Insert new assignment into assignwork_tb
        $insertSql = "INSERT INTO assignwork_tb 
                     (request_id, request_info, request_desc, requester_name, 
                      requester_add1, requester_add2, requester_city, requester_state, 
                      requester_zip, requester_email, requester_mobile, 
                      assign_tech, assign_date, tech_status) 
                     VALUES 
                     ($requestId, 
                      '".$conn->real_escape_string($reqData['request_info'])."',
                      '".$conn->real_escape_string($reqData['request_desc'])."',
                      '".$conn->real_escape_string($reqData['requester_name'])."',
                      '".$conn->real_escape_string($reqData['requester_add1'])."',
                      '".$conn->real_escape_string($reqData['requester_add2'])."',
                      '".$conn->real_escape_string($reqData['requester_city'])."',
                      '".$conn->real_escape_string($reqData['requester_state'])."',
                      ".$reqData['requester_zip'].",
                      '".$conn->real_escape_string($reqData['requester_email'])."',
                      ".$reqData['requester_mobile'].",
                      '$techName',
                      '$assignDate',
                      'pending')";
        
        if($conn->query($insertSql) === TRUE){
          echo '<script>
                  alert("Work assigned successfully to ' . $techName . '!");
                  window.location.href="request.php";
                </script>';
        } else {
          echo '<script>alert("Unable to assign work: ' . $conn->error . '");</script>';
        }
      }
    }
  } else {
    echo '<script>alert("Assignment date must be current or future date!");</script>';
  }
}

// Handle delete
if(isset($_POST['delete']) && isset($_POST['request_id'])){
  $requestId = (int)$_POST['request_id'];
  
  // Delete from assignwork_tb first (if exists)
  $deleteAssignSql = "DELETE FROM assignwork_tb WHERE request_id = $requestId";
  $conn->query($deleteAssignSql);
  
  // Delete from submitrequest_tb
  $deleteSubmitSql = "DELETE FROM submitrequest_tb WHERE request_id = $requestId";
  
  if($conn->query($deleteSubmitSql) === TRUE){
    echo '<script>
            alert("Request deleted successfully!");
            window.location.href="request.php";
          </script>';
  } else {
    echo '<script>alert("Unable to delete request: ' . $conn->error . '");</script>';
  }
}

include('includes/footer.php'); 
$conn->close();
?>