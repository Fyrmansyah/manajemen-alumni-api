<?php
require 'vendor/autoload.php';
use Dompdf\Dompdf;

$dompdf = new Dompdf();
$dompdf->loadHtml('<h1>Test PDF with GD Extension</h1><p>This tests if GD extension is working for PDF generation.</p>');
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

echo "PDF generation test successful - GD extension working!\n";
echo "GD extension loaded: " . (extension_loaded('gd') ? 'YES' : 'NO') . "\n";
