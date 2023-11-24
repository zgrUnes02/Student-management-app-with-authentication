<?php

    namespace App\Service ;

    use Dompdf\Dompdf;
    use Dompdf\Options;

    class PdfService
    {
        private $domPdf ;
        public function __construct() {

            //! Instance the Dompdf class
            $this -> domPdf = new Dompdf() ;

            //! Instance the Options class
            $pdfOptions = new Options() ;
            $pdfOptions -> set('defaultFont' , 'Garamond') ;

            //! Set options to Dompdf ;
            $this -> domPdf -> setOptions($pdfOptions) ;
        }

        public function showStudentPdf($html) {

            $this -> domPdf -> loadHtml($html);

            //! Render the HTML as PDF
            $this -> domPdf -> render();

            //! Output the generated PDF to Browser
            $this -> domPdf -> stream();
        }
    }