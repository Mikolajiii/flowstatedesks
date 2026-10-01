<?php

///* add this in ploi
//STRIPE_SECRET_KEY=sk_test_123456
//STRIPE_WEBHOOK_SECRET=whsec_ABCDEF
//SENDGRID_API_KEY=SG.xxxxxx
//orders_secret=pickle

/* --- SIMPLE ACCESS PROTECTION ---
if (!isset($_GET['key']) || $_GET['key'] !== getenv('orders_secret')) {
    die("Unauthorized");
}
?>
*/
// Path to your log file
$logFile = 'orders.log';

$orders = [];

if (file_exists($logFile)) {
    $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        $orders[] = json_decode($line, true);
    }
}
?>

    <title>Orders Viewer</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            padding: 20px;
        }
        h1 {
            text-align: center;
            margin-bottom: 30px;
        }
        table {
            width: 100%;
            background: white;
            border-collapse: collapse;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }
        th {
            background: #333;
            color: white;
        }
        tr:hover {
            background: #f1f1f1;
        }
    </style>


<h1>FlowState Orders</h1>

<?php if (empty($orders)): ?>
    <p>No orders found.</p>
<?php else: ?>
    <table>
        <tr>
            <th>Email</th>
            <th>Name</th>
            <th>Address Line 1</th>
            <th>Address Line 2</th>
            <th>City</th>
            <th>State</th>
            <th>Postal Code</th>
            <th>Country</th>
            <th>Product</th>
            <th>Price ID</th>
            <th>Amount</th>
            <th>Session ID</th>
            <th>Payment Intent</th>
            <th>Payment Status</th>
            <th>Timestamp</th>
            <th>Refund</th>
        </tr>

        <?php foreach ($orders as $order): ?>
            <tr>
                <td><?= htmlspecialchars($order['email']) ?></td>
                <td><?= htmlspecialchars($order['name'] ?? '') ?></td>
                <td><?= htmlspecialchars($order['address_line1'] ?? '') ?></td>
                <td><?= htmlspecialchars($order['address_line2'] ?? '') ?></td>
                <td><?= htmlspecialchars($order['city'] ?? '') ?></td>
                <td><?= htmlspecialchars($order['state'] ?? '') ?></td>
                <td><?= htmlspecialchars($order['postal_code'] ?? '') ?></td>
                <td><?= htmlspecialchars($order['country'] ?? '') ?></td>

                <td><?= htmlspecialchars($order['product'] ?? '') ?></td>
                <td><?= htmlspecialchars($order['price_id'] ?? '') ?></td>

                <td>$<?= htmlspecialchars($order['amount']) ?></td>
                <td><?= htmlspecialchars($order['session_id']) ?></td>
                <td><?= htmlspecialchars($order['payment_intent'] ?? '') ?></td>
                <td><?= htmlspecialchars($order['payment_status'] ?? '') ?></td> <!-- NEW -->
                <td><?= htmlspecialchars($order['timestamp']) ?></td>

                <td>
                    <button
                            class="refundBtn"
                            data-pi="<?= htmlspecialchars($order['payment_intent']) ?>"
                            data-email="<?= htmlspecialchars($order['email']) ?>"
                    >
                        Refund
                    </button>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <!-- Refund Modal -->
    <div id="refundModal" style="
    display:none;
    position:fixed;
    top:0; left:0;
    width:100%; height:100%;
    background:rgba(0,0,0,0.6);
">
        <div style="
        background:white;
        width:400px;
        margin:100px auto;
        padding:20px;
        border-radius:8px;
    ">
            <h2>Refund Customer</h2>

            <form method="POST" action="refund.blade.php">
                <input type="hidden" name="payment_intent" id="refundPi">

                <label>Amount to Refund (leave blank for full refund)</label>
                <input
                        type="number"
                        name="amount"
                        step="0.01"
                        min="0"
                        style="width:100%; padding:8px; margin-top:8px;"
                >

                <button type="submit" style="
                margin-top:15px;
                padding:10px 15px;
                background:#d9534f;
                color:white;
                border:none;
                border-radius:5px;
            ">
                    Process Refund
                </button>

                <button type="button" id="closeRefundModal" style="
                margin-left:10px;
                padding:10px 15px;
                background:#777;
                color:white;
                border:none;
                border-radius:5px;
            ">
                    Cancel
                </button>
            </form>
        </div>
    </div>
    <h2 style="margin-top:40px;">Refund History</h2>

    <?php
    $refundLog = 'refunds.log';
    $refunds = [];

    if (file_exists($refundLog)) {
        $lines = file($refundLog, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $refunds[] = json_decode($line, true);
        }
    }
    ?>

    <?php if (empty($refunds)): ?>
        <p>No refunds recorded.</p>
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

<?php endif; ?>

<script>
    document.querySelectorAll('.refundBtn').forEach(btn => {
        btn.addEventListener('click', () => {
            const pi = btn.getAttribute('data-pi');
            document.getElementById('refundPi').value = pi;
            document.getElementById('refundModal').style.display = 'block';
        });
    });

    document.getElementById('closeRefundModal').addEventListener('click', () => {
        document.getElementById('refundModal').style.display = 'none';
    });
</script>




