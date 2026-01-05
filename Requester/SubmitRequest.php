<?php
define('TITLE', 'Submit Request');
define('PAGE', 'SubmitRequest');
include('includes/header.php'); 
include('../dbConnection.php');

session_start();
if(!isset($_SESSION['is_login'])){
  echo "<script> location.href='RequesterLogin.php'; </script>";
  exit;
}

$rEmail = $_SESSION['rEmail'];   // email of logged-in user

// ----------- SUBMIT REQUEST FORM HANDLER ---------------
if(isset($_REQUEST['submitrequest'])){

    // Check empty fields (Address Line 2 is optional)
    if(
        empty($_REQUEST['requestinfo']) ||
        empty($_REQUEST['requestdesc']) ||
        empty($_REQUEST['requestername']) ||
        empty($_REQUEST['requesteradd1']) ||
        empty($_REQUEST['requestercity']) ||
        empty($_REQUEST['requesterstate']) ||
        empty($_REQUEST['requesterzip']) ||
        empty($_REQUEST['requesteremail']) ||
        empty($_REQUEST['requestermobile']) ||
        empty($_REQUEST['requestdate'])
    ){
        $msg = '<div class="alert alert-warning col-sm-6 ml-5 mt-2" role="alert">Please fill all required fields</div>';
    }
    // Validate mobile number
    elseif(!preg_match("/^01[0-9]{9}$/", $_REQUEST['requestermobile'])){
        $msg = '<div class="alert alert-warning col-sm-6 ml-5 mt-2" role="alert">Mobile number must be 11 digits and start with 01</div>';
    }
    else 
    {
        // Collect form values
        $rinfo  = $_REQUEST['requestinfo'];
        $rdesc  = $_REQUEST['requestdesc'];
        $rname  = $_REQUEST['requestername'];
        $radd1  = $_REQUEST['requesteradd1'];
        $radd2  = $_REQUEST['requesteradd2'];  // Optional field
        $rcity  = $_REQUEST['requestercity'];
        $rstate = $_REQUEST['requesterstate'];
        $rzip   = $_REQUEST['requesterzip'];
        $remail = $_REQUEST['requesteremail'];
        $rmobile= $_REQUEST['requestermobile'];
        $rdate  = $_REQUEST['requestdate'];

        // SQL Insert Query with prepared statement
        $sql = "INSERT INTO submitrequest_tb(
                    request_info, request_desc, requester_name, requester_add1, requester_add2,
                    requester_city, requester_state, requester_zip, requester_email, requester_mobile, request_date
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssssss", $rinfo, $rdesc, $rname, $radd1, $radd2, $rcity, $rstate, $rzip, $remail, $rmobile, $rdate);

        if($stmt->execute()){
            $genid = $stmt->insert_id;    // Auto-generated Request ID

            $_SESSION['myid'] = $genid;   // Save ID for next page

            echo "<script> location.href='submitrequestsuccess.php'; </script>";
            exit;
        }
        else {
            $msg = '<div class="alert alert-danger col-sm-6 ml-5 mt-2" role="alert">
                    Unable to Submit Your Request
                    </div>';
        }
        $stmt->close();
    }
}
?>

<!-- ---------------- PAGE UI ---------------- -->

<div class="col-sm-9 col-md-10 mt-5">

  <div class="card shadow-lg border-0 mb-4">
    <div class="card-header bg-primary text-white">
      <h4 class="mb-0">Submit a Service Request</h4>
    </div>

    <div class="card-body">

      <form class="mx-2" action="" method="POST">

        <div class="form-group mb-3">
          <label class="font-weight-bold">Service Type <span class="text-danger">*</span></label>
          <select class="form-control" name="requestinfo" required>
            <option value="">-- Select Device Type --</option>
            <option value="Laptop">Laptop</option>
            <option value="PC">PC</option>
          </select>
        </div>

        <div class="form-group mb-3">
          <label class="font-weight-bold">Description <span class="text-danger">*</span></label>
          <textarea class="form-control" rows="3" placeholder="Describe your issue in details" name="requestdesc" required></textarea>
        </div>

        <hr>

        <h5 class="text-secondary mb-3">Requester Details</h5>

        <div class="form-group mb-3">
          <label class="font-weight-bold">Name <span class="text-danger">*</span></label>
          <input type="text" class="form-control" placeholder="Your full name" name="requestername" required>
        </div>

        <div class="form-row">
          <div class="form-group col-md-6">
            <label class="font-weight-bold">Address Line 1 <span class="text-danger">*</span></label>
            <input type="text" class="form-control" placeholder="House / Road" name="requesteradd1" required>
          </div>
          <div class="form-group col-md-6">
            <label class="font-weight-bold">Address Line 2</label>
            <input type="text" class="form-control" placeholder="Area / Block (Optional)" name="requesteradd2">
          </div>
        </div>

        <div class="form-row">
          <div class="form-group col-md-6">
            <label class="font-weight-bold">Area <span class="text-danger">*</span></label>
            <input type="text" class="form-control" placeholder="Enter your area" name="requestercity" required>
          </div>
          <div class="form-group col-md-4">
            <label class="font-weight-bold">City <span class="text-danger">*</span></label>
            <select class="form-control" name="requesterstate" required>
              <option value="">-- Select City --</option>
              <option value="Dhaka">Dhaka</option>
              <option value="Sylhet">Sylhet</option>
            </select>
          </div>
          <div class="form-group col-md-2">
            <label class="font-weight-bold">Zip <span class="text-danger">*</span></label>
            <input type="text" class="form-control" placeholder="1234" name="requesterzip" maxlength="4" onkeypress="isInputNumber(event)" required>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group col-md-6">
            <label class="font-weight-bold">Email <span class="text-danger">*</span></label>
            <input type="email" class="form-control" 
                   value="<?php echo htmlspecialchars($rEmail); ?>" name="requesteremail" readonly>
          </div>

          <div class="form-group col-md-3">
            <label class="font-weight-bold">Mobile <span class="text-danger">*</span></label>
            <input type="text" class="form-control" placeholder="01XXXXXXXXX" name="requestermobile" 
                   pattern="01[0-9]{9}" 
                   maxlength="11" 
                   title="Mobile must be 11 digits and start with 01"
                   onkeypress="isInputNumber(event)" required>
            <small class="text-muted">Must start with 01 (11 digits)</small>
          </div>

          <div class="form-group col-md-3">
            <label class="font-weight-bold">Date <span class="text-danger">*</span></label>
            <input type="date" class="form-control"
                   value="<?php echo date('Y-m-d'); ?>" 
                   min="<?php echo date('Y-m-d'); ?>" 
                   name="requestdate" required>
          </div>
        </div>

        <div class="mt-4">
          <button type="submit" class="btn btn-primary px-4" name="submitrequest">
            <i class="fas fa-paper-plane"></i> Submit Request
          </button>
          <button type="reset" class="btn btn-secondary px-4 ml-2">
            <i class="fas fa-redo"></i> Reset
          </button>
        </div>

      </form>

      <div class="mt-3">
        <?php if(isset($msg)) { echo $msg; } ?>
      </div>

    </div>
  </div>

</div>

<script>
function isInputNumber(evt) {
  var ch = String.fromCharCode(evt.which);
  if (!(/[0-9]/.test(ch))) {
    evt.preventDefault();
  }
}
</script>

<?php
include('includes/footer.php'); 
$conn->close();
?>