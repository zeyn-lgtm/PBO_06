<?php
// Class Parent Mahasiswa
abstract class Mahasiswa {

    protected $nim;
    protected $nama;
    protected $prodi;
    protected $semester;

    public function __construct($nim, $nama, $prodi, $semester) {
        $this->nim = $nim;
        $this->nama = $nama;
        $this->prodi = $prodi;
        $this->semester = $semester;
    }

    // Getter
    public function getNim() {
        return $this->nim;
    }

    public function getNama() {
        return $this->nama;
    }

    public function getProdi() {
        return $this->prodi;
    }

    public function getSemester() {
        return $this->semester;
    }

    // Method yang wajib dibuat ulang oleh child
    abstract public function hitungBiaya();

    abstract public function getJenis();

    // Mengubah objek menjadi array
    public function toArray() {
        return [
            "nim" => $this->nim,
            "nama" => $this->nama,
            "prodi" => $this->prodi,
            "semester" => $this->semester
        ];
    }
}
?>