<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('categories')->insert([
            // INCOME
            ['name' => 'Gaji', 'type' => 'income', 'description' => 'Pendapatan utama dari pekerjaan tetap atau kontrak'],
            ['name' => 'Bonus', 'type' => 'income', 'description' => 'Pendapatan tambahan dari bonus kerja atau proyek'],
            ['name' => 'Freelane', 'type' => 'income', 'description' => 'Pendapatan tambahan dari freelance'],
            ['name' => 'Investasi', 'type' => 'income', 'description' => 'Pendapatan dari hasil investasi seperti dividen, bunga, dll'],
            ['name' => 'Sewa Properti', 'type' => 'income', 'description' => 'Pendapatan dari menyewakan properti'],
            ['name' => 'Bisnis Sampingan', 'type' => 'income', 'description' => 'Pendapatan dari usaha sampingan'],
            ['name' => 'Lainnya (Pendapatan)', 'type' => 'income', 'description' => 'Pendapatan lainnya yang tidak termasuk kategori di atas'],

            // EXPENSES
            ['name' => 'Makan dan Minum', 'type' => 'expense', 'description' => 'Pengeluaran harian untuk makanan dan minuman'],
            ['name' => 'Transportasi', 'type' => 'expense', 'description' => 'Biaya transportasi umum, bensin, parkir, dll'],
            ['name' => 'Sewa/Kontrakan', 'type' => 'expense', 'description' => 'Biaya menyewa tempat tinggal'],
            ['name' => 'Hiburan', 'type' => 'expense', 'description' => 'Biaya untuk hiburan seperti bioskop, konser, dll'],
            ['name' => 'Belanja', 'type' => 'expense', 'description' => 'Pengeluaran untuk kebutuhan atau keinginan pribadi'],
            ['name' => 'Kesehatan', 'type' => 'expense', 'description' => 'Biaya rumah sakit, obat-obatan, dan asuransi'],
            ['name' => 'Pendidikan', 'type' => 'expense', 'description' => 'Biaya sekolah, kursus, dan pelatihan'],
            ['name' => 'Cicilan Utang', 'type' => 'expense', 'description' => 'Pembayaran rutin cicilan utang'],
            ['name' => 'Investasi', 'type' => 'expense', 'description' => 'Dana yang dialokasikan untuk berinvestasi'],
            ['name' => 'Tabungan', 'type' => 'expense', 'description' => 'Pengalokasian uang untuk ditabung'],
            ['name' => 'Dana Darurat', 'type' => 'expense', 'description' => 'Pengalokasian untuk kondisi darurat'],
            ['name' => 'Zakat / Donasi', 'type' => 'expense', 'description' => 'Pengeluaran untuk amal atau zakat'],
            ['name' => 'Lainnya (Pengeluaran)', 'type' => 'expense', 'description' => 'Pengeluaran lainnya di luar kategori utama'],

            // ASSETS
            ['name' => 'Uang Tunai', 'type' => 'asset', 'description' => 'Jumlah uang fisik yang dimiliki'],
            ['name' => 'Tabungan Bank', 'type' => 'asset', 'description' => 'Dana yang disimpan di rekening tabungan'],
            ['name' => 'Deposito', 'type' => 'asset', 'description' => 'Simpanan berjangka di bank'],
            ['name' => 'Emas', 'type' => 'asset', 'description' => 'Kepemilikan emas fisik atau digital'],
            ['name' => 'Saham', 'type' => 'asset', 'description' => 'Investasi di pasar saham'],
            ['name' => 'Reksa Dana', 'type' => 'asset', 'description' => 'Investasi kolektif di berbagai instrumen'],
            ['name' => 'Properti', 'type' => 'asset', 'description' => 'Kepemilikan rumah, tanah, atau bangunan'],
            ['name' => 'Kendaraan', 'type' => 'asset', 'description' => 'Kepemilikan motor, mobil, atau alat transportasi lainnya'],
            ['name' => 'Koleksi Berharga', 'type' => 'asset', 'description' => 'Barang koleksi seperti lukisan, jam tangan, dll'],
            ['name' => 'Lainnya (Aset)', 'type' => 'asset', 'description' => 'Aset lain yang tidak disebutkan di atas'],

            // DEBTS
            ['name' => 'Kredit Rumah (KPR)', 'type' => 'debt', 'description' => 'Pinjaman untuk pembelian rumah'],
            ['name' => 'Kredit Kendaraan', 'type' => 'debt', 'description' => 'Pinjaman pembelian mobil atau motor'],
            ['name' => 'Kartu Kredit', 'type' => 'debt', 'description' => 'Saldo tagihan kartu kredit'],
            ['name' => 'Pinjaman Pribadi', 'type' => 'debt', 'description' => 'Utang pribadi dari bank atau lembaga keuangan'],
            ['name' => 'Pinjaman Teman/Keluarga', 'type' => 'debt', 'description' => 'Utang kepada individu lain'],
            ['name' => 'Pinjaman Lainnya', 'type' => 'debt', 'description' => 'Jenis utang lain yang belum disebutkan'],

            // EMERGENCY FUND
            ['name' => 'Dana Darurat', 'type' => 'emergency', 'description' => 'Dana yang dialokasikan khusus untuk keadaan darurat'],

            // DREAM
            ['name' => 'Liburan', 'type' => 'dream', 'description' => 'Tujuan keuangan untuk liburan'],
            ['name' => 'Pernikahan', 'type' => 'dream', 'description' => 'Dana untuk biaya pernikahan'],
            ['name' => 'Pendidikan Anak', 'type' => 'dream', 'description' => 'Tabungan untuk biaya pendidikan anak'],
            ['name' => 'Beli Rumah', 'type' => 'dream', 'description' => 'Tabungan untuk membeli rumah'],
            ['name' => 'Beli Mobil', 'type' => 'dream', 'description' => 'Tabungan untuk membeli kendaraan pribadi'],
            ['name' => 'Naik Haji / Umroh', 'type' => 'dream', 'description' => 'Dana untuk ibadah haji atau umroh'],
            ['name' => 'Lainnya (Impian)', 'type' => 'dream', 'description' => 'Tujuan keuangan lain yang menjadi impian pribadi'],
        ]);
    }
}
