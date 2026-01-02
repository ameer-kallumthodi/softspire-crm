<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// Authentication Routes
Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'doLogin'])->name('doLogin');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Redirect root to login
Route::get('/', function () {
    return redirect()->route('login');
});

// Test route for PDF view (no authentication required)
Route::get('/test-pdf/{quotation}', function (\App\Models\Quotation $quotation) {
    $quotation->load(['customer.country', 'items']);
    
    // Generate PDF using dompdf
    if (class_exists('\Barryvdh\DomPDF\Facade\Pdf')) {
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.quotations.pdf', compact('quotation'));
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isRemoteEnabled', true);
        $pdf->setOption('defaultFont', 'DejaVu Sans');
        $pdf->setOption('isPhpEnabled', true);
        $pdf->setOption('chroot', public_path());
        return $pdf->stream($quotation->quotation_number . '.pdf');
    } elseif (class_exists('\Dompdf\Dompdf')) {
        $html = view('admin.quotations.pdf', compact('quotation'))->render();
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->set_option('isHtml5ParserEnabled', true);
        $dompdf->set_option('isRemoteEnabled', true);
        $dompdf->set_option('defaultFont', 'DejaVu Sans');
        $dompdf->set_option('isPhpEnabled', true);
        $dompdf->render();
        return $dompdf->stream($quotation->quotation_number . '.pdf');
    } else {
        // Fallback: return HTML view
        return view('admin.quotations.pdf', compact('quotation'));
    }
})->name('test.pdf');
