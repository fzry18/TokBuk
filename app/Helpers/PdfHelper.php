<?php

namespace App\Helpers;

use Barryvdh\DomPDF\Facade as PDF;

class PdfHelper
{
    /**
     * Generate PDF dengan PHP 8+ compatibility
     *
     * @param string $view
     * @param array $data
     * @param string $paper
     * @param string $orientation
     * @return \Barryvdh\DomPDF\PDF
     */
    public static function generatePdf($view, $data = [], $paper = 'a4', $orientation = 'portrait')
    {
        // Suppress all warnings and deprecation errors for DOMPDF
        $old_error_reporting = error_reporting(0);
        ini_set('display_errors', 0);

        // Set memory limit untuk PDF generation
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 300);

        try {
            // Start output buffering to capture any warnings
            ob_start();

            // Set DomPDF options untuk PHP 8+ compatibility
            $pdf = PDF::loadView($view, $data)
                ->setPaper($paper, $orientation)
                ->setOptions([
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled' => false,
                    'defaultFont' => 'DejaVu Sans',
                    'enable_php' => false,
                    'enable_remote' => false,
                    'chroot' => realpath(base_path()),
                    'logOutputFile' => null,
                    'tempDir' => sys_get_temp_dir(),
                    'fontDir' => storage_path('fonts/'),
                    'fontCache' => storage_path('fonts/'),
                    'isPhp8Compat' => true
                ]);

            ob_end_clean(); // Clear any output buffer

            // Restore error reporting
            error_reporting($old_error_reporting);
            ini_set('display_errors', 1);

            return $pdf;
        } catch (\Exception $e) {
            // Restore error reporting in case of exception
            error_reporting($old_error_reporting);
            ini_set('display_errors', 1);

            throw $e;
        }
    }

    /**
     * Generate PDF dan langsung download
     *
     * @param string $view
     * @param array $data
     * @param string $filename
     * @param string $paper
     * @param string $orientation
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public static function downloadPdf($view, $data = [], $filename = 'document.pdf', $paper = 'a4', $orientation = 'portrait')
    {
        $pdf = self::generatePdf($view, $data, $paper, $orientation);
        return $pdf->download($filename);
    }

    /**
     * Generate PDF dan stream di browser
     *
     * @param string $view
     * @param array $data
     * @param string $filename
     * @param string $paper
     * @param string $orientation
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public static function streamPdf($view, $data = [], $filename = 'document.pdf', $paper = 'a4', $orientation = 'portrait')
    {
        $pdf = self::generatePdf($view, $data, $paper, $orientation);
        return $pdf->stream($filename);
    }
}
