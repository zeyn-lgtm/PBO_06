<?php

require_once "MahasiswaReguler.php";
require_once "MahasiswaBeasiswa.php";

class DataMahasiswa {

    private $data;

    public function __construct() {
        if (!isset($_SESSION["data_mahasiswa_v2"])) {
            $_SESSION["data_mahasiswa_v2"] = [];
        }

        $this->data = &$_SESSION["data_mahasiswa_v2"];
    }

    // Membuat objek berdasarkan jenis mahasiswa
    private function buatObjek($item) {

        if ($item["jenis"] == "Beasiswa") {
            return new MahasiswaBeasiswa(
                $item["nim"],
                $item["nama"],
                $item["prodi"],
                $item["semester"],
                $item["ukt"],
                $item["potonganPersen"]
            );
        }

        return new MahasiswaReguler(
            $item["nim"],
            $item["nama"],
            $item["prodi"],
            $item["semester"],
            $item["ukt"]
        );
    }

    // Menambahkan data
    public function tambah($mahasiswa) {

        $nim = $mahasiswa->getNim();

        if (isset($this->data[$nim])) {
            throw new Exception("NIM sudah dipakai!");
        }

        $this->data[$nim] = $mahasiswa->toArray();
    }

    // Mengambil semua data
    public function semua() {

        $hasil = [];

        foreach ($this->data as $item) {
            $hasil[] = $this->buatObjek($item);
        }

        return $hasil;
    }

    // Mencari data berdasarkan NIM
    public function cari($nim) {

        if (!isset($this->data[$nim])) {
            return null;
        }

        return $this->buatObjek($this->data[$nim]);
    }

    // Mengubah data tanpa mengubah NIM
    public function ubah($nim, $mahasiswa) {

        if (!isset($this->data[$nim])) {
            throw new Exception("Data mahasiswa tidak ditemukan!");
        }

        $this->data[$nim] = $mahasiswa->toArray();
    }

    // Menghapus data
    public function hapus($nim) {

        if (!isset($this->data[$nim])) {
            throw new Exception("Data mahasiswa tidak ditemukan!");
        }

        unset($this->data[$nim]);
    }

    // Mencari data berdasarkan kata kunci
    public function cariSemua($kataKunci) {

        $hasil = [];

        foreach ($this->semua() as $m) {

            if (
                stripos($m->getNim(), $kataKunci) !== false ||
                stripos($m->getNama(), $kataKunci) !== false ||
                stripos($m->getProdi(), $kataKunci) !== false
            ) {
                $hasil[] = $m;
            }
        }

        return $hasil;
    }
}
?>