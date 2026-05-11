<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load PHPMailer manually
require __DIR__ . '/src/Exception.php';
require __DIR__ . '/src/PHPMailer.php';
require __DIR__ . '/src/SMTP.php';

function sendPayslipEmail($toEmail, $empName, $month, $year, $pdfPath = null)
{
    try {

        $mail = new PHPMailer(true);

        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'aadinkpharmma@gmail.com';
        $mail->Password   = 'axwy itwp xsse qhcm';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('support@nirmalc.org', 'Payroll System');
        $mail->addAddress($toEmail, $empName);

        // 🔥 Attach PDF if available
        if ($pdfPath && file_exists($pdfPath)) {
            $mail->addAttachment($pdfPath, "Salary_Slip_{$empName}_{$month}_{$year}.pdf");
        }

        $mail->isHTML(true);
        $mail->Subject = "Payslip for $month $year";

        $mail->Body = "
<div style='font-family:Arial,sans-serif;line-height:1.6'>
    <p>Dear <b>$empName</b>,</p>

    <p>Please find your attached salary slip for <b>$month $year</b>.</p>

    <p>This is an automated message from the Payroll System.</p>

    <p>If you have any questions, feel free to contact HR.</p>

    <br>

    <p>Regards,<br>
    <b>HR Team</b><br>
    Aadink Pharma Pvt Ltd</p>
</div>
";

        $mail->AltBody = "Payslip for $month $year attached.";

        return $mail->send();

    } catch (Exception $e) {
        return $e->getMessage();
    }
}
?>