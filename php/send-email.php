<?php
// Require PHPMailer classes
require 'php/class.phpmailer.php';
require 'php/class.smtp.php';

// Replace with your email address
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
   // Get and clean input data
   $name = trim(strip_tags($_POST['name']));
   $email = trim(strip_tags($_POST['email']));
   $subject = trim(strip_tags($_POST['subject'] ?? 'Contact Form Submission'));
   $contact_message = trim(strip_tags($_POST['message']));
   $captchaResponse = $_POST['g-recaptcha-response'] ?? '';

   // Basic validation
   $err = false;

   if (empty($name) || empty($email) || empty($contact_message)) {
      echo "Please fill all required fields.";
      exit;
   }

   if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      echo "Invalid email format.";
      exit;
   }

   // Verify reCAPTCHA
   $secretKey = 'YOUR_RECAPTCHA_SECRET_KEY'; // Thay bằng secret key của bạn
   $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secretKey}&response={$captchaResponse}");
   $responseKeys = json_decode($response, true);

   if ($responseKeys['success']) {
      // Prepare email content
      $message = "";
      $message .= "Email from: " . htmlspecialchars($name) . "<br />";
      $message .= "Email address: " . htmlspecialchars($email) . "<br />";
      $message .= "Message: <br />";
      $message .= nl2br(htmlspecialchars($contact_message));
      $message .= "<br /> ----- <br /> This email was sent from your site " . url() . " contact form. <br />";

      // Customer email content
      $customer_message = "
            Dear " . htmlspecialchars($name) . ",<br><br>
            Thank you for contacting us!<br>
            We have received your message with the following details:<br><br>
            <strong>Name:</strong> " . htmlspecialchars($name) . "<br />
            <strong>Email:</strong> " . htmlspecialchars($email) . "<br />
            <strong>Message:</strong> " . nl2br(htmlspecialchars($contact_message)) . "<br><br>
            We'll get back to you as soon as possible.<br><br>
            Best regards,<br>Your Team";

      // Initialize PHPMailer
      $mail = new PHPMailer(true);

      try {
         // SMTP Configuration
         $mail->isSMTP();
         $mail->Host = 'smtp.gmail.com'; // Ví dụ: smtp.gmail.com
         $mail->SMTPAuth = true;
         $mail->Username = 'vothanhluan.business@gmail.com'; // Email gửi
         $mail->Password = 'suujygpsuflvdqut'; // Mật khẩu hoặc App Password
         $mail->SMTPSecure = 'tls'; // Hoặc 'ssl'
         $mail->Port = 587; // 587 cho TLS, 465 cho SSL

         // Email to admin
         $mail->setFrom($email, $name);
         $mail->addAddress($to, 'Admin');
         $mail->isHTML(true);
         $mail->Subject = $subject;
         $mail->Body = $message;
         $mail->send();

         // Clear recipients
         $mail->clearAddresses();

         // Email to customer
         $mail->setFrom($to, 'Your Company Name');
         $mail->addAddress($email, $name);
         $mail->Subject = "Thank You for Your Message";
         $mail->Body = $customer_message;
         $mail->send();

         echo "OK";
      } catch (Exception $e) {
         echo "Something went wrong. Error: " . $mail->ErrorInfo;
      }
   } else {
      echo "reCAPTCHA verification failed: " . implode(", ", $responseKeys["error-codes"]);
   }
}
