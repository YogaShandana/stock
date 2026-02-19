<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Rekapan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RekapanController extends Controller
{
    // Mapping aktifitas ke tipe
    private $aktifitasMasuk = ['HASIL PACKING', 'BONGKAR THERMO', 'DARI INTIMAS'];
    private $aktifitasKeluar = ['BB PACKING', 'BB PROSES', 'KIRIM INTIMAS', 'JUAL LOKAL', 'MUAT'];
    
    public function index()
    {
        return view('welcome');
    }

    public function upload(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        try {
            $file = $request->file('csv_file');
            $fileName = 'rekapan_' . time() . '.csv';
            
            // Store file
            $path = $file->storeAs('rekapan', $fileName, 'public');
            
            // Clear existing data
            Rekapan::truncate();
            
            // Read and process CSV - auto detect delimiter (comma or semicolon)
            $fileContent = file_get_contents($file->getPathname());
            $delimiter = (substr_count($fileContent, ';') > substr_count($fileContent, ',')) ? ';' : ',';
            
            $csvData = array_map(function($line) use ($delimiter) {
                return str_getcsv($line, $delimiter);
            }, file($file->getPathname()));
            $header = array_shift($csvData);
            
            $processedRows = 0;
            $stockCount = 0;
            $masukCount = 0;
            $keluarCount = 0;
            $lastAktifitas = 'stock'; // Default aktifitas
            
            foreach ($csvData as $rowIndex => $row) {
                if (count($row) >= 7) {
                    // Jika aktifitas kosong, gunakan aktifitas dari baris sebelumnya
                    $currentAktifitas = !empty(trim($row[0])) ? trim($row[0]) : $lastAktifitas;
                    $namaBarang = trim($row[1] ?? '');
                    $code = trim($row[2] ?? '');
                    
                    // Validasi: Skip hanya jika nama_barang dan code juga kosong
                    $boxMasuk = (int) ($row[3] ?? 0);
                    $kgsMasuk = (float) ($row[4] ?? 0);
                    $boxKeluar = (int) ($row[5] ?? 0);
                    $kgsKeluar = (float) ($row[6] ?? 0);
                    
                    // Import semua baris (tidak skip apapun)
                    Rekapan::create([
                        'aktifitas' => $currentAktifitas,
                        'kategori' => $currentAktifitas, // Aktifitas sama dengan kategori
                        'nama_barang' => $namaBarang,
                        'code' => $code,
                        'box_masuk' => $boxMasuk,
                        'kgs_masuk' => $kgsMasuk,
                        'box_keluar' => $boxKeluar,
                        'kgs_keluar' => $kgsKeluar,
                    ]);
                    
                    // Count per aktifitas type
                    if ($currentAktifitas === 'stock') {
                        $stockCount++;
                    } elseif (in_array($currentAktifitas, $this->aktifitasMasuk)) {
                        $masukCount++;
                    } elseif (in_array($currentAktifitas, $this->aktifitasKeluar)) {
                        $keluarCount++;
                    }
                    
                    // Update lastAktifitas hanya jika aktifitas tidak kosong  
                    if (!empty(trim($row[0]))) {
                        $lastAktifitas = $currentAktifitas;
                    }
                    
                    $processedRows++;
                }
            }
            
            return redirect()->route('rekapan-view')->with('success', "File CSV berhasil diupload! $processedRows baris data diproses. Breakdown: Stock=$stockCount, Masuk=$masukCount, Keluar=$keluarCount");
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        $templatePath = storage_path('app/public/template_rekapan.csv');
        
        if (file_exists($templatePath)) {
            return response()->download($templatePath, 'template_rekapan.csv');
        }
        
        // Jika file tidak ada, buat template dinamis
        $headers = [
            'Content-Type' => 'application/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="template_rekapan.csv"',
            'Content-Encoding' => 'UTF-8',
        ];
        
        $callback = function() {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8 encoding (Excel Mac compatibility)
            fprintf($file, "\xEF\xBB\xBF");
            
            // Header CSV dengan semicolon delimiter untuk Excel Mac
            fputcsv($file, [
                'Aktifitas',
                'Nama Barang',
                'Code', 
                'Sum of BOX/MASUK',
                'Sum of KGS/MASUK',
                'Sum of BOX/KELUAR', 
                'Sum of KGS/KELUAR'
            ], ';');
            
            // Template dengan aktifitas langsung sebagai kategori - format continuation
            
            // STOCK AWAL - 12 barang dengan stok awal
            fputcsv($file, ['stock', 'SALMON FILLET PREMIUM', 'SLM01', '10', '8.5', '', ''], ';');
            fputcsv($file, ['', 'SALMON STEAK CUT', 'SLM02', '15', '12.3', '', ''], ';');
            fputcsv($file, ['', 'TUNA SASHIMI GRADE', 'TUN01', '8', '6.2', '', ''], ';');
            fputcsv($file, ['', 'TUNA STEAK 200G', 'TUN02', '12', '9.8', '', ''], ';');
            fputcsv($file, ['', 'MAHI MAHI FILLET', 'MHI01', '20', '15.4', '', ''], ';');
            fputcsv($file, ['', 'RED SNAPPER WHOLE', 'RSP01', '5', '4.2', '', ''], ';');
            fputcsv($file, ['', 'MACKEREL FRESH', 'MCK01', '18', '14.1', '', ''], ';');
            fputcsv($file, ['', 'GROUPER FILLET', 'GRP01', '7', '5.8', '', ''], ';');
            fputcsv($file, ['', 'BARRAMUNDI CUT', 'BAR01', '14', '11.2', '', ''], ';');
            fputcsv($file, ['', 'POMFRET SILVER', 'PMF01', '9', '7.3', '', ''], ';');
            fputcsv($file, ['', 'KINGFISH STEAK', 'KNG01', '11', '8.9', '', ''], ';');
            fputcsv($file, ['', 'CORAL TROUT FRESH', 'CRL01', '6', '4.8', '', ''], ';');
            
            // MASUK - continuation format
            fputcsv($file, ['HASIL PACKING', 'SALMON FILLET PREMIUM', 'SLM01', '5', '4.2', '', ''], ';');
            fputcsv($file, ['', 'TUNA SASHIMI GRADE', 'TUN01', '3', '2.4', '', ''], ';');
            fputcsv($file, ['', 'MAHI MAHI FILLET', 'MHI01', '8', '6.1', '', ''], ';');
            fputcsv($file, ['', 'RED SNAPPER WHOLE', 'RSP01', '4', '3.2', '', ''], ';');
            fputcsv($file, ['', 'MACKEREL FRESH', 'MCK01', '6', '4.7', '', ''], ';');
            fputcsv($file, ['BONGKAR THERMO', 'BARRAMUNDI CUT', 'BAR01', '10', '8.0', '', ''], ';');
            fputcsv($file, ['', 'WAHOO FRESH CATCH', 'WHO01', '7', '5.6', '', ''], ';');
            fputcsv($file, ['', 'MARLIN BLUE STEAK', 'MRL01', '4', '3.1', '', ''], ';');
            fputcsv($file, ['DARI INTIMAS', 'DORY FILLET FRESH', 'DOR01', '12', '9.2', '', ''], ';');
            fputcsv($file, ['', 'SPANISH MACKEREL', 'SPM01', '8', '6.4', '', ''], ';');
            fputcsv($file, ['', 'COBIA PREMIUM', 'COB01', '5', '3.8', '', ''], ';');
            fputcsv($file, ['', 'THREADFIN SALMON', 'THR01', '9', '7.1', '', ''], ';');
            
            // KELUAR - continuation format
            fputcsv($file, ['BB PACKING', 'SALMON FILLET PREMIUM', 'SLM01', '', '', '12', '10.1'], ';');
            fputcsv($file, ['', 'SALMON STEAK CUT', 'SLM02', '', '', '10', '8.2'], ';');
            fputcsv($file, ['', 'GROUPER FILLET', 'GRP01', '', '', '6', '4.9'], ';');
            fputcsv($file, ['BB PROSES', 'TUNA STEAK 200G', 'TUN02', '', '', '8', '6.5'], ';');
            fputcsv($file, ['', 'BARRAMUNDI CUT', 'BAR01', '', '', '18', '14.2'], ';');
            fputcsv($file, ['', 'SPANISH MACKEREL', 'SPM01', '', '', '6', '4.7'], ';');
            fputcsv($file, ['KIRIM INTIMAS', 'POMFRET SILVER', 'PMF01', '', '', '7', '5.6'], ';');
            fputcsv($file, ['', 'DORY FILLET FRESH', 'DOR01', '', '', '10', '7.8'], ';');
            fputcsv($file, ['JUAL LOKAL', 'MAHI MAHI FILLET', 'MHI01', '', '', '15', '11.8'], ';');
            fputcsv($file, ['', 'KINGFISH STEAK', 'KNG01', '', '', '9', '7.2'], ';');
            fputcsv($file, ['MUAT', 'MACKEREL FRESH', 'MCK01', '', '', '20', '15.6'], ';');
            fputcsv($file, ['', 'WAHOO FRESH CATCH', 'WHO01', '', '', '5', '4.0'], ';');
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    public function rekapanView()
    {
        // Build WHERE clauses for different activity types
        $masukActivities = implode("','", $this->aktifitasMasuk);
        $keluarActivities = implode("','", $this->aktifitasKeluar);
        
        // Get all barang with their totals first
        $allBarang = Rekapan::selectRaw('
            code,
            MAX(nama_barang) as nama_barang,
            SUM(CASE WHEN aktifitas = \'stock\' THEN box_masuk ELSE 0 END) as stock_awal,
            SUM(CASE WHEN aktifitas IN (\'' . $masukActivities . '\') THEN box_masuk ELSE 0 END) as total_masuk,
            SUM(CASE WHEN aktifitas IN (\'' . $keluarActivities . '\') THEN box_keluar ELSE 0 END) as total_keluar,
            (SUM(CASE WHEN aktifitas = \'stock\' THEN box_masuk ELSE 0 END) + SUM(CASE WHEN aktifitas IN (\'' . $masukActivities . '\') THEN box_masuk ELSE 0 END) - SUM(CASE WHEN aktifitas IN (\'' . $keluarActivities . '\') THEN box_keluar ELSE 0 END)) as stock_final
        ')
        ->whereIn('aktifitas', array_merge(['stock'], $this->aktifitasMasuk, $this->aktifitasKeluar))
        ->groupBy('code')
        ->havingRaw('(SUM(CASE WHEN aktifitas = \'stock\' THEN box_masuk ELSE 0 END) > 0 OR SUM(CASE WHEN aktifitas IN (\'' . $masukActivities . '\') THEN box_masuk ELSE 0 END) > 0)')
        ->get()
        ->map(function($item) {
            $totalAvailable = (int)($item->stock_awal + $item->total_masuk);
            $totalKeluar = (int)$item->total_keluar;
            $item->presentase_box = $totalAvailable > 0 ? (int)round(($totalKeluar * 100.0) / $totalAvailable) : 0;
            return $item;
        });
        
        // 1. Laris: Persentase > 60% - sort by percentage descending (large to small), then by total_keluar descending
        $barangLaris = $allBarang
            ->filter(function($item) {
                return $item->presentase_box > 60;
            })
            ->sortBy([
                ['presentase_box', 'desc'],
                ['total_keluar', 'desc']
            ])
            ->values();
        
        // 2. Kurang Laris: Persentase >= 10% dan <= 60% - sort by percentage descending (large to small), then by total_keluar descending
        $barangKurangLaris = $allBarang
            ->filter(function($item) {
                return $item->presentase_box >= 10 && $item->presentase_box <= 60;
            })
            ->sortBy([
                ['presentase_box', 'desc'],
                ['total_keluar', 'desc']
            ])
            ->values();
        
        // 3. Mengendap: Persentase < 10% - sort by percentage ascending (small to large), then by stock_final descending
        $barangMengendap = $allBarang
            ->filter(function($item) {
                return $item->presentase_box < 10;
            })
            ->sortBy([
                ['presentase_box', 'asc'],
                ['stock_final', 'desc']
            ])
            ->values();
        
        // Data untuk chart (Top 10 saja)
        $chartLaris = $barangLaris->take(10);
        $chartKurangLaris = $barangKurangLaris->take(10);
        $chartMengendap = $barangMengendap->take(10);
            
        // Statistics berdasarkan total BOX dengan logika baru
        // Stock dari aktivitas 'stock'
        $totalStock = Rekapan::where('aktifitas', 'stock')
            ->sum('box_masuk');
            
        // Total Masuk dari aktivitas masuk
        $totalMasuk = Rekapan::whereIn('aktifitas', $this->aktifitasMasuk)
            ->sum('box_masuk');
            
        // Stock Awal = Stock + Masuk (total semua yang masuk)
        $totalStockAwal = $totalStock + $totalMasuk;
            
        // Total Keluar dari aktivitas keluar
        $totalKeluar = Rekapan::whereIn('aktifitas', $this->aktifitasKeluar)
            ->sum('box_keluar');
            
        // Stock Akhir = Stock Awal - Keluar
        $totalStockAkhir = $totalStockAwal - $totalKeluar;
            
        return view('rekapan', compact('barangLaris', 'barangKurangLaris', 'barangMengendap', 'chartLaris', 'chartKurangLaris', 'chartMengendap', 'totalStockAwal', 'totalMasuk', 'totalKeluar', 'totalStockAkhir'));
    }
    
    public function barangDetail($code)
    {
        // Get all data for specific code
        $allData = Rekapan::where('code', $code)
            ->select('aktifitas', 'kategori', 'nama_barang', 'code', 'created_at', 'stok_box', 'stok_kgs', 'box_masuk', 'kgs_masuk', 'box_keluar', 'kgs_keluar')
            ->orderBy('created_at', 'asc')
            ->get();
            
        if ($allData->isEmpty()) {
            return response()->json([
                'code' => $code,
                'nama_barang' => '',
                'stock_awal' => [],
                'masuk' => [],
                'keluar' => [],
                'stock_akhir' => []
            ]);
        }
        
        // Calculate stock awal (dari aktivitas 'stock' menggunakan box_masuk/kgs_masuk)
        $totalBoxStockAwal = $allData->where('aktifitas', 'stock')->sum('box_masuk') ?? 0;
        $totalKgsStockAwal = $allData->where('aktifitas', 'stock')->sum('kgs_masuk') ?? 0;
        
        // Calculate total masuk dan keluar
        $totalBoxMasuk = $allData->whereIn('aktifitas', $this->aktifitasMasuk)->sum('box_masuk') ?? 0;
        $totalKgsMasuk = $allData->whereIn('aktifitas', $this->aktifitasMasuk)->sum('kgs_masuk') ?? 0;
        
        $totalBoxKeluar = $allData->whereIn('aktifitas', $this->aktifitasKeluar)->sum('box_keluar') ?? 0;
        $totalKgsKeluar = $allData->whereIn('aktifitas', $this->aktifitasKeluar)->sum('kgs_keluar') ?? 0;
        
        // Stock Awal
        $stockAwalData = collect([
            [
                'kategori' => 'Stock Awal',
                'box_stock' => $totalBoxStockAwal,
                'stock' => $totalKgsStockAwal,
            ]
        ]);
        
        // Stock Akhir = Stock Awal + Masuk - Keluar
        $stockAkhirData = collect([
            [
                'kategori' => 'Stock Akhir',
                'box_stock' => $totalBoxStockAwal + $totalBoxMasuk - $totalBoxKeluar,
                'stock' => $totalKgsStockAwal + $totalKgsMasuk - $totalKgsKeluar,
            ]
        ]);
        
        $masukData = $allData->whereIn('aktifitas', $this->aktifitasMasuk)->map(function($item) {
            return [
                'kategori' => $item->aktifitas,
                'tanggal' => $item->created_at->format('d/m/Y'),
                'box_masuk' => $item->box_masuk ?? 0,
                'masuk' => $item->kgs_masuk ?? 0,
            ];
        })->values();
        
        $keluarData = $allData->whereIn('aktifitas', $this->aktifitasKeluar)->map(function($item) {
            return [
                'kategori' => $item->aktifitas,
                'tanggal' => $item->created_at->format('d/m/Y'),
                'box_keluar' => $item->box_keluar ?? 0,
                'keluar' => $item->kgs_keluar ?? 0,
            ];
        })->values();
        
        // Calculate percentage BOX (sama seperti di tabel)
        $persentaseBox = 0;
        $totalAvailableBox = $totalBoxStockAwal + $totalBoxMasuk;
        if ($totalAvailableBox > 0) {
            $persentaseBox = round(($totalBoxKeluar * 100.0) / $totalAvailableBox);
        }
        
        return response()->json([
            'code' => $code,
            'nama_barang' => $allData->first()->nama_barang,
            'stock_awal' => $stockAwalData,
            'masuk' => $masukData,
            'keluar' => $keluarData,
            'stock_akhir' => $stockAkhirData,
            'presentase_box' => $persentaseBox,
            'total_keluar_box' => $totalBoxKeluar,
            'total_available_box' => $totalAvailableBox,
            'debug_stock_awal' => $totalBoxStockAwal,
            'debug_total_masuk' => $totalBoxMasuk
        ]);
    }
    
    public function manageKategori()
    {
        // Get all records with their current aktifitas (now as category)
        $dataKeluar = Rekapan::whereIn('aktifitas', $this->aktifitasKeluar)
            ->select('id', 'nama_barang', 'code', 'aktifitas as kategori', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get();
            
        $dataMasuk = Rekapan::whereIn('aktifitas', $this->aktifitasMasuk)
            ->select('id', 'nama_barang', 'code', 'aktifitas as kategori', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('manage-kategori', compact('dataKeluar', 'dataMasuk'))->with([
            'kategoriKeluar' => $this->aktifitasKeluar,
            'kategoriMasuk' => $this->aktifitasMasuk
        ]);
    }
    
    public function updateKategori(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:rekapans,id',
            'kategori' => 'required|string'
        ]);
        
        Rekapan::where('id', $request->id)->update([
            'aktifitas' => $request->kategori,
            'kategori' => $request->kategori
        ]);
        
        return response()->json(['success' => true]);
    }
}
