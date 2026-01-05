@extends('layouts.main-web')

@section('page')
    @include('web.partials.main-web-content')
@endsection

@push('scripts')
<script>
  // === BATAS KAMPUS (dari hasil map.getBounds()) ===
  const kampusBounds = L.latLngBounds(
    [-6.3404110108397465, 106.82589410523678], // SouthWest
    [-6.335612543515934,  106.83911203126215]  // NorthEast
  );

  // === CENTER (hasil geser buletan) ===
  const kampusCenter = L.latLng(-6.339099434482051, 106.83311461711585);

  // === RADIUS AREA (meter) ===
  const RADIUS_M = 230;

  // map dengan batasan awal (batas kasar kampus)
  const map = L.map('map', {
    maxBounds: kampusBounds,
    maxBoundsViscosity: 1.0,
  }).setView(kampusCenter, 17);

  // tile
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxNativeZoom: 19,
    maxZoom: 22,
    minZoom: 16,
  }).addTo(map);

  map.setMaxZoom(22);

  // ===== 1) CIRCLE (SATU AJA) =====
  const radiusCircle = L.circle(kampusCenter, {
    radius: RADIUS_M,
    color: '#3b82f6',
    weight: 2,
    fillColor: '#3b82f6',
    fillOpacity: 0.10,
  }).addTo(map);

  // ===== 2) ZOOM OUT MENTOK DI AREA CIRCLE =====
  const circleBounds = radiusCircle.getBounds();

  map.fitBounds(circleBounds, { padding: [10, 10] });
  map.setView(kampusCenter, 21);

  map.setMinZoom(21);
  map.setZoom(map.getZoom() + 1);

  const minZoomCircle = map.getZoom();
  map.setMinZoom(minZoomCircle);

  map.setMaxBounds(circleBounds);

  // ===== 3) GELAPIN AREA LUAR CIRCLE (MASK HOLE) =====
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

  const outer = [
    [-90, -180],
    [-90,  180],
    [ 90,  180],
    [ 90, -180],
  ];

  const inner = circleToLatLngs(kampusCenter, RADIUS_M, 140).reverse();

  const mask = L.polygon([outer, inner], {
    stroke: false,
    fillColor: '#000',
    fillOpacity: 0.35,
    interactive: false,
  }).addTo(map);

  mask.bringToBack();

  // ===== 4) CLAMP: CENTER MAP GAK BOLEH KELUAR LINGKARAN =====
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
// ===== DATA (HILANG + TEMUAN)
// =========================
const lostRaw  = @json($barang ?? $lost ?? []);
const foundRaw = @json($found ?? []);
console.log('lostRaw[0]=', lostRaw?.[0]);
console.log('foundRaw[0]=', foundRaw?.[0]);
const IS_AUTH = @json(auth()->check());

function pickId(x, keys) {
  for (const k of keys) {
    const v = x?.[k];
    if (v === undefined || v === null) continue;

    const s = String(v).trim().toLowerCase();
    if (!s || s === 'undefined' || s === 'null') continue;

    return v;
  }
  return null;
}

const items = [
  ...lostRaw.map(x => ({
    _type: 'hilang',
    id: pickId(x, ['barang_id', 'id', 'id_barang']), // ✅ fallback
    nama_barang: x.nama_barang,
    lokasi_gedung: x.lokasi_gedung ?? x.lokasi ?? null,
    latitude: x.latitude,
    longitude: x.longitude,
    foto: x.foto_barang_1 ?? null,
    tanggal: x.tanggal_hilang ?? null,
    waktu: x.waktu_hilang ?? null,
  })),
  ...foundRaw.map(x => {
    const raw = x.waktu_ditemukan ? String(x.waktu_ditemukan).replace('T', ' ') : '';
    return {
      _type: 'temuan',
      id: pickId(x, ['penemuan_id', 'id', 'id_penemuan']), // ✅ fallback
      nama_barang: x.nama_barang,
      lokasi_gedung: x.lokasi_gedung ?? x.lokasi ?? null,
      latitude: x.latitude,
      longitude: x.longitude,
      foto: x.foto_barang_1 ?? null,
      tanggal: raw ? raw.slice(0, 10) : null,
      waktu: raw ? raw.slice(11, 16) : null,
    };
  }),
];

const PLACEHOLDER_IMG =
  `data:image/svg+xml;charset=UTF-8,${encodeURIComponent(`
    <svg xmlns="http://www.w3.org/2000/svg" width="200" height="120">
      <rect width="100%" height="100%" fill="#f3f4f6"/>
      <text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle"
            fill="#9ca3af" font-family="Arial" font-size="14">
        No Image
      </text>
    </svg>
  `)}`;

