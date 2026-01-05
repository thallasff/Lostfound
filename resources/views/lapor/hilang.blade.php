@extends('lapor.partials.lapor-content')

@section('lapor-title', 'Laporkan Barang Hilang')

@section('lapor-body')
@php
  $u = Auth::user();
  $username = $u->username ?? $u->name ?? '-';
@endphp

<form method="POST" action="{{ route('lost.store') }}" enctype="multipart/form-data" class="space-y-5">
  @csrf

  {{-- Username auto --}}
  <div>
    <label class="block text-center font-extrabold mb-2">Username Pelapor</label>
    <input type="text" value="{{ $username }}" readonly
           class="w-full bg-white border-2 border-orange-400 rounded-md px-3 py-2 opacity-90">
  </div>

  {{-- 1) Informasi Barang --}}
  <div>
    <label class="block text-center font-extrabold mb-2">Nama Barang</label>
    <input type="text" name="nama_barang" value="{{ old('nama_barang') }}"
           class="w-full bg-white border-2 border-orange-400 rounded-md px-3 py-2"
           placeholder="Contoh: Dompet, HP, Tumbler" required>
  </div>

  <div>
    <label class="block text-center font-extrabold mb-2">Kategori Barang</label>
    <select name="kategori"
            class="w-full bg-white border-2 border-orange-400 rounded-md px-3 py-2" required>
      <option value="" disabled {{ old('kategori') ? '' : 'selected' }}>Pilih kategori</option>
      <option value="Elektronik" {{ old('kategori')=='Elektronik'?'selected':'' }}>Elektronik</option>
      <option value="Barang Pribadi" {{ old('kategori')=='Barang Pribadi'?'selected':'' }}>Barang Pribadi</option>
      <option value="Tas" {{ old('kategori')=='Tas'?'selected':'' }}>Tas</option>
      <option value="Aksesoris" {{ old('kategori')=='Aksesoris'?'selected':'' }}>Aksesoris</option>
      <option value="Dokumen" {{ old('kategori')=='Dokumen'?'selected':'' }}>Dokumen</option>
      <option value="Lainnya" {{ old('kategori')=='Lainnya'?'selected':'' }}>Lainnya</option>
    </select>
  </div>

  <div>
    <label class="block text-center font-extrabold mb-2">Deskripsi Singkat</label>
    <textarea name="deskripsi_singkat" rows="3"
              class="w-full bg-white border-2 border-orange-400 rounded-md px-3 py-2"
              placeholder="Warna, merek, ciri khas, isi (jika ada)">{{ old('deskripsi_singkat') }}</textarea>
  </div>

  {{-- Foto barang (opsional 1-3) --}}
  <div class="flex justify-center">
    <input id="foto_barang_multi_lost" type="file" name="foto_barang[]" class="hidden" multiple accept="image/*">
    <label for="foto_barang_multi_lost"
           class="cursor-pointer w-64 bg-white border-2 border-orange-400 rounded-lg px-4 py-2
                  flex items-center justify-center gap-2 shadow-sm">
      <svg class="w-5 h-5 text-orange-500" fill="currentColor" viewBox="0 0 24 24">
        <path d="M21 19V5a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2ZM8.5 13.5l2.5 3 3.5-4.5 4.5 6H5l3.5-4.5Z"/>
      </svg>
      <span class="font-semibold">Foto Barang (Opsional 1–3)</span>
    </label>
  </div>
  <p id="fotoInfoLost" class="text-center text-sm text-gray-600">Belum ada foto dipilih</p>

  {{-- 2) Detail Tambahan (opsional) --}}
  <div class="grid grid-cols-1 gap-3">
    <div>
      <label class="block text-center font-extrabold mb-2">Warna (opsional)</label>
      <input type="text" name="warna" value="{{ old('warna') }}"
             class="w-full bg-white border-2 border-orange-400 rounded-md px-3 py-2"
             placeholder="Contoh: Hitam">
    </div>

    <div>
      <label class="block text-center font-extrabold mb-2">Merek / Brand (opsional)</label>
      <input type="text" name="merek" value="{{ old('merek') }}"
             class="w-full bg-white border-2 border-orange-400 rounded-md px-3 py-2"
             placeholder="Contoh: Samsung">
    </div>

    <div>
      <label class="block text-center font-extrabold mb-2">Kondisi Terakhir (opsional)</label>
      <select name="kondisi_terakhir"
              class="w-full bg-white border-2 border-orange-400 rounded-md px-3 py-2">
        <option value="" {{ old('kondisi_terakhir') ? '' : 'selected' }}>Pilih kondisi</option>
        <option value="baik" {{ old('kondisi_terakhir')=='baik'?'selected':'' }}>Baik</option>
        <option value="rusak" {{ old('kondisi_terakhir')=='rusak'?'selected':'' }}>Rusak</option>
      </select>
    </div>
  </div>

  {{-- Lokasi gedung --}}
  <div>
    <label class="block text-center font-extrabold mb-2">Lokasi (Gedung / Area)</label>
    <input type="text" name="lokasi_gedung" value="{{ old('lokasi_gedung') }}"
           class="w-full bg-white border-2 border-orange-400 rounded-md px-3 py-2"
           placeholder="Contoh: Ruang kelas FT204, Perpustakaan, Kantin Rektorat" required>
  </div>

  {{-- 3) Lokasi terakhir terlihat (map) --}}
  <div>
    <label class="block text-center font-extrabold mb-2">Lokasi Terakhir Terlihat</label>

    <div class="flex items-center justify-center gap-3 mb-2">
      <button type="button" id="btnGpsLost"
              class="text-xs px-3 py-1 rounded-full border border-orange-400 bg-white hover:bg-orange-50 font-semibold">
        Pakai lokasi saya (GPS)
      </button>
      <span class="text-xs text-gray-500">atau</span>
      <span class="text-xs font-semibold text-gray-700">klik peta</span>
    </div>

    <div id="map"
         class="w-full rounded-xl border-2 border-orange-400 overflow-hidden"
         style="height: 220px;"></div>

    <div class="mt-2 text-center text-sm">
      <span class="font-semibold">Lat:</span> <span id="latText">-</span>
      <span class="mx-2">|</span>
      <span class="font-semibold">Long:</span> <span id="lngText">-</span>
    </div>

    <input type="hidden" name="latitude" id="latInput" value="{{ old('latitude') }}">
    <input type="hidden" name="longitude" id="lngInput" value="{{ old('longitude') }}">
  </div>

  {{-- 4) Perkiraan waktu hilang (dipisah) --}}
  <div>
    <label class="block text-center font-extrabold mb-2">Perkiraan Terakhir Terlihat</label>

    <div class="grid grid-cols-1 gap-3">
      <div>
        <label class="block text-center font-semibold mb-2">Tanggal</label>
        <input type="date" name="tanggal_hilang" value="{{ old('tanggal_hilang') }}"
               class="block w-full bg-white border-2 border-orange-400 rounded-md px-3 py-2" required>
      </div>

      <div>
        <label class="block text-center font-semibold mb-2">Waktu</label>
        <input type="time" name="waktu_hilang" value="{{ old('waktu_hilang') }}"
               class="block w-full bg-white border-2 border-orange-400 rounded-md px-3 py-2" required>
      </div>
    </div>

    <p class="text-center text-xs text-gray-600 mt-1">Isi sesuai perkiraan terakhir barang terlihat.</p>
  </div>

  {{-- 5) Catatan tambahan --}}
  <div>
    <label class="block text-center font-extrabold mb-2">Catatan Tambahan (opsional)</label>
    <textarea name="catatan_tambahan" rows="3"
              class="w-full bg-white border-2 border-orange-400 rounded-md px-3 py-2"
              placeholder="Contoh: Kemungkinan tertinggal di kelas setelah kuliah jam 10">{{ old('catatan_tambahan') }}</textarea>
  </div>

  <div class="pt-2 flex justify-center">
    <button type="submit"
            class="w-40 bg-orange-500 hover:bg-orange-600 text-white font-extrabold py-2 rounded-lg shadow">
      Laporkan
    </button>
  </div>
