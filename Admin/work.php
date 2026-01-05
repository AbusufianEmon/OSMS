<?php
define('TITLE', 'Work Order');
define('PAGE', 'work');
include('includes/header.php'); 
include('../dbConnection.php');
session_start();
 if(isset($_SESSION['is_adminlogin'])){
  $aEmail = $_SESSION['aEmail'];
 } else {
  echo "<script> location.href='login.php'; </script>";
 }
?>
<div class="col-sm-9 col-md-10 mt-5">
  <p class="bg-dark text-white p-2">Assigned Work Orders</p>
  <?php 
 $sql = "SELECT * FROM assignwork_tb ORDER BY assign_date DESC";
 $result = $conn->query($sql);
 if($result->num_rows > 0){
  echo '<table class="table table-striped table-bordered">
  <thead class="table-dark">
    <tr>
      <th scope="col">Req ID</th>
      <th scope="col">Request Info</th>
      <th scope="col">Name</th>
      <th scope="col">Address</th>
      <th scope="col">City</th>
      <th scope="col">Mobile</th>
      <th scope="col">Technician</th>
      <th scope="col">Assigned Date</th>
      <th scope="col">Status</th>
      <th scope="col">Action</th>
    </tr>
  </thead>
  <tbody>';
  while($row = $result->fetch_assoc()){
    // Determine status badge
    $statusBadge = '';
    $statusText = ucfirst($row["tech_status"]);
    
    switch($row["tech_status"]) {
      case 'pending':
        $statusBadge = '<span class="badge badge-warning">Pending</span>';
        break;
      case 'accepted':
        $statusBadge = '<span class="badge badge-info">Accepted</span>';
        break;
      case 'completed':
        $statusBadge = '<span class="badge badge-success">Completed</span>';
        break;
      case 'rejected':
        $statusBadge = '<span class="badge badge-danger">Rejected</span>';
        break;
      default:
        $statusBadge = '<span class="badge badge-secondary">Unknown</span>';
    }
    
    echo '<tr>
    <th scope="row">'.htmlspecialchars($row["request_id"]).'</th>
    <td>'.htmlspecialchars($row["request_info"]).'</td>
    <td>'.htmlspecialchars($row["requester_name"]).'</td>
    <td>'.htmlspecialchars($row["requester_add2"]).'</td>
    <td>'.htmlspecialchars($row["requester_city"]).'</td>
    <td>'.htmlspecialchars($row["requester_mobile"]).'</td>
    <td>'.htmlspecialchars($row["assign_tech"]).'</td>
    <td>'.date('M d, Y', strtotime($row["assign_date"])).'</td>
    <td>'.$statusBadge.'</td>
    <td>
      <form action="viewassignwork.php" method="POST" class="d-inline">
        <input type="hidden" name="id" value="'. $row["request_id"] .'">
        <button type="submit" class="btn btn-warning btn-sm" name="view" value="View" title="View Details">
          <i class="fas fa-eye"></i>
        </button>
      </form>
      <form action="" method="POST" class="d-inline">
        <input type="hidden" name="id" value="'. $row["request_id"] .'">
        <button type="submit" class="btn btn-danger btn-sm" name="delete" value="Delete" 
                onclick="return confirm(\'Are you sure you want to delete this work order?\')" title="Delete">
          <i class="fas fa-trash"></i>
        </button>
      </form>
    </td>
    </tr>';
   }
   echo '</tbody> </table>';
  } else {
    echo '<div class="alert alert-info">No work orders assigned yet.</div>';
  }
  
  if(isset($_REQUEST['delete'])){
    $requestId = (int)$_REQUEST['id'];
    $sql = "DELETE FROM assignwork_tb WHERE request_id = $requestId";
    if($conn->query($sql) === TRUE){
      echo '<script>
              alert("Work order deleted successfully!");
              window.location.href="work.php";
            </script>';
    } else {
      echo '<div class="alert alert-danger">Unable to Delete Data: ' . $conn->error . '</div>';
    }
  }
  ?>
</div>
</div>
</div>
<?php
include('includes/footer.php'); 
?>