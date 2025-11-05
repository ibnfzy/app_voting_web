<?= $this->extend('panitia/base'); ?>

<?= $this->section('content'); ?>

<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <div class="card-title fw-bold mb-0">
          Data Pemilih
        </div>
        <a class="btn btn-primary" href="/PanitiaPanel/Laporan/Pemilih" target="_blank">
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
            <th>Status Validasi</th>
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
              <td>
                <?php if ((int) $item['validate'] === 1): ?>
                <span class="badge bg-success">Sudah Validasi</span>
                <?php elseif ((int) $item['validate'] === 2): ?>
                <span class="badge bg-warning text-dark">Data Tidak Valid</span>
                <?php else: ?>
                <span class="badge bg-danger">Belum Validasi</span>
                <?php endif; ?>
              </td>
              <td>
                <div class="btn-group-vertical">
                  <button type="button" class="btn btn-info btn-detail-pemilih" data-bs-toggle="modal"
                    data-bs-target="#detailPemilihModal"
                    data-pemilih='<?= htmlspecialchars(json_encode($item, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP), ENT_QUOTES, 'UTF-8'); ?>'>
                    Detail Pemilih
                  </button>
                  <a href="/PanitiaPanel/Pemilih/<?= $item['id_pemilih'] ?>" class="btn btn-danger">Hapus</a>
                </div>
              </td>
            </tr>
            <?php endforeach ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="detailPemilihModal" tabindex="-1" aria-labelledby="detailPemilihModalLabel"
  aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="detailPemilihModalLabel">Detail Pemilih</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row g-4">
          <div class="col-md-8">
            <dl class="row mb-0">
              <dt class="col-sm-5">NIK</dt>
              <dd class="col-sm-7" id="detail-nik">-</dd>
              <dt class="col-sm-5">Email</dt>
              <dd class="col-sm-7" id="detail-email">-</dd>
              <dt class="col-sm-5">Nama Lengkap</dt>
              <dd class="col-sm-7" id="detail-name">-</dd>
              <dt class="col-sm-5">Tempat, Tanggal Lahir</dt>
              <dd class="col-sm-7" id="detail-ttl">-</dd>
              <dt class="col-sm-5">Jenis Kelamin</dt>
              <dd class="col-sm-7" id="detail-jenis-kelamin">-</dd>
              <dt class="col-sm-5">Alamat</dt>
              <dd class="col-sm-7" id="detail-alamat">-</dd>
              <dt class="col-sm-5">RT/RW</dt>
              <dd class="col-sm-7" id="detail-rt-rw">-</dd>
              <dt class="col-sm-5">Kelurahan</dt>
              <dd class="col-sm-7" id="detail-kelurahan">-</dd>
              <dt class="col-sm-5">Kecamatan</dt>
              <dd class="col-sm-7" id="detail-kecamatan">-</dd>
              <dt class="col-sm-5">Kabupaten</dt>
              <dd class="col-sm-7" id="detail-kabupaten">-</dd>
              <dt class="col-sm-5">Provinsi</dt>
              <dd class="col-sm-7" id="detail-provinsi">-</dd>
            </dl>
          </div>
          <div class="col-md-4">
            <div class="border rounded p-2 text-center h-100 d-flex flex-column justify-content-between">
              <div>
                <h6 class="fw-bold">Foto KTP</h6>
                <img src="" alt="Foto KTP" id="detail-ktp-img" class="img-fluid rounded shadow-sm d-none">
                <p class="text-muted mb-0" id="detail-ktp-empty">Foto KTP belum diunggah.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <span class="me-auto" id="detail-status-wrapper">
          <span class="badge bg-secondary" id="detail-status-badge">-</span>
        </span>
        <div class="d-flex gap-2" id="validationActionButtons">
          <a href="#" class="btn btn-success" id="btnValidasiPemilih">Proses Validasi</a>
          <a href="#" class="btn btn-outline-danger" id="btnTidakValidPemilih">Data Tidak Valid</a>
        </div>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

