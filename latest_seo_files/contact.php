<?php
/**
 * Subaita Foundation — Contact Form Mailer
 * cPanel / Shared Hosting Compatible
 * 
 * SETUP: Change $to_email to your real email address below.
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// ── CONFIG ── Change this to your real email ─────────────────
$to_email   = 'info@subaitafoundation.com';
$site_name  = 'Subaita Foundation';
$site_url   = 'https://subaitafoundation.com';
// ─────────────────────────────────────────────────────────────

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Sanitize inputs
function clean($val) {
    return htmlspecialchars(strip_tags(trim($val)), ENT_QUOTES, 'UTF-8');
}

$fname   = clean($_POST['fname']   ?? '');
$lname   = clean($_POST['lname']   ?? '');
$email   = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
$phone   = clean($_POST['phone']   ?? '');
$subject = clean($_POST['subject'] ?? 'General Inquiry');
$message = clean($_POST['message'] ?? '');

// Validate required fields
if (empty($fname) || empty($email) || empty($message)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Please fill in all required fields.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid email address.']);
    exit;
}

// Basic spam check
if (strlen($message) < 10) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Message is too short.']);
    exit;
}

$full_name = $fname . ' ' . $lname;

// Build email
$email_subject = "[{$site_name}] {$subject} — from {$full_name}";

$email_body = "
========================================
  NEW MESSAGE — {$site_name}
========================================

Name    : {$full_name}
Email   : {$email}
Phone   : {$phone ?: 'Not provided'}
Subject : {$subject}

Message:
--------
{$message}

========================================
Sent from: {$site_url}/contact.html
Date/Time: " . date('Y-m-d H:i:s') . " (Server Time)
========================================
";

$headers  = "From: {$site_name} <noreply@subaitafoundation.com>\r\n";
$headers .= "Reply-To: {$full_name} <{$email}>\r\n";
$headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

// Send email
$sent = mail($to_email, $email_subject, $email_body, $headers);

if ($sent) {
    // Auto-reply to sender
    $reply_subject = "Thank you for contacting {$site_name}";
    $reply_body = "
Dear {$fname},

Thank you for reaching out to Subaita Foundation!

We have received your message and our team will get back to you within 1–2 business days.

Your message:
--------------
{$message}
--------------

If your matter is urgent, please call us at: +880 1700 000000
Office Hours: Saturday – Thursday, 9:00 AM – 6:00 PM

With warm regards,
Subaita Foundation Team
Dolon Chapa City Plaza (Ground Floor), Godnail, Narayanganj
info@subaitafoundation.com
https://subaitafoundation.com
";
    $reply_headers  = "From: {$site_name} <info@subaitafoundation.com>\r\n";
    $reply_headers .= "MIME-Version: 1.0\r\n";
    $reply_headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    mail($email, $reply_subject, $reply_body, $reply_headers);

    echo json_encode(['success' => true, 'message' => 'Your message has been sent successfully! We will reply within 1–2 business days.']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to send message. Please try calling us directly.']);
}
?>
