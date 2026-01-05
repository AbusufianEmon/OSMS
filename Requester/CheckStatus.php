<?php
define('TITLE', 'My Requests');
define('PAGE', 'CheckStatus');
include('includes/header.php'); 
include('../dbConnection.php');

session_start();
if(!isset($_SESSION['is_login'])){
  echo "<script> location.href='RequesterLogin.php'; </script>";
  exit();
}

$rEmail = $_SESSION['rEmail'];

// Fetch all requests for this requester from BOTH tables
$sql = "SELECT 
        sr.request_id,
        sr.request_info,
        sr.request_desc,
        sr.requester_name,
        sr.requester_add1,
        sr.requester_add2,
        sr.requester_city,
        sr.requester_state,
        sr.requester_zip,
        sr.requester_email,
        sr.requester_mobile,
        sr.request_date,
        aw.assign_tech,
        aw.assign_date,
        aw.tech_status,
        t.empName as t_name,
        t.empEmail as t_email,
        t.empMobile as t_mobile
        FROM submitrequest_tb sr
        LEFT JOIN assignwork_tb aw ON sr.request_id = aw.request_id
        LEFT JOIN technician_tb t ON aw.assign_tech = t.empName
        WHERE sr.requester_email = ?
        ORDER BY sr.request_id DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $rEmail);
$stmt->execute();
$result = $stmt->get_result();

// Count statistics
$sql_stats = "SELECT 
              COUNT(sr.request_id) as total,
              SUM(CASE WHEN aw.assign_date IS NULL THEN 1 ELSE 0 END) as not_assigned,
              SUM(CASE WHEN aw.tech_status = 'pending' THEN 1 ELSE 0 END) as pending,
              SUM(CASE WHEN aw.tech_status = 'accepted' THEN 1 ELSE 0 END) as accepted,
              SUM(CASE WHEN aw.tech_status = 'completed' THEN 1 ELSE 0 END) as completed
              FROM submitrequest_tb sr
              LEFT JOIN assignwork_tb aw ON sr.request_id = aw.request_id
              WHERE sr.requester_email = ?";
$stmt_stats = $conn->prepare($sql_stats);
$stmt_stats->bind_param("s", $rEmail);
$stmt_stats->execute();
$stats = $stmt_stats->get_result()->fetch_assoc();
?>

