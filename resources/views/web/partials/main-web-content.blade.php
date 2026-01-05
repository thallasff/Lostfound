{{-- HERO --}}
<div class="text-center my-12">
    @auth
        <h2 class="text-3xl font-bold text-gray-800">
            Selamat Datang, <span class="font-semibold text-orange-600">
                {{ Auth::user()->username }} <span class="text-gray-800">di </span>Lost<span class="text-gray-800">Found</span>
            </span>!<br>
        </h2>
    @else
        <h2 class="text-3xl font-bold text-gray-800">
            Selamat Datang di <span class="font-semibold text-orange-600">Lost<span class="text-gray-800">Found</span></span>!
        </h2>
    @endauth

    <p class="text-gray-600 mt-3">
        Laporkan barang hilang di area kampus agar dapat membantu proses pencarian dan memudahkan pihak lain yang menemukannya untuk menghubungi Anda
    </p>
</div>

{{-- SEARCH BAR --}}
<div class="max-w-4xl mx-auto mb-6">
    <div class="relative">
        <input
            type="text"
            id="searchBarang"
            placeholder="Cari barang hilang..."
            class="w-full px-5 py-3 pr-12 rounded-xl shadow border border-gray-200
                   focus:outline-none focus:ring-2 focus:ring-orange-400"
        >

        <button
            type="button"
            id="btnCari"
            class="absolute right-3 top-1/2 -translate-y-1/2
                   w-9 h-9 rounded-lg bg-orange-500 hover:bg-orange-600
                   flex items-center justify-center"
            aria-label="Cari"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="m21 21-4.35-4.35m1.85-5.15a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" />
            </svg>
        </button>
    </div>
</div>

{{-- MAP --}}
<div id="map" class="w-full h-[450px] rounded-xl shadow mb-16"></div>

{{-- SPACE --}}
<div class="mt-16"></div>

{{-- APA ITU LOSTFOUND --}}
<div class="max-w-5xl mx-auto my-16 px-4">
    <div class="bg-[#e4dcd3] rounded-3xl shadow-lg p-10">
        <h3 class="text-3xl font-bold text-center text-gray-800 mb-8">
            Apa itu <span class="text-orange-600">Lost<span class="text-gray-800">Found</span></span>?
        </h3>

        <div class="bg-white rounded-2xl shadow-md p-8 max-w-3xl mx-auto">
            <p class="text-gray-700 text-lg leading-relaxed text-center text-sm">
                <strong>LostFound</strong> adalah platform pencarian dan pelaporan barang hilang
                di lingkungan kampus. Sistem ini membantu pelapor, penemu, dan pihak rektorat
                agar proses pengembalian barang menjadi lebih cepat, transparan,
                dan terverifikasi.
            </p>
        </div>
    </div>
</div>

{{-- SPACE --}}
<div class="mt-20"></div>

