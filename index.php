<?php

require_once "DataMahasiswa.php";
session_start();

$db = new DataMahasiswa();

$pesan = "";
$status = "";

$edit = null;

// Fungsi keamanan output
function aman($data) {
    return htmlspecialchars((string)$data, ENT_QUOTES, "UTF-8");
}

// Proses tambah dan edit
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $aksi = $_POST["aksi"] ?? "";

    if ($aksi == "hapus") {
        try {
            $db->hapus($_POST["nim"]);
            $pesan = "Data mahasiswa berhasil dihapus!";
            $status = "sukses";
        } catch (Exception $e) {
            $pesan = $e->getMessage();
            $status = "error";
        }

    } else {

        $nim = trim($_POST["nim"] ?? "");
        $nama = trim($_POST["nama"] ?? "");
        $prodi = trim($_POST["prodi"] ?? "");
        $semester = trim($_POST["semester"] ?? "");
        $jenis = $_POST["jenis"] ?? "";
        $ukt = trim($_POST["ukt"] ?? "");
        $potongan = trim($_POST["potongan"] ?? "0");

        try {

            if ($nim == "" || $nama == "" || $prodi == "" ||
                $semester == "" || $ukt == "") {
                throw new Exception("Semua data wajib diisi!");
            }

            if (!ctype_digit($semester) ||
                (int)$semester < 1 || (int)$semester > 14) {
                throw new Exception("Semester harus antara 1-14!");
            }

            if (!ctype_digit($ukt)) {
                throw new Exception("UKT harus berupa angka!");
            }

            if ((int)$ukt < 0) {
                throw new Exception("UKT tidak boleh kurang dari 0!");
            }

            if ($jenis == "Beasiswa") {

                if (!ctype_digit($potongan) ||
                    (int)$potongan < 0 || (int)$potongan > 100) {
                    throw new Exception("Potongan harus antara 0-100!");
                }

                $mahasiswa = new MahasiswaBeasiswa(
                    $nim, $nama, $prodi, (int)$semester,
                    (int)$ukt, (int)$potongan
                );

            } elseif ($jenis == "Reguler") {

                $mahasiswa = new MahasiswaReguler(
                    $nim, $nama, $prodi, (int)$semester,
                    (int)$ukt
                );

            } else {
                throw new Exception("Jenis mahasiswa tidak valid!");
            }

            if ($aksi == "edit") {
                $nimLama = $_POST["nim_lama"] ?? "";

                if ($nim != $nimLama) {
                    throw new Exception("NIM tidak boleh diubah!");
                }

                $db->ubah($nimLama, $mahasiswa);
                $pesan = "Data mahasiswa berhasil diperbarui!";

            } else {
                $db->tambah($mahasiswa);
                $pesan = "Data mahasiswa berhasil disimpan!";
            }

            $status = "sukses";

        } catch (Exception $e) {
            $pesan = "Error: " . $e->getMessage();
            $status = "error";
        }
    }
}

// Mengambil data untuk diedit
if (isset($_GET["edit"])) {
    $edit = $db->cari($_GET["edit"]);
}

// Pencarian data
$kataKunci = trim($_GET["cari"] ?? "");

if ($kataKunci != "") {
    $daftar = $db->cariSemua($kataKunci);
} else {
    $daftar = $db->semua();
}

function rupiah($angka) {
    return "Rp " . number_format($angka, 0, ",", ".");
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Data Mahasiswa</title>

<style>
* {
    box-sizing: border-box;
}

body {
    margin: 0;
    padding: 30px;
    font-family: Arial, sans-serif;
    background: #f1f5f9;
    color: #333;
}

.container {
    max-width: 1200px;
    margin: auto;
}

h2 {
    text-align: center;
    color: #263b59;
    margin-bottom: 30px;
}

.content {
    display: grid;
    grid-template-columns: 1fr 330px;
    gap: 25px;
    align-items: start;
}

.card {
    background: white;
    padding: 22px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.07);
}

h3 {
    font-size: 17px;
    color: #28547c;
    margin-top: 0;
    margin-bottom: 20px;
    border-bottom: 1px solid #eee;
    padding-bottom: 12px;
}

label {
    display: block;
    font-size: 13px;
    margin-bottom: 6px;
    font-weight: bold;
}

input, select {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    margin-bottom: 15px;
}

button, .tombol {
    padding: 9px 12px;
    background: #176b9b;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    text-decoration: none;
    font-size: 12px;
}

button:hover, .tombol:hover {
    background: #10577f;
}

.pesan {
    padding: 12px;
    border-radius: 5px;
    margin-bottom: 15px;
    font-size: 13px;
    font-weight: bold;
}

.sukses {
    background: #d9f2df;
    color: #216b35;
}

.error {
    background: #f8d7da;
    color: #842029;
}

.tabel {
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
    font-size: 12px;
}

th, td {
    padding: 11px 8px;
    border-bottom: 1px solid #eee;
    text-align: left;
}

th {
    color: #28547c;
    background: #f5f8fc;
}

.cari {
    display: flex;
    gap: 8px;
}

.cari input {
    flex: 1;
}

.aksi {
    display: flex;
    gap: 5px;
    align-items: center;
}

.hapus {
    background: #c0392b;
}

@media (max-width: 900px) {
    body {
        padding: 15px;
    }

    .content {
        grid-template-columns: 1fr;
    }
}
</style>
</head>

<body>

