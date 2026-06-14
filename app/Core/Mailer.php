<?php
namespace App\Core;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Mailer utility using PHPMailer with Gmail SMTP.
 * Falls back to PHP's built-in mail() if .env credentials are not found.
 */
class Mailer {

    private static function getEnvVars() {
        // get the .env variable
        $envFile = __DIR__ . '/../../.env';
        
        //provide auth credentials such as host, port, username and password
        $credentials = ['host' => 'smtp.gmail.com', 'port' => 465, 'username' => '', 'password' => ''];
        
        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line) || $line[0] === '#') continue;
                
                $parts = explode('=', $line, 2);
                if (count($parts) === 2) {
                    $key = trim($parts[0]);
                    $value = trim($parts[1]);
                    // Strip enclosing quotes if present
                    $value = preg_replace('/^["\']|["\']$/', '', $value);
                    
                    if ($key === 'APP_KEY') {
                        $credentials['password'] = str_replace(' ', '', $value);
                    } elseif ($key === 'USER_MAIL') {
                        $credentials['username'] = $value;
                    }
                }
            }
        }
        return $credentials;
    }

    /**
     * Core send method.
     */
    // Set to false to enable real email sending
    private static bool $testingMode = false;

    //sending the actual email function
    public static function send(string $to, string $subject, string $htmlBody): bool {
        if (empty($to) || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
            error_log("Mailer: Invalid recipient email — $to");
            return false;
        }

        // Testing mode: skip actual sending, just log
        if (self::$testingMode) {
            error_log("Mailer [TEST MODE]: Would send to=$to | subject=$subject");
            return true;
        }

        $env = self::getEnvVars();
        if (empty($env['username']) || empty($env['password'])) {
            error_log("Mailer: SMTP credentials missing in .env. Falling back to built-in mail().");
            $fromEmail = 'michellewachira25@gmail.com';
            $fromName  = 'CUEA Attachment System';
            $headers  = "MIME-Version: 1.0\r\n";
            $headers .= "Content-type: text/html; charset=UTF-8\r\n";
            $headers .= "From: $fromName <$fromEmail>\r\n";
            $headers .= "X-Mailer: PHP/" . phpversion();
            $result = mail($to, $subject, self::wrapHtml($subject, $htmlBody), $headers);
            if (!$result) {
                error_log("Mailer: Failed to send email to $to via mail() — Subject: $subject");
            }
            return $result;
        }

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = $env['host'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $env['username'];
            $mail->Password   = $env['password'];
            // Using SMTPS on port 465
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = $env['port'];

            $mail->setFrom($env['username'], 'CUEA Attachment System');
            $mail->addAddress($to);

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = self::wrapHtml($subject, $htmlBody);
            
            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Mailer could not send email to $to. Error: {$mail->ErrorInfo}");
            return false;
        }
    }

    // ─── Notification Methods ───────────────────────────────────────────────

    // Notify a student that their attachment application was approved
    public static function notifyStudentApproved(string $email, string $studentName, string $orgName): bool {
        $subject = "Attachment Application Approved";
        $body = "
            <p>Dear <strong>" . htmlspecialchars($studentName) . "</strong>,</p>
            <p>Your attachment application to <strong>" . htmlspecialchars($orgName) . "</strong> has been <span style='color:#16a34a;font-weight:600'>approved</span>.</p>
            <p>You may now log into your student portal to access your logbook and view your supervision details.</p>
            <p>Best regards,<br>CUEA - Industrial Attachment Department</p>
        ";
        return self::send($email, $subject, $body);
    }

    /**
     * Notify a student that their attachment application was rejected.
     */
    public static function notifyStudentRejected(string $email, string $studentName, string $reason): bool {
        $subject = "Attachment Application Update";
        $body = "
            <p>Dear <strong>" . htmlspecialchars($studentName) . "</strong>,</p>
            <p>Unfortunately, your attachment application has been <span style='color:#dc2626;font-weight:600'>declined</span>.</p>
            <p><strong>Reason:</strong> " . htmlspecialchars($reason) . "</p>
            <p>Please contact the Attachment Office for further assistance.</p>
            <p>Best regards,<br>CUEA - Industrial Attachment Department</p>
        ";
        return self::send($email, $subject, $body);
    }

    /**
     * Notify a host organization that a student has been placed with them.
     */
    public static function notifyHostPlacement(string $email, string $orgName, string $studentName, string $startDate, string $endDate): bool {
        $subject = "New Student Placement: Action Required";
        $body = "
            <p>Dear <strong>" . htmlspecialchars($orgName) . "</strong> Team,</p>
            <p>A student has been approved for attachment at your organization:</p>
            <ul>
                <li><strong>Student:</strong> " . htmlspecialchars($studentName) . "</li>
                <li><strong>Period:</strong> " . htmlspecialchars($startDate) . " to " . htmlspecialchars($endDate) . "</li>
            </ul>
            <p>Please log into your portal to review their logbook and provide weekly feedback.</p>
            <p>Best regards,<br>CUEA - Industrial Attachment Department</p>
        ";
        return self::send($email, $subject, $body);
    }

    /**
     * Send new host organization login credentials.
     */
    public static function sendHostCredentials(string $email, string $orgName, string $username, string $password): bool {
        $subject = "Your Portal Login Credentials: CUEA Attachment System";
        $body = "
            <p>Dear <strong>" . htmlspecialchars($orgName) . "</strong>,</p>
            <p>An account has been created for your organization on the CUEA Student Attachment Portal.</p>
            <p><strong>Login Credentials:</strong></p>
            <ul>
                <li><strong>Username:</strong> " . htmlspecialchars($username) . "</li>
                <li><strong>Password:</strong> " . htmlspecialchars($password) . "</li>
            </ul>
            <p style='color:#b45309;'><strong>Important:</strong> You will be required to change your password on first login.</p>
            <p>Best regards,<br>CUEA - Industrial Attachment Department</p>
        ";
        return self::send($email, $subject, $body);
    }

    /**
     * Send reset password default.
     */
    public static function sendDefaultPassword(string $email, string $name, string $defaultPassword): bool {
        $subject = "Password Reset - CUEA Attachment System";
        $body = "
            <p>Dear <strong>" . htmlspecialchars($name) . "</strong>,</p>
            <p>Your password has been reset. Please use the following default password to log in:</p>
            <p style='font-size: 1.2em; font-weight: bold; color: #8B1538; padding: 10px; background: #fee2e2; display: inline-block; border-radius: 4px;'>" . htmlspecialchars($defaultPassword) . "</p>
            <p>You will be required to change this password upon your first login.</p>
            <p>If you did not request this change, please contact the administrator immediately.</p>
            <p>Best regards,<br>CUEA - Industrial Attachment Department</p>
        ";
        return self::send($email, $subject, $body);
    }

    /**
     * Send password reset link.
     */
    public static function sendPasswordResetLink(string $email, string $name, string $resetLink): bool {
        $subject = "Password Reset Link - CUEA Attachment System";
        $body = "
            <p>Dear <strong>" . htmlspecialchars($name) . "</strong>,</p>
            <p>We received a request to reset your password. Click the link below to choose a new password:</p>
            <p><a href='" . htmlspecialchars($resetLink) . "' style='display: inline-block; padding: 10px 20px; background-color: #8B1538; color: #ffffff; text-decoration: none; border-radius: 4px; font-weight: bold;'>Reset Password</a></p>
            <p>If the button does not work, copy and paste this link into your browser:</p>
            <p><a href='" . htmlspecialchars($resetLink) . "'>" . htmlspecialchars($resetLink) . "</a></p>
            <p>This link will expire in 1 hour.</p>
            <p>If you did not request this, please ignore this email or contact the administrator.</p>
            <p>Best regards,<br>CUEA - Industrial Attachment Department</p>
        ";
        return self::send($email, $subject, $body);
    }

    /**
     * Notify a student about their assigned supervisor.
     */
    public static function notifySupervisorAssigned(string $studentEmail, string $studentName, string $lecturerName, string $lecturerEmail): bool {
        $subject = "Your Attachment Supervisor Has Been Assigned";
        $body = "
            <p>Dear <strong>" . htmlspecialchars($studentName) . "</strong>,</p>
            <p>A university supervisor has been assigned to oversee your industrial attachment.</p>
            <p><strong>Supervisor Details:</strong></p>
            <ul>
                <li><strong>Name:</strong> " . htmlspecialchars($lecturerName) . "</li>
                <li><strong>Email:</strong> " . htmlspecialchars($lecturerEmail) . "</li>
            </ul>
            <p>Please log into the portal to review any further details and ensure your logbook is kept up-to-date for their review.</p>
            <p>Best regards,<br>CUEA Industrial Attachment Department</p>
        ";
        return self::send($studentEmail, $subject, $body);
    }

    /**
     * Notify a student and/or host about an upcoming scheduled assessment.
     */
    public static function notifyAssessmentScheduled(string $email, string $recipientName, string $studentName, string $assessmentDate, string $assessmentType): bool {
        $subject = "Upcoming Assessment Scheduled: $assessmentType";
        $body = "
            <p>Dear <strong>" . htmlspecialchars($recipientName) . "</strong>,</p>
            <p>This is a formal notification that an attachment assessment has been scheduled.</p>
            <ul>
                <li><strong>Student:</strong> " . htmlspecialchars($studentName) . "</li>
                <li><strong>Assessment Type:</strong> " . htmlspecialchars($assessmentType) . "</li>
                <li><strong>Scheduled Date:</strong> " . htmlspecialchars($assessmentDate) . "</li>
            </ul>
            <p>Please ensure all necessary preparations are made prior to this date. The host organization will need to generate a unique assessment code from their portal for the supervisor to conduct the assessment.</p>
            <p>Best regards,<br>CUEA Industrial Attachment Department</p>
        ";
        return self::send($email, $subject, $body);
    }

    // ─── Private Helpers ─────────────────────────────────────────────────────

    private static function wrapHtml(string $title, string $body): string {
        return "<!DOCTYPE html>
<html>
<head><meta charset='UTF-8'><title>" . htmlspecialchars($title) . "</title></head>
<body style='font-family:sans-serif;color:#1e293b;background:#f8fafc;margin:0;padding:20px;'>
  <div style='max-width:600px;margin:0 auto;background:#fff;border-radius:8px;padding:32px;border:1px solid #e2e8f0;'>
    <div style='border-bottom:3px solid #8B1538;padding-bottom:16px;margin-bottom:24px;'>
      <h2 style='color:#8B1538;margin:0;'>CUEA Attachment System</h2>
    </div>
    $body
  </div>
</body>
</html>";
    }
}
