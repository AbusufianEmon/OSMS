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
<div class="col-sm-6 mt-5 mx-3">
 <h3 class="text-center">Assigned Work Details</h3>
 <?php
 if(isset($_REQUEST['view'])){
  $requestId = (int)$_REQUEST['id'];
  $sql = "SELECT * FROM assignwork_tb WHERE request_id = $requestId";
  $result = $conn->query($sql);
  $row = $result->fetch_assoc();
 }
 
 // Determine status badge
 $statusBadge = '';
 $statusClass = '';
 if(isset($row['tech_status'])) {
   switch($row['tech_status']) {
     case 'pending':
       $statusBadge = '<span class="badge badge-warning badge-lg">Pending</span>';
       $statusClass = 'table-warning';
       break;
     case 'accepted':
       $statusBadge = '<span class="badge badge-info badge-lg">Accepted</span>';
       $statusClass = 'table-info';
       break;
     case 'completed':
       $statusBadge = '<span class="badge badge-success badge-lg">Completed</span>';
       $statusClass = 'table-success';
       break;
     case 'rejected':
       $statusBadge = '<span class="badge badge-danger badge-lg">Rejected</span>';
       $statusClass = 'table-danger';
       break;
     default:
       $statusBadge = '<span class="badge badge-secondary badge-lg">Unknown</span>';
   }
 }
 ?>
 <table class="table table-bordered">
  <tbody>
   <tr>
    <td><strong>Request ID</strong></td>
    <td>
     <?php if(isset($row['request_id'])) {echo htmlspecialchars($row['request_id']); }?>
    </td>
   </tr>
   <tr>
    <td><strong>Request Info</strong></td>
    <td>
     <?php if(isset($row['request_info'])) {echo htmlspecialchars($row['request_info']); }?>
    </td>
   </tr>
   <tr>
    <td><strong>Request Description</strong></td>
    <td>
     <?php if(isset($row['request_desc'])) {echo nl2br(htmlspecialchars($row['request_desc'])); }?>
    </td>
   </tr>
   <tr>
    <td><strong>Name</strong></td>
    <td>
     <?php if(isset($row['requester_name'])) {echo htmlspecialchars($row['requester_name']); }?>
    </td>
   </tr>
   <tr>
    <td><strong>Address Line 1</strong></td>
    <td>
     <?php if(isset($row['requester_add1'])) {echo htmlspecialchars($row['requester_add1']); }?>
    </td>
   </tr>
   <tr>
    <td><strong>Address Line 2</strong></td>
    <td>
     <?php if(isset($row['requester_add2'])) {echo htmlspecialchars($row['requester_add2']); }?>
    </td>
   </tr>
   <tr>
    <td><strong>City</strong></td>
    <td>
     <?php if(isset($row['requester_city'])) {echo htmlspecialchars($row['requester_city']); }?>
    </td>
   </tr>
   <tr>
    <td><strong>State</strong></td>
    <td>
     <?php if(isset($row['requester_state'])) {echo htmlspecialchars($row['requester_state']); }?>
    </td>
   </tr>
   <tr>
    <td><strong>Pin Code</strong></td>
    <td>
     <?php if(isset($row['requester_zip'])) {echo htmlspecialchars($row['requester_zip']); }?>
    </td>
   </tr>
   <tr>
    <td><strong>Email</strong></td>
    <td>
     <?php if(isset($row['requester_email'])) {echo htmlspecialchars($row['requester_email']); }?>
    </td>
   </tr>
   <tr>
    <td><strong>Mobile</strong></td>
    <td>
     <?php if(isset($row['requester_mobile'])) {echo htmlspecialchars($row['requester_mobile']); }?>
    </td>
   </tr>
   <tr>
    <td><strong>Assigned Date</strong></td>
    <td>
     <?php if(isset($row['assign_date'])) {echo date('M d, Y', strtotime($row['assign_date'])); }?>
    </td>
   </tr>
   <tr>
    <td><strong>Technician Name</strong></td>
    <td>
     <?php if(isset($row['assign_tech'])) {echo htmlspecialchars($row['assign_tech']); }?>
    </td>
   </tr>
   <tr class="<?php echo $statusClass; ?>">
    <td><strong>Work Status</strong></td>
    <td>
     <?php echo $statusBadge; ?>
    </td>
   </tr>
   <?php if($row['tech_status'] == 'completed'): ?>
   <tr>
    <td><strong>Customer Sign</strong></td>
    <td>
      <div style="height: 60px; border-bottom: 2px solid #000; margin-top: 40px;"></div>
    </td>
   </tr>
   <tr>
    <td><strong>Technician Sign</strong></td>
    <td>
      <div style="height: 60px; border-bottom: 2px solid #000; margin-top: 40px;"></div>
    </td>
   </tr>
   <?php endif; ?>
  </tbody>
 </table>
 
 <?php if($row['tech_status'] == 'rejected'): ?>
 <div class="alert alert-danger d-print-none" role="alert">
   <strong><i class="fas fa-exclamation-triangle"></i> Notice:</strong> This work order was rejected by the technician. Please reassign to another technician.
 </div>
 <?php endif; ?>
 
 <div class="text-center">
  <form class='d-print-none d-inline mr-3'>
    <input class='btn btn-danger' type='submit' value='Print' onClick='window.print()'>
  </form>
  <form class='d-print-none d-inline' action="work.php">
    <input class='btn btn-secondary' type='submit' value='Close'>
  </form>
 </div>
</div>

<style>
  @media print {
    .badge-lg {
      font-size: 1rem !important;
      padding: 0.5rem 1rem !important;
    }
  }
  
  .badge-lg {
    font-size: 1.1rem;
    padding: 0.5rem 1rem;
  }
</style>

<?php
include('includes/footer.php'); 
?>