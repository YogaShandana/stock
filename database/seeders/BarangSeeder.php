<?php

namespace Database\Seeders;

use App\Models\Barang;
use Illuminate\Database\Seeder;

class BarangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $filePath = database_path('dataBarang/barang.txt');
        
        if (file_exists($filePath)) {
            $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            
            foreach ($lines as $line) {
                $parts = explode(' - ', $line, 2);
                
                if (count($parts) === 2) {
                    $kode = trim($parts[0]);
                    $nama = trim($parts[1]);
                    
                    Barang::updateOrCreate(
                        ['kode' => $kode],
                        ['nama' => $nama]
                    );
                }
            }
            
            $this->command->info('Data barang berhasil diimport dari barang.txt');
        } else {
            $this->command->error('File barang.txt tidak ditemukan');
        }
    }
}