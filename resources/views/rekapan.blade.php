<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekapan Stock</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-white min-h-screen p-3">
    <div class="w-full p-6">
            <div class="text-center mb-6">
                <h1 class="text-2xl font-semibold text-gray-800 mb-1">Rekapan Stock</h1>
                <p class="text-xs text-gray-600">Analisis barang mengendap dan laris</p>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
                <div class="bg-blue-50 p-3 rounded-lg text-center border-2 border-blue-200">
                    <div class="text-lg font-bold text-blue-600">{{ number_format($totalStockAwal ?? 0, 0, ',', '.') }}</div>
                    <div class="text-xs text-blue-600">📦 Stock Awal</div>
                </div>
                <div class="bg-green-50 p-3 rounded-lg text-center border-2 border-green-200">
                    <div class="text-lg font-bold text-green-600">{{ number_format($totalMasuk ?? 0, 0, ',', '.') }}</div>
                    <div class="text-xs text-green-600">📥 Masuk</div>
                </div>
                <div class="bg-orange-50 p-3 rounded-lg text-center border-2 border-orange-200">
                    <div class="text-lg font-bold text-orange-600">{{ number_format($totalKeluar ?? 0, 0, ',', '.') }}</div>
                    <div class="text-xs text-orange-600">📤 Keluar</div>
                </div>
                <div class="bg-purple-50 p-3 rounded-lg text-center border-2 border-purple-200">
                    <div class="text-lg font-bold text-purple-600">{{ number_format($totalStockAkhir ?? 0, 0, ',', '.') }}</div>
                    <div class="text-xs text-purple-600">📊 Stock Akhir</div>
                </div>
            </div>

            <!-- Charts -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="p-4">
                    <h3 class="text-sm font-semibold text-green-600 mb-3">Top 10 Laris (>60%)</h3>
                    <div style="height: 200px; position: relative;">
                        <canvas id="chartLaris"></canvas>
                    </div>
                </div>
                <div class="p-4">
                    <h3 class="text-sm font-semibold text-orange-600 mb-3">Top 10 Kurang Laris (10-60%)</h3>
                    <div style="height: 200px; position: relative;">
                        <canvas id="chartKurangLaris"></canvas>
                    </div>
                </div>
                <div class="p-4">
                    <h3 class="text-sm font-semibold text-red-600 mb-3">Top 10 Mengendap (<10%)</h3>
                    <div style="height: 200px; position: relative;">
                        <canvas id="chartMengendap"></canvas>
                    </div>
                </div>
            </div>

            <!-- Tables -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Tabel Laris -->
                <div>
                    <h4 class="text-sm font-semibold text-green-600 mb-2">Detail Laris (>60%)</h4>
                    <div class="max-h-96 overflow-y-auto rounded border">
                        <table class="w-full text-xs">
                            <thead class="bg-green-50 sticky top-0">
                                <tr>
                                    <th class="px-2 py-1 text-left">Code</th>
                                    <th class="px-2 py-1 text-left">Nama Barang</th>
                                    <th class="px-2 py-1 text-right">Keluar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($barangLaris as $item)
                                <tr class="border-b hover:bg-green-100 cursor-pointer" onclick="showDetailModal('{{ $item->code }}', '{{ $item->nama_barang }}')">
                                    <td class="px-2 py-1">{{ $item->code }}</td>
                                    <td class="px-2 py-1">{{ $item->nama_barang }}</td>
                                    <td class="px-2 py-1 text-right font-medium text-green-600">{{ $item->total_keluar }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Tabel Kurang Laris -->
                <div>
                    <h4 class="text-sm font-semibold text-orange-600 mb-2">Detail Kurang Laris (10-60%)</h4>
                    <div class="max-h-96 overflow-y-auto rounded border">
                        <table class="w-full text-xs">
                            <thead class="bg-orange-50 sticky top-0">
                                <tr>
                                    <th class="px-2 py-1 text-left">Code</th>
                                    <th class="px-2 py-1 text-left">Nama Barang</th>
                                    <th class="px-2 py-1 text-right">Keluar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($barangKurangLaris as $item)
                                <tr class="border-b hover:bg-orange-100 cursor-pointer" onclick="showDetailModal('{{ $item->code }}', '{{ $item->nama_barang }}')">
                                    <td class="px-2 py-1">{{ $item->code }}</td>
                                    <td class="px-2 py-1">{{ $item->nama_barang }}</td>
                                    <td class="px-2 py-1 text-right font-medium text-orange-600">{{ $item->total_keluar }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Tabel Mengendap -->
                <div>
                    <h4 class="text-sm font-semibold text-red-600 mb-2">Detail Mengendap (<10%)</h4>
                    <div class="max-h-96 overflow-y-auto rounded border">
                        <table class="w-full text-xs">
                            <thead class="bg-red-50 sticky top-0">
                                <tr>
                                    <th class="px-2 py-1 text-left">Code</th>
                                    <th class="px-2 py-1 text-left">Nama Barang</th>
                                    <th class="px-2 py-1 text-right">Stok</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($barangMengendap as $item)
                                <tr class="border-b hover:bg-red-100 cursor-pointer" onclick="showDetailModal('{{ $item->code }}', '{{ $item->nama_barang }}')">
                                    <td class="px-2 py-1">{{ $item->code }}</td>
                                    <td class="px-2 py-1">{{ $item->nama_barang }}</td>
                                    <td class="px-2 py-1 text-right font-medium text-red-600">{{ $item->stock_final }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="text-center mt-6">
                <a href="/" class="inline-block bg-blue-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-600 transition-colors duration-300">
                    ← Upload Lagi
                </a>
            </div>
    </div>

    <!-- Modal Detail Barang -->
    <div id="detailModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900" id="modalTitle">Detail Barang</h3>
                    <button onclick="closeDetailModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div id="modalContent" class="text-left">
                    <!-- Loading spinner -->
                    <div id="loadingSpinner" class="text-center py-4">
                        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-gray-900"></div>
                        <p class="mt-2 text-sm text-gray-600">Memuat data...</p>
                    </div>
                    
                    <!-- Content akan diisi dari AJAX -->
                    <div id="modalData" class="hidden"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Data untuk 3 chart
        const larisData = {
            labels: @json($chartLaris->pluck('code')),
            datasets: [{
                label: 'Total Keluar',
                data: @json($chartLaris->pluck('total_keluar')),
                backgroundColor: [
                    'rgba(34, 197, 94, 0.8)', 'rgba(74, 222, 128, 0.8)', 'rgba(134, 239, 172, 0.8)',
                    'rgba(34, 197, 94, 0.7)', 'rgba(74, 222, 128, 0.7)', 'rgba(134, 239, 172, 0.7)',
                    'rgba(34, 197, 94, 0.6)', 'rgba(74, 222, 128, 0.6)', 'rgba(134, 239, 172, 0.6)',
                    'rgba(34, 197, 94, 0.5)'
                ],
                borderColor: 'rgba(34, 197, 94, 1)',
                borderWidth: 1
            }]
        };

        const kurangLarisData = {
            labels: @json($chartKurangLaris->pluck('code')),
            datasets: [{
                label: 'Total Keluar',
                data: @json($chartKurangLaris->pluck('total_keluar')),
                backgroundColor: [
                    'rgba(249, 115, 22, 0.8)', 'rgba(251, 146, 60, 0.8)', 'rgba(253, 186, 116, 0.8)',
                    'rgba(249, 115, 22, 0.7)', 'rgba(251, 146, 60, 0.7)', 'rgba(253, 186, 116, 0.7)',
                    'rgba(249, 115, 22, 0.6)', 'rgba(251, 146, 60, 0.6)', 'rgba(253, 186, 116, 0.6)',
                    'rgba(249, 115, 22, 0.5)'
                ],
                borderColor: 'rgba(249, 115, 22, 1)',
                borderWidth: 1
            }]
        };

        const mengendapData = {
            labels: @json($chartMengendap->pluck('code')),
            datasets: [{
                label: 'Stock Final',
                data: @json($chartMengendap->pluck('stock_final')),
                backgroundColor: [
                    'rgba(239, 68, 68, 0.8)', 'rgba(248, 113, 113, 0.8)', 'rgba(252, 165, 165, 0.8)',
                    'rgba(239, 68, 68, 0.7)', 'rgba(248, 113, 113, 0.7)', 'rgba(252, 165, 165, 0.7)',
                    'rgba(239, 68, 68, 0.6)', 'rgba(248, 113, 113, 0.6)', 'rgba(252, 165, 165, 0.6)',
                    'rgba(239, 68, 68, 0.5)'
                ],
                borderColor: 'rgba(239, 68, 68, 1)',
                borderWidth: 1
            }]
        };

        const config = {
            type: 'bar',
            options: {
                responsive: true,
                maintainAspectRatio: false,
                layout: {
                    padding: 5
                },
                scales: {
                    y: { 
                        beginAtZero: true, 
                        ticks: { 
                            font: { size: 9 },
                            maxTicksLimit: 6
                        } 
                    },
                    x: { 
                        ticks: { 
                            font: { size: 8 },
                            maxRotation: 45,
                            minRotation: 45
                        } 
                    }
                },
                plugins: { 
                    legend: { display: false },
                    tooltip: {
                        titleFont: { size: 10 },
                        bodyFont: { size: 9 }
                    }
                },
                indexAxis: 'x',
                barPercentage: 0.8,
                categoryPercentage: 0.9
            }
        };

        // Membuat 3 chart
        new Chart(document.getElementById('chartLaris'), {
            ...config,
            data: larisData
        });

        new Chart(document.getElementById('chartKurangLaris'), {
            ...config,
            data: kurangLarisData
        });

        new Chart(document.getElementById('chartMengendap'), {
            ...config,
            data: mengendapData
        });

        // Modal Functions
        function showDetailModal(code, namaBarang) {
            const modal = document.getElementById('detailModal');
            const modalTitle = document.getElementById('modalTitle');
            const modalData = document.getElementById('modalData');
            const loadingSpinner = document.getElementById('loadingSpinner');
            
            modalTitle.textContent = `Detail Barang: ${namaBarang} (${code})`;
            modal.classList.remove('hidden');
            loadingSpinner.classList.remove('hidden');
            modalData.classList.add('hidden');
            
            // Fetch data detail barang
            fetch(`/barang-detail/${code}`)
                .then(response => response.json())
                .then(data => {
                    console.log('Data dari backend:', data);
                    console.log('Persentase:', data.presentase_box);
                    loadingSpinner.classList.add('hidden');
                    modalData.classList.remove('hidden');
                    modalData.innerHTML = generateDetailContent(data);
                })
                .catch(error => {
                    console.error('Error:', error);
                    loadingSpinner.classList.add('hidden');
                    modalData.classList.remove('hidden');
                    modalData.innerHTML = '<div class="text-red-500 text-center">Error memuat data</div>';
                });
        }

        function closeDetailModal() {
            document.getElementById('detailModal').classList.add('hidden');
        }

        function generateDetailContent(data) {
            let content = '<div class="grid grid-cols-1 md:grid-cols-3 gap-4">';
            
            // Kolom 1: STOCK (biru)
            content += '<div class="border rounded-lg p-3">';
            content += '<h4 class="font-semibold text-blue-600 mb-3 text-center">STOCK</h4>';
            
            // Stock Awal
            if (data.stock_awal && data.stock_awal.length > 0) {
                let hasStock = data.stock_awal.some(item => (item.box_stock > 0 || item.stock > 0));
                if (hasStock) {
                    content += '<div class="mb-3"><div class="font-medium text-sm text-blue-700 mb-1">Stock Awal</div>';
                    content += '<div class="bg-blue-50 rounded p-2">';
                    data.stock_awal.forEach(item => {
                        content += `<div class="text-xs">
                            <div>Sum of BOX: <span class="font-medium">${item.box_stock || 0}</span></div>
                        </div>`;
                    });
                    content += '</div></div>';
                }
            }
            
            // Stock Akhir
            if (data.stock_akhir && data.stock_akhir.length > 0) {
                content += '<div class="mb-2"><div class="font-medium text-sm text-blue-700 mb-1">Stock Akhir</div>';
                content += '<div class="bg-blue-50 rounded p-2">';
                data.stock_akhir.forEach(item => {
                    content += `<div class="text-xs">
                        <div>Sum of BOX: <span class="font-medium">${item.box_stock || 0}</span></div>
                    </div>`;
                });
                content += '</div></div>';
            }
            content += '</div>';
            
            // Kolom 2: MASUK (hijau)
            content += '<div class="border rounded-lg p-3">';
            content += '<h4 class="font-semibold text-green-600 mb-3 text-center">MASUK</h4>';
            if (data.masuk && data.masuk.length > 0) {
                // Group by kategori
                let masukGrouped = {};
                data.masuk.forEach(item => {
                    let kategori = item.kategori;
                    if (!masukGrouped[kategori]) {
                        masukGrouped[kategori] = [];
                    }
                    masukGrouped[kategori].push(item);
                });
                
                Object.keys(masukGrouped).forEach(kategori => {
                    content += `<div class="mb-3"><div class="font-medium text-sm text-green-700 mb-1">${kategori}</div>`;
                    content += '<div class="bg-green-50 rounded p-2">';
                    masukGrouped[kategori].forEach(item => {
                        content += `<div class="text-xs">
                            <div>Sum of BOX: <span class="font-medium">${item.box_masuk || 0}</span></div>
                        </div>`;
                    });
                    content += '</div></div>';
                });
            } else {
                content += '<div class="text-xs text-gray-500 text-center">Tidak ada data masuk</div>';
            }
            content += '</div>';
            
            // Kolom 3: KELUAR (merah)
            content += '<div class="border rounded-lg p-3">';
            content += '<h4 class="font-semibold text-red-600 mb-3 text-center">KELUAR</h4>';
            if (data.keluar && data.keluar.length > 0) {
                // Group by kategori
                let keluarGrouped = {};
                data.keluar.forEach(item => {
                    let kategori = item.kategori;
                    if (!keluarGrouped[kategori]) {
                        keluarGrouped[kategori] = [];
                    }
                    keluarGrouped[kategori].push(item);
                });
                
                Object.keys(keluarGrouped).forEach(kategori => {
                    content += `<div class="mb-3"><div class="font-medium text-sm text-red-700 mb-1">${kategori}</div>`;
                    content += '<div class="bg-red-50 rounded p-2">';
                    keluarGrouped[kategori].forEach(item => {
                        content += `<div class="text-xs">
                            <div>Sum of BOX: <span class="font-medium">${item.box_keluar || 0}</span></div>
                        </div>`;
                    });
                    content += '</div></div>';
                });
            } else {
                content += '<div class="text-xs text-gray-500 text-center">Tidak ada data keluar</div>';
            }
            content += '</div>';
            
            content += '</div>';
            
            // Add percentage section using data from backend (more accurate)
            if (data.presentase_box !== undefined) {
                content += '<div class="mt-4 border-t pt-4">';
                content += '<h4 class="font-semibold text-purple-600 mb-3 text-center">PRESENTASE PENJUALAN</h4>';
                content += '<div class="w-full">';
                
                // BOX Percentage
                content += '<div class="border rounded-lg p-4 bg-purple-50 w-full">';
                content += '<h5 class="font-medium text-purple-700 mb-3 text-center">BOX</h5>';
                content += '<div class="text-center">';
                content += `<div class="text-3xl font-bold text-purple-600">${data.presentase_box}%</div>`;
                content += '<div class="text-sm text-purple-600 mt-2">';
                content += `Keluar: ${data.total_keluar_box || 0} dari ${data.total_available_box || 0} BOX`;
                content += '</div>';
                content += '</div>';
                content += '</div>';
                
                content += '</div>';
                content += '</div>';
            }
            
            if (!data.stock_awal || data.stock_awal.length === 0) {
                if (!data.masuk || data.masuk.length === 0) {
                    if (!data.keluar || data.keluar.length === 0) {
                        content = '<div class="text-center text-gray-500 py-4">Tidak ada data untuk barang ini</div>';
                    }
                }
            }
            
            return content;
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('detailModal');
            if (event.target === modal) {
                closeDetailModal();
            }
        }
    </script>
</body>
</html>