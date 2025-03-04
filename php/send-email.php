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
   $name = trim(stripslashes($_POST['name']));
   $email = trim(stripslashes($_POST['email']));
   $subject = "Contact Form Submission"; // Vì form không có field subject
   $contact_message = trim(stripslashes($_POST['message']));

   // Validation cơ bản
   if (empty($name) || empty($email) || empty($contact_message)) {
      echo "Please fill all required fields.";
      exit;
   }

   if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      echo "Invalid email format.";
      exit;
   }

   // Khởi tạo message
   $message = ""; // Khởi tạo biến trước khi sử dụng
   $message .= "Email from: " . htmlspecialchars($name) . "<br />";
   $message .= "Email address: " . htmlspecialchars($email) . "<br />";
   $message .= "Message: <br />";
   $message .= nl2br(htmlspecialchars($contact_message));
   $message .= "<br /> ----- <br /> This email was sent from your site " . url() . " contact form. <br />";

   // Set From header
   $from = htmlspecialchars($name) . " <" . htmlspecialchars($email) . ">";

   // Email Headers
   $headers = "From: " . $from . "\r\n";
   $headers .= "Reply-To: " . htmlspecialchars($email) . "\r\n";
   $headers .= "MIME-Version: 1.0\r\n";
   $headers .= "Content-Type: text/html; charset=UTF-8\r\n"; // Thay ISO-8859-1 bằng UTF-8

   // Cấu hình sendmail
   ini_set("sendmail_from", $to);

   // Gửi mail
   $mail = mail($to, $subject, $message, $headers);

   if ($mail) {
      echo "OK";
   } else {
      echo "Something went wrong. Please try again.";
   }
}
