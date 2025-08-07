<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Jadwal Sholat Hari Ini</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      background: rgb(190, 61, 61);
      font-family: 'Segoe UI', sans-serif;
    }

    .overlay {
      display: none;
    }

    .content {
      position: relative;
      z-index: 1;
    }

    .card {
      border-radius: 1rem;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .countdown {
      font-size: 1.5rem;
      font-weight: bold;
      color: #1c5c5c;
    }

    .icon-sholat {
      margin-right: 8px;
      color: #bdbdbd;
    }

    #btn-reminder {
      background-color: #000000;
      color: #fff;
      border: none;
      padding: 8px 16px;
      border-radius: 4px;
      transition: all 0.3s ease;
    }

    #btn-reminder:hover {
      background-color: #fff !important;
      color: #000 !important;
    }

    #save-reminder {
      background-color: rgb(190, 61, 61);
      color: #fff;
      transition: all 0.3s ease;
    }

    #save-reminder:hover {
      background-color: #bdbdbd !important;
      color: #fff !important;
    }

    .reminder-form {
      display: none;
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      background: white;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
      z-index: 1000;
      width: 90%;
      max-width: 400px;
    }

    .reminder-form h4 {
      color: #c13e3e;
      margin-bottom: 20px;
    }

    .form-check-label {
      margin-left: 5px;
    }

    .close-btn {
      position: absolute;
      top: 10px;
      right: 10px;
      cursor: pointer;
      font-size: 1.5rem;
    }

    #select-kota {
      margin-right: 10px;
    }

    .alarm-timer {
      margin-top: 10px;
      font-size: 0.9rem;
      color: #666;
      text-align: center;
    }

    .alarm-info {
      font-size: 0.8rem;
      color: #666;
      margin-top: 5px;
    }
  </style>
</head>

