<?= $this->extend('bpd/base'); ?>

<?= $this->section('content'); ?>

<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <div class="card-title fw-bold">
          Data Pemilih
        </div>
      </div>
      <div class="card-body table-responsive">
        <table class="table table-bordered datatables">'
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
            <th>Status Akses Kode</th>
            <th>Aksi</th>
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
              <td><?= $item['access_code'] ?? 'Belum Ada'; ?></td>
              <td>
                <button class="btn btn-primary">
                  Edit
                </button>
                <a href="#" class="btn btn-danger">Hapus</a>
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