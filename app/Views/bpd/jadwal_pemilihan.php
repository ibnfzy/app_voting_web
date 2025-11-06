<?= $this->extend('bpd/base'); ?>
<?= $this->section('content'); ?>

<div class="container mt-4">
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0">Riwayat Jadwal Pemilihan</h5>
      <a class="btn btn-primary" href="<?= base_url('BPDPanel/prosesPemilihan'); ?>">
        Kembali ke Proses Pemilihan
      </a>
    </div>
    <div class="card-body table-responsive">
      <?php $now = $currentTime ?? time(); ?>
      <table class="table table-bordered table-striped align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th class="text-center" style="width: 60px;">No</th>
            <th>Mulai</th>
            <th>Selesai</th>
            <th>Keterangan</th>
            <th>Status</th>
            <th class="text-center" style="width: 160px;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($schedules)): ?>
          <?php foreach ($schedules as $index => $schedule): ?>
          <?php
            $startTimestamp = strtotime($schedule['start_time'] ?? '');
            $endTimestamp = strtotime($schedule['end_time'] ?? '');
            $startDisplay = $startTimestamp ? date('d/m/Y H:i', $startTimestamp) : '-';
            $endDisplay = $endTimestamp ? date('d/m/Y H:i', $endTimestamp) : '-';

            $statusLabel = 'Belum Dimulai';
            $statusClass = 'bg-secondary';

            if ($startTimestamp && $endTimestamp) {
              if ($now < $startTimestamp) {
                $statusLabel = 'Belum Dimulai';
                $statusClass = 'bg-warning text-dark';
              } elseif ($now >= $startTimestamp && $now <= $endTimestamp) {
                $statusLabel = 'Sedang Berlangsung';
                $statusClass = 'bg-info text-dark';
              } elseif ($now > $endTimestamp) {
                $statusLabel = 'Selesai';
                $statusClass = 'bg-success';
              }
            }
          ?>
          <tr>
            <td class="text-center"><?= $index + 1; ?></td>
            <td><?= esc($startDisplay); ?></td>
            <td><?= esc($endDisplay); ?></td>
            <td><?= esc($schedule['description'] ?? '-'); ?></td>
            <td>
              <span class="badge <?= esc($statusClass); ?>"><?= esc($statusLabel); ?></span>
            </td>
            <td class="text-center">
              <?php if ($endTimestamp && $endTimestamp < $now): ?>
              <a class="btn btn-sm btn-primary" href="<?= base_url('BPDPanel/Laporan/' . $schedule['id_schedule']); ?>"
                target="_blank">
                <i class="fa-solid fa-chart-column me-1"></i>
                Lihat Laporan
              </a>
              <?php else: ?>
              <span class="text-muted">-</span>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php else: ?>
          <tr>
            <td colspan="6" class="text-center text-muted">Belum ada jadwal pemilihan.</td>
          </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?= $this->endSection(); ?>
