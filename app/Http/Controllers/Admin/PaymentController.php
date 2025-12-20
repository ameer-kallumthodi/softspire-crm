<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Models\Payment;
use App\Models\Quotation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PaymentController extends BaseController
{
    public function index()
    {
        // Get all payments with quotations
        $payments = Payment::with(['quotation.customer'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('admin.payments.index', compact('payments'));
    }

    public function pendingQuotations()
    {
        // Get pending quotations (accepted but not fully paid)
        $pendingQuotations = Quotation::where('is_accepted', 1)
            ->with(['customer', 'payments'])
            ->get()
            ->filter(function($quotation) {
                $totalPaid = $quotation->payments->sum('amount');
                return $totalPaid < $quotation->total_amount;
            })
            ->map(function($quotation) {
                $totalPaid = $quotation->payments->sum('amount');
                $quotation->total_paid = $totalPaid;
                $quotation->pending_amount = $quotation->total_amount - $totalPaid;
                return $quotation;
            });
        
        return view('admin.payments.pending-quotations', compact('pendingQuotations'));
    }

    public function create(Request $request)
    {
        // Get only accepted quotations that are not fully paid
        $quotations = Quotation::where('is_accepted', 1)
            ->with(['customer', 'payments'])
            ->get()
            ->filter(function($quotation) {
                $totalPaid = $quotation->payments->sum('amount');
                return $totalPaid < $quotation->total_amount;
            })
            ->map(function($quotation) {
                $totalPaid = $quotation->payments->sum('amount');
                $quotation->total_paid = $totalPaid;
                $quotation->pending_amount = $quotation->total_amount - $totalPaid;
                return $quotation;
            });
        
        $selectedQuotationId = $request->get('quotation_id');
        
        return view('admin.payments.create', compact('quotations', 'selectedQuotationId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'quotation_id' => 'required|exists:quotations,id',
            'amount' => 'required|numeric|min:0.01',
            'transaction_id' => [
                'required',
                'string',
                'max:255',
                'unique:payments,transaction_id'
            ],
            'payment_type' => 'required|in:online,cash,bank_transfer,cheque,other',
            'payment_date' => 'required|date',
            'receipt' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB max
            'notes' => 'nullable|string',
        ], [
            'transaction_id.required' => 'Transaction ID is required.',
            'transaction_id.unique' => 'This Transaction ID already exists in the database. Please use a different Transaction ID.',
            'receipt.required' => 'Receipt upload is required.',
            'receipt.file' => 'Receipt must be a valid file.',
            'receipt.mimes' => 'Receipt must be a PDF, JPG, JPEG, or PNG file.',
            'receipt.max' => 'Receipt file size must not exceed 5MB.',
        ]);

        $quotation = Quotation::with('payments')->findOrFail($request->quotation_id);
        
        // Check if quotation is accepted
        if (!$quotation->is_accepted) {
            if ($request->ajax()) {
                return $this->jsonError('Only accepted quotations can receive payments.');
            }
            return redirect()->back()->with('error', 'Only accepted quotations can receive payments.');
        }

        // Calculate pending amount
        $totalPaid = $quotation->payments->sum('amount');
        $pendingAmount = $quotation->total_amount - $totalPaid;

        // Validate amount doesn't exceed pending amount
        if ($request->amount > $pendingAmount) {
            if ($request->ajax()) {
                return $this->jsonError('Payment amount cannot exceed pending amount of &#8377;' . number_format($pendingAmount, 2));
            }
            return redirect()->back()->with('error', 'Payment amount cannot exceed pending amount of ₹' . number_format($pendingAmount, 2));
        }

        // Handle receipt file upload
        $receiptPath = null;
        if ($request->hasFile('receipt')) {
            $file = $request->file('receipt');
            $fileName = 'receipt_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $receiptPath = $file->storeAs('receipts', $fileName, 'public');
        }

        $payment = Payment::create([
            'quotation_id' => $request->quotation_id,
            'amount' => $request->amount,
            'transaction_id' => $request->transaction_id,
            'payment_type' => $request->payment_type,
            'payment_date' => $request->payment_date,
            'receipt_path' => $receiptPath,
            'notes' => $request->notes,
            'payment_number' => Payment::generatePaymentNumber(),
        ]);

        if ($request->ajax()) {
            return $this->jsonSuccess('Payment added successfully', $payment);
        }

        return redirect()->route('admin.payments.receipt', $payment)->with('success', 'Payment added successfully.');
    }

    public function generateReceiptPDF(Payment $payment)
    {
        $payment->load(['quotation.customer.country', 'quotation.items']);
        
        // Check if dompdf is available
        if (class_exists('\Barryvdh\DomPDF\Facade\Pdf')) {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.payments.receipt', compact('payment'));
            $pdf->setOption('isHtml5ParserEnabled', true);
            $pdf->setOption('isRemoteEnabled', true);
            $pdf->setOption('defaultFont', 'DejaVu Sans');
            $pdf->setOption('isPhpEnabled', true);
            $pdf->setOption('chroot', public_path());
            return $pdf->stream('payment-receipt-' . $payment->payment_number . '.pdf');
        } elseif (class_exists('\Dompdf\Dompdf')) {
            $html = view('admin.payments.receipt', compact('payment'))->render();
            $dompdf = new \Dompdf\Dompdf();
            $dompdf->loadHtml($html, 'UTF-8');
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->set_option('isHtml5ParserEnabled', true);
            $dompdf->set_option('isRemoteEnabled', true);
            $dompdf->set_option('defaultFont', 'DejaVu Sans');
            $dompdf->set_option('isPhpEnabled', true);
            $dompdf->render();
            return $dompdf->stream('payment-receipt-' . $payment->payment_number . '.pdf');
        } else {
            // Fallback: return HTML view for printing
            return view('admin.payments.receipt', compact('payment'));
        }
    }

    public function getPendingAmount(Request $request)
    {
        $quotation = Quotation::with('payments')->findOrFail($request->quotation_id);
        
        if (!$quotation->is_accepted) {
            return response()->json([
                'success' => false,
                'message' => 'Quotation is not accepted'
            ]);
        }

        $totalPaid = $quotation->payments->sum('amount');
        $pendingAmount = $quotation->total_amount - $totalPaid;

        return response()->json([
            'success' => true,
            'total_amount' => $quotation->total_amount,
            'total_paid' => $totalPaid,
            'pending_amount' => $pendingAmount,
        ]);
    }

    public function checkTransactionId(Request $request)
    {
        $transactionId = $request->input('transaction_id');
        $paymentId = $request->input('payment_id'); // For edit scenarios
        
        if (empty($transactionId)) {
            return response()->json([
                'success' => true,
                'exists' => false
            ]);
        }

        $exists = Payment::where('transaction_id', $transactionId)
            ->when($paymentId, function($query) use ($paymentId) {
                return $query->where('id', '!=', $paymentId);
            })
            ->exists();

        return response()->json([
            'success' => true,
            'exists' => $exists,
            'message' => $exists ? 'This Transaction ID already exists in the database' : 'Transaction ID is available'
        ]);
    }
}
