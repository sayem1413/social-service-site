<?php

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(403);
    exit("Access denied.");
}

function clean($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

$name = clean($_POST['name'] ?? '');
$email = clean($_POST['email'] ?? '');
$subject = clean($_POST['subject'] ?? 'Website Contact');
$message = clean($_POST['message'] ?? '');

if (!$name || !$email || !$message) {
    exit("Please fill all required fields.");
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    exit("Invalid email address.");
}

$to = "info@subaita.org";

$email_subject = "New Website Message: " . $subject;

$email_body = "
Name: $name

Email: $email

Message:
$message
";

$headers = "From: noreply@subaita.org\r\n";
$headers .= "Reply-To: $email\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

if (mail($to, $email_subject, $email_body, $headers)) {
    header("Location: contact.html?success=1");
    exit;
} else {
    header("Location: contact.html?error=1");
    exit;
}