{{-- CARA KERJA --}}
<section class="mt-20">
    <div class="max-w-6xl mx-auto bg-[#e4dcd3] rounded-3xl shadow-lg p-10">

        <h2 class="text-3xl font-bold text-center mb-12">
            Bagaimana Cara Kerja
            <span class="font-semibold text-orange-600">
                Lost<span class="text-gray-800">Found</span>
            </span>?
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div data-aos="fade-up" class="bg-white rounded-2xl p-8 text-center shadow">
                <div class="w-12 h-12 mx-auto mb-4 bg-[#e4dcd3] rounded-full flex items-center justify-center">
                    ✏️
                </div>
                <p class="text-gray-700 text-sm">
                    Pelapor membuat laporan dengan mengisi data yang diperlukan pada fitur laporan barang.
                </p>
            </div>

            <div data-aos="fade-up" data-aos-delay="150" class="bg-white rounded-2xl p-8 text-center shadow">
                <div class="w-12 h-12 mx-auto mb-4 bg-[#e4dcd3] rounded-full flex items-center justify-center text-black">
                    🔍
                </div>
                <p class="text-gray-700 text-sm">
                    Setelah membuat laporan, pelapor dapat mencari dan melihat status barang.
                </p>
            </div>

            <div data-aos="fade-up" data-aos-delay="300" class="bg-white rounded-2xl p-8 text-center shadow">
                <div class="w-12 h-12 mx-auto mb-4 bg-[#e4dcd3] rounded-full flex items-center justify-center">
                    📍
                </div>
                <p class="text-gray-700 text-sm">
                    Barang ditampilkan pada peta beserta detail lokasi dan informasinya.
                </p>
            </div>
        </div>

        <div class="mt-10 flex flex-col md:flex-row justify-center gap-8">
            <div data-aos="fade-up" data-aos-delay="450"
                 class="bg-orange-400 text-white rounded-2xl p-8 text-center shadow w-full md:max-w-sm">
                <div class="w-12 h-12 mx-auto mb-4 bg-[#e4dcd3] rounded-full flex items-center justify-center text-black">
                    📦
                </div>
                <p class="text-white text-sm">
                    Penemu menyerahkan barang ke pihak rektorat untuk proses verifikasi.
                </p>
            </div>

            <div data-aos="fade-up" data-aos-delay="600"
                 class="bg-orange-400 rounded-2xl p-8 text-center shadow w-full md:max-w-sm">
                <div class="w-12 h-12 mx-auto mb-4 bg-[#e4dcd3] rounded-full flex items-center justify-center">
                    ✅
                </div>
                <p class="text-white text-sm">
                    Setelah diverifikasi, pemilik dapat mengambil barang dan status diperbarui.
                </p>
            </div>
        </div>

    </div>
</section>

{{-- KEUNGGULAN --}}
<section class="mt-24">
    <div class="max-w-7xl mx-auto px-6">
        <div data-aos="fade-up" class="bg-[#e4dcd3] rounded-3xl shadow-lg p-10">

            <h3 class="text-3xl font-bold text-center text-gray-800 mb-12">
                Kenapa <span class="font-semibold text-orange-600">
                    Lost<span class="text-gray-800">Found</span>
                </span>?
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div data-aos="fade-up" data-aos-delay="0"
                     class="bg-white rounded-2xl p-8 text-center shadow">
                    <div class="w-14 h-14 mx-auto mb-4 bg-[#e4dcd3] rounded-full flex items-center justify-center text-2xl">
                        🗺️
                    </div>
                    <h4 class="font-semibold text-lg mb-2">Berbasis Peta</h4>
                    <p class="text-gray-700 text-sm">
                        Lokasi barang hilang langsung ditampilkan di peta kampus
                        agar mudah ditemukan secara visual.
                    </p>
                </div>

                <div data-aos="fade-up" data-aos-delay="150"
                     class="bg-orange-400 text-white rounded-2xl p-8 text-center shadow">
                    <div class="w-14 h-14 mx-auto mb-4 bg-[#e4dcd3] rounded-full flex items-center justify-center text-2xl text-black">
                        ⚡
                    </div>
                    <h4 class="font-semibold text-lg mb-2">Real-Time Update</h4>
                    <p class="text-sm">
                        Setiap laporan dan perubahan status barang langsung
                        diperbarui tanpa perlu refresh.
                    </p>
                </div>

                <div data-aos="fade-up" data-aos-delay="300"
                     class="bg-white rounded-2xl p-8 text-center shadow">
                    <div class="w-14 h-14 mx-auto mb-4 bg-[#e4dcd3] rounded-full flex items-center justify-center text-2xl">
                        🔐
                    </div>
                    <h4 class="font-semibold text-lg mb-2">Aman & Terverifikasi</h4>
                    <p class="text-gray-700 text-sm">
                        Data laporan dan barang temuan diverifikasi oleh pihak kampus
                        untuk menjaga keamanan dan keakuratan.
                    </p>
                </div>
            </div>

        </div>
    </div>
</section>
