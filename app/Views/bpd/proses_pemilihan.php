<?= $this->extend('bpd/base'); ?>
<?= $this->section('content'); ?>

<style>
  .candidate-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
  }

  .candidate-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 0.75rem 1.5rem rgba(0, 0, 0, 0.15);
  }

  .candidate-card .candidate-photo {
    height: 200px;
    object-fit: cover;
  }

  .candidate-card .status-badge {
    position: absolute;
    top: 0.75rem;
    right: 0.75rem;
    z-index: 2;
  }

  .candidate-card.winner-card {
    border: 2px solid #198754 !important;
    box-shadow: 0 0.75rem 1.5rem rgba(25, 135, 84, 0.2);
  }

  .candidate-card.leader-card {
    border: 2px solid #0d6efd !important;
  }

  .candidate-card.tied-card {
    border: 2px solid #ffc107 !important;
  }

  .candidate-card .progress {
    height: 6px;
  }
</style>

<div class="container mt-4">
  <div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0">Proses Pemilihan Kepala Desa</h5>
      <div class="d-flex gap-2">
        <?php if (isset($jadwal) && strtotime($jadwal->end_time) < time()) : ?>
        <a class="btn btn-primary" href="/BPDPanel/Laporan" target="_blank">
          Lihat Laporan Pemilihan
        </a>
        <button class="btn btn-secondary disabled">
          Pemilihan Telah Berakhir
        </button>
        <?php else: ?>
        <button class="btn btn-primary <?= $pemilihanAktif ? 'disabled' : '' ?>" data-bs-toggle="modal"
          data-bs-target="#aturJadwalModal">
          Atur Jadwal Pemilihan
        </button>
        <?php endif; ?>
        <!-- <a href="/BPDPanel/ResetPemilhan" class="btn btn-danger">
          Reset Pemilihan
        </a> -->
      </div>
    </div>
    <div class="card-body">
      <?php
      $totalSuara = array_sum(array_map(fn($item) => (int)($item['jumlah'] ?? 0), $chartData));
      $maxSuara = 0;
      $topCandidates = [];
      foreach ($chartData as $candidateData) {
        $jumlahSuara = (int)($candidateData['jumlah'] ?? 0);
        if ($jumlahSuara > $maxSuara) {
          $maxSuara = $jumlahSuara;
          $topCandidates = [$candidateData['id_candidate'] ?? null];
        } elseif ($jumlahSuara === $maxSuara) {
          $topCandidates[] = $candidateData['id_candidate'] ?? null;
        }
      }
      $pemilihanSelesai = isset($jadwal) && strtotime($jadwal->end_time) < time();
      $jumlahTopKandidat = ($valid = array_filter($topCandidates, static fn($id) => $id !== null)) ? count($valid) : count($topCandidates);

      $ringkasTeks = static function (?string $visi, ?string $misi): string {
          $allowedTags = ['p', 'strong', 'b', 'em', 'i', 'u', 'ul', 'ol', 'li', 'br', 'span', 'div'];

          $previousErrorLevel = libxml_use_internal_errors(true);

          $sanitizeFragment = static function (?string $html) use ($allowedTags): string {
              if ($html === null || trim($html) === '') {
                  return '';
              }

              $fragmentDom = new DOMDocument();
              $fragmentDom->loadHTML('<?xml encoding="utf-8" ?>' . $html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

              $fragmentXpath = new DOMXPath($fragmentDom);

              foreach ($fragmentXpath->query('//comment()') as $commentNode) {
                  if ($commentNode->parentNode) {
                      $commentNode->parentNode->removeChild($commentNode);
                  }
              }

              foreach ($fragmentXpath->query('//*') as $node) {
                  if (!in_array($node->nodeName, $allowedTags, true)) {
                      $replacement = $fragmentDom->createDocumentFragment();
                      while ($node->childNodes->length > 0) {
                          $replacement->appendChild($node->childNodes->item(0));
                      }
                      if ($node->parentNode) {
                          $node->parentNode->replaceChild($replacement, $node);
                      }
                      continue;
                  }

                  if ($node instanceof DOMElement) {
                      while ($node->attributes->length > 0) {
                          $attributeName = $node->attributes->item(0)->nodeName;
                          if ($attributeName === null) {
                              break;
                          }

                          $node->removeAttribute($attributeName);
                      }
                  }
              }

              $textPreview = trim(preg_replace('/\s+/u', ' ', $fragmentDom->textContent ?? ''));
              if ($textPreview === '') {
                  libxml_clear_errors();
                  return '';
              }

              $result = trim($fragmentDom->saveHTML());
              libxml_clear_errors();

              return $result;
          };

          $truncateNode = static function (DOMNode $node, int $limit, int &$lengthCount) use (&$truncateNode): void {
              if (!$node->hasChildNodes()) {
                  return;
              }

              $children = [];
              foreach ($node->childNodes as $child) {
                  $children[] = $child;
              }

              foreach ($children as $child) {
                  if ($lengthCount >= $limit) {
                      $node->removeChild($child);
                      continue;
                  }

                  if ($child->nodeType === XML_TEXT_NODE) {
                      $text = $child->nodeValue ?? '';
                      if ($text === '') {
                          continue;
                      }

                      $textLength = mb_strlen($text);
                      $remaining = $limit - $lengthCount;

                      if ($textLength <= $remaining) {
                          $lengthCount += $textLength;
                          continue;
                      }

                      $child->nodeValue = rtrim(mb_substr($text, 0, $remaining)) . '...';
                      $lengthCount = $limit;

                      while ($child->nextSibling) {
                          $child->parentNode->removeChild($child->nextSibling);
                      }

                      continue;
                  }

                  if ($child->nodeType === XML_ELEMENT_NODE) {
                      $truncateNode($child, $limit, $lengthCount);

                      if ($lengthCount >= $limit) {
                          while ($child->nextSibling) {
                              $child->parentNode->removeChild($child->nextSibling);
                          }
                      }
                  } else {
                      $node->removeChild($child);
                  }
              }
          };

          $visiClean = $sanitizeFragment($visi);
          $misiClean = $sanitizeFragment($misi);

          $segments = array_filter([$visiClean, $misiClean], static fn($segment) => $segment !== '');
          if (empty($segments)) {
              libxml_clear_errors();
              libxml_use_internal_errors($previousErrorLevel);
              return '';
          }

          $combinedHtml = implode('<br />', $segments);

          $dom = new DOMDocument();
          $dom->loadHTML('<?xml encoding="utf-8" ?>' . $combinedHtml, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

          $lengthCount = 0;
          $truncateNode($dom, 160, $lengthCount);

          $result = trim($dom->saveHTML());

          libxml_clear_errors();
          libxml_use_internal_errors($previousErrorLevel);

          return $result;
      };
      ?>

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

      <div class="mt-4">
        <h6 class="fw-bold mb-3">Kandidat Kepala Desa</h6>
        <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4" id="candidateCards">
          <?php if (!empty($chartData)) : ?>
            <?php foreach ($chartData as $index => $candidate) :
              $jumlahSuara = (int)($candidate['jumlah'] ?? 0);
              $persentase = $totalSuara > 0 ? round(($jumlahSuara / $totalSuara) * 100, 1) : 0;
              $namaCalon = !empty($candidate['name']) ? $candidate['name'] : 'Calon #' . ($index + 1);
              $nomorUrut = $candidate['nomor_urut'] ?? $index + 1;
              $fotoCalon = !empty($candidate['photo']) ? base_url('uploads/' . $candidate['photo']) : base_url('uploads/default.png');
              $ringkasan = $ringkasTeks($candidate['visi'] ?? null, $candidate['misi'] ?? null);

              $statusBadge = '';
              $badgeClass = 'bg-info';
              $cardClasses = 'card candidate-card h-100 shadow-sm border-0';
              $progressClass = 'bg-primary';

              $isTopCandidate = in_array($candidate['id_candidate'] ?? null, $topCandidates, true);

              if ($pemilihanAktif) {
                if ($maxSuara > 0 && $isTopCandidate) {
                  $statusBadge = 'Teratas Saat Ini';
                  $badgeClass = 'bg-success';
                  $cardClasses .= ' leader-card';
                  $progressClass = 'bg-success';
                } elseif ($maxSuara === 0) {
                  $statusBadge = 'Belum Ada Suara';
                  $badgeClass = 'bg-secondary';
                  $progressClass = 'bg-secondary';
                }
              } elseif ($pemilihanSelesai) {
                if ($maxSuara === 0) {
                  $statusBadge = 'Belum Ada Suara';
                  $badgeClass = 'bg-secondary';
                  $progressClass = 'bg-secondary';
                } elseif ($isTopCandidate) {
                  if ($jumlahTopKandidat > 1) {
                    $statusBadge = 'Seri';
                    $badgeClass = 'bg-warning text-dark';
                    $cardClasses .= ' tied-card';
                    $progressClass = 'bg-warning';
                  } else {
                    $statusBadge = 'Menang';
                    $badgeClass = 'bg-success';
                    $cardClasses .= ' winner-card';
                    $progressClass = 'bg-success';
                  }
                } else {
                  $statusBadge = 'Kalah';
                  $badgeClass = 'bg-secondary';
                  $progressClass = 'bg-secondary';
                }
              } else {
                $statusBadge = 'Menunggu Pemilihan';
                $badgeClass = 'bg-info';
              }
            ?>
              <div class="col">
                <div class="<?= $cardClasses ?>" data-candidate-id="<?= esc($candidate['id_candidate']) ?>">
                  <div class="position-relative">
                    <img src="<?= esc($fotoCalon) ?>" class="card-img-top candidate-photo"
                      alt="Foto <?= esc($namaCalon) ?>">
                    <?php if ($statusBadge) : ?>
                      <span class="badge <?= esc($badgeClass) ?> status-badge"><?= esc($statusBadge) ?></span>
                    <?php endif; ?>
                  </div>
                  <div class="card-body d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                      <h5 class="card-title mb-0"><?= esc($namaCalon) ?></h5>
                      <span class="badge bg-primary-subtle text-primary-emphasis">No. <?= esc($nomorUrut) ?></span>
                    </div>
                    <?php
                      if ($ringkasan === '') {
                          $ringkasan = '<p>Visi dan misi belum tersedia.</p>';
                      }
                    ?>
                    <div class="text-muted small flex-grow-1 summary-content"><?= $ringkasan ?></div>
                    <div class="mt-3">
                      <div class="d-flex justify-content-between small mb-1">
                        <span class="fw-semibold"><?= esc(number_format($jumlahSuara)) ?> suara</span>
                        <span class="text-muted"><?= esc(number_format($persentase, 1)) ?>%</span>
                      </div>
                      <div class="progress">
                        <div class="progress-bar <?= esc($progressClass) ?>" role="progressbar"
                          style="width: <?= esc($persentase) ?>%" aria-valuenow="<?= esc($persentase) ?>"
                          aria-valuemin="0" aria-valuemax="100"></div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          <?php else : ?>
            <div class="col">
              <div class="card candidate-card h-100 shadow-sm border-0">
                <div class="card-body d-flex flex-column justify-content-center text-center">
                  <h6 class="text-muted mb-0">Data calon belum tersedia.</h6>
                </div>
              </div>
            </div>
          <?php endif; ?>
        </div>
      </div>


      <?php if ($pemilihanAktif): ?>
      <div class="mb-4">
        <h6>Statistik Pemilihan diupdate automatis tiap 30 Detik</h6>
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
              <!-- <th>Calon Dipilih</th> -->
            </tr>
          </thead>
          <tbody>
            <?php foreach ($votingData as $i => $vote): ?>
            <tr>
              <td><?= $i + 1 ?></td>
              <td><?= esc($vote['nik']) ?></td>
              <td><?= esc($vote['nama']) ?></td>
              <td><?= esc($vote['voted_at']) ?></td>
              <!-- <td><?= esc($vote['nama_calon']) ?></td> -->
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
    <form action="/BPDPanel/aturJadwal" method="post" class="modal-content">
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

<script src="https://cdn.jsdelivr.net/npm/sanitize-html@2.17.0/index.min.js"></script>
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

  const pemilihanAktif = <?= $pemilihanAktif ? 'true' : 'false' ?>;
  const pemilihanSelesai = <?= ($pemilihanSelesai ?? false) ? 'true' : 'false' ?>;
  const uploadsBaseUrl = "<?= base_url('uploads') ?>";
  const defaultPhotoUrl = "<?= base_url('uploads/default.png') ?>";
  const candidateCardsContainer = document.getElementById('candidateCards');

  const escapeHtml = (text) => {
    return (text || '').replace(/[&<>"']/g, (ch) => ({
      '&': '&amp;',
      '<': '&lt;',
      '>': '&gt;',
      '"': '&quot;',
      "'": '&#039;'
    }[ch] || ch));
  };

  const allowedSummaryTags = ['p', 'strong', 'b', 'em', 'i', 'u', 'ul', 'ol', 'li', 'br', 'span', 'div'];

  const truncateNode = (node, limit, state) => {
    const children = Array.from(node.childNodes);

    for (const child of children) {
      if (state.count >= limit) {
        node.removeChild(child);
        continue;
      }

      if (child.nodeType === Node.TEXT_NODE) {
        const text = child.nodeValue || '';
        if (!text) {
          continue;
        }

        const textLength = text.length;
        const remaining = limit - state.count;

        if (textLength <= remaining) {
          state.count += textLength;
          continue;
        }

        const truncated = text.slice(0, remaining).replace(/\s+$/u, '');
        child.nodeValue = `${truncated}...`;
        state.count = limit;

        let sibling = child.nextSibling;
        while (sibling) {
          const next = sibling.nextSibling;
          sibling.parentNode.removeChild(sibling);
          sibling = next;
        }

        continue;
      }

      if (child.nodeType === Node.ELEMENT_NODE) {
        truncateNode(child, limit, state);

        if (state.count >= limit) {
          let sibling = child.nextSibling;
          while (sibling) {
            const next = sibling.nextSibling;
            sibling.parentNode.removeChild(sibling);
            sibling = next;
          }
        }
      } else {
        node.removeChild(child);
      }
    }
  };

  const sanitizeSummaryPart = (html) => {
    if (!html) {
      return '';
    }

    const sanitized = sanitizeHtml(html, {
      allowedTags: allowedSummaryTags,
      allowedAttributes: {}
    });

    const wrapper = document.createElement('div');
    wrapper.innerHTML = sanitized;

    if (!wrapper.textContent.trim()) {
      return '';
    }

    return wrapper.innerHTML;
  };

  const buildSummaryHtml = (visi, misi, maxLength = 160) => {
    const parts = [];
    const visiPart = sanitizeSummaryPart(visi);
    if (visiPart) {
      parts.push(visiPart);
    }
    const misiPart = sanitizeSummaryPart(misi);
    if (misiPart) {
      parts.push(misiPart);
    }

    if (!parts.length) {
      return '';
    }

    const container = document.createElement('div');
    container.innerHTML = parts.join('<br />');

    const state = { count: 0 };
    truncateNode(container, maxLength, state);

    if (!container.textContent.trim()) {
      return '';
    }

    return container.innerHTML;
  };

  const renderCandidateCards = (data) => {
    if (!candidateCardsContainer) {
      return;
    }

    if (!Array.isArray(data) || data.length === 0) {
      candidateCardsContainer.innerHTML = `
        <div class="col">
          <div class="card candidate-card h-100 shadow-sm border-0">
            <div class="card-body d-flex flex-column justify-content-center text-center">
              <h6 class="text-muted mb-0">Data calon belum tersedia.</h6>
            </div>
          </div>
        </div>`;
      return;
    }

    const totalVotes = data.reduce((total, item) => total + parseInt(item.jumlah ?? 0, 10), 0);
    const maxVotes = data.reduce((max, item) => Math.max(max, parseInt(item.jumlah ?? 0, 10)), 0);
    const topCandidates = data
      .filter((item) => parseInt(item.jumlah ?? 0, 10) === maxVotes)
      .map((item) => item.id_candidate ?? null);
    const validTopCandidates = topCandidates.filter((id) => id !== null);
    const topCandidateCount = validTopCandidates.length > 0 ? validTopCandidates.length : topCandidates.length;

    candidateCardsContainer.innerHTML = '';

    data.forEach((candidate, index) => {
      const votes = parseInt(candidate.jumlah ?? 0, 10);
      const percentage = totalVotes > 0 ? (votes / totalVotes) * 100 : 0;
      const percentageDisplay = percentage.toFixed(1);
      const name = (candidate.name && candidate.name.trim()) ? candidate.name : `Calon #${index + 1}`;
      const orderNumber = candidate.nomor_urut ?? (index + 1);
      const sanitizedSummary = buildSummaryHtml(candidate.visi, candidate.misi);

      let photo = candidate.photo ? `${uploadsBaseUrl}/${candidate.photo}` : defaultPhotoUrl;
      if (!candidate.photo) {
        photo = defaultPhotoUrl;
      }

      let statusBadge = '';
      let badgeClass = 'bg-info';
      let cardClassNames = ['card', 'candidate-card', 'h-100', 'shadow-sm', 'border-0'];
      let progressClass = 'bg-primary';
      const isTopCandidate = topCandidates.includes(candidate.id_candidate ?? null);

      if (pemilihanAktif) {
        if (maxVotes > 0 && isTopCandidate) {
          statusBadge = 'Teratas Saat Ini';
          badgeClass = 'bg-success';
          cardClassNames.push('leader-card');
          progressClass = 'bg-success';
        } else if (maxVotes === 0) {
          statusBadge = 'Belum Ada Suara';
          badgeClass = 'bg-secondary';
          progressClass = 'bg-secondary';
        }
      } else if (pemilihanSelesai) {
        if (maxVotes === 0) {
          statusBadge = 'Belum Ada Suara';
          badgeClass = 'bg-secondary';
          progressClass = 'bg-secondary';
        } else if (isTopCandidate) {
          if (topCandidateCount > 1) {
            statusBadge = 'Seri';
            badgeClass = 'bg-warning text-dark';
            cardClassNames.push('tied-card');
            progressClass = 'bg-warning';
          } else {
            statusBadge = 'Menang';
            badgeClass = 'bg-success';
            cardClassNames.push('winner-card');
            progressClass = 'bg-success';
          }
        } else {
          statusBadge = 'Kalah';
          badgeClass = 'bg-secondary';
          progressClass = 'bg-secondary';
        }
      } else {
        statusBadge = 'Menunggu Pemilihan';
      }

      const badgeHtml = statusBadge ? `<span class="badge ${badgeClass} status-badge">${escapeHtml(statusBadge)}</span>` : '';

      const col = document.createElement('div');
      col.className = 'col';
      col.innerHTML = `
        <div class="${cardClassNames.join(' ')}" data-candidate-id="${escapeHtml(String(candidate.id_candidate ?? ''))}">
          <div class="position-relative">
            <img src="${escapeHtml(photo)}" class="card-img-top candidate-photo" alt="Foto ${escapeHtml(name)}">
            ${badgeHtml}
          </div>
          <div class="card-body d-flex flex-column">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <h5 class="card-title mb-0">${escapeHtml(name)}</h5>
              <span class="badge bg-primary-subtle text-primary-emphasis">No. ${escapeHtml(String(orderNumber))}</span>
            </div>
            <div class="text-muted small flex-grow-1 summary-content"></div>
            <div class="mt-3">
              <div class="d-flex justify-content-between small mb-1">
                <span class="fw-semibold">${escapeHtml(votes.toLocaleString('id-ID'))} suara</span>
                <span class="text-muted">${escapeHtml(percentageDisplay)}%</span>
              </div>
              <div class="progress">
                <div class="progress-bar ${progressClass}" role="progressbar"
                  style="width: ${percentage.toFixed(2)}%" aria-valuenow="${escapeHtml(percentageDisplay)}"
                  aria-valuemin="0" aria-valuemax="100"></div>
              </div>
            </div>
          </div>
        </div>`;

      const summaryContainer = col.querySelector('.summary-content');
      summaryContainer.innerHTML = sanitizedSummary || '<p>Visi dan misi belum tersedia.</p>';

      candidateCardsContainer.appendChild(col);
    });
  };

  renderCandidateCards(<?= json_encode($chartData) ?>);

  <?php if ($pemilihanAktif): ?>
  // Inisialisasi Chart dan DataTable
  const ctx = document.getElementById('chartSuara').getContext('2d');
  chartInstance = new Chart(ctx, {
    type: 'pie',
    data: {
      labels: <?= json_encode(array_column($chartData, 'name')) ?>,
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
    $.getJSON('<?= base_url('BPDPanel/getVotingData') ?>', function(res) {
      // Update chart
      chartInstance.data.labels = res.chartData.map(item => item.name ?? item.nama_calon ?? `Calon`);
      chartInstance.data.datasets[0].data = res.chartData.map(item => item.jumlah);
      chartInstance.update();

      renderCandidateCards(res.chartData);

      // Update DataTable
      table.clear();
      res.votingData.forEach((row, index) => {
        table.row.add([
          index + 1,
          row.nik || '-',
          row.nama || '-',
          row.voted_at || '-',
          row.nama_calon || '-'
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