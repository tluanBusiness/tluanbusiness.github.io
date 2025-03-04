<?php
// Email nhận thư
$to = 'vothanhluan.business@gmail.com';

function url()
{
   return sprintf(
      "%s://%s",
      isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
      $_SERVER['SERVER_NAME']
   );
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
   // Lấy và làm sạch dữ liệu
   $name = trim(strip_tags($_POST['name']));
   $email = trim(strip_tags($_POST['email']));
   $contact_message = trim(strip_tags($_POST['message']));
   $subject = "Contact Form Submission from $name";

   // Validation cơ bản
   if (empty($name) || empty($email) || empty($contact_message)) {
      header("Location: index.html?status=error&msg=Please fill all required fields");
      exit;
   }

   if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      header("Location: index.html?status=error&msg=Invalid email format");
      exit;
   }

   // Chuẩn bị nội dung email
   $message = "";
   $message .= "Email from: " . htmlspecialchars($name) . "<br />";
   $message .= "Email address: " . htmlspecialchars($email) . "<br />";
   $message .= "Message: <br />";
   $message .= nl2br(htmlspecialchars($contact_message));
   $message .= "<br /> ----- <br /> This email was sent from your site " . url() . " contact form. <br />";

   // Headers
   $headers = "From: " . htmlspecialchars($name) . " <" . htmlspecialchars($email) . ">\r\n";
   $headers .= "Reply-To: " . htmlspecialchars($email) . "\r\n";
   $headers .= "MIME-Version: 1.0\r\n";
   $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

   // Gửi email
   $mail_sent = mail($to, $subject, $message, $headers);

   // Chuyển hướng về landing page với thông báo
   if ($mail_sent) {
      header("Location: index.html?status=success");
   } else {
      header("Location: index.html?status=error&msg=Failed to send email");
   }
   exit;
} else {
   // Nếu truy cập trực tiếp file PHP
   header("Location: index.html");
   exit;
}
