<?php
define('TITLE', 'Contact Messages');
define('PAGE', 'messages');
include('includes/header.php'); 
include('../dbConnection.php');

// Check if admin is logged in
session_start();
if(!isset($_SESSION['is_adminlogin'])){
  echo "<script> location.href='login.php'; </script>";
  exit();
}

// Generate CSRF token if not exists
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Handle mark as read with security
if (isset($_GET['mark_read']) && isset($_GET['token'])) {
    // Verify CSRF token
    if (hash_equals($_SESSION['csrf_token'], $_GET['token'])) {
        $msg_id = intval($_GET['mark_read']);
        
        // Use prepared statement to prevent SQL injection
        $stmt = $conn->prepare("UPDATE contact_messages SET status='read' WHERE id=?");
        $stmt->bind_param("i", $msg_id);
        
        if ($stmt->execute()) {
            $_SESSION['success_msg'] = "Message marked as read.";
        } else {
            $_SESSION['error_msg'] = "Failed to update message.";
        }
        $stmt->close();
        
        echo "<script>location.href='view_messages.php';</script>";
        exit();
    } else {
        $_SESSION['error_msg'] = "Invalid security token.";
    }
}

// Handle delete with security
if (isset($_GET['delete']) && isset($_GET['token'])) {
    // Verify CSRF token
    if (hash_equals($_SESSION['csrf_token'], $_GET['token'])) {
        $msg_id = intval($_GET['delete']);
        
        // Use prepared statement
        $stmt = $conn->prepare("DELETE FROM contact_messages WHERE id=?");
        $stmt->bind_param("i", $msg_id);
        
        if ($stmt->execute()) {
            $_SESSION['success_msg'] = "Message deleted successfully.";
        } else {
            $_SESSION['error_msg'] = "Failed to delete message.";
        }
        $stmt->close();
        
        echo "<script>location.href='view_messages.php';</script>";
        exit();
    } else {
        $_SESSION['error_msg'] = "Invalid security token.";
    }
}

// Handle mark all as read
if (isset($_POST['mark_all_read']) && isset($_POST['csrf_token'])) {
    if (hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $conn->query("UPDATE contact_messages SET status='read' WHERE status='unread'");
        $_SESSION['success_msg'] = "All messages marked as read.";
        echo "<script>location.href='view_messages.php';</script>";
        exit();
    }
}

// Fetch all messages with pagination
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

// Filter by status
$status_filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$where_clause = "";
if ($status_filter == 'unread') {
    $where_clause = "WHERE status='unread'";
} elseif ($status_filter == 'read') {
    $where_clause = "WHERE status='read'";
}

$sql = "SELECT * FROM contact_messages $where_clause ORDER BY created_at DESC LIMIT $per_page OFFSET $offset";
$result = $conn->query($sql);

// Count messages for pagination
$count_sql = "SELECT COUNT(*) as total FROM contact_messages $where_clause";
$count_result = $conn->query($count_sql);
$total_filtered = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_filtered / $per_page);

// Count unread messages
$unread_result = $conn->query("SELECT COUNT(*) as count FROM contact_messages WHERE status='unread'");
$unread_count = $unread_result->fetch_assoc()['count'];

// Count total messages
$total_result = $conn->query("SELECT COUNT(*) as count FROM contact_messages");
$total_messages = $total_result->fetch_assoc()['count'];
$read_messages = $total_messages - $unread_count;
?>