</form>

{{-- =========================
     LOSTFOUND MODAL (ALERT CANTIK)
========================== --}}
<div id="lfModal" class="fixed inset-0 z-[9999] hidden items-center justify-center">
  <div id="lfModalBackdrop" class="absolute inset-0 bg-black/50"></div>

  <div class="relative w-[92%] max-w-md rounded-2xl bg-white shadow-2xl border border-gray-200 overflow-hidden">
    <div id="lfModalHeader" class="px-5 py-4 bg-orange-500 text-white">
      <div class="flex items-center justify-between">
        <div class="font-extrabold tracking-wide">LostFound</div>
        <button type="button" id="lfModalCloseX"
                class="h-8 w-8 rounded-full bg-white/20 hover:bg-white/30 flex items-center justify-center">✕</button>
      </div>
      <div id="lfModalTitle" class="mt-1 text-sm font-semibold opacity-95"></div>
    </div>

    <div class="px-5 py-4">
      <p id="lfModalMessage" class="text-sm text-gray-700 leading-relaxed"></p>
      <div class="mt-5 flex justify-end gap-2">
        <button type="button" id="lfModalOk"
                class="px-4 py-2 rounded-lg bg-orange-500 hover:bg-orange-600 text-white font-bold">
          OK
        </button>
      </div>
    </div>
  </div>
