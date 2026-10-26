<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

use App\Models\FundCategory;
use App\Models\FundTransaction;
use App\Models\Labs;
use App\Models\Period;

class FundingController extends Controller
{
    public function viewFunding()
    {
        $transactions = FundTransaction::with([
            'lab',
            'category'
        ])
            ->latest('transaction_date')
            ->get();

        $labs = Labs::orderBy('name')->get();
        $categories = FundCategory::all();
        $periods = Period::all();
        
        $total_income = $transactions->where('category.type', 'INCOME')->sum('amount');
        $total_expense = $transactions->where('category.type', 'EXPENSE')->sum('amount');
        $balance = $total_income - $total_expense;

        $total_income_formatted = 'Rp ' . number_format($total_income, 0, ',', '.');
        $total_expense_formatted = 'Rp ' . number_format($total_expense, 0, ',', '.');
        $balance_formatted = 'Rp ' . number_format($balance, 0, ',', '.');

        return view('admin.funding',[
            'transactions'=>$transactions,
            'labs'=>$labs,
            'categories'=>$categories,
            'periods'=>$periods,
            'total_income_formatted'=>$total_income_formatted,
            'total_expense_formatted'=>$total_expense_formatted,
            'balance_formatted'=>$balance_formatted,
        ]);
    }

    public function storeData(Request $request){
        
        if (!Auth::check()) {
            $message = 'Anda harus login terlebih dahulu untuk mencatat transaksi kas.';
            if ($request->wantsJson()) {
                return response()->json(['message' => $message], 401);
            }
            return redirect()->route('user.login')->with('error', $message);
        }

        $validated = $request->validate([
            'category_id' => 'required|exists:fund_categories,id',
            'lab_id' => 'required|exists:labs,id',
            'amount' => 'required|numeric|min:1|max:1000000000',
            'description' => ['required', 'string', 'max:300', 'regex:/^[a-zA-Z0-9\s]+$/'],
            'transaction_date' => 'required|date',
            'period_id' => 'required|exists:periods,id',
            'receipt_number' => ['nullable', 'string', 'max:100', 'regex:/^[a-zA-Z0-9\s]+$/'],
            'receipt_file' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048'
        ], [
            'lab_id.required' => 'Laboratorium wajib dipilih.',
            'lab_id.exists' => 'Laboratorium yang dipilih tidak valid.',
            'category_id.required' => 'Kategori wajib dipilih.',
            'amount.required' => 'Nominal wajib diisi.',
            'amount.min' => 'Nominal transaksi minimal adalah Rp 1.',
            'amount.max' => 'Nominal transaksi maksimal Rp 1.000.000.000 (1 Milyar).',
            'description.required' => 'Keterangan transaksi wajib diisi.',
            'description.max' => 'Keterangan transaksi maksimal 300 karakter.',
            'description.regex' => 'Keterangan transaksi tidak boleh mengandung simbol (hanya boleh huruf, angka, dan spasi).',
            'receipt_number.max' => 'Nomor nota maksimal 100 karakter.',
            'receipt_number.regex' => 'Nomor nota tidak boleh mengandung simbol (hanya boleh huruf, angka, dan spasi).',
            'period_id.required' => 'Periode wajib dipilih.',
            'transaction_date.required' => 'Tanggal transaksi wajib diisi.',
            'receipt_file.mimes' => 'Format file nota harus JPG, PNG, atau PDF.',
            'receipt_file.max' => 'Ukuran file nota maksimal 2 MB.',
        ]);

        if ($request->hasFile('receipt_file')){
            $validated ['receipt_file'] = $request->file('receipt_file')->store('receipts', 'public');
        }

        $validated['recorded_by'] = Auth::id();
        FundTransaction::create($validated);
        return redirect()->route('admin.funding')->with('success', 'Transaksi kas berhasil dicatat.');

    }

    public function destroy(FundTransaction $transaction){
        $transaction->delete();
        return redirect()->route('admin.funding')->with('success', 'Transaksi kas berhasil dihapus.');
    }
    
