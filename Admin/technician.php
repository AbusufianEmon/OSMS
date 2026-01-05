<?php
define('TITLE', 'Technician');
define('PAGE', 'technician');
include('includes/header.php'); 
include('../dbConnection.php');
session_start();
 if(isset($_SESSION['is_adminlogin'])){
  $aEmail = $_SESSION['aEmail'];
 } else {
  echo "<script> location.href='login.php'; </script>";
 }
?>
<div class="col-sm-9 col-md-10 mt-5 text-center">
  <p class="bg-dark text-white p-2">List of Technicians</p>
  <?php
    $sql = "SELECT * FROM technician_tb";
    $result = $conn->query($sql);
    if($result->num_rows > 0){
 echo '<table class="table table-striped">
  <thead>
   <tr>
    <th scope="col">Emp ID</th>
    <th scope="col">Name</th>
    <th scope="col">City</th>
    <th scope="col">Mobile</th>
    <th scope="col">Email</th>
    <th scope="col">Password</th>
    <th scope="col">Action</th>
   </tr>
  </thead>
  <tbody>';
  while($row = $result->fetch_assoc()){
   echo '<tr>';
    echo '<th scope="row">'.$row["empid"].'</th>';
    echo '<td>'. htmlspecialchars($row["empName"]).'</td>';
    echo '<td>'.htmlspecialchars($row["empCity"]).'</td>';
    echo '<td>'.htmlspecialchars($row["empMobile"]).'</td>';
    echo '<td>'.htmlspecialchars($row["empEmail"]).'</td>';
    echo '<td><span class="badge badge-secondary">'.str_repeat('*', 8).'</span></td>';
    echo '<td>
            <form action="editemp.php" method="POST" class="d-inline">
              <input type="hidden" name="id" value="'. $row["empid"] .'">
              <button type="submit" class="btn btn-info btn-sm mr-2" name="view" value="View" title="Edit">
                <i class="fas fa-pen"></i>
              </button>
            </form>
            <form action="" method="POST" class="d-inline">
              <input type="hidden" name="id" value="'. $row["empid"] .'">
              <button type="submit" class="btn btn-danger btn-sm" name="delete" value="Delete" 
                      onclick="return confirm(\'Are you sure you want to delete this technician?\')" title="Delete">
                <i class="far fa-trash-alt"></i>
              </button>
            </form>
          </td>';
   echo '</tr>';
  }
 echo '</tbody>
 </table>';
} else {
  echo '<div class="alert alert-info">No technicians found. Add your first technician!</div>';
}

// Handle delete
if(isset($_REQUEST['delete'])){
  $empId = (int)$_REQUEST['id'];
  $sql = "DELETE FROM technician_tb WHERE empid = $empId";
  if($conn->query($sql) === TRUE){
    echo '<script>
            alert("Technician deleted successfully!");
            window.location.href="technician.php";
          </script>';
  } else {
    echo '<div class="alert alert-danger">Unable to Delete Data: ' . $conn->error . '</div>';
  }
}
?>
</div>
</div>
<div><a class="btn btn-danger box" href="insertemp.php"><i class="fas fa-plus fa-2x"></i></a></div>
</div>
<?php
include('includes/footer.php'); 
?>