<div class="modal modal-blur fade" id="modalAddEvent" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Tambah Event Wisata Baru</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form id="formAddEvent">
        <input type="hidden" name="action" value="tambah">

        <div class="modal-body">
          <div class="row">
            <div class="col-lg-6">
              <div class="mb-3">
                <label class="form-label required">Nama Event</label>
                <input type="text" class="form-control" name="namaEvent" id="namaEvent" required placeholder="Contoh: Festival Jazz Bromo">
              </div>
            </div>
            <div class="col-lg-6">
              <div class="mb-3">
                <label class="form-label required">Kategori</label>
                <input type="text" class="form-control" name="kategori" id="kategori" required placeholder="Contoh: Festival, Konser, Budaya">
              </div>
            </div>
            <div class="col-lg-4">
              <div class="mb-3">
                <label class="form-label required">Tanggal Event</label>
                <input type="date" class="form-control" name="tanggalEvent" id="tanggalEvent" required>
              </div>
            </div>
            <div class="col-lg-4">
              <div class="mb-3">
                <label class="form-label required">Waktu Mulai</label>
                <input type="time" class="form-control" name="waktuMulai" id="waktuMulai" required>
              </div>
            </div>
            <div class="col-lg-4">
              <div class="mb-3">
                <label class="form-label required">Waktu Selesai</label>
                <input type="time" class="form-control" name="waktuSelesai" id="waktuSelesai" required>
              </div>
            </div>
            <div class="col-lg-6">
              <div class="mb-3">
                <label class="form-label required">Lokasi</label>
                <input type="text" class="form-control" name="lokasi" id="lokasi" required placeholder="Contoh: Kawasan Gunung Bromo">
              </div>
            </div>
            <div class="col-lg-6">
              <div class="mb-3">
                <label class="form-label required">Status</label>
                <select class="form-select" name="status" id="status" required>
                  <option value="Aktif" selected>Aktif</option>
                  <option value="Selesai">Selesai</option>
                  <option value="Batal">Batal</option>
                </select>
              </div>
            </div>
            <div class="col-lg-12">
              <div class="mb-3">
                <label class="form-label">Deskripsi</label>
                <textarea class="form-control" name="deskripsi" id="deskripsi" rows="3" placeholder="Jelaskan detail acara, daya tarik, atau ketentuan event ini..."></textarea>
              </div>
            </div>
            <div class="col-lg-12">
              <div class="mb-3">
                <label class="form-label required">Upload Foto</label>
                <input type="file" class="form-control" name="foto" accept="image/*" required id="foto">
                <small class="form-hint">Format yang diizinkan: JPG, JPEG, PNG. Maksimal ukuran 2MB.</small>
              </div>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btnCancelEvent">
            Batal
          </button>
          <button type="button" class="btn btn-primary ms-auto" id="btnSaveEvent">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
              <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
              <path d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2" />
              <circle cx="12" cy="14" r="2" />
              <polyline points="14 4 14 8 8 8 8 4" />
            </svg>
            Simpan Data
          </button>
        </div>
      </form>

    </div>
  </div>
</div>
