<?php
// send_form.php - simple handler to email tracking registrations to sales@saham-sa.com
header('Content-Type: application/json; charset=utf-8');

// Basic CORS (if you'll POST from other host)
if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
    header('Access-Control-Allow-Credentials: true');
}
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Methods: POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    exit(json_encode(['success' => false]));
}

// Helper
function clean($v) {
    return trim(strip_tags($v));
}

// Honeypot anti-spam field (hidden input named hp)
if (!empty($_POST['hp'])) {
    echo json_encode(['success' => false, 'error' => 'spam']);
    exit;
}

$name    = isset($_POST['name']) ? clean($_POST['name']) : '';
$phone   = isset($_POST['phone']) ? clean($_POST['phone']) : '';
$email   = isset($_POST['email']) ? clean($_POST['email']) : '';
$fleet    = isset($_POST['fleet']) ? clean($_POST['fleet']) : '';
$company = isset($_POST['company']) ? clean($_POST['company']) : '';
$details  = isset($_POST['details']) ? clean($_POST['details']) : '';

// Basic validation
$errors = [];
if (mb_strlen($name) < 2) $errors[] = 'الاسم غير صالح';
if (!preg_match('/\d{8,15}/', preg_replace('/[^0-9]/','',$phone))) $errors[] = 'رقم الجوال غير صالح';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'البريد الإلكتروني غير صالح';
if (empty($fleet)) $errors[] = 'اختر حجم الأسطول';

if (!empty($errors)) {
    echo json_encode(['success' => false, 'error' => implode('; ', $errors)]);
    exit;
}

// Recipient
$to = 'sales@saham-sa.com';

$subject = "طلب تسجيل لخدمة التتبع من: " . ($name ?: 'زائر');

$body = "<html><body>";
$body .= "<h2>طلب تسجيل لخدمة التتبع</h2>";
$body .= "<p><strong>الاسم:</strong> " . htmlspecialchars($name) . "</p>";
$body .= "<p><strong>الجوال:</strong> " . htmlspecialchars($phone) . "</p>";
$body .= "<p><strong>البريد الإلكتروني:</strong> " . htmlspecialchars($email) . "</p>";
$body .= "<p><strong>حجم الأسطول المتوقع:</strong> " . htmlspecialchars($fleet) . "</p>";
$body .= "<p><strong>اسم المنشأة:</strong> " . htmlspecialchars($company) . "</p>";
$body .= "<p><strong>تفاصيل إضافية:</strong><br>" . nl2br(htmlspecialchars($details)) . "</p>";
$body .= "</body></html>";

// Headers
$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
// From must be a domain email; create no-reply@ or use sales@ as From if allowed
$from_email = 'no-reply@' . ($_SERVER['SERVER_NAME'] ?? 'saham-sa.com');
$headers .= "From: Sahm Naqil <" . $from_email . ">" . "\r\n";
$headers .= "Reply-To: " . $email . "\r\n";

// Send
$sent = mail($to, $subject, $body, $headers);

if ($sent) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'mail_failed']);
}

exit;