<style>
  .status-badge {
    padding: 6px 14px;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.8rem;
    text-transform: uppercase;
    display: inline-block;
  }

  .status-pending {
    background: #fef3c7;
    color: #d97706;
  }

  .status-accepted {
    background: #dbeafe;
    color: #2563eb;
  }

  .status-completed {
    background: #d1fae5;
    color: #059669;
  }

  .status-rejected {
    background: #fee2e2;
    color: #dc2626;
  }

  .status-not-assigned {
    background: #f3f4f6;
    color: #6b7280;
  }

  .stats-card {
    background: white;
    border-radius: 15px;
    padding: 20px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    transition: transform 0.3s ease;
    border-left: 4px solid;
    height: 100%;
  }

  .stats-card:hover {
    transform: translateY(-5px);
  }

  .stats-card.primary { border-color: #667eea; }
  .stats-card.warning { border-color: #f59e0b; }
  .stats-card.info { border-color: #3b82f6; }
  .stats-card.success { border-color: #10b981; }

  .stats-number {
    font-size: 2.5rem;
    font-weight: 800;
    margin-bottom: 5px;
  }

  .stats-label {
    color: #6b7280;
    font-weight: 500;
  }

  .request-card {
    background: white;
    border-radius: 15px;
    padding: 25px;
    margin-bottom: 20px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
  }

  .request-card:hover {
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
  }

  .request-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid #f3f4f6;
  }

  .request-id {
    font-weight: 700;
    font-size: 1.3rem;
    color: #667eea;
  }

  .request-info {
    margin-bottom: 12px;
  }

  .info-label {
    font-weight: 600;
    color: #374151;
    display: inline-block;
    min-width: 140px;
  }

  .info-value {
    color: #6b7280;
  }

  .empty-state {
    text-align: center;
    padding: 60px 20px;
    background: white;
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
  }

  .empty-state i {
    font-size: 4rem;
    color: #d1d5db;
    margin-bottom: 20px;
  }

  .tech-info-box {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 15px;
    margin-top: 15px;
  }

  .cancel-notice {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 15px;
    padding: 20px;
    margin-bottom: 25px;
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
  }

  .cancel-notice i {
    font-size: 1.5rem;
    margin-right: 10px;
  }

  .cancel-notice a {
    color: white;
    font-weight: 700;
    text-decoration: underline;
  }

  .cancel-notice a:hover {
    color: #f0f0f0;
  }
</style>

<div class="col-sm-9 col-md-10 mt-5">
  <div class="container-fluid">
    
    <h3 class="mb-4">
      <i class="fas fa-clipboard-list"></i> My Service Requests
    </h3>

    <!-- Cancellation Notice -->
    <div class="cancel-notice">
      <div class="d-flex align-items-center">
        <i class="fas fa-phone-alt"></i>
        <div>
          <strong>Need to cancel or modify a request?</strong><br>
          <small>Please contact our customer service team at <a href="tel:01640000000">01640000000</a> for assistance with cancellations or modifications.</small>
        </div>
      </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
      <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card primary">
          <div class="stats-number text-primary"><?php echo $stats['total']; ?></div>
          <div class="stats-label">Total Requests</div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card warning">
          <div class="stats-number text-warning"><?php echo $stats['not_assigned'] + $stats['pending']; ?></div>
          <div class="stats-label">Pending</div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card info">
          <div class="stats-number text-info"><?php echo $stats['accepted']; ?></div>
          <div class="stats-label">In Progress</div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card success">
          <div class="stats-number text-success"><?php echo $stats['completed']; ?></div>
          <div class="stats-label">Completed</div>
        </div>
      </div>
    </div>

    <!-- Requests List -->
    <?php if($result->num_rows > 0): ?>
      <?php while($row = $result->fetch_assoc()): 
        // Determine status - check both tech_status and assign_date
        $tech_status = !empty($row['tech_status']) ? strtolower(trim($row['tech_status'])) : null;
        $is_assigned = !empty($row['assign_date']);
        
        // Set final status
        if(!$is_assigned) {
          $status = 'not-assigned';
        } elseif($tech_status) {
          $status = $tech_status;
        } else {
          $status = 'pending';
        }
      ?>
        <div class="request-card">
          <div class="request-header">
            <div>
              <div class="request-id">
                <i class="fas fa-hashtag"></i> Request #<?php echo htmlspecialchars($row['request_id']); ?>
              </div>
              <small class="text-muted">
                <i class="fas fa-calendar"></i> Submitted: <?php echo date('M d, Y', strtotime($row['request_date'])); ?>
              </small>
            </div>
            <span class="status-badge status-<?php echo $status; ?>">
              <?php 
                $status_icons = [
                  'not-assigned' => 'fas fa-clock',
                  'pending' => 'fas fa-hourglass-half',
                  'accepted' => 'fas fa-check-circle',
                  'completed' => 'fas fa-check-double',
                  'rejected' => 'fas fa-times-circle'
                ];
                $status_labels = [
                  'not-assigned' => 'Not Assigned',
                  'pending' => 'Pending',
                  'accepted' => 'In Progress',
                  'completed' => 'Completed',
                  'rejected' => 'Rejected'
                ];
                echo '<i class="' . ($status_icons[$status] ?? 'fas fa-info-circle') . '"></i> ' . ($status_labels[$status] ?? 'Unknown');
              ?>
            </span>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="request-info">
                <span class="info-label">
                  <i class="fas fa-laptop"></i> Device:
                </span>
                <span class="info-value"><?php echo htmlspecialchars($row['request_info']); ?></span>
              </div>
              
              <div class="request-info">
                <span class="info-label">
                  <i class="fas fa-info-circle"></i> Description:
                </span>
                <div class="info-value mt-1">
                  <?php echo nl2br(htmlspecialchars($row['request_desc'])); ?>
                </div>
              </div>
            </div>

            <div class="col-md-6">
              <div class="request-info">
                <span class="info-label">
                  <i class="fas fa-map-marker-alt"></i> Location:
                </span>
                <span class="info-value">
                  <?php echo htmlspecialchars($row['requester_city'] . ', ' . $row['requester_state']); ?>
                </span>
              </div>

              <div class="request-info">
                <span class="info-label">
                  <i class="fas fa-phone"></i> Mobile:
                </span>
                <span class="info-value"><?php echo htmlspecialchars($row['requester_mobile']); ?></span>
              </div>

              <?php if($is_assigned): ?>
                <div class="request-info">
                  <span class="info-label">
                    <i class="fas fa-calendar-check"></i> Assigned:
                  </span>
                  <span class="info-value"><?php echo date('M d, Y', strtotime($row['assign_date'])); ?></span>
                </div>
              <?php endif; ?>
            </div>
          </div>

          <?php if($is_assigned && !empty($row['assign_tech'])): ?>
            <div class="tech-info-box">
              <strong>
                <i class="fas fa-user-cog"></i> Assigned Technician:
              </strong>
              <div class="mt-2">
                <i class="fas fa-user"></i> <strong><?php echo htmlspecialchars($row['assign_tech']); ?></strong><br>
                <?php if(!empty($row['t_email'])): ?>
                  <i class="fas fa-envelope"></i> <?php echo htmlspecialchars($row['t_email']); ?><br>
                <?php endif; ?>
                <?php if(!empty($row['t_mobile'])): ?>
                  <i class="fas fa-phone"></i> <?php echo htmlspecialchars($row['t_mobile']); ?>
                <?php endif; ?>
              </div>
            </div>
          <?php elseif(!$is_assigned): ?>
            <div class="alert alert-info mb-0 mt-3">
              <i class="fas fa-info-circle"></i>
              <small>Your request is pending technician assignment. You will be notified once a technician is assigned.</small>
            </div>
          <?php endif; ?>

          <?php if($status == 'pending'): ?>
            <div class="alert alert-warning mb-0 mt-3">
              <i class="fas fa-hourglass-half"></i>
              <small><strong>Awaiting Confirmation:</strong> A technician has been assigned and is reviewing your request.</small>
            </div>
          <?php elseif($status == 'accepted'): ?>
            <div class="alert alert-success mb-0 mt-3">
              <i class="fas fa-tools"></i>
              <small><strong>Work in Progress:</strong> The technician has accepted your request and is currently working on it.</small>
            </div>
          <?php elseif($status == 'completed'): ?>
            <div class="alert alert-success mb-0 mt-3">
              <i class="fas fa-check-circle"></i>
              <small><strong>Service Completed:</strong> Your service request has been successfully completed! Thank you for choosing OSMS.</small>
            </div>
          <?php elseif($status == 'rejected'): ?>
            <div class="alert alert-danger mb-0 mt-3">
              <i class="fas fa-exclamation-triangle"></i>
              <small><strong>Request Declined:</strong> Unfortunately, this request could not be accepted. Please contact our support team at <strong>01640027997</strong> for assistance.</small>
            </div>
          <?php endif; ?>
        </div>
      <?php endwhile; ?>

    <?php else: ?>
      <div class="empty-state">
        <i class="fas fa-inbox"></i>
        <h5>No Service Requests Yet</h5>
        <p class="text-muted">You haven't submitted any service requests.</p>
        <a href="SubmitRequest.php" class="btn btn-primary mt-3">
          <i class="fas fa-plus-circle me-2"></i>Submit New Request
        </a>
      </div>
    <?php endif; ?>

  </div>
</div>

<?php
include('includes/footer.php');
$stmt->close();
$stmt_stats->close();
$conn->close();
?>