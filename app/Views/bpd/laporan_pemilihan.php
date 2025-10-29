<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>Laporan Hasil Pemilihan</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
    padding: 10px;
    text-align: center;
  }

  th {
    background: #f2f2f2;
  }

  .candidate-photo {
    width: 60px;
    height: 60px;
    object-fit: cover;
  }

  .chart-container {
    margin-top: 30px;
    width: 40%;
    /* lebih kecil dari 80% */
    max-width: 400px;
    /* batasi lebar maksimum */
    height: 250px;
    /* tinggi tetap */
    margin-left: auto;
    margin-right: auto;
  }

  .chart-container canvas {
    width: 100% !important;
    height: 100% !important;
  }

  .footer {
    margin-top: 40px;
    text-align: right;
  }

  .misi {
    text-align: left;
    padding-left: 7%;
  }
  </style>
</head>

<body>
  <div class="header">
    <!-- <img src="https://www.adaptivewfs.com/wp-content/uploads/2020/07/logo-placeholder-image.png" alt="Logo"> -->
    <h2>PANITIA PEMILIHAN</h2>
    <h3>LAPORAN HASIL PEMILIHAN KEPALA DESA</h3>
    <p>Kecamatan Balantak Utara, Kabupaten Banggai, Sulawesi Tengah</p>
  </div>
  <div class="line"></div>

  <!-- Statistik -->
  <div class="stats">
    <div class="card">
      <h3>Total Pemilih</h3>
      <p><?= $totalPemilih ?></p>
    </div>
    <div class="card">
      <h3>Yang Memilih</h3>
      <p><?= $totalVotes ?></p>
    </div>
    <div class="card">
      <h3>Partisipasi</h3>
      <p><?= $totalPemilih > 0 ? round(($totalVotes / $totalPemilih) * 100, 2) : 0 ?>%</p>
    </div>
  </div>

  <!-- Tabel Hasil -->
  <h3>Hasil Perolehan Suara</h3>
  <table>
    <thead>
      <tr>
        <th>No</th>
        <th>Foto</th>
        <th>Nama Calon</th>
        <th>Visi</th>
        <th>Misi</th>
        <th>Jumlah Suara</th>
      </tr>
    </thead>
    <tbody>
      <?php $no = 1;
      $labels = [];
      $data = [];
      foreach ($hasil as $row): ?>
      <tr>
        <td><?= $no++ ?></td>
        <td>
          <?php if ($row->photo): ?>
          <img src="<?= base_url('uploads/' . $row->photo) ?>" class="candidate-photo">
          <?php else: ?>
          <img src="<?= base_url('uploads/default.png') ?>" class="candidate-photo">
          <?php endif; ?>
        </td>
        <td><?= esc($row->name) ?></td>
        <td><?= $row->visi ?></td>
        <td class="misi"><?= $row->misi ?></td>
        <td><strong><?= $row->jumlah ?></strong></td>
      </tr>
      <?php
        $labels[] = $row->name;
        $data[] = $row->jumlah;
        ?>
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