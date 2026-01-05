<?php
define('TITLE', 'Request Details');
define('PAGE', 'request');
include('includes/header.php'); 
include('../dbConnection.php');
session_start();

if(isset($_SESSION['is_adminlogin'])){
  $aEmail = $_SESSION['aEmail'];
} else {
  echo "<script> location.href='login.php'; </script>";
  exit();
}

// Get current date
$currentDate = date('Y-m-d');

// Check if request ID is provided
if(!isset($_GET['id']) || empty($_GET['id'])){
  echo "<script> alert('Invalid Request ID'); location.href='request.php'; </script>";
  exit();
}

$requestId = (int)$_GET['id'];

// Fetch request details with assignment info
$sql = "SELECT sr.*, aw.assign_tech, aw.assign_date, aw.tech_status 
        FROM submitrequest_tb sr
        LEFT JOIN assignwork_tb aw ON sr.request_id = aw.request_id
        WHERE sr.request_id = $requestId";
$result = $conn->query($sql);

if($result->num_rows == 0){
  echo "<script> alert('Request not found'); location.href='request.php'; </script>";
  exit();
}

$row = $result->fetch_assoc();
?>

<div class="col-sm-10 mt-5">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <?php
        // Change header color based on status
        $headerClass = 'bg-primary';
        if($row['tech_status'] == 'rejected'){
          $headerClass = 'bg-danger';
        } elseif($row['tech_status'] == 'accepted'){
          $headerClass = 'bg-success';
        } elseif($row['tech_status'] == 'pending'){
          $headerClass = 'bg-info';
        } elseif($row['tech_status'] == 'completed'){
          $headerClass = 'bg-success';
        }
        ?>
        
        <div class="card-header <?php echo $headerClass; ?> text-white d-flex justify-content-between align-items-center">
          <h5 class="mb-0">Request Details - ID: <?php echo $row['request_id']; ?></h5>
          <a href="request.php" class="btn btn-light btn-sm">Back to Requests</a>
        </div>
        <div class="card-body">
          
          <!-- Request Information -->
          <h6 class="border-bottom pb-2 mb-3">Request Information</h6>
          <div class="row mb-3">
            <div class="col-md-6">
              <p><strong>Request ID:</strong> <?php echo $row['request_id']; ?></p>
              <p><strong>Request Info:</strong> <?php echo htmlspecialchars($row['request_info']); ?></p>
              <p><strong>Request Description:</strong><br>
                <?php echo nl2br(htmlspecialchars($row['request_desc'])); ?>
              </p>
              <p><strong>Request Date:</strong> <?php echo date('M d, Y', strtotime($row['request_date'])); ?></p>
            </div>
            <div class="col-md-6">
              <?php if(!empty($row['assign_tech'])): ?>
                <?php
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
                } elseif($row['tech_status'] == 'completed'){
                  $alertClass = 'alert-success';
                  $statusText = 'Completed';
                }
                ?>
                <div class="alert <?php echo $alertClass; ?>">
                  <p class="mb-1"><strong>Assigned Technician:</strong> <?php echo htmlspecialchars($row['assign_tech']); ?></p>
                  <p class="mb-1"><strong>Assignment Date:</strong> <?php echo htmlspecialchars($row['assign_date']); ?></p>
                  <p class="mb-0"><strong>Status:</strong> 
                    <span class="badge badge-<?php echo ($row['tech_status'] == 'completed') ? 'success' : (($row['tech_status'] == 'rejected') ? 'danger' : 'warning'); ?>">
                      <?php echo strtoupper(htmlspecialchars($statusText)); ?>
                    </span>
                  </p>
                </div>
              <?php else: ?>
                <div class="alert alert-warning">
                  <p class="mb-0"><strong>Status:</strong> Not Assigned Yet</p>
                </div>
              <?php endif; ?>
            </div>
          </div>

          <!-- Requester Information -->
          <h6 class="border-bottom pb-2 mb-3">Requester Information</h6>
          <div class="row mb-3">
            <div class="col-md-6">
              <p><strong>Name:</strong> <?php echo htmlspecialchars($row['requester_name']); ?></p>
              <p><strong>Email:</strong> <?php echo htmlspecialchars($row['requester_email']); ?></p>
              <p><strong>Mobile:</strong> <?php echo htmlspecialchars($row['requester_mobile']); ?></p>
            </div>
            <div class="col-md-6">
              <p><strong>Address:</strong><br>
                <?php echo htmlspecialchars($row['requester_add1']); ?><br>
                <?php echo htmlspecialchars($row['requester_add2']); ?><br>
                <?php echo htmlspecialchars($row['requester_city']); ?>, 
                <?php echo htmlspecialchars($row['requester_state']); ?> 
                <?php echo htmlspecialchars($row['requester_zip']); ?>
              </p>
            </div>
          </div>

          <?php
          // Check if request is accepted or completed
          $isAccepted = ($row['tech_status'] == 'accepted');
          $isCompleted = ($row['tech_status'] == 'completed');
          ?>

          <!-- Assignment Form or Completed Message -->
          <?php if($isCompleted): ?>
            <div class="alert alert-success">
              <h6><i class="fas fa-check-circle"></i> This request has been completed!</h6>
              <form action="" method="POST" class="mt-3">
                <input type="hidden" name="request_id" value="<?php echo $row['request_id']; ?>">
                <button type="submit" name="delete" class="btn btn-danger btn-sm" 
                        onclick="return confirm('Are you sure you want to delete this completed request?');">
                  <i class="fas fa-trash"></i> Delete Request
                </button>
              </form>
            </div>
          <?php elseif($isAccepted): ?>
            <h6 class="border-bottom pb-2 mb-3">Assignment Status</h6>
            <div class="alert alert-info">
              <p><i class="fas fa-info-circle"></i> <strong>This request has been accepted by the technician and cannot be reassigned.</strong></p>
              <p class="mb-0">The technician is currently working on this request. You can delete it if needed.</p>
            </div>
            <form action="" method="POST">
              <input type="hidden" name="request_id" value="<?php echo $row['request_id']; ?>">
              <button type="submit" name="delete" class="btn btn-danger" 
                      onclick="return confirm('Are you sure you want to delete this request?');">
                <i class="fas fa-trash"></i> Delete Request
              </button>
            </form>
          <?php else: ?>
            <!-- Assignment Form for non-accepted requests -->
            <h6 class="border-bottom pb-2 mb-3">
              <?php echo ($row['tech_status'] == 'rejected') ? 'Reassign Work' : 'Assign Work'; ?>
            </h6>
            <form action="" method="POST">
              <input type="hidden" name="request_id" value="<?php echo $row['request_id']; ?>">
              
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="technician">Select Technician *</label>
                    <select name="technician" id="technician" class="form-control" required>
                      <option value="">-- Select Technician --</option>
                      <?php
                      $techSql = "SELECT empName FROM technician_tb";
                      $techResult = $conn->query($techSql);
                      if($techResult->num_rows > 0){
                        while($techRow = $techResult->fetch_assoc()){
                          $selected = ($row['assign_tech'] == $techRow['empName']) ? 'selected' : '';
                          echo '<option value="'. htmlspecialchars($techRow['empName']) .'" '.$selected.'>'. htmlspecialchars($techRow['empName']) .'</option>';
                        }
                      }
                      ?>
                    </select>
                  </div>
                </div>
                
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="assign_date">Assignment Date *</label>
                    <?php 
                    $assignDateValue = (!empty($row['assign_date'])) ? $row['assign_date'] : $currentDate;
                    ?>
                    <input type="date" name="assign_date" id="assign_date" class="form-control" 
                           min="<?php echo $currentDate; ?>" value="<?php echo $assignDateValue; ?>" required>
                    <small class="form-text text-muted">Assignment date must be current or future date</small>
                  </div>
                </div>
              </div>
              
              <div class="form-group">
                <button type="submit" name="assign" class="btn btn-success">
                  <i class="fas fa-user-check"></i> <?php echo ($row['tech_status'] == 'rejected') ? 'Reassign Work' : 'Assign Work'; ?>
                </button>
                
                <button type="submit" name="delete" class="btn btn-danger" 
                        onclick="return confirm('Are you sure you want to delete this request?');">
                  <i class="fas fa-trash"></i> Delete Request
                </button>
              </div>
            </form>
          <?php endif; ?>

        </div>
      </div>
    </div>
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
              window.location.href="requestdetails.php?id=' . $requestId . '";
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
                window.location.href="requestdetails.php?id=' . $requestId . '";
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
                  window.location.href="requestdetails.php?id=' . $requestId . '";
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