<body>
  <div class="container pt-2">
    <div class="row justify-content-center">
      <div class="col-12 col-md-auto text-center">
        <img src="image/logotelkom.png" alt="Telkom University Logo" class="img-fluid" style="height: 60px; max-width: 100%; opacity: 0.95; display: block; margin: 20px auto 0 auto;">
      </div>
    </div>
  </div>
  <div class="overlay"></div>
  <div class="content" style="padding-top: 10px;">
    <div class="container-fluid px-2 px-md-4">
      <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
          <div class="text-center mb-4">
            <h1 class="fw-bold"><i class="bi bi-moon-stars icon-sholat"></i> Jadwal Sholat Hari Ini</h1>
            <div class="d-flex flex-column flex-sm-row justify-content-center align-items-center mt-2 mb-2 gap-2">
              <label for="select-kota" class="me-2 mb-0">Pilih Kota:</label>
              <select id="select-kota" class="form-select w-auto">
                <option value="">Deteksi Otomatis</option>
                <option value="-6.2,106.816666">Jakarta</option>
                <option value="-6.914744,107.609810">Bandung</option>
                <option value="-7.250445,112.768845">Surabaya</option>
                <option value="3.595196,98.672226">Medan</option>
                <option value="-5.147665,119.432732">Makassar</option>
                <option value="-0.789275,113.921327">Palangkaraya</option>
                <option value="-7.801194,110.364917">Yogyakarta</option>
                <option value="-6.121435,106.774124">Tangerang</option>
                <option value="-6.990398,110.422910">Semarang</option>
                <option value="-8.409518,115.188919">Denpasar</option>
              </select>
              <button id="btn-reminder" class="btn">
                <i class="bi bi-alarm"></i> Ingatkan Waktu Sholat
              </button>
            </div>
          </div>
          <div class="row justify-content-center">
            <div class="col-12 col-lg-6">
              <div class="card p-3 mb-2">
                <h5 class="mb-2"><i class="bi bi-calendar-week icon-sholat"></i> Waktu Sholat Hari Ini:</h5>
                <ul class="list-group pb-5" id="jadwal-sholat">
                  <!-- Data dari API akan dimasukkan di sini -->
                </ul>
              </div>
            </div>
            <div class="col-12 col-lg-6 -1 d-flex flex-column">
              <div class="card text-center position-relative mb-3 flex-grow-1" style="background: #fff; color: #c13e3e; height: 300px;">
                <button id="prev-ayat" class="btn btn-link position-absolute top-50 start-0 translate-middle-y" style="font-size:2rem; color: #6c757d; z-index: 10; background: rgba(255,255,255,0.8);"><i class="bi bi-chevron-left"></i></button>
                <button id="play-audio-ayat" class="btn btn-link position-absolute bottom-0 end-0 m-2" style="font-size:1.7rem; color:#c13e3e;">
                  <i id="icon-audio-ayat" class="bi bi-volume-mute-fill"></i>
                </button>
                <div id="ayat-quran" class="p-3 h-100 d-flex flex-column justify-content-center" style="min-height: 90px;">
                  <!-- Ayat akan muncul di sini -->
                </div>
                <button id="next-ayat" class="btn btn-link position-absolute top-50 end-0 translate-middle-y" style="font-size:2rem; color: #6c757d; z-index: 10; background: rgba(255,255,255,0.8);"><i class="bi bi-chevron-right"></i></button>
                <audio id="audio-ayat" src="" preload="none"></audio>
              </div>
              <div class="card p-3 text-center flex-grow-1 mb-5">
                <h5><i class="bi bi-alarm icon-sholat"></i> Sholat Selanjutnya: <span id="sholat-selanjutnya">-</span></h5>
                <div class="countdown mt-2" id="countdown">00:00:00</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Form untuk pengingat sholat -->
  <div id="reminder-form" class="reminder-form">
    <span class="close-btn">&times;</span>
    <h4><i class="bi bi-bell-fill"></i> Atur Pengingat Sholat</h4>
    <form id="reminder-settings">
      <div class="mb-3">
        <label class="form-label">Pilih Waktu Sholat:</label>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" id="reminder-subuh" name="sholat" value="Shubuh">
          <label class="form-check-label" for="reminder-subuh">Shubuh</label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" id="reminder-dzuhur" name="sholat" value="Dzuhur">
          <label class="form-check-label" for="reminder-dzuhur">Dzuhur</label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" id="reminder-ashar" name="sholat" value="Ashar">
          <label class="form-check-label" for="reminder-ashar">Ashar</label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" id="reminder-maghrib" name="sholat" value="Maghrib">
          <label class="form-check-label" for="reminder-maghrib">Maghrib</label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" id="reminder-isya" name="sholat" value="Isya">
          <label class="form-check-label" for="reminder-isya">Isya</label>
        </div>
      </div>
      <div class="mb-3">
        <label class="form-label">Pilih Suara Alarm:</label>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="alarm-sound" id="alarm-adzan" value="adzan" checked>
          <label class="form-check-label" for="alarm-adzan">Suara Adzan</label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="alarm-sound" id="alarm-beep" value="beep">
          <label class="form-check-label" for="alarm-beep">Alarm Jam</label>
        </div>
      </div>
      <div class="mb-3">
        <label for="reminder-minutes" class="form-label">Pengingat Sebelum Waktu Sholat (menit):</label>
        <input type="number" class="form-control" id="reminder-minutes" min="0" max="60" value="5">
        <div class="alarm-timer" id="alarm-timer-display"></div>
        <div class="alarm-info">Alarm akan berbunyi <span id="alarm-minutes-display">5</span> menit sebelum waktu sholat</div>
      </div>
      <button type="button" id="save-reminder" class="btn w-100">
        Simpan Pengaturan
      </button>
    </form>
  </div>

  <!-- Text bergerak di paling bawah -->
  <div class="position-fixed bottom-0 start-0 w-100 bg-white py-2" style="z-index: 1000;">
    <marquee scrollamount="5" class="text-dark" style="font-family: 'Segoe UI', sans-serif;">
      Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
    </marquee>
  </div>

  <script>
    // Variabel global
    let reminderSettings = {
      sholat: [],
      alarmSound: 'adzan',
      minutesBefore: 5,
      alarms: {}
    };

    // Data ayat Al-Qur'an
    const ayatList = [{
        arab: 'إِنَّ الصَّلَاةَ كَانَتْ عَلَى الْمُؤْمِنِينَ كِتَابًا مَوْقُوتًا',
        arti: 'Sesungguhnya shalat itu adalah fardhu yang ditentukan waktunya atas orang-orang yang beriman.',
        sumber: 'QS. An-Nisa: 103',
        audio: 'image/an-nisa103.mp3'
      },
      {
        arab: 'فَوَيْلٌ لِّلْمُصَلِّينَ الَّذِينَ هُمْ عَن صَلَاتِهِمْ سَاهُونَ',
        arti: 'Maka celakalah orang-orang yang shalat,(yaitu) orang-orang yang lalai dari shalatnya.',
        sumber: 'QS. Al-Ma\'un: 4-5',
        audio: 'image/almaun.mp3'
      },
      {
        arab: 'إِنَّنِي أَنَا اللَّهُ لَا إِلَٰهَ إِلَّا أَنَا فَاعْبُدْنِي وَأَقِمِ الصَّلَاةَ لِذِكْرِي',
        arti: 'Sesungguhnya Aku ini adalah Allah, tidak ada Tuhan (yang berhak disembah) selain Aku,<br> maka sembahlah Aku dan dirikanlah shalat untuk mengingat-Ku.',
        sumber: 'QS. Thaha: 14',
        audio: 'image/thahaayat4.mp3'
      },
      {
        arab: 'الَّذِينَ يُؤْمِنُونَ بِالْغَيْبِ وَيُقِيمُونَ الصَّلَاةَ وَمِمَّا رَزَقْنَاهُمْ يُنفِقُونَ',
        arti: 'yaitu mereka yang beriman kepada yang gaib, yang mendirikan salat,<br> dan menafkahkan sebagian rezeki yang Kami anugerahkan kepada mereka.',
        sumber: 'QS. Al-Baqarah: 3',
        audio: 'image/albaqarah3.mp3'
      },
      {
        arab: 'وَاسْتَعِينُوا بِالصَّبْرِ وَالصَّلَاةِ وَإِنَّهَا لَكَبِيرَةٌ إِلَّا عَلَى الْخَاشِعِينَ',
        arti: 'Dan mintalah pertolongan (kepada Allah) dengan sabar dan salat.<br> Dan sesungguhnya yang demikian itu sungguh berat, kecuali bagi orang-orang yang khusyuk.',
        sumber: 'QS. Al-Baqarah: 45',
        audio: 'image/albaqarah45.mp3'
      }
    ];
    let ayatIndex = 0;

    // Fungsi untuk memainkan sound effect
    function playButtonSound() {
      try {
        const audioContext = new(window.AudioContext || window.webkitAudioContext)();
        const oscillator = audioContext.createOscillator();
        const gainNode = audioContext.createGain();

        oscillator.connect(gainNode);
        gainNode.connect(audioContext.destination);

        oscillator.frequency.setValueAtTime(800, audioContext.currentTime);
        oscillator.type = 'sine';

        gainNode.gain.setValueAtTime(0.1, audioContext.currentTime);
        gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.1);

        oscillator.start(audioContext.currentTime);
        oscillator.stop(audioContext.currentTime + 0.1);
      } catch (error) {
        console.log('Sound effect error:', error);
      }
    }

    // Fungsi untuk memainkan sound effect success
    function playSuccessSound() {
      try {
        const audioContext = new(window.AudioContext || window.webkitAudioContext)();
        const oscillator = audioContext.createOscillator();
        const gainNode = audioContext.createGain();

        oscillator.connect(gainNode);
        gainNode.connect(audioContext.destination);

        oscillator.frequency.setValueAtTime(1000, audioContext.currentTime);
        oscillator.frequency.exponentialRampToValueAtTime(1500, audioContext.currentTime + 0.2);
        oscillator.type = 'sine';

        gainNode.gain.setValueAtTime(0.2, audioContext.currentTime);
        gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.3);

        oscillator.start(audioContext.currentTime);
        oscillator.stop(audioContext.currentTime + 0.3);
      } catch (error) {
        console.log('Success sound error:', error);
      }
    }

    // Fungsi untuk menampilkan ayat
    function tampilkanAyat() {
      const ayat = ayatList[ayatIndex];
      document.getElementById('ayat-quran').innerHTML = `
        <div style="font-size: clamp(1.5rem, 4vw, 2.1rem); font-family:'Scheherazade', serif; font-weight:bold; min-height: 120px; display: flex; align-items: center; justify-content: center; text-align: center; padding: 0 15px;">${ayat.arab}</div>
        <div class="mt-3" style="font-size: clamp(0.9rem, 3vw, 1.15rem); font-weight:bold; min-height: 80px; display: flex; align-items: center; justify-content: center; text-align: center; padding: 0 15px; line-height: 1.4;">${ayat.arti}</div>
        <div class="mt-2 text-center" style="font-size: clamp(0.8rem, 2.5vw, 1.05rem); font-style:italic; color:#c13e3e; min-height: 40px; display: flex; align-items: center; justify-content: center; text-align: center; word-wrap: break-word; overflow-wrap: break-word; padding: 0 15px;">${ayat.sumber}</div>
      `;
      document.getElementById('audio-ayat').src = ayat.audio;
      document.getElementById('icon-audio-ayat').className = 'bi bi-volume-mute-fill';
    }

    // Fungsi untuk menampilkan form reminder
    function showReminderForm() {
      document.getElementById('reminder-form').style.display = 'block';
      playButtonSound();
    }

    // Fungsi untuk menyembunyikan form reminder
    function hideReminderForm() {
      playButtonSound();
      document.getElementById('reminder-form').style.display = 'none';
    }

    // Fungsi untuk update tampilan menit alarm
    function updateAlarmMinutesDisplay() {
      const minutes = document.getElementById('reminder-minutes').value || 5;
      document.getElementById('alarm-minutes-display').textContent = minutes;
    }

    // Fungsi untuk menyimpan pengaturan reminder
    function saveReminderSettings() {
      const selectedSholat = [];
      document.querySelectorAll('input[name="sholat"]:checked').forEach(checkbox => {
        selectedSholat.push(checkbox.value);
      });

      reminderSettings.sholat = selectedSholat;
      reminderSettings.alarmSound = document.querySelector('input[name="alarm-sound"]:checked').value;
      reminderSettings.minutesBefore = parseInt(document.getElementById('reminder-minutes').value) || 5;

      // Hapus semua alarm yang ada sebelumnya
      Object.values(reminderSettings.alarms).forEach(alarmId => {
        clearTimeout(alarmId);
      });
      reminderSettings.alarms = {};

      // Set alarm baru untuk setiap sholat yang dipilih
      if (reminderSettings.sholat.length > 0) {
        setReminderAlarms();
        playSuccessSound();
        alert('Pengingat sholat berhasil diatur!');
      } else {
        alert('Tidak ada sholat yang dipilih untuk diingatkan.');
      }

      hideReminderForm();
    }

    // Fungsi untuk mengatur alarm pengingat
    function setReminderAlarms() {
      const now = new Date();
      const jadwalElement = document.getElementById('jadwal-sholat');

      if (!jadwalElement) return;

      const sholatItems = jadwalElement.querySelectorAll('li');
      sholatItems.forEach(item => {
        const sholatName = item.textContent.trim().split(/\s+/)[0];
        if (reminderSettings.sholat.includes(sholatName)) {
          const timeText = item.textContent.trim().split(/\s+/).pop();
          const [hours, minutes] = timeText.split(':').map(Number);

          // Hitung waktu pengingat (sebelum waktu sholat)
          const reminderTime = new Date();
          reminderTime.setHours(hours, minutes - reminderSettings.minutesBefore, 0, 0);

          // Jika waktu pengingat sudah lewat hari ini, set untuk besok
          if (reminderTime < now) {
            reminderTime.setDate(reminderTime.getDate() + 1);
          }

          const timeDiff = reminderTime - now;

          // Set timeout untuk alarm
          const alarmId = setTimeout(() => {
            triggerAlarm(sholatName);
          }, timeDiff);

          // Simpan ID timeout
          reminderSettings.alarms[sholatName] = alarmId;
        }
      });
    }

    // Fungsi untuk memicu alarm
    function triggerAlarm(sholatName) {
      let audioSrc = '';

      if (reminderSettings.alarmSound === 'adzan') {
        audioSrc = 'path/to/adzan.mp3';
      } else {
        audioSrc = 'path/to/beep.mp3';
      }

      // Buat elemen audio dan mainkan
      const alarmAudio = new Audio(audioSrc);
      alarmAudio.play().catch(e => console.error('Gagal memainkan alarm:', e));

      // Tampilkan notifikasi
      if (Notification.permission === 'granted') {
        new Notification(`Waktu Sholat ${sholatName}`, {
          body: `Waktu sholat ${sholatName} akan segera tiba dalam ${reminderSettings.minutesBefore} menit!`,
          icon: 'path/to/icon.png'
        });
      } else if (Notification.permission !== 'denied') {
        Notification.requestPermission().then(permission => {
          if (permission === 'granted') {
            new Notification(`Waktu Sholat ${sholatName}`, {
              body: `Waktu sholat ${sholatName} akan segera tiba dalam ${reminderSettings.minutesBefore} menit!`,
              icon: 'path/to/icon.png'
            });
          }
        });
      }

      // Set alarm untuk hari berikutnya
      const nextAlarmTime = new Date();
      nextAlarmTime.setDate(nextAlarmTime.getDate() + 1);
      nextAlarmTime.setHours(nextAlarmTime.getHours(), nextAlarmTime.getMinutes() - reminderSettings.minutesBefore, 0, 0);

      const timeDiff = nextAlarmTime - new Date();
      const alarmId = setTimeout(() => {
        triggerAlarm(sholatName);
      }, timeDiff);

      // Update ID alarm
      reminderSettings.alarms[sholatName] = alarmId;
    }

    // Fungsi untuk mendapatkan jadwal sholat
    async function getJadwal(lat, long) {
      const today = new Date().toISOString().split('T')[0];
      const response = await fetch(`https://api.aladhan.com/v1/timings/${today}?latitude=${lat}&longitude=${long}&method=20`);
      const data = await response.json();
      return data.data.timings;
    }

    // Fungsi untuk menampilkan jadwal sholat
    async function tampilkanJadwal(lat, long) {
      const jadwal = await getJadwal(lat, long);
      const list = document.getElementById('jadwal-sholat');
      list.innerHTML = '';

      // Hitung waktu Dhuha (15 menit setelah Syuruq)
      let dhuha = '';
      if (jadwal.Sunrise) {
        const [h, m] = jadwal.Sunrise.split(':');
        let jam = parseInt(h);
        let menit = parseInt(m) + 15;
        if (menit >= 60) {
          jam += 1;
          menit -= 60;
        }
        dhuha = `${String(jam).padStart(2, '0')}:${String(menit).padStart(2, '0')}`;
      }

      const tampilkan = [{
          key: 'Imsak',
          label: 'Imsak'
        },
        {
          key: 'Fajr',
          label: 'Shubuh'
        },
        {
          key: 'Sunrise',
          label: 'Syuruq'
        },
        {
          key: 'Dhuha',
          label: 'Dhuha'
        },
        {
          key: 'Dhuhr',
          label: 'Dzuhur'
        },
        {
          key: 'Asr',
          label: 'Ashar'
        },
        {
          key: 'Maghrib',
          label: 'Maghrib'
        },
        {
          key: 'Isha',
          label: 'Isya'
        }
      ];

      tampilkan.forEach((item) => {
        let waktu = '';
        if (item.key === 'Dhuha') {
          waktu = dhuha;
        } else {
          waktu = jadwal[item.key] || '-';
        }

        const li = document.createElement('li');
        li.className = 'list-group-item d-flex justify-content-between align-items-center';
        let icon = '';

        switch (item.key) {
          case 'Imsak':
            icon = '<i class="bi bi-cloud-moon icon-sholat"></i>';
            break;
          case 'Fajr':
            icon = '<i class="bi bi-sunrise icon-sholat"></i>';
            break;
          case 'Sunrise':
            icon = '<i class="bi bi-sun icon-sholat"></i>';
            break;
          case 'Dhuha':
            icon = '<i class="bi bi-brightness-high icon-sholat"></i>';
            break;
          case 'Dhuhr':
            icon = '<i class="bi bi-brightness-high icon-sholat"></i>';
            break;
          case 'Asr':
            icon = '<i class="bi bi-sun icon-sholat"></i>';
            break;
          case 'Maghrib':
            icon = '<i class="bi bi-sunset icon-sholat"></i>';
            break;
          case 'Isha':
            icon = '<i class="bi bi-moon icon-sholat"></i>';
            break;
          default:
            icon = '';
        }

        li.innerHTML = `<span>${icon}${item.label}</span><span>${waktu !== '-' ? waktu : '-'}</span>`;
        list.appendChild(li);
      });

      // Set sholat selanjutnya
      const next = getNextMainPrayer(jadwal);
      if (next) {
        document.getElementById('sholat-selanjutnya').textContent = next.name;
        startCountdown(next.time);
      }

      // Set ulang alarm jika ada pengaturan reminder
      if (reminderSettings.sholat.length > 0) {
        setReminderAlarms();
      }
    }

    // Fungsi untuk mendapatkan sholat utama selanjutnya
    function getNextMainPrayer(jadwal) {
      const mainPrayers = [{
          key: 'Fajr',
          label: 'Shubuh'
        },
        {
          key: 'Dhuhr',
          label: 'Dzuhur'
        },
        {
          key: 'Asr',
          label: 'Ashar'
        },
        {
          key: 'Maghrib',
          label: 'Maghrib'
        },
        {
          key: 'Isha',
          label: 'Isya'
        }
      ];

      const now = new Date();
      for (let i = 0; i < mainPrayers.length; i++) {
        const time = jadwal[mainPrayers[i].key];
        if (time) {
          const [h, m] = time.split(':');
          const t = new Date();
          t.setHours(h, m, 0, 0);
          if (t > now) {
            return {
              name: mainPrayers[i].label,
              time: t
            };
          }
        }
      }

      // Jika sudah lewat Isya, sholat berikutnya adalah Shubuh besok
      const fajr = jadwal['Fajr'];
      if (fajr) {
        const [h, m] = fajr.split(':');
        const t = new Date();
        t.setDate(t.getDate() + 1);
        t.setHours(h, m, 0, 0);
        return {
          name: 'Shubuh',
          time: t
        };
      }
      return null;
    }

    // Fungsi untuk countdown
    let countdownInterval = null;

    function startCountdown(targetTime) {
      if (countdownInterval) {
        clearInterval(countdownInterval);
      }

      function update() {
        const now = new Date();
        const diff = targetTime - now;

        if (diff <= 0) {
          document.getElementById('countdown').textContent = 'Waktu Sholat';
          clearInterval(countdownInterval);
          return;
        }

        const h = String(Math.floor(diff / 3600000)).padStart(2, '0');
        const m = String(Math.floor((diff % 3600000) / 60000)).padStart(2, '0');
        const s = String(Math.floor((diff % 60000) / 1000)).padStart(2, '0');
        document.getElementById('countdown').textContent = `${h}:${m}:${s}`;
      }

      update();
      countdownInterval = setInterval(update, 1000);
    }

    // Event listener saat DOM selesai dimuat
    document.addEventListener('DOMContentLoaded', function() {
      tampilkanAyat();

      // Event listener untuk tombol reminder
      document.getElementById('btn-reminder').addEventListener('click', showReminderForm);
      document.querySelector('.close-btn').addEventListener('click', hideReminderForm);
      document.getElementById('save-reminder').addEventListener('click', saveReminderSettings);

      // Event listener untuk checkbox dan radio button
      document.querySelectorAll('input[type="checkbox"], input[type="radio"]').forEach(input => {
        input.addEventListener('change', function() {
          playButtonSound();
        });
      });

      // Event listener untuk input menit
      document.getElementById('reminder-minutes').addEventListener('input', updateAlarmMinutesDisplay);

      // Event listener untuk tombol ayat
      document.getElementById('next-ayat').addEventListener('click', function() {
        ayatIndex = (ayatIndex + 1) % ayatList.length;
        tampilkanAyat();
        playButtonSound();
      });

      document.getElementById('prev-ayat').addEventListener('click', function() {
        ayatIndex = (ayatIndex - 1 + ayatList.length) % ayatList.length;
        tampilkanAyat();
        playButtonSound();
      });

      // Event listener untuk play audio ayat
      let isPlaying = false;
      document.getElementById('play-audio-ayat').addEventListener('click', function() {
        const audio = document.getElementById('audio-ayat');

        if (isPlaying) {
          audio.pause();
          audio.currentTime = 0;
          document.getElementById('icon-audio-ayat').className = 'bi bi-volume-mute-fill';
          isPlaying = false;
        } else {
          audio.play().catch(e => console.error('Gagal memainkan audio:', e));
          document.getElementById('icon-audio-ayat').className = 'bi bi-volume-up-fill';
          isPlaying = true;
        }
      });

      // Event listener untuk select kota
      document.getElementById('select-kota').addEventListener('change', function() {
        playButtonSound();
        if (this.value) {
          const [lat, long] = this.value.split(',');
          tampilkanJadwal(lat, long);
        } else {
          if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(async (pos) => {
              tampilkanJadwal(pos.coords.latitude, pos.coords.longitude);
            }, () => {
              alert('Gagal mendeteksi lokasi. Mohon aktifkan GPS atau izinkan lokasi.');
            });
          }
        }
      });

      // Minta izin notifikasi saat pertama kali load
      if (Notification.permission !== 'granted' && Notification.permission !== 'denied') {
        Notification.requestPermission();
      }

      // Inisialisasi tampilan menit alarm
      updateAlarmMinutesDisplay();

      // Default: deteksi otomatis saat load
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(async (pos) => {
          tampilkanJadwal(pos.coords.latitude, pos.coords.longitude);
        }, () => {
          // Jika gagal, default ke Jakarta
          tampilkanJadwal(-6.2, 106.816666);
        });
      }
    });
  </script>
</body>

</html>