<div class="container">

<h2>Data Mahasiswa - Inheritance</h2>

<?php if ($pesan != ""): ?>
    <div class="pesan <?= aman($status) ?>">
        <?= aman($pesan) ?>
    </div>
<?php endif; ?>

<div class="content">

<!-- BAGIAN KIRI: DAFTAR MAHASISWA -->
<div class="card">

    <h3>Daftar Mahasiswa (<?= count($daftar) ?>)</h3>

    <form method="GET" class="cari">
        <input type="text" name="cari"
            placeholder="Cari NIM, nama, atau prodi"
            value="<?= aman($kataKunci) ?>">
        <button type="submit">Cari</button>
        <a href="index.php" class="tombol">Reset</a>
    </form>

    <div class="tabel">

    <table>
        <tr>
            <th>NIM</th>
            <th>Nama</th>
            <th>Semester</th>
            <th>Prodi</th>
            <th>Jenis</th>
            <th>UKT</th>
            <th>Potongan</th>
            <th>Biaya Akhir</th>
            <th>Aksi</th>
        </tr>

        <?php if (count($daftar) > 0): ?>

            <?php foreach ($daftar as $m): ?>

            <tr>
                <td><?= aman($m->getNim()) ?></td>
                <td><?= aman($m->getNama()) ?></td>
                <td><?= aman($m->getSemester()) ?></td>
                <td><?= aman($m->getProdi()) ?></td>
                <td><?= aman($m->getJenis()) ?></td>
                <td><?= rupiah($m->getJenis() == "Reguler"
                    ? $m->hitungBiaya()
                    : $m->toArray()["ukt"]) ?></td>
                <td>
                    <?= $m->getJenis() == "Beasiswa"
                        ? aman($m->toArray()["potonganPersen"]) . "%"
                        : "-" ?>
                </td>
                <td><?= rupiah($m->hitungBiaya()) ?></td>

                <td>
                    <div class="aksi">
                        <a class="tombol"
                           href="index.php?edit=<?= urlencode($m->getNim()) ?>">
                           Edit
                        </a>

                        <form method="POST"
                              onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                            <input type="hidden" name="aksi" value="hapus">
                            <input type="hidden" name="nim"
                                   value="<?= aman($m->getNim()) ?>">
                            <button class="hapus" type="submit">Hapus</button>
                        </form>
                    </div>
                </td>
            </tr>

            <?php endforeach; ?>

        <?php else: ?>

            <tr>
                <td colspan="9" style="text-align:center">
                    Belum ada data mahasiswa.
                </td>
            </tr>

        <?php endif; ?>

    </table>
    </div>
</div>

<!-- BAGIAN KANAN: FORM INPUT -->
<div class="card">

    <h3><?= $edit ? "Edit Data Mahasiswa" : "Form Input Mahasiswa" ?></h3>

    <form method="POST">

        <input type="hidden" name="aksi"
               value="<?= $edit ? "edit" : "tambah" ?>">

        <?php if ($edit): ?>
            <input type="hidden" name="nim_lama"
                   value="<?= aman($edit->getNim()) ?>">
        <?php endif; ?>

        <label>NIM</label>
        <input type="text" name="nim"
               value="<?= aman($edit ? $edit->getNim() : "") ?>"
               placeholder="Masukkan NIM"
               <?= $edit ? "readonly" : "" ?>>

        <label>Nama Lengkap</label>
        <input type="text" name="nama"
               value="<?= aman($edit ? $edit->getNama() : "") ?>"
               placeholder="Masukkan Nama Lengkap">

        <label>Semester</label>
        <input type="number" name="semester"
               value="<?= aman($edit ? $edit->getSemester() : "") ?>"
               placeholder="1-14" min="1" max="14">

        <label>Program Studi</label>
        <input type="text" name="prodi"
               value="<?= aman($edit ? $edit->getProdi() : "") ?>"
               placeholder="Masukkan Program Studi">

        <label>Jenis Mahasiswa</label>
        <select name="jenis" id="jenis" onchange="aturPotongan()">
            <option value="Reguler"
                <?= $edit && $edit->getJenis() == "Reguler" ? "selected" : "" ?>>
                Reguler
            </option>

            <option value="Beasiswa"
                <?= $edit && $edit->getJenis() == "Beasiswa" ? "selected" : "" ?>>
                Beasiswa
            </option>
        </select>

        <label>UKT (Rp)</label>
        <input type="number" name="ukt" min="0"
               value="<?= aman($edit ? $edit->toArray()["ukt"] : "") ?>"
               placeholder="Masukkan nominal UKT">

        <div id="bagianPotongan">
            <label>Potongan Beasiswa (%)</label>
            <input type="number" name="potongan" min="0" max="100"
                   value="<?= aman($edit ? $edit->toArray()["potonganPersen"] : "0") ?>"
                   placeholder="0-100">
        </div>

        <button type="submit">
            <?= $edit ? "Simpan Perubahan" : "Simpan Data" ?>
        </button>

        <?php if ($edit): ?>
            <a href="index.php" class="tombol">Batal Edit</a>
        <?php endif; ?>

    </form>
</div>

</div>
</div>

<script>
function aturPotongan() {
    const jenis = document.getElementById("jenis").value;
    const bagian = document.getElementById("bagianPotongan");

    bagian.style.display = jenis === "Beasiswa" ? "block" : "none";
}

aturPotongan();
</script>

</body>
</html>