<?php
  /**
  * Requires the "PHP Email Form" library
  * The "PHP Email Form" library is available only in the pro version of the template
  * The library should be uploaded to: vendor/php-email-form/php-email-form.php
  * For more info and help: https://bootstrapmade.com/php-email-form/
  */

  // Replace contact@example.com with your real receiving email address
  $receiving_email_address = 'contact@example.com';

  if( file_exists($php_email_form = '../assets/vendor/php-email-form/php-email-form.php' )) {
    include( $php_email_form );
  } else {
    die( 'Unable to load the "PHP Email Form" Library!');
  }

  $contact = new PHP_Email_Form;
  $contact->ajax = true;

  $contact->to = $receiving_email_address;
  $contact->from_name = $_POST['name'];
  $contact->from_email = $_POST['email'];
  $contact->subject = $_POST['subject'];

  // Uncomment below code if you want to use SMTP to send emails. You need to enter your correct SMTP credentials
  /*
  $contact->smtp = array(
    'host' => 'example.com',
    'username' => 'example',
    'password' => 'pass',
    'port' => '587'
  );
  */

  $contact->add_message( $_POST['name'], 'From');
  $contact->add_message( $_POST['email'], 'Email');
  if(isset($_POST['phone'])) {
    $contact->add_message( $_POST['phone'], 'Phone');
  }
  if(isset($_POST['preferred_time'])) {
    $contact->add_message( $_POST['preferred_time'], 'Preferred Time to Call');
  }
  if(isset($_POST['preferred_language'])) {
    $contact->add_message( $_POST['preferred_language'], 'Preferred Language');
  }
  $contact->add_message( $_POST['message'], 'Message', 10);

  // Store contact submission in database
  try {
    require_once '../backend/config.php';
    $db = getDB();

    $stmt = $db->prepare("INSERT INTO contact_submissions (name, email, subject, message, phone, preferred_time, preferred_language, submitted_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->execute([
      $_POST['name'],
      $_POST['email'],
      $_POST['subject'],
      $_POST['message'],
      $_POST['phone'] ?? null,
      $_POST['preferred_time'] ?? null,
      $_POST['preferred_language'] ?? null
    ]);

  } catch (Exception $e) {
    // Log database error but don't fail the email sending
    error_log('Contact form database error: ' . $e->getMessage());
  }

  echo $contact->send();
?>
