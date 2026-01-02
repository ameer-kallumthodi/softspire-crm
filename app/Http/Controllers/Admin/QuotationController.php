<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class QuotationController extends BaseController
{
    public function index(Customer $customer)
    {
        $quotations = Quotation::where('customer_id', $customer->id)
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('admin.quotations.index', compact('customer', 'quotations'));
    }

    public function create(Customer $customer)
    {
        return view('admin.quotations.create', compact('customer'));
    }

    public function store(Request $request, Customer $customer)
    {
        $request->validate([
            'quotation_date' => 'required|date',
            'duration_months' => 'nullable|string|max:50',
            'technologies' => 'nullable|string',
            'total_amount' => 'required|numeric|min:0',
            'annual_amount' => 'nullable|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.item_name' => 'required|string|max:255',
            'items.*.amount' => 'required|numeric|min:0',
            'items.*.description' => 'nullable|string',
        ]);

        $quotation = Quotation::create([
            'customer_id' => $customer->id,
            'quotation_date' => $request->quotation_date,
            'duration_months' => $request->duration_months,
            'technologies' => $request->technologies,
            'total_amount' => $request->total_amount,
            'annual_amount' => $request->annual_amount,
            'quotation_number' => Quotation::generateQuotationNumber(),
        ]);

        foreach ($request->items as $item) {
            QuotationItem::create([
                'quotation_id' => $quotation->id,
                'item_name' => $item['item_name'],
                'amount' => $item['amount'],
                'quantity' => 1, // Default to 1 for backward compatibility
                'unit_price' => $item['amount'], // Set unit_price same as amount
                'description' => $item['description'] ?? null,
            ]);
        }

        if ($request->ajax()) {
            return $this->jsonSuccess('Quotation created successfully', $quotation);
        }

        return redirect()->route('admin.quotations.pdf', $quotation)->with('success', 'Quotation created successfully.');
    }

    public function generatePDF(Quotation $quotation)
    {
        $quotation->load(['customer.country', 'items']);
        
        // Check if dompdf is available
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
            // Fallback: return HTML view for printing
            return view('admin.quotations.pdf', compact('quotation'));
        }
    }

    public function accept(Quotation $quotation)
    {
        if ($quotation->is_accepted) {
            if (request()->ajax()) {
                return $this->jsonError('This quotation is already accepted.');
            }
            return redirect()->back()->with('error', 'This quotation is already accepted.');
        }

        $quotation->update(['is_accepted' => 1]);

        if (request()->ajax()) {
            return $this->jsonSuccess('Quotation accepted successfully', $quotation);
        }

        return redirect()->back()->with('success', 'Quotation accepted successfully.');
    }
}