function getFotoUrl(item) {
  return item.foto ? `/storage/${item.foto}` : PLACEHOLDER_IMG;
}
function fmtDate(d) {
  return d ? String(d).slice(0, 10) : '-';
}
function fmtTime(t) {
  return t ? String(t).slice(0, 5) : '-';
}

const markers = [];

items.forEach(item => {
  if (item.latitude == null || item.longitude == null) return;

  const foto = getFotoUrl(item);

  const pinClass = (item._type === 'temuan')
    ? 'item-pin item-pin--temuan'
    : 'item-pin item-pin--hilang';

  const customIcon = L.divIcon({
    className: 'item-pin-icon',
    html: `
      <div class="${pinClass}">
        <div class="bubble">
          <img src="${foto}" alt="foto barang"
               onerror="this.onerror=null;this.src='${PLACEHOLDER_IMG}'">
        </div>
        <div class="pointer"></div>
      </div>
    `,
    iconSize: [54, 68],
    iconAnchor: [27, 68],
    popupAnchor: [0, -70],
  });

const hasId = item.id !== null && item.id !== undefined && item.id !== '';

if (!hasId) {
  console.warn('ID TIDAK VALID:', item._type, item.id, item);
}

const chatLink  = hasId ? `/chat/start/${item._type}/${item.id}` : null;
const claimLink = hasId ? `/chat/claim/${item._type}/${item.id}` : null;

const lokasiGedung = item.lokasi_gedung ?? '-';
const tanggal = fmtDate(item.tanggal);
const waktu = fmtTime(item.waktu);

const ctaText = (item._type === 'temuan')
  ? 'Ini barang saya'
  : 'Saya menemukan barang ini';

const popupHtml = `
  <div class="w-[220px]">
    <img src="${foto}" class="w-full h-28 object-cover rounded-lg mb-2"
         onerror="this.onerror=null;this.src='${PLACEHOLDER_IMG}'" />
    <strong>${item.nama_barang ?? '-'}</strong><br>
    Lokasi: ${lokasiGedung}<br>
    Tanggal: ${tanggal}<br>
    Waktu: ${waktu}<br>

    <div class="mt-2 flex gap-2">
      ${
        chatLink
          ? `
            <a href="${chatLink}"
               class="px-3 py-1 rounded-lg bg-orange-500 text-white text-xs font-bold hover:bg-orange-600">
              Chat
            </a>
            <a href="${claimLink}"
               class="px-3 py-1 rounded-lg border border-orange-400 text-orange-600 text-xs font-bold hover:bg-orange-50">
              ${ctaText}
            </a>
          `
          : `<span class="text-xs text-red-500">Chat tidak tersedia (ID kosong)</span>`
      }
    </div>
  </div>
`;


  const marker = L.marker([Number(item.latitude), Number(item.longitude)], { icon: customIcon })
    .addTo(map)
    .bindPopup(popupHtml);

  markers.push({ marker, item });
});

  function applySearchFilter(q) {
    q = (q || '').trim().toLowerCase();

    markers.forEach(({ marker }) => marker.remove());

    if (!q) {
      markers.forEach(({ marker }) => marker.addTo(map));
      map.setView(kampusCenter, 17);
      return;
    }

    const matched = [];

    markers.forEach(({ marker, item }) => {
      const text = `
        ${item.nama_barang ?? ''}
        ${item.lokasi_gedung ?? ''}
        ${item.deskripsi ?? ''}
        ${item.deskripsi_singkat ?? ''}
        ${item.kategori ?? ''}
      `.toLowerCase();

      if (text.includes(q)) {
        marker.addTo(map);
        matched.push(marker);
      }
    });

    if (matched.length) {
      const group = L.featureGroup(matched);
      map.fitBounds(group.getBounds().pad(0.25), { maxZoom: 19 });
      map.panInsideBounds(circleBounds, { animate: true });
    }
  }

  function attachSearch() {
    const searchInput = document.getElementById('searchBarang');
    const btnCari = document.getElementById('btnCari');
    if (!searchInput || !btnCari) return;

    btnCari.addEventListener('click', () => applySearchFilter(searchInput.value));
    searchInput.addEventListener('keydown', (e) => {
      if (e.key === 'Enter') {
        e.preventDefault();
        applySearchFilter(searchInput.value);
      }
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', attachSearch);
  } else {
    attachSearch();
  }
</script>
@endpush
