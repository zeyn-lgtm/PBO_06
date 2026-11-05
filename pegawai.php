<?php
// Parent Class Pegawai
class Pegawai {
    protected $id;
    protected $nama;
    public function __construct($id, $nama) {
        $this->id = $id;
        $this->nama = $nama;
    }
    public function getNama() {
        return $this->nama;
    }
}
// Child Class PegawaiTetap
class PegawaiTetap extends Pegawai {
    private $gajiPokok;
    public function __construct($id, $nama, $gajiPokok) {
        parent::__construct($id, $nama);
        if ($gajiPokok < 0) {
            throw new Exception("Gaji tidak boleh negatif!");
        }
        $this->gajiPokok = $gajiPokok;
    }
    public function hitungGaji() {
        return $this->gajiPokok;
    }
}
// Child Class PegawaiKontrak
class PegawaiKontrak extends Pegawai {
    private $upahPerJam;
    private $jamKerja;
    public function __construct($id, $nama, $upahPerJam, $jamKerja) {
        parent::__construct($id, $nama);
        if ($upahPerJam < 0 || $jamKerja < 0) {
            throw new Exception("Upah dan jam kerja tidak boleh negatif!");
        }
        $this->upahPerJam = $upahPerJam;
        $this->jamKerja = $jamKerja;
    }
    public function hitungGaji() {
        return $this->upahPerJam * $this->jamKerja;
    }
}
// Membuat dua object pegawai
try {
    $pegawai1 = new PegawaiTetap(
        "P001", "Andi", 5000000
    );
    $pegawai2 = new PegawaiKontrak(
        "P002", "Budi", 25000, 160
    );
    // Menampilkan hasil
    echo "<h3>Data Pegawai</h3>";
    echo "Nama Pegawai Tetap: " . $pegawai1->getNama() . "<br>";
    echo "Gaji Pegawai Tetap: Rp " . number_format($pegawai1->hitungGaji(), 0, ',', '.') . "<br><br>";
    echo "Nama Pegawai Kontrak: " . $pegawai2->getNama() . "<br>";
    echo "Gaji Pegawai Kontrak: Rp " . number_format($pegawai2->hitungGaji(), 0, ',', '.');
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}