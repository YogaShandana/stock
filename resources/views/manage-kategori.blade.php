<!DOCTYPE html>  
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Kategori - Stock System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Manage Kategori</h1>
                <div class="space-x-2">
                    <a href="/rekapan" class="inline-block bg-blue-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-600 transition-colors">
                        ← Kembali ke Rekapan
                    </a>
                </div>
            </div>

            <!-- Kategori Available -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="bg-green-50 p-4 rounded-lg">
                    <h3 class="text-lg font-semibold text-green-600 mb-2">Aktifitas Masuk</h3>
                    <ul class="text-sm text-green-700">
                        @foreach($kategoriMasuk as $kat)
                        <li class="py-1">• {{ $kat }}</li>
                        @endforeach
                    </ul>
                </div>
                <div class="bg-red-50 p-4 rounded-lg">
                    <h3 class="text-lg font-semibold text-red-600 mb-2">Aktifitas Keluar</h3>
                    <ul class="text-sm text-red-700">
                        @foreach($kategoriKeluar as $kat)
                        <li class="py-1">• {{ $kat }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <!-- Data Tables -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Data Masuk -->
                <div>
                    <h3 class="text-lg font-semibold text-green-600 mb-3">Data Masuk ({{ $dataMasuk->count() }} records)</h3>
                    <div class="max-h-96 overflow-y-auto rounded border">
                        <table class="w-full text-sm">
                            <thead class="bg-green-50 sticky top-0">
                                <tr>
                                    <th class="px-2 py-2 text-left">Barang</th>
                                    <th class="px-2 py-2 text-left">Kategori</th>
                                    <th class="px-2 py-2 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($dataMasuk as $item)
                                <tr class="border-b hover:bg-green-50">
                                    <td class="px-2 py-2">
                                        <div class="font-medium">{{ $item->nama_barang }}</div>
                                        <div class="text-xs text-gray-500">{{ $item->code }}</div>
                                    </td>
                                    <td class="px-2 py-2">
                                        <span class="inline-block bg-green-100 text-green-800 px-2 py-1 rounded text-xs">
                                            {{ $item->kategori ?? 'Belum dikategorikan' }}
                                        </span>
                                    </td>
                                    <td class="px-2 py-2 text-center">
                                        <button onclick="editKategori({{ $item->id }}, 'masuk', '{{ $item->kategori }}')" 
                                                class="bg-blue-500 text-white px-2 py-1 rounded text-xs hover:bg-blue-600">
                                            Edit
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Data Keluar -->
                <div>
                    <h3 class="text-lg font-semibold text-red-600 mb-3">Data Keluar ({{ $dataKeluar->count() }} records)</h3>
                    <div class="max-h-96 overflow-y-auto rounded border">
                        <table class="w-full text-sm">
                            <thead class="bg-red-50 sticky top-0">
                                <tr>
                                    <th class="px-2 py-2 text-left">Barang</th>
                                    <th class="px-2 py-2 text-left">Kategori</th>
                                    <th class="px-2 py-2 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($dataKeluar as $item)
                                <tr class="border-b hover:bg-red-50">
                                    <td class="px-2 py-2">
                                        <div class="font-medium">{{ $item->nama_barang }}</div>
                                        <div class="text-xs text-gray-500">{{ $item->code }}</div>
                                    </td>
                                    <td class="px-2 py-2">
                                        <span class="inline-block bg-red-100 text-red-800 px-2 py-1 rounded text-xs">
                                            {{ $item->kategori ?? 'Belum dikategorikan' }}
                                        </span>
                                    </td>
                                    <td class="px-2 py-2 text-center">
                                        <button onclick="editKategori({{ $item->id }}, 'keluar', '{{ $item->kategori }}')" 
                                                class="bg-blue-500 text-white px-2 py-1 rounded text-xs hover:bg-blue-600">
                                            Edit
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit Kategori -->
    <div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Edit Kategori</h3>
                <form id="editForm">
                    <input type="hidden" id="editId">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Kategori</label>
                        <select id="editKategori" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <!-- Options will be populated by JavaScript -->
                        </select>
                    </div>
                    <div class="flex justify-end space-x-2">
                        <button type="button" onclick="closeEditModal()" 
                                class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                            Batal
                        </button>
                        <button type="submit" 
                                class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const kategoriMasuk = @json($kategoriMasuk);
        const kategoriKeluar = @json($kategoriKeluar);
        
        function editKategori(id, type, currentKategori) {
            document.getElementById('editId').value = id;
            const select = document.getElementById('editKategori');
            select.innerHTML = '';
            
            const kategoriList = type === 'masuk' ? kategoriMasuk : kategoriKeluar;
            
            kategoriList.forEach(kat => {
                const option = document.createElement('option');
                option.value = kat;
                option.textContent = kat;
                if (kat === currentKategori) {
                    option.selected = true;
                }
                select.appendChild(option);
            });
            
            document.getElementById('editModal').classList.remove('hidden');
        }
        
        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }
        
        document.getElementById('editForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const id = document.getElementById('editId').value;
            const kategori = document.getElementById('editKategori').value;
            
            fetch('/update-kategori', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ id, kategori })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error updating kategori');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error updating kategori');
            });
        });
        
        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('editModal');
            if (event.target === modal) {
                closeEditModal();
            }
        }
    </script>
</body>
</html>