<div class="col-sm-9 col-md-10 mt-5">
  <!-- Success/Error Messages -->
  <?php if (isset($_SESSION['success_msg'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($_SESSION['success_msg']); ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php unset($_SESSION['success_msg']); ?>
  <?php endif; ?>

  <?php if (isset($_SESSION['error_msg'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($_SESSION['error_msg']); ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php unset($_SESSION['error_msg']); ?>
  <?php endif; ?>

  <!-- Statistics Cards -->
  <div class="row mb-4">
    <div class="col-md-4">
      <div class="card text-white bg-primary mb-3">
        <div class="card-body">
          <h3 class="card-title"><?php echo $total_messages; ?></h3>
          <p class="card-text"><i class="fas fa-envelope"></i> Total Messages</p>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card text-white bg-danger mb-3">
        <div class="card-body">
          <h3 class="card-title"><?php echo $unread_count; ?></h3>
          <p class="card-text"><i class="fas fa-envelope-open"></i> Unread Messages</p>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card text-white bg-success mb-3">
        <div class="card-body">
          <h3 class="card-title"><?php echo $read_messages; ?></h3>
          <p class="card-text"><i class="fas fa-check"></i> Read Messages</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Page Header with Filters -->
  <div class="row mb-3">
    <div class="col-sm-6">
      <h4 class="mb-0">Contact Messages</h4>
    </div>
    <div class="col-sm-6 text-right">
      <!-- Filter Buttons -->
      <div class="btn-group" role="group">
        <a href="?filter=all" class="btn btn-sm btn-<?php echo $status_filter == 'all' ? 'primary' : 'outline-primary'; ?>">
          All (<?php echo $total_messages; ?>)
        </a>
        <a href="?filter=unread" class="btn btn-sm btn-<?php echo $status_filter == 'unread' ? 'danger' : 'outline-danger'; ?>">
          Unread (<?php echo $unread_count; ?>)
        </a>
        <a href="?filter=read" class="btn btn-sm btn-<?php echo $status_filter == 'read' ? 'success' : 'outline-success'; ?>">
          Read (<?php echo $read_messages; ?>)
        </a>
      </div>
      
      <!-- Mark All as Read -->
      <?php if ($unread_count > 0): ?>
        <form method="post" style="display:inline;" onsubmit="return confirm('Mark all messages as read?');">
          <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
          <button type="submit" name="mark_all_read" class="btn btn-sm btn-info ml-2">
            <i class="fas fa-check-double"></i> Mark All Read
          </button>
        </form>
      <?php endif; ?>
    </div>
  </div>

  <!-- Messages List -->
  <?php if ($result && $result->num_rows > 0): ?>
    <?php while ($row = $result->fetch_assoc()): ?>
      <div class="card mb-3 message-card <?php echo $row['status'] == 'unread' ? 'border-primary unread-message' : ''; ?>">
        <div class="card-header d-flex justify-content-between align-items-center">
          <div>
            <strong><i class="fas fa-user"></i> <?php echo htmlspecialchars($row['name']); ?></strong>
            <span class="text-muted ml-3">
              <i class="fas fa-envelope"></i> 
              <a href="mailto:<?php echo htmlspecialchars($row['email']); ?>" class="text-muted">
                <?php echo htmlspecialchars($row['email']); ?>
              </a>
            </span>
          </div>
          <div>
            <?php if ($row['status'] == 'unread'): ?>
              <span class="badge badge-danger">Unread</span>
            <?php else: ?>
              <span class="badge badge-success">Read</span>
            <?php endif; ?>
          </div>
        </div>
        <div class="card-body">
          <p class="card-text"><?php echo nl2br(htmlspecialchars($row['message'])); ?></p>
          <small class="text-muted">
            <i class="fas fa-clock"></i> 
            <?php 
              $msg_time = strtotime($row['created_at']);
              $time_diff = time() - $msg_time;
              
              if ($time_diff < 3600) {
                echo floor($time_diff / 60) . ' minutes ago';
              } elseif ($time_diff < 86400) {
                echo floor($time_diff / 3600) . ' hours ago';
              } else {
                echo date('F d, Y h:i A', $msg_time);
              }
            ?>
          </small>
        </div>
        <div class="card-footer">
          <div class="btn-group" role="group">
            <?php if ($row['status'] == 'unread'): ?>
              <a href="?mark_read=<?php echo $row['id']; ?>&token=<?php echo $_SESSION['csrf_token']; ?>&filter=<?php echo $status_filter; ?>&page=<?php echo $page; ?>" 
                 class="btn btn-sm btn-primary">
                <i class="fas fa-check"></i> Mark as Read
              </a>
            <?php endif; ?>
            
            <a href="mailto:<?= htmlspecialchars($row['email']) ?>?subject=Re:%20Your%20message%20to%20OSMS&body=Dear%20<?=        rawurlencode($row['name']) ?>,%0D%0A%0D%0AThank%20you%20for%20contacting%20OSMS.%0D%0A%0D%0ABest%20regards,%0D%0A     OSMS %20Support%20Team" 
                class="btn btn-sm btn-success">
                <i class="fas fa-reply"></i> Reply via Email
            </a>           
            <a href="?delete=<?php echo $row['id']; ?>&token=<?php echo $_SESSION['csrf_token']; ?>&filter=<?php echo $status_filter; ?>&page=<?php echo $page; ?>" 
               class="btn btn-sm btn-danger" 
               onclick="return confirm('Are you sure you want to delete this message? This action cannot be undone.')">
              <i class="fas fa-trash"></i> Delete
            </a>
          </div>
        </div>
      </div>
    <?php endwhile; ?>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
      <nav aria-label="Message pagination">
        <ul class="pagination justify-content-center">
          <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
            <a class="page-link" href="?page=<?php echo $page - 1; ?>&filter=<?php echo $status_filter; ?>">Previous</a>
          </li>
          
          <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <?php if ($i == 1 || $i == $total_pages || abs($i - $page) <= 2): ?>
              <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                <a class="page-link" href="?page=<?php echo $i; ?>&filter=<?php echo $status_filter; ?>">
                  <?php echo $i; ?>
                </a>
              </li>
            <?php elseif (abs($i - $page) == 3): ?>
              <li class="page-item disabled"><span class="page-link">...</span></li>
            <?php endif; ?>
          <?php endfor; ?>
          
          <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
            <a class="page-link" href="?page=<?php echo $page + 1; ?>&filter=<?php echo $status_filter; ?>">Next</a>
          </li>
        </ul>
      </nav>
    <?php endif; ?>

  <?php else: ?>
    <div class="alert alert-info">
      <i class="fas fa-info-circle"></i> 
      <?php 
        if ($status_filter == 'unread') {
          echo "No unread messages.";
        } elseif ($status_filter == 'read') {
          echo "No read messages.";
        } else {
          echo "No contact messages yet.";
        }
      ?>
    </div>
  <?php endif; ?>
</div>

<style>
.message-card {
  transition: all 0.3s ease;
}

.message-card:hover {
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
  transform: translateY(-2px);
}

.unread-message {
  border-left: 4px solid #007bff !important;
  background-color: #f8f9ff;
}

.card-header a.text-muted:hover {
  color: #007bff !important;
  text-decoration: none;
}

.btn-group .btn {
  margin-right: 5px;
}

.alert {
  animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>

<?php
include('includes/footer.php'); 
$conn->close();
?>