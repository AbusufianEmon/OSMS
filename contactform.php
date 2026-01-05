<?php
// Start session to use flash messages
session_start();

// Include database connection
include('dbConnection.php');

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Sanitize and validate input data
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';
    
    // Basic validation
    $errors = [];
    
    if (empty($name)) {
        $errors[] = "Name is required";
    }
    
    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    
    if (empty($message)) {
        $errors[] = "Message is required";
    }
    
    // If no errors, proceed
    if (empty($errors)) {
        
        // Escape data for database
        $name = $conn->real_escape_string($name);
        $email = $conn->real_escape_string($email);
        $message = $conn->real_escape_string($message);
        
        // Insert into database
        $sql = "INSERT INTO contact_messages (name, email, message, status, created_at) 
                VALUES ('$name', '$email', '$message', 'unread', NOW())";
        
        if ($conn->query($sql) === TRUE) {
            
            // OPTION 1: Send email to admin (Optional - uncomment to use)
            /*
            $to = "admin@osmspvt.com.bd"; // Replace with your admin email
            $subject = "New Contact Form Message from OSMS Website";
            $email_message = "New contact form submission:\n\n";
            $email_message .= "Name: " . strip_tags($_POST['name']) . "\n";
            $email_message .= "Email: " . strip_tags($_POST['email']) . "\n";
            $email_message .= "Message: " . strip_tags($_POST['message']) . "\n\n";
            $email_message .= "Submitted on: " . date('Y-m-d H:i:s') . "\n";
            
            $headers = "From: noreply@osmspvt.com.bd\r\n";
            $headers .= "Reply-To: " . $_POST['email'] . "\r\n";
            $headers .= "X-Mailer: PHP/" . phpversion();
            
            // Send email
            @mail($to, $subject, $email_message, $headers);
            */
            
            // OPTION 2: Send auto-reply to user (Optional - uncomment to use)
            /*
            $user_subject = "Thank you for contacting OSMS";
            $user_message = "Dear " . $_POST['name'] . ",\n\n";
            $user_message .= "Thank you for reaching out to us. We have received your message and will respond within 24 hours.\n\n";
            $user_message .= "Best regards,\nOSMS Team\n";
            $user_message .= "Temuki, Sylhet\nPhone: 01640027997";
            
            $user_headers = "From: noreply@osmspvt.com.bd\r\n";
            $user_headers .= "X-Mailer: PHP/" . phpversion();
            
            @mail($_POST['email'], $user_subject, $user_message, $user_headers);
            */
            
            // Set success message
            $_SESSION['contact_success'] = "Thank you for contacting us! We'll get back to you soon.";
            
        } else {
            // Database error
            $_SESSION['contact_error'] = "Sorry, something went wrong. Please try again later.";
            error_log("Contact Form DB Error: " . $conn->error);
        }
        
    } else {
        // Validation errors
        $_SESSION['contact_error'] = implode("<br>", $errors);
    }
    
} else {
    // Invalid request method
    $_SESSION['contact_error'] = "Invalid request method.";
}

// Close connection
$conn->close();

// Redirect back to homepage
header("Location: index.php#contact");
exit();
?>