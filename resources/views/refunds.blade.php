<?php

/* Simple access protection
if (!isset($_GET['key']) || $_GET['key'] !== getenv('orders_secret')) {
    die("Unauthorized");
}
*/
$logFile = 'refunds.log';
$refunds = [];

if (file_exists($logFile)) {
    $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $refunds[] = json_decode($line, true);
    }
}
?>
<
    <title>Refund History</title>
    <style>
        body {
            font-family: Arial, serif; background:#f4f4f4; padding:20px; }
        table { width:100%; background:white; border-collapse:collapse; }
        th, td { padding:12px; border-bottom:1px solid #ddd; }
        th { background:#333; color:white; }
    </style>


<h1>Refund History</h1>

<?php if (empty($refunds)): ?>
    <p>No refunds were found.</p>
<?php else: ?>
    <table>
        <tr>
            <th>Payment Intent</th>
            <th>Amount Refunded</th>
            <th>Timestamp</th>
        </tr>

        <?php foreach ($refunds as $refund): ?>
            <tr>
                <td><?= htmlspecialchars($refund['payment_intent']) ?></td>
                <td>$<?= htmlspecialchars(number_format($refund['amount'] / 100, 2)) ?></td>
                <td><?= htmlspecialchars($refund['timestamp']) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>



