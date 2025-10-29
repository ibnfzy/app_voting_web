<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Pemilihan Kepala Desa</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(20px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .fade-in-up {
      animation: fadeInUp 0.8s ease-out forwards;
    }
  </style>
</head>

<body class="bg-gray-100 text-gray-800 font-sans">

  <!-- Navbar -->
  <nav id="navbar" class="fixed top-0 left-0 w-full z-50 transition-all duration-300 bg-transparent text-white">
    <div class="max-w-6xl mx-auto px-4 py-4 flex justify-between items-center">
      <h1 class="text-2xl font-bold">PILKADES</h1>
      <div class="space-x-6 hidden md:block">
        <a href="#jadwal" class="hover:text-white/80 font-medium transition">Jadwal</a>
        <a href="#kandidat" class="hover:text-white/80 font-medium transition">Kandidat</a>
        <a href="https://drive.google.com/file/d/1auPci5FE5IFscfA618SQD0gwlItjNehV/view?usp=sharing" class="hover:text-white/80 font-medium transition" target="_blank">Download APK Pemilihan</a>
        <a href="/BPDPanel" class="hover:text-white/80 font-medium transition">Login BPD</a>
        <a href="/PanitiaPanel" class="hover:text-white/80 font-medium transition">Login Panitia</a>
      </div>
    </div>
  </nav>
  <!-- Hero Section -->
  <section class="bg-gradient-to-b from-emerald-600 to-teal-500 text-white pt-40 pb-20 text-center">
    <h2 class="text-4xl font-extrabold fade-in-up">Pemilihan Kepala Desa</h2>
    <p class="text-lg mt-4 fade-in-up">Kenali kandidat terbaik dan gunakan hak pilih Anda!</p>
  </section>

  <!-- Jadwal -->
  <!-- Jadwal Section -->
  <section id="jadwal" class="max-w-6xl mx-auto mt-10 px-4">
    <h2 class="text-2xl font-bold text-green-700 mb-6 text-center">🗓️ Jadwal Pemilihan</h2>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <!-- Card Jadwal -->
      <div class="bg-white border rounded-2xl shadow-sm p-6 hover:shadow-md transition-shadow duration-300 fade-in-up">
        <div class="flex items-center gap-4">
          <div class="bg-green-100 text-green-600 p-2 rounded-full">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
          </div>
          <div>
            <h3 class="text-lg font-semibold text-gray-800">Tanggal Pemilihan</h3>
            <p class="text-sm text-gray-500">Gunakan hak suara Anda pada tanggal berikut:</p>
          </div>
        </div>

        <div class="mt-4 text-sm text-gray-700 space-y-1">
          <?php if ($jadwalStatus['status'] === 'no_schedule'): ?>
            <div class="bg-red-100 border border-red-300 text-red-700 p-3 rounded-lg">
              <strong>Belum ada jadwal pemilihan.</strong>
            </div>
          <?php elseif ($jadwalStatus['status'] === 'upcoming'): ?>
            <p><strong>Mulai:</strong> <span id="startTime"><?= $jadwalStatus['start_time'] ?></span></p>
            <p><strong>Berakhir:</strong> <span id="endTime"><?= $jadwalStatus['end_time'] ?></span></p>
            <div class="mt-4 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
              <p class="text-sm font-medium text-yellow-700">Waktu Menuju Pemilihan:</p>
              <div id="countdown" class="text-xl font-bold text-yellow-800 mt-1">--:--:--</div>
            </div>
          <?php elseif ($jadwalStatus['status'] === 'active'): ?>
            <p><strong>Mulai:</strong> <?= $jadwalStatus['start_time'] ?></p>
            <p><strong>Berakhir:</strong> <?= $jadwalStatus['end_time'] ?></p>
            <div class="mt-4 bg-green-50 border border-green-200 rounded-lg p-4">
              <p class="text-sm font-medium text-green-700">Pemilihan sedang berlangsung</p>
            </div>
          <?php elseif ($jadwalStatus['status'] === 'finished'): ?>
            <p><strong>Mulai:</strong> <?= $jadwalStatus['start_time'] ?></p>
            <p><strong>Berakhir:</strong> <?= $jadwalStatus['end_time'] ?></p>
            <div class="mt-4 bg-gray-100 border border-gray-300 rounded-lg p-4">
              <p class="text-sm font-medium text-gray-700">Pemilihan telah selesai.</p>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </section>


  <!-- Kandidat -->
  <section id="kandidat" class="max-w-6xl mx-auto mt-12 px-4">
    <h2 class="text-2xl font-bold text-green-700 mb-6 text-center">Daftar Kandidat</h2>

    <div class="flex flex-wrap justify-center gap-8">
      <!-- Kandidat 1 -->
      <?php foreach ($dataCalon as $item) : ?>
        <div
          class="w-full sm:w-[300px] bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-2xl transition-shadow duration-300 transform hover:-translate-y-1 fade-in-up">
          <img src="/uploads/<?= $item['photo'] ?>" alt="Foto Kandidat" class="w-full h-52 object-cover">
          <div class="p-4">
            <h3 class="text-xl font-bold text-gray-800"><?= $item['name']; ?></h3>
            <p class="mt-2 text-sm text-gray-600"><strong>Visi:</strong> <?= $item['visi']; ?></p>
            <p class="mt-2 text-sm text-gray-600"><strong>Misi:</strong></p>
            <ul class="list-disc list-inside text-sm text-gray-700">
              <?= $item['misi']; ?>
            </ul>
          </div>
        </div>
      <?php endforeach ?>

    </div>
  </section>

  <!-- Footer -->
  <footer class="mt-16 bg-green-700 text-white text-center py-6">
    <p>&copy; 2025 JULTDEV</p>
  </footer>

  <script>
    const endTime = new Date(document.getElementById("endTime").innerText).getTime();

    function updateCountdown() {
      const now = new Date().getTime();
      const distance = endTime - now;

      if (distance < 0) {
        document.getElementById("countdown").innerText = "Pemilihan telah selesai.";
        return;
      }

      const days = Math.floor(distance / (1000 * 60 * 60 * 24));
      const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
      const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
      const seconds = Math.floor((distance % (1000 * 60)) / 1000);

      document.getElementById("countdown").innerText =
        `${days}h ${hours}j ${minutes}m ${seconds}d`;
    }

    setInterval(updateCountdown, 1000);
    updateCountdown();

    // navbar
    const navbar = document.getElementById("navbar");

    window.addEventListener("scroll", () => {
      if (window.scrollY > 10) {
        navbar.classList.remove("bg-transparent", "text-white");
        navbar.classList.add(
          "bg-gradient-to-r",
          "from-emerald-600",
          "to-teal-500",
          "text-white",
          "shadow-md"
        );
      } else {
        navbar.classList.remove(
          "bg-gradient-to-r",
          "from-emerald-600",
          "to-teal-500",
          "shadow-md"
        );
        navbar.classList.add("bg-transparent", "text-white");
      }
    });
  </script>
</body>

</html>