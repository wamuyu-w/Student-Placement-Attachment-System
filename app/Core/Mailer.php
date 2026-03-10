<?php
namespace App\Core;

/**
 * Simple Mailer utility using PHP's built-in mail().
 * Requires a working SMTP relay configured in php.ini (or via sendmail).
 * For production, replace with PHPMailer/SwiftMailer + SMTP credentials.
 */
class Mailer {

    private static string $fromEmail = 'noreply@cuea-attachment.ac.ke';
    private static string $fromName  = 'CUEA Attachment System';

    /**
     * Core send method.
     */
    public static function send(string $to, string $subject, string $htmlBody): bool {
        if (empty($to) || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
            error_log("Mailer: Invalid recipient email — $to");
            return false;
        }

        $headers  = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8\r\n";
        $headers .= "From: " . self::$fromName . " <" . self::$fromEmail . ">\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();

        $result = mail($to, $subject, self::wrapHtml($subject, $htmlBody), $headers);

        if (!$result) {
            error_log("Mailer: Failed to send email to $to — Subject: $subject");
        }

        return $result;
    }

    // ─── Notification Methods ───────────────────────────────────────────────

    /**
     * Notify a student that their attachment application was approved.
     */
    public static function notifyStudentApproved(string $email, string $studentName, string $orgName): bool {
        $subject = "Attachment Application Approved";
        $body = "
            <p>Dear <strong>" . htmlspecialchars($studentName) . "</strong>,</p>
            <p>Your attachment application to <strong>" . htmlspecialchars($orgName) . "</strong> has been <span style='color:#16a34a;font-weight:600'>approved</span>.</p>
            <p>You may now log into your student portal to access your logbook and view your supervision details.</p>
            <p>Best regards,<br>CUEA Attachment Office</p>
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
            <p>Best regards,<br>CUEA Attachment Office</p>
        ";
        return self::send($email, $subject, $body);
    }

    /**
     * Notify a host organization that a student has been placed with them.
     */
    public static function notifyHostPlacement(string $email, string $orgName, string $studentName, string $startDate, string $endDate): bool {
        $subject = "New Student Placement — Action Required";
        $body = "
            <p>Dear <strong>" . htmlspecialchars($orgName) . "</strong> Team,</p>
            <p>A student has been approved for attachment at your organization:</p>
            <ul>
                <li><strong>Student:</strong> " . htmlspecialchars($studentName) . "</li>
                <li><strong>Period:</strong> " . htmlspecialchars($startDate) . " to " . htmlspecialchars($endDate) . "</li>
            </ul>
            <p>Please log into your portal to review their logbook and provide weekly feedback.</p>
            <p>Best regards,<br>CUEA Attachment Office</p>
        ";
        return self::send($email, $subject, $body);
    }

    /**
     * Send new host organization login credentials.
     */
    public static function sendHostCredentials(string $email, string $orgName, string $username, string $password): bool {
        $subject = "Your Portal Login Credentials — CUEA Attachment System";
        $body = "
            <p>Dear <strong>" . htmlspecialchars($orgName) . "</strong>,</p>
            <p>An account has been created for your organization on the CUEA Student Attachment Portal.</p>
            <p><strong>Login Credentials:</strong></p>
            <ul>
                <li><strong>Username:</strong> " . htmlspecialchars($username) . "</li>
                <li><strong>Password:</strong> " . htmlspecialchars($password) . "</li>
            </ul>
            <p style='color:#b45309;'><strong>Important:</strong> You will be required to change your password on first login.</p>
            <p>Best regards,<br>CUEA Attachment Office</p>
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
