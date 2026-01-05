<?php
define('TITLE', 'Request Submitted');
define('PAGE', 'SubmitRequest');

include('includes/header.php');
include('../dbConnection.php');

session_start();

if(!isset($_SESSION['is_login'])){
    exit;
}

$rEmail = $_SESSION['rEmail'] ?? '';
$myid = $_SESSION['myid'] ?? 0;

$sql = "SELECT * FROM submitrequest_tb WHERE request_id = $myid";
$result = $conn->query($sql);
?>

<div class="col-sm-9 col-md-10 mt-5">

<?php
if($result->num_rows == 1){
    $row = $result->fetch_assoc();
?>
<div class="card shadow-lg border-0">
    <div class="card-header bg-success text-white">
        <h4 class="mb-0">Request Submitted Successfully!</h4>
    </div>

    <div class="card-body">
        <p class="mb-3">Thank you! Your service request has been submitted successfully.</p>
        <p class="h5">Your Request ID: <strong><?php echo $row['request_id']; ?></strong></p>

        <div class="table-responsive mt-4">
            <table class="table table-bordered">
                <tbody>
                    <tr>
                        <th>Name</th>
                        <td><?php echo $row['requester_name']; ?></td>
                    </tr>
                    <tr>
                        <th>Email ID</th>
                        <td><?php echo $row['requester_email']; ?></td>
                    </tr>
                    <tr>
                        <th>Request Info</th>
                        <td><?php echo $row['request_info']; ?></td>
                    </tr>
                    <tr>
                        <th>Request Description</th>
                        <td><?php echo $row['request_desc']; ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="mt-3 d-print-none">
            <input class="btn btn-danger" type="button" value="Print" onclick="window.print()">
            <a href="SubmitRequest.php" class="btn btn-primary ml-2">Submit Another Request</a>
            <a href="RequesterProfile.php" class="btn btn-secondary ml-2">Go to Profile</a>
        </div>
    </div>
</div>

<?php
} else {
    echo "<div class='alert alert-danger mt-5'>Failed to retrieve request details.</div>";
}
?>

</div>

<?php
include('includes/footer.php'); 
$conn->close();
?>