</div>

<script>
  // modal helper
  function showLFModal(title, message = '', type = 'warn') {
    const modal = document.getElementById('lfModal');
    const backdrop = document.getElementById('lfModalBackdrop');
    const closeX = document.getElementById('lfModalCloseX');
    const okBtn = document.getElementById('lfModalOk');
    const t = document.getElementById('lfModalTitle');
    const m = document.getElementById('lfModalMessage');
    const header = document.getElementById('lfModalHeader');

    const mapType = {
      success: 'bg-green-600',
      error: 'bg-red-600',
      warn: 'bg-orange-500',
      info: 'bg-blue-600',
    };
    header.className = `px-5 py-4 text-white ${mapType[type] ?? mapType.warn}`;

    t.textContent = title || '';
    m.textContent = message || '';

    modal.classList.remove('hidden');
    modal.classList.add('flex');

    function close() {
      modal.classList.add('hidden');
      modal.classList.remove('flex');
      backdrop.removeEventListener('click', close);
      closeX.removeEventListener('click', close);
      okBtn.removeEventListener('click', close);
      document.removeEventListener('keydown', escClose);
    }
    function escClose(e) { if (e.key === 'Escape') close(); }

    backdrop.addEventListener('click', close);
    closeX.addEventListener('click', close);
    okBtn.addEventListener('click', close);
    document.addEventListener('keydown', escClose);
  }

  // max 3 foto (opsional)
  const fotoInput = document.getElementById('foto_barang_multi_lost');
  const fotoInfo = document.getElementById('fotoInfoLost');

  fotoInput.addEventListener('change', function () {
    const files = Array.from(this.files || []);
    if (files.length > 3) {
      showLFModal('Terlalu banyak foto', 'Maksimal 3 foto.', 'warn');
      this.value = '';
      fotoInfo.textContent = 'Belum ada foto dipilih';
      return;
    }
    fotoInfo.textContent = files.length ? `${files.length} foto dipilih` : 'Belum ada foto dipilih';
  });
</script>

