<?php
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
require_once 'vendor/autoload.php';

// Generate QR code
$tempDir = "temp/";  // Relative to web root
if (!file_exists($tempDir)) {
    mkdir($tempDir, 0755, true);
}
$qrFileName = 'qr_' . $item['serial_number'] . '.png';
$qrFile = $tempDir . $qrFileName;

// Create QR code
$qrCode = new QrCode($item['serial_number']);
$qrCode->setSize(200);  // Smaller size for label
$qrCode->setMargin(5);  // Smaller margin

// Create generic writer
$writer = new PngWriter();

// Write the QR code to a file
$result = $writer->write($qrCode);
$result->saveToFile($qrFile);

// Label dimensions (in mm)
$labelWidth = 62;
$labelHeight = 14;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Equipment Label - <?= htmlspecialchars($item['serial_number']) ?></title>
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
            align-items: center;
            justify-content: space-between;
            width: 100%;
            height: 100%;
            padding: 1mm;
            box-sizing: border-box;
        }
        .qr-code {
            height: 12mm;
            width: 12mm;
            object-fit: contain;
        }
        .serial-number {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            text-align: center;
            flex-grow: 1;
            padding: 0 2mm;
            font-weight: bold;
        }
        .company-logo {
            height: 12mm;
            width: auto;
            max-width: 20mm;
            object-fit: contain;
        }
    </style>
</head>
<body>
    <a href="index.php" class="back-button no-print">← Back to List</a>
    <div class="label-container">
        <img src="temp/<?= $qrFileName ?>" class="qr-code" alt="QR Code">
        <div class="serial-number"><?= htmlspecialchars($item['serial_number']) ?></div>
        <img src="assets/company-logo.png" class="company-logo" alt="Company Logo">
    </div>
</body>
</html> 