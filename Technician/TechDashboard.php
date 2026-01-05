<?php
include('../dbConnection.php');
session_start();

// Check if technician is logged in
if(!isset($_SESSION['is_tech_login'])){
  header("Location: TechnicianLogin.php");
  exit();
}

$tId = $_SESSION['tId'];
$tName = $_SESSION['tName'];
$tEmail = $_SESSION['tEmail'];

$success_msg = '';
$error_msg = '';

// Handle Accept Request
if(isset($_POST['accept_request'])){
  $request_id = intval($_POST['request_id']);
  
  $sql = "UPDATE assignwork_tb SET tech_status = 'accepted' WHERE request_id = ? AND assign_tech = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("is", $request_id, $tName);
  
  if($stmt->execute()){
    $success_msg = "Request accepted successfully!";
  } else {
    $error_msg = "Failed to accept request.";
  }
  $stmt->close();
}

// Handle Reject Request
if(isset($_POST['reject_request'])){
  $request_id = intval($_POST['request_id']);
  
  $sql = "UPDATE assignwork_tb SET tech_status = 'rejected' WHERE request_id = ? AND assign_tech = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("is", $request_id, $tName);
  
  if($stmt->execute()){
    $success_msg = "Request rejected.";
  } else {
    $error_msg = "Failed to reject request.";
  }
  $stmt->close();
}

// Handle Complete Request
if(isset($_POST['complete_request'])){
  $request_id = intval($_POST['request_id']);
  
  $sql = "UPDATE assignwork_tb SET tech_status = 'completed' WHERE request_id = ? AND assign_tech = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("is", $request_id, $tName);
  
  if($stmt->execute()){
    $success_msg = "Request marked as completed!";
  } else {
    $error_msg = "Failed to complete request.";
  }
  $stmt->close();
}

// Fetch all assigned requests for this technician
$sql = "SELECT aw.*, sr.request_info, sr.request_desc, sr.requester_name, 
        sr.requester_email, sr.requester_mobile, sr.request_date 
        FROM assignwork_tb aw
        LEFT JOIN submitrequest_tb sr ON aw.request_id = sr.request_id
        WHERE aw.assign_tech = ?
        ORDER BY aw.assign_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $tName);
$stmt->execute();
$result = $stmt->get_result();

// Count statistics
$total_requests = 0;
$pending_requests = 0;
$accepted_requests = 0;
$completed_requests = 0;
$rejected_requests = 0;

$sql_stats = "SELECT 
              COUNT(*) as total,
              SUM(CASE WHEN tech_status = 'pending' THEN 1 ELSE 0 END) as pending,
              SUM(CASE WHEN tech_status = 'accepted' THEN 1 ELSE 0 END) as accepted,
              SUM(CASE WHEN tech_status = 'completed' THEN 1 ELSE 0 END) as completed,
              SUM(CASE WHEN tech_status = 'rejected' THEN 1 ELSE 0 END) as rejected
              FROM assignwork_tb WHERE assign_tech = ?";
$stmt_stats = $conn->prepare($sql_stats);
$stmt_stats->bind_param("s", $tName);
$stmt_stats->execute();
$stats = $stmt_stats->get_result()->fetch_assoc();

$total_requests = $stats['total'];
$pending_requests = $stats['pending'];
$accepted_requests = $stats['accepted'];
$completed_requests = $stats['completed'];
$rejected_requests = $stats['rejected'];