{{-- Leaflet --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
  // =========================
  // MAP LOCK AREA KAMPUS + ZOOM OUT + GPS
  // =========================

  const kampusBounds = L.latLngBounds(
    [-6.3404110108397465, 106.82589410523678],
    [-6.335612543515934,  106.83911203126215]
  );

  const kampusCenter = L.latLng(-6.339099434482051, 106.83311461711585);
  const RADIUS_M = 230;

  // bisa zoom-out dikit (ubah kalau mau lebih luas)
  const MIN_ZOOM = 19;
  const MAX_ZOOM = 22;

  const map = L.map('map', {
    maxBounds: kampusBounds,
    maxBoundsViscosity: 1.0,
  }).setView(kampusCenter, 21);

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxNativeZoom: 19,
    maxZoom: MAX_ZOOM,
    minZoom: 16,
    attribution: '&copy; OpenStreetMap'
  }).addTo(map);

  map.setMaxZoom(MAX_ZOOM);

  const radiusCircle = L.circle(kampusCenter, {
    radius: RADIUS_M,
    color: '#3b82f6',
    weight: 2,
    fillColor: '#3b82f6',
    fillOpacity: 0.10,
  }).addTo(map);

  const circleBounds = radiusCircle.getBounds();
  map.fitBounds(circleBounds, { padding: [10, 10] });

  // zoom-out mentok di MIN_ZOOM
  map.setMinZoom(MIN_ZOOM);

  // lock pan di bounds circle (batas kasar)
  map.setMaxBounds(circleBounds);

  // ===== gelapin area luar circle =====
  function destinationPoint(lat, lng, brngDeg, distM) {
    const R = 6378137;
    const brng = brngDeg * Math.PI / 180;
    const lat1 = lat * Math.PI / 180;
    const lng1 = lng * Math.PI / 180;

    const lat2 = Math.asin(
      Math.sin(lat1) * Math.cos(distM / R) +
      Math.cos(lat1) * Math.sin(distM / R) * Math.cos(brng)
    );

    const lng2 = lng1 + Math.atan2(
      Math.sin(brng) * Math.sin(distM / R) * Math.cos(lat1),
      Math.cos(distM / R) - Math.sin(lat1) * Math.sin(lat2)
    );

    return [lat2 * 180 / Math.PI, lng2 * 180 / Math.PI];
  }

  function circleToLatLngs(center, radiusM, steps = 120) {
    const pts = [];
    for (let i = 0; i < steps; i++) {
      const ang = (i * 360) / steps;
      pts.push(destinationPoint(center.lat, center.lng, ang, radiusM));
    }
    return pts;
  }

  const outer = [[-90,-180],[-90,180],[90,180],[90,-180]];
  const inner = circleToLatLngs(kampusCenter, RADIUS_M, 140).reverse();

  const mask = L.polygon([outer, inner], {
    stroke: false,
    fillColor: '#000',
    fillOpacity: 0.35,
    interactive: false,
  }).addTo(map);

  mask.bringToBack();

  // clamp center map biar gak keluar lingkaran
  function clampCenterToCircle() {
    const center = map.getCenter();
    const dist = center.distanceTo(kampusCenter);
    if (dist <= RADIUS_M) return;

    const toRad = (x) => x * Math.PI / 180;
    const toDeg = (x) => x * 180 / Math.PI;

    const lat1 = toRad(kampusCenter.lat), lat2 = toRad(center.lat);
    const dLng = toRad(center.lng - kampusCenter.lng);
    const y = Math.sin(dLng) * Math.cos(lat2);
    const x = Math.cos(lat1) * Math.sin(lat2) - Math.sin(lat1) * Math.cos(lat2) * Math.cos(dLng);
    const bearing = (toDeg(Math.atan2(y, x)) + 360) % 360;

    const edge = destinationPoint(kampusCenter.lat, kampusCenter.lng, bearing, RADIUS_M - 1);
    map.panTo(edge, { animate: false });
  }

  map.on('move', clampCenterToCircle);
  map.on('zoom', clampCenterToCircle);

  // =========================
  // SET LAT/LONG + MARKER
  // =========================
  let marker = null;

  function setLatLng(lat, lng) {
    document.getElementById('latInput').value = lat;
    document.getElementById('lngInput').value = lng;
    document.getElementById('latText').textContent = lat.toFixed(6);
    document.getElementById('lngText').textContent = lng.toFixed(6);
  }

  function placeMarker(lat, lng) {
    if (marker) marker.setLatLng([lat, lng]);
    else marker = L.marker([lat, lng]).addTo(map);
    setLatLng(lat, lng);
  }

  function isInsideCampus(lat, lng) {
    const p = L.latLng(lat, lng);
    return p.distanceTo(kampusCenter) <= RADIUS_M;
  }

  // restore old
  const oldLat = parseFloat(document.getElementById('latInput').value);
  const oldLng = parseFloat(document.getElementById('lngInput').value);
  if (!isNaN(oldLat) && !isNaN(oldLng)) {
    placeMarker(oldLat, oldLng);
    map.setView([oldLat, oldLng], Math.max(MIN_ZOOM, 21));
  }

  // klik peta
  map.on('click', function(e) {
    const lat = e.latlng.lat;
    const lng = e.latlng.lng;

    if (!isInsideCampus(lat, lng)) {
      showLFModal('Lokasi tidak valid', 'Lokasi harus di dalam area kampus (lingkaran biru).', 'warn');
      return;
    }
    placeMarker(lat, lng);
  });

  // GPS button
  const btnGps = document.getElementById('btnGpsLost');
  if (btnGps) {
    btnGps.addEventListener('click', function() {
      if (!navigator.geolocation) {
        showLFModal('GPS tidak tersedia', 'Browser kamu tidak mendukung GPS.', 'error');
        return;
      }

      btnGps.disabled = true;
      const oldText = btnGps.textContent;
      btnGps.textContent = 'Mendeteksi...';

      navigator.geolocation.getCurrentPosition(
        (pos) => {
          const lat = pos.coords.latitude;
          const lng = pos.coords.longitude;

          if (!isInsideCampus(lat, lng)) {
            showLFModal(
              'Lokasi di luar area kampus',
              'Lokasi kamu terdeteksi di luar area kampus. Silakan klik peta manual di dalam lingkaran biru.',
              'warn'
            );
            return;
          }

          placeMarker(lat, lng);
          map.setView([lat, lng], Math.max(MIN_ZOOM, 21));
        },
        () => {
          showLFModal('Gagal mengambil lokasi', 'Pastikan izin lokasi diaktifkan, lalu coba lagi.', 'error');
        },
        { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
      );

      setTimeout(() => {
        btnGps.disabled = false;
        btnGps.textContent = oldText;
      }, 1200);
    });
  }
</script>
@endsection
