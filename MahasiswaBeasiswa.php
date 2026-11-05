<?php

require_once "Mahasiswa.php";

// Class Child MahasiswaBeasiswa
class MahasiswaBeasiswa extends Mahasiswa {

    private $ukt;
    private $potonganPersen;

    public function __construct($nim, $nama, $prodi, $semester, $ukt, $potonganPersen) {
        parent::__construct($nim, $nama, $prodi, $semester);
        $this->ukt = $ukt;
        $this->potonganPersen = $potonganPersen;
    }

    public function hitungBiaya() {
        return intdiv(
            $this->ukt * (100 - $this->potonganPersen),
            100
        );
    }

    public function getJenis() {
        return "Beasiswa";
    }

    public function toArray() {
        return array_merge(parent::toArray(), [
            "jenis" => "Beasiswa",
            "ukt" => $this->ukt,
            "potonganPersen" => $this->potonganPersen
        ]);
    }
}
?>