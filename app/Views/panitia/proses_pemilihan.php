<?= $this->extend('panitia/base'); ?>
<?= $this->section('content'); ?>

<div class="container mt-4">
  <div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0">Proses Pemilihan Kepala Desa</h5>
      <div class="d-flex gap-2">
        <?php if (isset($jadwal) && strtotime($jadwal->end_time) < time()) : ?>
          <a class="btn btn-primary" href="/PanitiaPanel/Laporan" target="_blank">
            Lihat Laporan Pemilihan
          </a>
          <button class="btn btn-secondary disabled">
            Pemilihan Telah Berakhir
          </button>
        <?php else: ?>
          <button class="btn btn-primary <?= $pemilihanAktif ? 'disabled' : '' ?>" 
                  data-bs-toggle="modal"
                  data-bs-target="#aturJadwalModal">
            Atur Jadwal Pemilihan
          </button>
        <?php endif; ?>
        <a href="/PanitiaPanel/ResetPemilhan" class="btn btn-danger">
          Reset Pemilihan
        </a>
      </div>
    </div>
    <div class="card-body">
      <?php if (!$pemilihanAktif && isset($countdownTarget) && strtotime($countdownTarget) > time()) : ?>
          <div class="alert alert-info">
            Pemilihan akan dimulai dalam <span id="countdown"></span>
          </div>
      <?php elseif ($pemilihanAktif) : ?>
          <div class="alert alert-success">
            <strong>Pemilihan sedang berlangsung.</strong><br>
            Jadwal:
            <span class="fw-bold"><?= date('d M Y H:i', strtotime($jadwal->start_time)) ?></span>
            s/d
            <span class="fw-bold"><?= date('d M Y H:i', strtotime($jadwal->end_time)) ?></span>
          </div>
      <?php elseif (isset($jadwal) && strtotime($jadwal->end_time) < time()) : ?>
          <div class="alert alert-danger">
            <strong>Pemilihan sudah berakhir.</strong><br>
            Terima kasih atas partisipasi Anda.
          </div>
      <?php else: ?>
          <div class="alert alert-warning">Jadwal pemilihan belum diatur.</div>
      <?php endif; ?>

      <?php if ($pemilihanAktif): ?>
      <div class="mb-4">
        <h6>Statistik Pemilihan diupdate automatis tiap 30 Detik</< /h6>
          <canvas id="chartSuara" style="max-width: 300px; max-height: 300px; margin: auto;"></canvas>
      </div>

      <h6>Data Pemilih Yang Sudah Voting</h6>
      <div class="table-responsive">
        <table id="tabelVoting" class="table table-striped table-bordered">
          <thead>
            <tr>
              <th>No</th>
              <th>NIK</th>
              <th>Nama Pemilih</th>
              <th>Waktu Voting</th>
              <th>Calon Dipilih</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($votingData as $i => $vote): ?>
            <tr>
              <td><?= $i + 1 ?></td>
              <td><?= esc($vote['nik']) ?></td>
              <td><?= esc($vote['nama']) ?></td>
              <td><?= esc($vote['voted_at']) ?></td>
              <td><?= esc($vote['nama_calon']) ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <?php endif; ?>
    </div>

  </div>
</div>

<!-- Modal Atur Jadwal -->
<div class="modal fade" id="aturJadwalModal" tabindex="-1" aria-labelledby="aturJadwalModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form action="/PanitiaPanel/aturJadwal" method="post" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Atur Jadwal Pemilihan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label for="start_time" class="form-label">Waktu Mulai</label>
          <input type="datetime-local" name="start_time" id="start_time" class="form-control" required>
        </div>
        <div class="mb-3">
          <label for="end_time" class="form-label">Waktu Selesai</label>
          <input type="datetime-local" name="end_time" id="end_time" class="form-control" required>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan Jadwal</button>
      </div>
    </form>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  let chartInstance;

  <?php if (isset($countdownTarget) && !$pemilihanAktif): ?>
  // Countdown sebelum pemilihan mulai
  let countdownTarget = new Date("<?= $countdownTarget ?>").getTime();

  const countdownEl = document.getElementById("countdown");
  const interval = setInterval(() => {
    const now = new Date().getTime();
    const distance = countdownTarget - now;

    if (distance <= 0) {
      clearInterval(interval);
      countdownEl.textContent = "Waktu pemilihan telah dimulai!";
      // location.reload();
    } else {
      const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
      const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
      const seconds = Math.floor((distance % (1000 * 60)) / 1000);
      countdownEl.textContent = `${hours} jam ${minutes} menit ${seconds} detik`;
    }
  }, 1000);
  <?php endif; ?>

  <?php if ($pemilihanAktif): ?>
  // Inisialisasi Chart dan DataTable
  const ctx = document.getElementById('chartSuara').getContext('2d');
  chartInstance = new Chart(ctx, {
    type: 'pie',
    data: {
      labels: <?= json_encode(array_column($chartData, 'nama_calon')) ?>,
      datasets: [{
        data: <?= json_encode(array_column($chartData, 'jumlah')) ?>,
        backgroundColor: ['#007bff', '#dc3545', '#ffc107', '#28a745', '#6610f2'],
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: {
          position: 'bottom'
        }
      }
    }
  });

  const table = $('#tabelVoting').DataTable({
    destroy: true
  });

  // Polling data setiap 30 detik
  setInterval(() => {
    $.getJSON('<?= base_url('PanitiaPanel/getVotingData') ?>', function(res) {
      // Update chart
      chartInstance.data.labels = res.chartData.map(item => item.nama_calon);
      chartInstance.data.datasets[0].data = res.chartData.map(item => item.jumlah);
      chartInstance.update();

      // Update DataTable
      table.clear();
      res.votingData.forEach((row, index) => {
        table.row.add([
          index + 1,
          row.nik,
          row.nama,
          row.voted_at,
          row.nama_calon
        ]);
      });
      table.draw();
    }).fail(function(xhr) {
      console.error("Gagal ambil data:", xhr.responseText);
    });
  }, 30000);
  <?php endif; ?>
});
</script>

<?= $this->endSection(); ?>