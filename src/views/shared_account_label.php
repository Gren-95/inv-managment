<?php
use Picqer\Barcode\BarcodeGeneratorPNG;
require_once 'vendor/autoload.php';

// Generate barcode for the passcode
$tempDir = "temp/";
if (!file_exists($tempDir)) {
    mkdir($tempDir, 0755, true);
}
$barcodeFileName = 'barcode_' . $account['id'] . '.png';
$barcodeFile = $tempDir . $barcodeFileName;

// Create barcode with the passcode
$generator = new BarcodeGeneratorPNG();
$barcode = $generator->getBarcode($account['passcode'], $generator::TYPE_CODE_128, 3, 50);
file_put_contents($barcodeFile, $barcode);

// Label dimensions
$labelWidth = 62;   // narrower width
$labelHeight = 30;  // shorter height
?>
<!DOCTYPE html>
<html>
<head>
    <title>Account Label - <?= htmlspecialchars($account['username']) ?></title>
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
        }

        .back-button {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            background-color: #0d6efd;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-family: Arial, sans-serif;
        }

        .back-button:hover {
            background-color: #0b5ed7;
        }

        .print-button {
            position: fixed;
            top: 20px;
            right: 100px; /* Adjusted to place next to back button */
            padding: 10px 20px;
            background-color: #28a745; /* Green color */
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-family: Arial, sans-serif;
        }

        .print-button:before {
            margin-right: 5px;
        }

        .print-button:hover {
            background-color: #218838; /* Darker green on hover */
        }

        @page {
            size: <?= $labelWidth ?>mm <?= $labelHeight ?>mm;
            margin: 0;
        }
        body {
            margin: 0;
            padding: 0;
            width: <?= $labelWidth ?>mm;
            height: <?= $labelHeight ?>mm;
        }
        .label-container {
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            width: 100%;
            height: 100%;
            padding: 3mm 5mm;
            box-sizing: border-box;
        }
        .account-info {
            font-family: Arial, sans-serif;
            margin-bottom: 2mm;
            width: 100%;
        }
        .username, .email, .passcode {
            margin-bottom: 1mm;
            display: flex;
            justify-content: space-between; /* Changed to space-between for right alignment */
            align-items: center;
        }
        .username-label, .email-label, .passcode-label {
            font-size: 10pt;
            color: #666;
            width: 22mm;
            text-align: left;
            padding-right: 2mm;
        }
        .username-value, .email-value, .passcode-value {
            font-size: 11pt;
            width: auto;
            text-align: right; /* Right align the label values */
        }
        .barcode {
            height: 8mm;
            width: 60mm;
            object-fit: contain;
            margin: 0 auto; /* Center the barcode */
            display: block;
        }
    </style>
</head>
<body>
    <a href="?action=shared_accounts" class="back-button no-print">Back</a>
    <a href="javascript:window.print()" class="print-button no-print"> Print</a>
    <div class="label-container">
        <div class="account-info">
            <div class="username">
                <div class="username-label"><strong>Username:</strong> </div>
                <div class="username-value"><?= htmlspecialchars($account['username']) ?></div>
            </div>
            <div class="email">
                <div class="email-label"><strong>Email:</strong> </div>
                <div class="email-value"><?= htmlspecialchars($account['email']) ?></div>
            </div>
            <div class="passcode">
                <div class="passcode-label"><strong>Passcode:</strong> </div>
                <div class="passcode-value"><?= htmlspecialchars($account['passcode']) ?></div>
            </div>
        </div>
        <img src="temp/<?= $barcodeFileName ?>" class="barcode" alt="Passcode Barcode">
    </div>
</body>
</html> 