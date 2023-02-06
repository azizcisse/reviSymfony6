<?php

namespace App\Service;

use Dompdf\Dompdf;
use Dompdf\Options;
use App\Service\PdfService;

class PdfService
{
   private $domPdf; 

   public function __construct(){
    $this->domPdf = new Dompdf();

    $pdfOptions = new Options();

    $pdfOptions->set('defaultFont', 'Garamond');

    $this->domPdf->setOptions($pdfOptions);
   }

   public function showPdfFile($html)
   {
    $this->domPdf->loadHtml($html);
    $this->domPdf->render();
    $this->domPdf->stream("details.pdf", [
        'Attachement' => false,
    ]);  
   }
   public function generateBinrayPDF($html)
   {
    $this->domPdf->loadHtml($html);
    $this->domPdf->render();
    $this->domPdf->output();
   }
}