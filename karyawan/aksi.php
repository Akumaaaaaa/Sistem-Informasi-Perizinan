<?php
require "../db_login.php";

function esc($data) {
    global $conn;
    return $conn->real_escape_string(trim(stripslashes($data)));
}

// Add Surat Izin
$valid = true;
if (isset($_POST['add_surat_izin'])) {
    $nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
    if ($nama_lengkap == '') {
        $error_nama_lengkap = "Nama Lengkap harus diisi";
        $valid = false;
    } elseif (!preg_match("/^[a-zA-Z ]+$/", $nama_lengkap)) {
        $error_nama_lengkap = "Hanya huruf dan spasi yang diperbolehkan";
        $valid = false;
    }

    $id_surat_izin = trim($_POST['id_surat_izin'] ?? '');
    if (empty($id_surat_izin)) {
        $valid = false;
        $error_id_surat_izin = "NIP Atasan tidak boleh kosong";
    } elseif (!preg_match("/^[0-9]+$/", $id_surat_izin)) {
        $valid = false;
        $error_id_surat_izin = "Hanya angka yang diperbolehkan";
    }

    $keperluan = trim($_POST['keperluan'] ?? '');
    if ($keperluan == '') {
        $error_keperluan = "Keperluan harus diisi";
        $valid = false;
    }

    $tanggal = esc($_POST['tanggal'] ?? '');
    $jam_pergi = esc($_POST['jam_pergi'] ?? '');
    $jam_kembali = esc($_POST['jam_kembali'] ?? '');
    $id_jenis_izin = intval($_POST['id_jenis_izin'] ?? 0);

    if ($valid) {
        $nama_esc = esc($nama_lengkap);
        $nip_atasan = intval($id_surat_izin);
        $keperluan_esc = esc($keperluan);
        $addtouser = mysqli_query($conn, "INSERT INTO tb_surat_izin (id_surat_izin, nama_lengkap, tanggal, jam_pergi, jam_kembali, id_jenis_izin, keperluan, verifikasi) VALUES($nip_atasan, '$nama_esc', '$tanggal', '$jam_pergi', '$jam_kembali', $id_jenis_izin, '$keperluan_esc', 'Pending')");
        header($addtouser ? 'location:karyawan.php' : 'location:karyawan.php?err=1');
        exit();
    }
}
