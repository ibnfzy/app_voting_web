<?= $this->extend('bpd/base'); ?>

<?= $this->section('content'); ?>

<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <div class="card-title fw-bold mb-0">
          Data Pemilih
        </div>
        <a class="btn btn-primary" href="/BPDPanel/Laporan/Pemilih" target="_blank">
          <i class="fa-solid fa-print me-1"></i>
          Cetak Laporan
        </a>
      </div>
      <div class="card-body table-responsive">
        <table class="table table-bordered datatables">
          <thead>
            <th>~</th>
            <th>NIK</th>
            <th>Email</th>
            <th>Nama Lengkap</th>
            <th>Tempat Lahir, Tanggal Lahir</th>
            <th>Jenis Kelamin</th>
            <th>Alamat</th>
            <th>RT/RW</th>
            <th>Kelurahan</th>
            <th>Kecamatan</th>
            <th>Kabupaten</th>
            <th>Provinsi</th>
            <th>Tanggal, Waktu Registrasi</th>
            <th>Status Validasi</th>
          </thead>
          <tbody>
            <?php foreach ($data as $key => $item) : ?>
            <tr>
              <td><?= $key + 1; ?></td>
              <td><?= $item['nik']; ?></td>
              <td><?= $item['email']; ?></td>
              <td><?= $item['name']; ?></td>
              <td><?= $item['tempat_lahir'] . ' ' . $item['tanggal_lahir']; ?></td>
              <td><?= $item['jenis_kelamin']; ?></td>
              <td><?= $item['alamat']; ?></td>
              <td><?= $item['rt'] . '/' . $item['rw']; ?></td>
              <td><?= $item['kelurahan']; ?></td>
              <td><?= $item['kecamatan']; ?></td>
              <td><?= $item['kabupaten']; ?></td>
              <td><?= $item['provinsi']; ?></td>
              <td><?= $item['registered_at']; ?></td>
              <td>
                <?php if ((int) $item['validate'] === 1): ?>
                <span class="badge bg-success">Sudah Validasi</span>
                <?php elseif ((int) $item['validate'] === 2): ?>
                <span class="badge bg-warning text-dark">Data Tidak Valid</span>
                <?php else: ?>
                <span class="badge bg-danger">Belum Validasi</span>
                <?php endif; ?>
              </td>
            </tr>
            <?php endforeach ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection(); ?>
