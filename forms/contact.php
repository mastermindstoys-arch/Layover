<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../assets/vendor/phpmailer/src/Exception.php';
require '../assets/vendor/phpmailer/src/PHPMailer.php';
require '../assets/vendor/phpmailer/src/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $preferred_time = $_POST['preferred_time'];
    $preferred_language = $_POST['preferred_language'];
    $message = $_POST['message'];

    $preferred_time_display = $preferred_time;
    if ($preferred_time == 'morning') $preferred_time_display = 'Morning (9 AM - 12 PM)';
    elseif ($preferred_time == 'afternoon') $preferred_time_display = 'Afternoon (12 PM - 5 PM)';
    elseif ($preferred_time == 'evening') $preferred_time_display = 'Evening (5 PM - 8 PM)';

    $preferred_language_display = ucfirst($preferred_language);

    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Replace with your SMTP host
        $mail->SMTPAuth = true;
        $mail->Username = 'contactlayoversolutions@gmail.com'; // Replace with your email
        $mail->Password = 'bygw edkv qsqi zcxs'; // Replace with your app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom($email, $name);
        $mail->addAddress('contactlayoversolutions@gmail.com');

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'New Contact Form Submission from Layover Solutions Website';
        $mail->Body = "
          <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; border: 1px solid #ddd; border-radius: 8px; overflow: hidden;'>
            <div style='background-color: #28a745; color: white; padding: 20px; text-align: center;'>
              <h2>New Contact Form Submission</h2>
              <p>From Layover Solutions Website</p>
            </div>
            <div style='padding: 20px;'>
              <p><strong>Name:</strong> $name</p>
              <p><strong>Email:</strong> $email</p>
              <p><strong>Phone:</strong> $phone</p>
              <p><strong>Preferred Time:</strong> $preferred_time_display</p>
              <p><strong>Preferred Language:</strong> $preferred_language_display</p>
              <p><strong>Message:</strong></p>
              <p style='background-color: #f8f9fa; padding: 10px; border-radius: 4px;'>$message</p>
            </div>
            <div style='background-color: #f8f9fa; padding: 10px; text-align: center; font-size: 12px; color: #666;'>
              This email was sent from the contact form on Layover Solutions website.
            </div>
          </div>
        ";
        $mail->AltBody = "Name: $name\nEmail: $email\nPhone: $phone\nPreferred Time: $preferred_time_display\nPreferred Language: $preferred_language_display\nMessage:\n$message";

        $mail->send();
        echo 'OK';
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
} else {
    echo 'Invalid request.';
}
?>
