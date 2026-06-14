<?php
    require '../db_login.php';
    session_start();
    if (!isset($_SESSION['email'])) {
        header("location:../index.php");
        exit();
    }
    error_reporting(E_ERROR | E_PARSE);

    function getAtasanDetail($email)
    {
        global $conn;
        $stmt = $conn->prepare("SELECT * FROM tb_user WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $data = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $data;
    }

    $AtasanDetail = getAtasanDetail($_SESSION['email']);

    // Handle approve/refuse before any HTML output
    if (isset($_POST['approve']) && isset($_POST['pk_id'])) {
        $pk_id = intval($_POST['pk_id']);
        $stmt = $conn->prepare("UPDATE tb_surat_izin SET verifikasi = 'Disetujui' WHERE pk_id = ?");
        $stmt->bind_param("i", $pk_id);
        $stmt->execute();
        $stmt->close();
        header("location:atasan.php?msg=approved");
        exit();
    }

    if (isset($_POST['tolak']) && isset($_POST['pk_id'])) {
        $pk_id = intval($_POST['pk_id']);
        $stmt = $conn->prepare("UPDATE tb_surat_izin SET verifikasi = 'Ditolak' WHERE pk_id = ?");
        $stmt->bind_param("i", $pk_id);
        $stmt->execute();
        $stmt->close();
        header("location:atasan.php?msg=rejected");
        exit();
    }
?>


<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
    <title> Atasan Dashboard </title>
    <link rel="icon" type="image/x-icon" href="../asset/favicon.ico">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
</head>

<body>
<div class="sidebar">
  <div class="logo-details">
      <i> <img src="../asset/favicon.ico" style="width:40px ; padding-bottom:5px" alt="PLN"></i>
      <div class="logo_name" style="padding-top: 5px;"> <div style="font-size:15px; color:#01B9EF; font-weight: bold;">PLN</div>  Indonesia Power</div>
    </div>
    <ul class="nav-list" id="nav-list">
      <li>
        <a class="nav-link active" href="atasan.php">
          <i class='bx bx-grid-alt'></i>
          <span class="links_name">Home</span>
        </a>
         <span class="tooltip">Home</span>
      </li>
      <li>
       <a class="nav-link " href="edit_profil.php">
         <i class='bx bx-user' ></i>
         <span class="links_name">Edit Profil</span>
       </a>
       <span class="tooltip">Edit Profil</span>
     </li>
     <li>
       <a class="nav-link " href="bawahan.php">
         <i class='bx bx-chat' ></i>
         <span class="links_name">Daftar Karyawan</span>
       </a>
       <span class="tooltip">Daftar Karyawan</span>
     </li>
     <li>
       <a class="nav-link" href="../logout.php">
         <i class="bi bi-box-arrow-right"></i>
         <span class="links_name">Keluar</span>
       </a>
       <span class="tooltip">Keluar</span>
     </li>
     <li class="profile">
        <div class="profile-details">
            <img src="atasan.png" alt="Atasan">
            <div class="name_job">
                <div class="name">Atasan</div>
                <div class="email"><?php echo htmlspecialchars($AtasanDetail['email']); ?></div>
            </div>
        </div>
    </li>
    </ul>
    </div>
        <?php
          $ambildata = mysqli_query($conn, "SELECT * from tb_karyawan, tb_jabatan, tb_bidang WHERE tb_karyawan.id_jabatan = tb_jabatan.id_jabatan AND tb_karyawan.id_bidang = tb_bidang.id_bidang AND nip_karyawan = '".intval($AtasanDetail['nip_user'])."'");
          $nip_karyawan = $nama_karyawan = $nip_atasan = $nama_bidang = $nama_jabatan = '';
          while ($data = mysqli_fetch_array($ambildata)) {
              $nip_karyawan = $data['nip_karyawan'];
              $nama_karyawan = $data['nama_karyawan'];
              $nip_atasan = $data['nip_atasan'];
              $nama_bidang = $data['nama_bidang'];
              $nama_jabatan = $data['nama_jabatan'];
          }
        ?>

    <section class="home-section">
        <div class="container-fluid">
            <div class="h4 mt-5 w-100 ">Home
                <div class="h4 float-end">
                    <h4>Halo, <?= htmlspecialchars($nama_karyawan); ?></h4>
                </div>
            </div><br>

            <?php if (isset($_GET['msg'])): ?>
            <div class="alert alert-<?= $_GET['msg'] === 'approved' ? 'success' : 'danger' ?> alert-dismissible fade show" role="alert">
                <?= $_GET['msg'] === 'approved' ? 'Permohonan izin berhasil disetujui.' : 'Permohonan izin berhasil ditolak.' ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <div class="row row-cols-1 row-cols-md-1 g-4 mt-1">
                <div class="col">
                    <div class="card rounded-4 card-active ">
                        <div class="card-body">
                            <div class="px-5">
                                <table class="table table-responsive">
                                    <tr>
                                        <th>NIP Karyawan</th>
                                        <td><?= htmlspecialchars($nip_karyawan); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Nama Karyawan</th>
                                        <td><?= htmlspecialchars($nama_karyawan); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Nama Jabatan</th>
                                        <td><?= htmlspecialchars($nama_jabatan); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Nama Bidang</th>
                                        <td><?= htmlspecialchars($nama_bidang); ?></td>
                                    </tr>
                                    <tr>
                                        <th>NIP Atasan</th>
                                        <td><?= htmlspecialchars($nip_atasan); ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="h4 mt-5 w-100 ">Riwayat Perizinan</div>

			<div class="row row-cols-1 row-cols-md-1 g-4 mt-1">
                <div class="col">
                    <div class="card rounded-4 card-active ">
                        <div class="card-body">
                            <div class="px-5">
                                <table id="example" class="table rounded-3" style="width:100%">
								<thead>
									<tr>
										<th>No</th>
										<th>Nama Lengkap</th>
										<th>Tanggal</th>
										<th>Jam Pergi</th>
										<th>Jam Kembali</th>
                                        <th>Jenis Izin</th>
										<th>Keperluan</th>
                                        <th>Aksi</th>
									</tr>
								</thead>
								<tbody>
								  <?php
								  $ambildata = mysqli_query($conn, "SELECT tb_surat_izin.*, tb_jenis_izin.nama_izin FROM tb_surat_izin INNER JOIN tb_jenis_izin ON tb_surat_izin.id_jenis_izin = tb_jenis_izin.id_jenis_izin WHERE tb_surat_izin.verifikasi = 'Pending' AND tb_surat_izin.id_surat_izin = ".intval($AtasanDetail['nip_user']));
								  $i = 1;
								  while ($data = mysqli_fetch_array($ambildata)) {
                                      $pk_id = $data['pk_id'];
									  $nama_lengkap = $data['nama_lengkap'];
									  $tanggal = $data['tanggal'];
									  $jam_pergi = $data['jam_pergi'];
									  $jam_kembali = $data['jam_kembali'];
                                      $jenis_izin = $data['nama_izin'];
									  $keperluan = $data['keperluan'];
								  ?>

								<tr>
								  <td><?= $i++; ?></td>
								  <td><?= htmlspecialchars($nama_lengkap); ?></td>
								  <td><?= htmlspecialchars($tanggal); ?></td>
								  <td><?= htmlspecialchars($jam_pergi); ?></td>
								  <td><?= htmlspecialchars($jam_kembali); ?></td>
                                  <td><?= htmlspecialchars($jenis_izin); ?></td>
								  <td><?= htmlspecialchars($keperluan); ?></td>
                                  <td>
                                    <form action="" method="POST">
                                      <input type="hidden" name="pk_id" value="<?= intval($pk_id); ?>">
                                      <input type="submit" class="btn btn-sm btn-success" name="approve" value="Approve">
                                      <input type="submit" class="btn btn-sm btn-danger" name="tolak" value="Refuse">
                                    </form>
								  </td>
								</tr>

								<?php
								  }
								?>
							  </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>
<script src="./script.js"></script>
<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdn.datatables.net/1.13.3/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.12.1/js/dataTables.bootstrap5.min.js"></script>
<script>$(document).ready(function () {
    $('#example').DataTable({ ordering: true });
});</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3" crossorigin="anonymous"></script>
</html>