    public function showReceipt(FundTransaction $transaction){
        if (!$transaction->receipt_file || !Storage::disk('public')->exists($transaction->receipt_file)) {
            return redirect()->back()->with('error', 'File bukti nota tidak ditemukan.');
        }

        return Storage::disk('public')->response($transaction->receipt_file);

    }

    public function editData(Request $request, FundTransaction $transaction){
        
        if(!Auth::check()){
            $message = 'Anda harus login terlebih dahulu untuk mengedit transaksi kas.';
            if ($request->wantsJson()) {
                return response()->json(['message' => $message], 401);
            }
            return redirect()->route('user.login')->with('error', $message);
        }

        $validated = $request->validate([
            'category_id' => 'required|exists:fund_categories,id',
            'lab_id' => 'required|exists:labs,id',
            'amount' => 'required|numeric|min:1|max:1000000000',
            'description' => ['required', 'string', 'max:300', 'regex:/^[a-zA-Z0-9\s]+$/'],
            'transaction_date' => 'required|date',
            'period_id' => 'required|exists:periods,id',
            'receipt_number' => ['nullable', 'string', 'max:100', 'regex:/^[a-zA-Z0-9\s]+$/'],
            'receipt_file' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048'
        ], [
            'lab_id.required' => 'Laboratorium wajib dipilih.',
            'lab_id.exists' => 'Laboratorium yang dipilih tidak valid.',
            'category_id.required' => 'Kategori wajib dipilih.',
            'amount.required' => 'Nominal wajib diisi.',
            'amount.min' => 'Nominal transaksi minimal adalah Rp 1.',
            'amount.max' => 'Nominal transaksi maksimal Rp 1.000.000.000 (1 Milyar).',
            'description.required' => 'Keterangan transaksi wajib diisi.',
            'description.max' => 'Keterangan transaksi maksimal 300 karakter.',
            'description.regex' => 'Keterangan transaksi tidak boleh mengandung simbol (hanya boleh huruf, angka, dan spasi).',
            'receipt_number.max' => 'Nomor nota maksimal 100 karakter.',
            'receipt_number.regex' => 'Nomor nota tidak boleh mengandung simbol (hanya boleh huruf, angka, dan spasi).',
            'period_id.required' => 'Periode wajib dipilih.',
            'transaction_date.required' => 'Tanggal transaksi wajib diisi.',
            'receipt_file.mimes' => 'Format file nota harus JPG, PNG, atau PDF.',
            'receipt_file.max' => 'Ukuran file nota maksimal 2 MB.',
        ]);

        if ($request->hasFile('receipt_file')){
            if($transaction->receipt_file){
                Storage::disk('public')->delete($transaction->receipt_file);
            }
            $validated ['receipt_file'] = $request->file('receipt_file')->store('receipts', 'public');
        } else {
            unset($validated['receipt_file']);
        }


        $validated['recorded_by'] = Auth::id();
        $transaction->update($validated);
        return redirect()->route('admin.funding')->with('success', 'Transaksi kas berhasil diedit.');
    }

    public function exportExcel(){

        $fileName = 'laporan-kas-lab-' . date('Y-m-d') . '.csv';
        $transactions = FundTransaction::with(['category', 'lab', 'recorder'])
            ->latest('transaction_date')
            ->get();

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['No', 'Tanggal', 'Tipe', 'Kategori', 'Lab', 'Nominal', 'Keterangan', 'No Nota', 'Dicatat Oleh'];

        return response()->streamDownload(function() use ($transactions, $columns) {
            $file = fopen('php://output', 'w');
            
            // Tambahkan UTF-8 BOM agar Excel di Windows membaca encoding dengan benar
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, $columns);

            $no = 1;
            foreach ($transactions as $item) {
                fputcsv($file, [
                    $no++,
                    $item->transaction_date ? \Carbon\Carbon::parse($item->transaction_date)->format('Y-m-d') : '-',
                    $item->category ? $item->category->type : '-',
                    $item->category ? $item->category->name : '-',
                    $item->lab ? $item->lab->name : '-',
                    $item->amount,
                    $item->description,
                    $item->receipt_number ?? '-',
                    $item->recorder ? $item->recorder->name : '-',
                ]);
            }
            fclose($file);
        }, $fileName, $headers);
    }

}
