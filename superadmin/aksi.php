<?php
require "../db_login.php";

function esc($data) {
    global $conn;
    return $conn->real_escape_string(trim(stripslashes($data)));
}

// Delete All Users
if (isset($_POST['delete_all_user'])) {
    $querydelete = mysqli_query($conn, "DELETE FROM tb_user");
    header('location:superadmin.php');
    exit();
}

// Add User
$valid = true;
if (isset($_POST['add_user'])) {
    $email = trim($_POST['email'] ?? '');
    if ($email == '') {
        $error_email = "Email harus diisi";
        $valid = false;
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_email = "Format email tidak benar";
        $valid = false;
    }

    $nip_user = trim($_POST['nip_user'] ?? '');
    if (empty($nip_user)) {
        $valid = false;
        $error_nip_user = "NIP tidak boleh kosong";
    } elseif (!preg_match("/^[0-9]+$/", $nip_user)) {
        $valid = false;
        $error_nip_user = "Hanya angka yang diperbolehkan";
    }

    $password = $_POST['password'] ?? '';
    if (empty($password)) {
        $valid = false;
        $error_password = "Password tidak boleh kosong";
    } elseif (strlen($password) < 8) {
        $valid = false;
        $error_password = "Password minimal 8 karakter";
    }

    if ($valid) {
        $nip_int = intval($nip_user);
        $ceknip = mysqli_num_rows(mysqli_query($conn, "SELECT nip_user FROM tb_user WHERE nip_user=$nip_int"));
        if ($ceknip > 0) {
            $error_nip_user = "NIP sudah ada";
            $valid = false;
        }
    }

    if ($valid) {
        $email_esc = esc($email);
        $cekEmail = mysqli_num_rows(mysqli_query($conn, "SELECT email FROM tb_user WHERE email='$email_esc'"));
        if ($cekEmail > 0) {
            $error_email = "Email sudah ada";
            $valid = false;
        }
    }

    if ($valid) {
        $status_user = esc($_POST['status_user'] ?? '');
        $nip_int = intval($nip_user);
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $email_esc = esc($email);
        $addtouser = mysqli_query($conn, "INSERT INTO tb_user (email, password, status_user, nip_user) VALUES('$email_esc', '$password_hash', '$status_user', $nip_int)");
        header($addtouser ? 'location:superadmin.php' : 'location:superadmin.php?err=1');
        exit();
    }
}

// Add Karyawan
$valid = true;
if (isset($_POST['add_karyawan'])) {
    $nip_karyawan = trim($_POST['nip_karyawan'] ?? '');
    if (empty($nip_karyawan)) {
        $valid = false;
        $error_nip_karyawan = "NIP tidak boleh kosong";
    } elseif (!preg_match("/^[0-9]+$/", $nip_karyawan)) {
        $valid = false;
        $error_nip_karyawan = "Hanya angka yang diperbolehkan";
    }

    $nama_karyawan = trim($_POST['nama_karyawan'] ?? '');
    if ($nama_karyawan == '') {
        $error_nama_karyawan = "Nama Karyawan harus diisi";
        $valid = false;
    } elseif (!preg_match("/^[a-zA-Z ]+$/", $nama_karyawan)) {
        $error_nama_karyawan = "Hanya huruf dan spasi yang diperbolehkan";
        $valid = false;
    }

    $nip_atasan = trim($_POST['nip_atasan'] ?? '');
    if (empty($nip_atasan)) {
        $valid = false;
        $error_nip_atasan = "NIP tidak boleh kosong";
    } elseif (!preg_match("/^[0-9]+$/", $nip_atasan)) {
        $valid = false;
        $error_nip_atasan = "Hanya angka yang diperbolehkan";
    }

    if ($valid) {
        $nip_k_int = intval($nip_karyawan);
        $ceknip = mysqli_num_rows(mysqli_query($conn, "SELECT nip_karyawan FROM tb_karyawan WHERE nip_karyawan=$nip_k_int"));
        if ($ceknip > 0) {
            $error_nip_karyawan = "NIP Karyawan sudah ada";
            $valid = false;
        }
    }

    if ($valid) {
        $nama_esc = esc($nama_karyawan);
        $cek = mysqli_num_rows(mysqli_query($conn, "SELECT nama_karyawan FROM tb_karyawan WHERE nama_karyawan='$nama_esc'"));
        if ($cek > 0) {
            $error_nama_karyawan = "Nama Karyawan sudah ada";
            $valid = false;
        }
    }

    if ($valid) {
        $nip_k_int = intval($nip_karyawan);
        $nip_a_int = intval($nip_atasan);
        $nama_esc = esc($nama_karyawan);
        $id_jabatan = intval($_POST['id_jabatan'] ?? 0);
        $id_bidang = intval($_POST['id_bidang'] ?? 0);
        $addtouser = mysqli_query($conn, "INSERT INTO tb_karyawan (nama_karyawan, nip_karyawan, nip_atasan, id_jabatan, id_bidang) VALUES('$nama_esc', $nip_k_int, $nip_a_int, $id_jabatan, $id_bidang)");
        header($addtouser ? 'location:datakaryawan.php' : 'location:datakaryawan.php?err=1');
        exit();
    }
}

// Edit User
if (isset($_POST['edit_user'])) {
    $email = esc($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $id = intval($_POST['id'] ?? 0);

    if (!empty($password)) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $queryupdate = mysqli_query($conn, "UPDATE tb_user SET email='$email', password='$password_hash' WHERE nip_user=$id");
    } else {
        $queryupdate = mysqli_query($conn, "UPDATE tb_user SET email='$email' WHERE nip_user=$id");
    }
    header($queryupdate ? 'location:superadmin.php' : 'location:superadmin.php?err=edit');
    exit();
}

// Delete User
if (isset($_POST['delete_user'])) {
    $id = esc($_POST['id'] ?? '');
    $querydelete = mysqli_query($conn, "DELETE FROM tb_user WHERE email='$id'");
    header('location:superadmin.php');
    exit();
}

// Delete Karyawan
if (isset($_POST['delete_karyawan'])) {
    $nip_karyawan = intval($_POST['nip_karyawan'] ?? 0);
    $querydelete = mysqli_query($conn, "DELETE FROM tb_karyawan WHERE nip_karyawan=$nip_karyawan");
    header('location:datakaryawan.php');
    exit();
}