$stmt_stats->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Technician Dashboard - OSMS</title>
  
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="../css/bootstrap.min.css">
  
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  
  <style>
    body {
      background: #f5f7fa;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    /* Navbar */
    .navbar {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      box-shadow: 0 2px 20px rgba(0,0,0,0.1);
    }

    .navbar-brand {
      font-size: 1.5rem;
      font-weight: 700;
      color: white !important;
    }

    .navbar-text {
      color: white !important;
    }

    /* Stats Cards */
    .stats-card {
      background: white;
      border-radius: 15px;
      padding: 25px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.08);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      border-left: 4px solid;
    }

    .stats-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    }

    .stats-card.primary {
      border-color: #667eea;
    }

    .stats-card.warning {
      border-color: #f59e0b;
    }

    .stats-card.info {
      border-color: #3b82f6;
    }

    .stats-card.success {
      border-color: #10b981;
    }

    .stats-card.danger {
      border-color: #ef4444;
    }

    .stats-number {
      font-size: 2.5rem;
      font-weight: 800;
      margin-bottom: 5px;
    }

    .stats-label {
      color: #6b7280;
      font-weight: 500;
      font-size: 0.95rem;
    }

    .stats-icon {
      font-size: 2.5rem;
      opacity: 0.2;
    }

    /* Request Cards */
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
      font-size: 1.2rem;
      color: #667eea;
    }

    .status-badge {
      padding: 8px 16px;
      border-radius: 20px;
      font-weight: 600;
      font-size: 0.85rem;
      text-transform: uppercase;
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

    .request-info {
      margin-bottom: 15px;
    }

    .info-label {
      font-weight: 600;
      color: #374151;
      display: inline-block;
      min-width: 120px;
    }

    .info-value {
      color: #6b7280;
    }

    .btn-action {
      margin-right: 10px;
      border-radius: 8px;
      font-weight: 600;
      padding: 8px 20px;
      transition: all 0.3s ease;
    }

    .btn-accept {
      background: #10b981;
      border: none;
      color: white;
    }

    .btn-accept:hover {
      background: #059669;
      transform: translateY(-2px);
    }

    .btn-reject {
      background: #ef4444;
      border: none;
      color: white;
    }

    .btn-reject:hover {
      background: #dc2626;
      transform: translateY(-2px);
    }

    .btn-complete {
      background: #3b82f6;
      border: none;
      color: white;
    }

    .btn-complete:hover {
      background: #2563eb;
      transform: translateY(-2px);
    }

    .alert {
      border-radius: 10px;
      border: none;
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

    .filter-tabs {
      background: white;
      border-radius: 10px;
      padding: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.05);
      margin-bottom: 20px;
    }

    .filter-btn {
      border: none;
      background: transparent;
      padding: 10px 20px;
      border-radius: 8px;
      font-weight: 600;
      color: #6b7280;
      transition: all 0.3s ease;
    }

    .filter-btn:hover {
      background: #f3f4f6;
      color: #667eea;
    }

    .filter-btn.active {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
    }
  </style>
</head>
<body>
  
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid">
      <a class="navbar-brand" href="#">
        <i class="fas fa-tools"></i> OSMS Technician
      </a>
      <div class="ms-auto d-flex align-items-center">
        <span class="navbar-text me-4">
          <i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($tName); ?>
        </span>
        <a href="TechLogout.php" class="btn btn-outline-light btn-sm">
          <i class="fas fa-sign-out-alt"></i> Logout
        </a>
      </div>
    </div>
  </nav>

  <!-- Main Content -->
  <div class="container-fluid mt-4 px-4">
    
    <!-- Success/Error Messages -->
    <?php if($success_msg): ?>
      <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($success_msg); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>
    
    <?php if($error_msg): ?>
      <div class="alert alert-danger alert-dismissible fade show">
        <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($error_msg); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>

    <!-- Statistics Cards -->
    <div class="row mb-4">
      <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card primary">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <div class="stats-number text-primary"><?php echo $total_requests; ?></div>
              <div class="stats-label">Total Requests</div>
            </div>
            <i class="fas fa-clipboard-list stats-icon text-primary"></i>
          </div>
        </div>
      </div>
      
      <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card warning">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <div class="stats-number text-warning"><?php echo $pending_requests; ?></div>
              <div class="stats-label">Pending</div>
            </div>
            <i class="fas fa-clock stats-icon text-warning"></i>
          </div>
        </div>
      </div>
      
      <div class="col-lg-2 col-md-6 mb-3">
        <div class="stats-card info">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <div class="stats-number text-info"><?php echo $accepted_requests; ?></div>
              <div class="stats-label">Accepted</div>
            </div>
            <i class="fas fa-check-circle stats-icon text-info"></i>
          </div>
        </div>
      </div>
      
      <div class="col-lg-2 col-md-6 mb-3">
        <div class="stats-card success">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <div class="stats-number text-success"><?php echo $completed_requests; ?></div>
              <div class="stats-label">Completed</div>
            </div>
            <i class="fas fa-check-double stats-icon text-success"></i>
          </div>
        </div>
      </div>

      <div class="col-lg-2 col-md-6 mb-3">
        <div class="stats-card danger">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <div class="stats-number text-danger"><?php echo $rejected_requests; ?></div>
              <div class="stats-label">Rejected</div>
            </div>
            <i class="fas fa-times-circle stats-icon text-danger"></i>
          </div>
        </div>
      </div>
    </div>

    <!-- Filter Tabs -->
    <div class="filter-tabs">
      <button class="filter-btn active" onclick="filterRequests('all')">
        <i class="fas fa-list"></i> All Requests
      </button>
      <button class="filter-btn" onclick="filterRequests('pending')">
        <i class="fas fa-clock"></i> Pending
      </button>
      <button class="filter-btn" onclick="filterRequests('accepted')">
        <i class="fas fa-check-circle"></i> Accepted
      </button>
      <button class="filter-btn" onclick="filterRequests('completed')">
        <i class="fas fa-check-double"></i> Completed
      </button>
      <button class="filter-btn" onclick="filterRequests('rejected')">
        <i class="fas fa-times-circle"></i> Rejected
      </button>
    </div>

    <!-- Service Requests -->
    <div class="row">
      <div class="col-12">
        <h4 class="mb-4">
          <i class="fas fa-tasks"></i> Assigned Service Requests
        </h4>
        
        <?php if($result->num_rows > 0): ?>
          <?php while($row = $result->fetch_assoc()): ?>
            <div class="request-card" data-status="<?php echo $row['tech_status']; ?>">
              <div class="request-header">
                <div>
                  <div class="request-id">
                    <i class="fas fa-hashtag"></i> Request #<?php echo htmlspecialchars($row['request_id']); ?>
                  </div>
                  <small class="text-muted">
                    Assigned: <?php echo date('M d, Y', strtotime($row['assign_date'])); ?>
                  </small>
                </div>
                <span class="status-badge status-<?php echo $row['tech_status']; ?>">
                  <?php echo htmlspecialchars($row['tech_status']); ?>
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
                      <i class="fas fa-user"></i> Customer:
                    </span>
                    <span class="info-value"><?php echo htmlspecialchars($row['requester_name']); ?></span>
                  </div>
                  
                  <div class="request-info">
                    <span class="info-label">
                      <i class="fas fa-phone"></i> Mobile:
                    </span>
                    <span class="info-value"><?php echo htmlspecialchars($row['requester_mobile']); ?></span>
                  </div>
                </div>
                
                <div class="col-md-6">
                  <div class="request-info">
                    <span class="info-label">
                      <i class="fas fa-envelope"></i> Email:
                    </span>
                    <span class="info-value"><?php echo htmlspecialchars($row['requester_email']); ?></span>
                  </div>
                  
                  <div class="request-info">
                    <span class="info-label">
                      <i class="fas fa-calendar"></i> Request Date:
                    </span>
                    <span class="info-value"><?php echo date('M d, Y', strtotime($row['request_date'])); ?></span>
                  </div>
                  
                  <div class="request-info">
                    <span class="info-label">
                      <i class="fas fa-info-circle"></i> Description:
                    </span>
                    <div class="info-value mt-2">
                      <?php echo nl2br(htmlspecialchars($row['request_desc'])); ?>
                    </div>
                  </div>
                </div>
              </div>
              
              <hr>
              
              <!-- Action Buttons -->
              <div class="d-flex flex-wrap gap-2">
                <?php if($row['tech_status'] == 'pending'): ?>
                  <form method="POST" style="display:inline;">
                    <input type="hidden" name="request_id" value="<?php echo $row['request_id']; ?>">
                    <button type="submit" name="accept_request" class="btn btn-accept" 
                            onclick="return confirm('Accept this service request?')">
                      <i class="fas fa-check"></i> Accept
                    </button>
                  </form>
                  
                  <form method="POST" style="display:inline;">
                    <input type="hidden" name="request_id" value="<?php echo $row['request_id']; ?>">
                    <button type="submit" name="reject_request" class="btn btn-reject"
                            onclick="return confirm('Reject this service request?')">
                      <i class="fas fa-times"></i> Reject
                    </button>
                  </form>
                <?php endif; ?>
                
                <?php if($row['tech_status'] == 'accepted'): ?>
                  <form method="POST" style="display:inline;">
                    <input type="hidden" name="request_id" value="<?php echo $row['request_id']; ?>">
                    <button type="submit" name="complete_request" class="btn btn-complete"
                            onclick="return confirm('Mark this request as completed?')">
                      <i class="fas fa-check-double"></i> Mark Complete
                    </button>
                  </form>
                <?php endif; ?>
                
                <?php if($row['tech_status'] == 'completed'): ?>
                  <span class="text-success fw-bold">
                    <i class="fas fa-check-circle"></i> This request has been completed
                  </span>
                <?php endif; ?>
                
                <?php if($row['tech_status'] == 'rejected'): ?>
                  <span class="text-danger fw-bold">
                    <i class="fas fa-times-circle"></i> This request was rejected
                  </span>
                <?php endif; ?>
              </div>
            </div>
          <?php endwhile; ?>
        <?php else: ?>
          <div class="empty-state">
            <i class="fas fa-inbox"></i>
            <h5>No Service Requests Assigned</h5>
            <p class="text-muted">You don't have any assigned service requests at the moment.</p>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <script>
    // Filter requests by status
    function filterRequests(status) {
      const cards = document.querySelectorAll('.request-card');
      const buttons = document.querySelectorAll('.filter-btn');
      
      // Update active button
      buttons.forEach(btn => btn.classList.remove('active'));
      event.target.closest('.filter-btn').classList.add('active');
      
      // Filter cards
      cards.forEach(card => {
        const cardStatus = card.getAttribute('data-status');
        
        if(status === 'all') {
          // Show all EXCEPT rejected
          if(cardStatus !== 'rejected') {
            card.style.display = 'block';
          } else {
            card.style.display = 'none';
          }
        } else {
          // Show only matching status
          if(cardStatus === status) {
            card.style.display = 'block';
          } else {
            card.style.display = 'none';
          }
        }
      });
    }

    // Auto-hide alerts
    setTimeout(function() {
      const alerts = document.querySelectorAll('.alert');
      alerts.forEach(function(alert) {
        alert.style.transition = 'opacity 0.5s';
        alert.style.opacity = '0';
        setTimeout(function() {
          alert.remove();
        }, 500);
      });
    }, 5000);

    // On page load, hide rejected requests by default
    window.addEventListener('DOMContentLoaded', function() {
      filterRequests('all');
    });
  </script>

  <!-- Bootstrap JS -->
  <script src="../js/jquery.min.js"></script>
  <script src="../js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>