<?= $this->section('script'); ?>
<script>
  (function () {
    const modalEl = document.getElementById('detailPemilihModal');
    if (!modalEl) {
      return;
    }

    const uploadsBaseUrl = "<?= rtrim(base_url('uploads'), '/'); ?>";
    const validateBaseUrl = "<?= rtrim(base_url('PanitiaPanel/Pemilih/Validate'), '/'); ?>";
    const invalidateBaseUrl = "<?= rtrim(base_url('PanitiaPanel/Pemilih/TidakValid'), '/'); ?>";

    const statusBadge = modalEl.querySelector('#detail-status-badge');
    const validationButtons = modalEl.querySelector('#validationActionButtons');
    const btnValid = modalEl.querySelector('#btnValidasiPemilih');
    const btnInvalid = modalEl.querySelector('#btnTidakValidPemilih');
    const ktpImg = modalEl.querySelector('#detail-ktp-img');
    const ktpEmpty = modalEl.querySelector('#detail-ktp-empty');

    const fieldMapping = {
      nik: '#detail-nik',
      email: '#detail-email',
      name: '#detail-name',
      ttl: '#detail-ttl',
      jenis_kelamin: '#detail-jenis-kelamin',
      alamat: '#detail-alamat',
      rt_rw: '#detail-rt-rw',
      kelurahan: '#detail-kelurahan',
      kecamatan: '#detail-kecamatan',
      kabupaten: '#detail-kabupaten',
      provinsi: '#detail-provinsi'
    };

    const statusConfig = {
      0: { text: 'Belum Validasi', className: 'bg-danger' },
      1: { text: 'Sudah Validasi', className: 'bg-success' },
      2: { text: 'Data Tidak Valid', className: 'bg-warning text-dark' }
    };

    const setText = (selector, value) => {
      const element = modalEl.querySelector(selector);
      if (element) {
        element.textContent = value || '-';
      }
    };

    document.querySelectorAll('.btn-detail-pemilih').forEach((button) => {
      button.addEventListener('click', () => {
        const rawData = button.getAttribute('data-pemilih');

        if (!rawData) {
          return;
        }

        let pemilih;

        try {
          pemilih = JSON.parse(rawData);
        } catch (error) {
          console.error('Gagal membaca data pemilih', error);
          return;
        }

        setText(fieldMapping.nik, pemilih.nik ?? '-');
        setText(fieldMapping.email, pemilih.email ?? '-');
        setText(fieldMapping.name, pemilih.name ?? '-');
        const ttl = [pemilih.tempat_lahir, pemilih.tanggal_lahir].filter(Boolean).join(', ');
        setText(fieldMapping.ttl, ttl || '-');
        setText(fieldMapping.jenis_kelamin, pemilih.jenis_kelamin ?? '-');
        setText(fieldMapping.alamat, pemilih.alamat ?? '-');
        const rtRw = [pemilih.rt, pemilih.rw].filter(Boolean).join(' / ');
        setText(fieldMapping.rt_rw, rtRw || '-');
        setText(fieldMapping.kelurahan, pemilih.kelurahan ?? '-');
        setText(fieldMapping.kecamatan, pemilih.kecamatan ?? '-');
        setText(fieldMapping.kabupaten, pemilih.kabupaten ?? '-');
        setText(fieldMapping.provinsi, pemilih.provinsi ?? '-');

        if (pemilih.nama_file_ktp) {
          ktpImg.src = `${uploadsBaseUrl}/${pemilih.nama_file_ktp}`;
          ktpImg.classList.remove('d-none');
          ktpEmpty.classList.add('d-none');
        } else {
          ktpImg.src = '';
          ktpImg.classList.add('d-none');
          ktpEmpty.classList.remove('d-none');
        }

        const status = Number.parseInt(pemilih.validate, 10);
        const statusData = statusConfig[status] ?? { text: 'Belum Validasi', className: 'bg-danger' };

        statusBadge.textContent = statusData.text;
        statusBadge.className = `badge ${statusData.className}`;

        if (status === 0) {
          validationButtons.classList.remove('d-none');
          btnValid.href = `${validateBaseUrl}/${pemilih.id_pemilih}`;
          btnInvalid.href = `${invalidateBaseUrl}/${pemilih.id_pemilih}`;
        } else {
          validationButtons.classList.add('d-none');
          btnValid.removeAttribute('href');
          btnInvalid.removeAttribute('href');
        }
      });
    });
  })();
</script>
<?= $this->endSection(); ?>

<?= $this->endSection(); ?>
