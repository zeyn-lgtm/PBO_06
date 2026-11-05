<?php
require_once "Mahasiswa.php";

// Class Child MahasiswaReguler
class MahasiswaReguler extends Mahasiswa {

    private $ukt;

    public function __construct($nim, $nama, $prodi, $semester, $ukt) {
        parent::__construct($nim, $nama, $prodi, $semester);
        $this->ukt = $ukt;
    }

    public function hitungBiaya() {
        return $this->ukt;
    }

    public function getJenis() {
        return "Reguler";
    }

    public function toArray() {
        return array_merge(parent::toArray(), [
            "jenis" => "Reguler",
            "ukt" => $this->ukt,
            "potonganPersen" => 0
        ]);
    }
}
?>