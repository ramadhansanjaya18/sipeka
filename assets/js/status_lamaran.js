/**
 * Script khusus untuk halaman Status Lamaran
 * Menangani Modal Popup untuk detail jadwal wawancara.
 */

document.addEventListener("DOMContentLoaded", function () {
  const modal = document.getElementById("modalJadwal");
  const closeBtn = document.querySelector(".close-modal");
  const btnCloseBottom = document.querySelector(".btn-close-modal");
  const jadwalButtons = document.querySelectorAll(".btn-jadwal");

  // Event Listener untuk tombol "Lihat Jadwal"
  jadwalButtons.forEach((btn) => {
    btn.addEventListener("click", function () {
      // Ambil data dari atribut data-*
      const idLamaran = this.dataset.id;
      const nama = this.dataset.nama;
      const posisi = this.dataset.posisi;
      const status = this.dataset.status;
      const tanggal = this.dataset.tanggal;
      const jam = this.dataset.jam;
      const lokasi = this.dataset.lokasi;
      const catatan = this.dataset.catatan;
      const belum = this.dataset.belum;

      // Set Data Utama
      const inputPelamar = document.getElementById("modalPelamar");
      const inputPosisi = document.getElementById("modalPosisi");
      const inputStatus = document.getElementById("modalStatus");

      if (inputPelamar) inputPelamar.value = `${nama} (ID:${idLamaran})`;
      if (inputPosisi) inputPosisi.value = posisi;
      if (inputStatus) inputStatus.value = status;

      // Logika Tampilan Berdasarkan Status Jadwal
      const elTanggal = document.getElementById("modalTanggal");
      const elJam = document.getElementById("modalJam");
      const elLokasi = document.getElementById("modalLokasi");
      const elCatatan = document.getElementById("modalCatatan");

      if (belum === "1") {
        // Jika belum dijadwalkan
        if (elTanggal) elTanggal.value = "-";
        if (elJam) elJam.value = "-";
        if (elLokasi) elLokasi.value = "-";
        if (elCatatan)
          elCatatan.value =
            "Belum ada jadwal wawancara yang ditetapkan. Tunggu informasi selanjutnya.";
      } else {
        // Jika sudah ada jadwal
        if (elTanggal) elTanggal.value = tanggal;
        if (elJam) elJam.value = jam;
        if (elLokasi) elLokasi.value = lokasi;
        if (elCatatan) elCatatan.value = catatan;
      }

      // Tampilkan Modal
      if (modal) modal.style.display = "flex";
    });
  });

  // Fungsi Tutup Modal
  function closeModal() {
    if (modal) modal.style.display = "none";
  }

  // Event Listener Tutup Modal
  if (closeBtn) closeBtn.addEventListener("click", closeModal);
  if (btnCloseBottom) btnCloseBottom.addEventListener("click", closeModal);

  // Tutup modal jika klik di luar area konten
  window.addEventListener("click", function (e) {
    if (e.target == modal) closeModal();
  });
});
