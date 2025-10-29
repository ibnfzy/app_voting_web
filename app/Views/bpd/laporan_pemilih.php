<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>Laporan Data Pemilih</title>
  <style>
  body {
    font-family: Arial, sans-serif;
    margin: 30px;
    color: #333;
  }

  .header {
    text-align: center;
    margin-bottom: 10px;
    position: relative;
  }

  .header img {
    width: 80px;
    position: absolute;
    top: 0;
    left: 40px;
  }

  .header h2,
  .header h3,
  .header p {
    margin: 2px 0;
  }

  .line {
    border-top: 3px solid #000;
    border-bottom: 1px solid #000;
    margin-top: 10px;
    margin-bottom: 30px;
    padding: 2px 0;
  }

  .stats {
    display: flex;
    gap: 20px;
    justify-content: space-around;
    margin-bottom: 30px;
  }

  .card {
    flex: 1;
    padding: 20px;
    background: #f9f9f9;
    border-radius: 8px;
    text-align: center;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
  }

  .card h3 {
    margin: 5px 0;
    font-size: 18px;
    color: #555;
  }

  .card p {
    font-size: 24px;
    font-weight: bold;
    margin: 0;
    color: #222;
  }

  table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
  }

  table,
  th,
  td {
    border: 1px solid #ccc;
  }

  th,
  td {
    padding: 8px 10px;
    text-align: left;
    font-size: 12px;
  }

  th {
    background: #f2f2f2;
    text-align: center;
  }

  .status-badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: bold;
    color: #fff;
  }

  .status-valid {
    background: #28a745;
  }

  .status-not-valid {
    background: #dc3545;
  }

  .footer {
    margin-top: 40px;
    text-align: right;
  }
  </style>
</head>

<body>
  <div class="header">
    <h2>PANITIA PEMILIHAN</h2>
    <h3>LAPORAN DATA PEMILIH</h3>
    <p>Kecamatan Balantak Utara, Kabupaten Banggai, Sulawesi Tengah</p>
  </div>
  <div class="line"></div>

  <div class="stats">
    <div class="card">
      <h3>Total Pemilih</h3>
      <p><?= $totalPemilih ?></p>
    </div>
    <div class="card">
      <h3>Sudah Validasi</h3>
      <p><?= $totalValidated ?></p>
    </div>
    <div class="card">
      <h3>Belum Validasi</h3>
      <p><?= $totalNotValidated ?></p>
    </div>
  </div>

  <h3>Daftar Pemilih</h3>
  <table>
    <thead>
      <tr>
        <th>No</th>
        <th>NIK</th>
        <th>Nama Lengkap</th>
        <th>Jenis Kelamin</th>
        <th>Tempat, Tanggal Lahir</th>
        <th>Alamat</th>
        <th>RT/RW</th>
        <th>Kelurahan</th>
        <th>Kecamatan</th>
        <th>Kabupaten</th>
        <th>Provinsi</th>
        <th>Email</th>
        <th>Status Validasi</th>
        <th>Tanggal Registrasi</th>
      </tr>
    </thead>
    <tbody>
      <?php $no = 1; ?>
      <?php foreach ($pemilih as $row): ?>
      <?php
        $ttl = trim(($row['tempat_lahir'] ?? '') . ', ' . ($row['tanggal_lahir'] ?? ''), ', ');
        $rtrw = trim(($row['rt'] ?? '') . '/' . ($row['rw'] ?? ''), '/');
      ?>
      <tr>
        <td style="text-align: center;">
          <?= $no++ ?>
        </td>
        <td><?= esc($row['nik']) ?></td>
        <td><?= esc($row['name']) ?></td>
        <td><?= esc($row['jenis_kelamin']) ?></td>
        <td><?= esc($ttl) ?></td>
        <td><?= esc($row['alamat']) ?></td>
        <td><?= esc($rtrw) ?></td>
        <td><?= esc($row['kelurahan']) ?></td>
        <td><?= esc($row['kecamatan']) ?></td>
        <td><?= esc($row['kabupaten']) ?></td>
        <td><?= esc($row['provinsi']) ?></td>
        <td><?= esc($row['email']) ?></td>
        <td style="text-align: center;">
          <?php if ((int) ($row['validate'] ?? 0) === 1): ?>
          <span class="status-badge status-valid">Sudah Validasi</span>
          <?php else: ?>
          <span class="status-badge status-not-valid">Belum Validasi</span>
          <?php endif; ?>
        </td>
        <td><?= esc($row['registered_at']) ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <div class="footer">
    <p>Banggai, <?= date('d M Y') ?></p>
    <p><strong>Ketua Panitia</strong></p>
    <br><br>
    <p>_________________________</p>
  </div>

  <script>
  window.onload = function() {
    window.print();
  }
  </script>
</body>

</html>
