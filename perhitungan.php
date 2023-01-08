<?php
require_once('includes/init.php');

$user_role = get_role();
if($user_role == 'admin') {

$page = "Perhitungan";
require_once('template/header.php');

mysqli_query($koneksi,"TRUNCATE TABLE hasil;");

$kriteria = array();
$q1 = mysqli_query($koneksi,"SELECT * FROM kriteria ORDER BY kode_kriteria ASC");			
while($krit = mysqli_fetch_array($q1)){
	$kriteria[$krit['id_kriteria']]['id_kriteria'] = $krit['id_kriteria'];
	$kriteria[$krit['id_kriteria']]['kode_kriteria'] = $krit['kode_kriteria'];
	$kriteria[$krit['id_kriteria']]['nama'] = $krit['nama'];
	$kriteria[$krit['id_kriteria']]['type'] = $krit['type'];
	$kriteria[$krit['id_kriteria']]['bobot'] = $krit['bobot'];
	$kriteria[$krit['id_kriteria']]['ada_pilihan'] = $krit['ada_pilihan'];
}

$alternatif = array();
$q2 = mysqli_query($koneksi,"SELECT * FROM alternatif");			
while($alt = mysqli_fetch_array($q2)){
	$alternatif[$alt['id_alternatif']]['id_alternatif'] = $alt['id_alternatif'];
	$alternatif[$alt['id_alternatif']]['nama'] = $alt['nama'];
} 

$q3 = mysqli_query($koneksi,"SELECT * FROM kriteria");
$total_kriteria = mysqli_num_rows($q3);
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-fw fa-calculator"></i> Data Perhitungan</h1>
</div>

<div class="card shadow mb-4">
    <!-- /.card-header -->
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary"><i class="fa fa-table"></i> Matrix Keputusan (X)</h6>
    </div>

    <div class="card-body">
		<div class="table-responsive">
			<table class="table table-bordered" width="100%" cellspacing="0">
				<thead class="bg-primary text-white">
					<tr align="center">
						<th width="5%" rowspan="2">No</th>
						<th rowspan="2">Nama Alternatif</th>
						<th colspan="<?= $total_kriteria; ?>">Kriteria</th>
					</tr>
					<tr align="center">
						<?php foreach ($kriteria as $key): ?>
						<th><?= $key['kode_kriteria'] ?></th>
						<?php endforeach ?>
					</tr>
				</thead>
				<tbody>
					<?php 
						$no=1;
						foreach ($alternatif as $keys): ?>
					<tr align="center">
						<td><?= $no; ?></td>
						<td align="left"><?= $keys['nama'] ?></td>
						<?php foreach ($kriteria as $key): ?>
						<td>
						<?php 
							if($key['ada_pilihan']==1){
								$q4 = mysqli_query($koneksi,"SELECT sub_kriteria.nilai FROM penilaian JOIN sub_kriteria WHERE penilaian.nilai=sub_kriteria.id_sub_kriteria AND penilaian.id_alternatif='$keys[id_alternatif]' AND penilaian.id_kriteria='$key[id_kriteria]'");
								$data = mysqli_fetch_array($q4);
								echo $data['nilai'];
							}else{
								$q4 = mysqli_query($koneksi,"SELECT nilai FROM penilaian WHERE id_alternatif='$keys[id_alternatif]' AND id_kriteria='$key[id_kriteria]'");
								$data = mysqli_fetch_array($q4);
								echo $data['nilai'];
							}
						?>
						</td>
						<?php endforeach ?>
					</tr>
					<?php
						$no++;
						endforeach
					?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<div class="card shadow mb-4">
    <!-- /.card-header -->
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary"><i class="fa fa-table"></i> Bobot Kriteria (W)</h6>
    </div>

    <div class="card-body">
		<div class="table-responsive">
			<table class="table table-bordered" width="100%" cellspacing="0">
				<thead class="bg-primary text-white">
					<tr align="center">
						<?php foreach ($kriteria as $key): ?>
						<th><?= $key['kode_kriteria'] ?> <!--(<?= $key['type'] ?>)--></th>
						<?php endforeach ?>
					</tr>
				</thead>
				<tbody>
					<tr align="center">
						<?php foreach ($kriteria as $key): ?>
						<td>
						<?php 
						echo $key['bobot'];
						?>
						</td>
						<?php endforeach ?>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>

<div class="card shadow mb-4">
    <!-- /.card-header -->
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary"><i class="fa fa-table"></i> Normalisasi Bobot Kriteria (W)</h6>
    </div>

    <div class="card-body">
		<div class="table-responsive">
			<table class="table table-bordered" width="100%" cellspacing="0">
				<thead class="bg-primary text-white">
					<tr align="center">
						<?php foreach ($kriteria as $key): ?>
						<th><?= $key['kode_kriteria'] ?> </th>
						<?php endforeach ?>
					</tr>
				</thead>
				<tbody>
					<tr align="center">
						<?php 
						foreach ($kriteria as $key):
						$q5 = mysqli_query($koneksi,"SELECT SUM(bobot) as total_bobot FROM kriteria");
						$total_bobot = mysqli_fetch_array($q5);
						?>
						<td>
						<?php 
							if ($key['type'] == "Benefit") {
								echo @(($key['bobot']/$total_bobot['total_bobot'])*1);
							}else {
								echo @(($key['bobot']/$total_bobot['total_bobot'])*-1);
							}
						?>
						</td>
						<?php endforeach ?>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>

<div class="card shadow mb-4">
    <!-- /.card-header -->
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary"><i class="fa fa-table"></i> Nilai Vektor (S)</h6>
    </div>

    <div class="card-body">
		<div class="table-responsive">
			<table class="table table-bordered" width="100%" cellspacing="0">
				<thead class="bg-primary text-white">
					<tr align="center">
						<th width="5%" rowspan="2">No</th>
						<th rowspan="2">Nama Alternatif</th>
						<th colspan="<?= $total_kriteria; ?>">Kriteria</th>
						<th rowspan="2" width="15%">Nilai (S)</th>
					</tr>
					<tr align="center">
						<?php foreach ($kriteria as $key): ?>
						<th><?= $key['kode_kriteria'] ?></th>
						<?php endforeach ?>
						
					</tr>
				</thead>
				<tbody>
					<?php 
						$no=1;
						$total_vs = 0;
						foreach ($alternatif as $keys): ?>
					<tr align="center">
						<td><?= $no; ?></td>
						<td align="left"><?= $keys['nama'] ?></td>
						<?php 
						$total_s = 1;
						foreach ($kriteria as $key): ?>
						<td>
						<?php 
							$q5 = mysqli_query($koneksi,"SELECT SUM(bobot) as total_bobot FROM kriteria");
							$total_bobot = mysqli_fetch_array($q5);
							if($key['ada_pilihan']==1){
								$q4 = mysqli_query($koneksi,"SELECT sub_kriteria.nilai FROM penilaian JOIN sub_kriteria WHERE penilaian.nilai=sub_kriteria.id_sub_kriteria AND penilaian.id_alternatif='$keys[id_alternatif]' AND penilaian.id_kriteria='$key[id_kriteria]'");
								$data = mysqli_fetch_array($q4);
								
								if ($key['type'] == "Benefit") {
									$bobot_r = @(($key['bobot']/$total_bobot['total_bobot'])*1);
									echo $nilai_s = pow($data['nilai'],$bobot_r);
								}else {
									$bobot_r = @(($key['bobot']/$total_bobot['total_bobot'])*-1);
									echo $nilai_s = pow($data['nilai'],$bobot_r);
								}
							}else{
								$q4 = mysqli_query($koneksi,"SELECT nilai FROM penilaian WHERE id_alternatif='$keys[id_alternatif]' AND id_kriteria='$key[id_kriteria]'");
								$data = mysqli_fetch_array($q4);
								
								if ($key['type'] == "Benefit") {
									$bobot_r = @(($key['bobot']/$total_bobot['total_bobot'])*1);
									echo $nilai_s = pow($data['nilai'],$bobot_r);
								}else {
									$bobot_r = @(($key['bobot']/$total_bobot['total_bobot'])*-1);
									echo $nilai_s = pow($data['nilai'],$bobot_r);
								}
							}
							$total_s *= $nilai_s;
						?>
						</td>
						<?php endforeach; ?>
						<td><?= $total_s; ?></td>
					</tr>
					<?php
						$total_vs += $total_s;
						$no++;
						endforeach;
					?>
					<tr align="center">
						<td colspan="<?= $total_kriteria+2; ?>" class="bg-light">Total</td>
						<td class="bg-light"><?= $total_vs;?></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>

<div class="card shadow mb-4">
    <!-- /.card-header -->
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary"><i class="fa fa-table"></i> Nilai Vektor (V)</h6>
    </div>

    <div class="card-body">
		<div class="table-responsive">
			<table class="table table-bordered" width="100%" cellspacing="0">
				<thead class="bg-primary text-white">
					<tr align="center">
						<th width="5%">No</th>
						<th>Nama Alternatif</th>
						<th>Perhitungan</th>
						<th width="15%">Nilai (V)</th>
					</tr>
				</thead>
				<tbody>
					<?php 
						$no=1;
						foreach ($alternatif as $keys): ?>
					<tr align="center">
						<td><?= $no; ?></td>
						<td align="left"><?= $keys['nama'] ?></td>
						<?php 
						$total_s = 1;
						foreach ($kriteria as $key): ?>
						<?php
							$q5 = mysqli_query($koneksi,"SELECT SUM(bobot) as total_bobot FROM kriteria");
							$total_bobot = mysqli_fetch_array($q5);
							if($key['ada_pilihan']==1){
								$q4 = mysqli_query($koneksi,"SELECT sub_kriteria.nilai FROM penilaian JOIN sub_kriteria WHERE penilaian.nilai=sub_kriteria.id_sub_kriteria AND penilaian.id_alternatif='$keys[id_alternatif]' AND penilaian.id_kriteria='$key[id_kriteria]'");
								$data = mysqli_fetch_array($q4);
								
								if ($key['type'] == "Benefit") {
									$bobot_r = @(($key['bobot']/$total_bobot['total_bobot'])*1);
									$nilai_s = pow($data['nilai'],$bobot_r);
								}else {
									$bobot_r = @(($key['bobot']/$total_bobot['total_bobot'])*-1);
									$nilai_s = pow($data['nilai'],$bobot_r);
								}
							}else{
								$q4 = mysqli_query($koneksi,"SELECT nilai FROM penilaian WHERE id_alternatif='$keys[id_alternatif]' AND id_kriteria='$key[id_kriteria]'");
								$data = mysqli_fetch_array($q4);
								
								if ($key['type'] == "Benefit") {
									$bobot_r = @(($key['bobot']/$total_bobot['total_bobot'])*1);
									$nilai_s = pow($data['nilai'],$bobot_r);
								}else {
									$bobot_r = @(($key['bobot']/$total_bobot['total_bobot'])*-1);
									$nilai_s = pow($data['nilai'],$bobot_r);
								}
							}
							$total_s *= $nilai_s;
						?>
						<?php endforeach; ?>
						<td><?=$total_s; ?> / <?=$total_vs; ?></td>
						<td><?php echo $nilai_v = $total_s/$total_vs; ?></td>
					</tr>
					<?php
						mysqli_query($koneksi,"INSERT INTO hasil (id_hasil, id_alternatif, nilai) VALUES ('', '$keys[id_alternatif]', '$nilai_v')");
						$no++;
						endforeach;
					?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<?php
require_once('template/footer.php');
}
else {
	header('Location: login.php');
}
?>