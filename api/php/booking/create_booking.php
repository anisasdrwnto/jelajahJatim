<div class="modal modal-blur fade" id="modalAddBooking" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Tambah Jenis Tiket</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form id="formAddBooking">
        <input type="hidden" name="action" value="tambah">

        <div class="modal-body">
          <div class="row">
            <div class="col-lg-12">
              <div class="mb-3">
                <label class="form-label required">Event</label>
                <select class="form-select" name="idEvent" id="idEvent" required>
                  <option value="" selected disabled>Pilih event...</option>
                </select>
              </div>
            </div>
            <div class="col-lg-6">
              <div class="mb-3">
                <label class="form-label">Nama Tiket</label>
                <input type="text" class="form-control" name="namaTiket" id="namaTiket" placeholder="Contoh: Tiket Reguler, Tiket VIP">
              </div>
            </div>
            <div class="col-lg-6">
              <div class="mb-3">
                <label class="form-label required">Harga (Rp)</label>
                <input type="number" class="form-control" name="harga" id="harga" required min="0" step="0.01" placeholder="Contoh: 50000">
              </div>
            </div>
            <div class="col-lg-6">
              <div class="mb-3">
                <label class="form-label required">Kuota</label>
                <input type="number" class="form-control" name="kuota" id="kuota" required min="0" placeholder="Contoh: 100">
              </div>
            </div>
            <div class="col-lg-6">
              <div class="mb-3">
                <label class="form-label">Kuota Terjual</label>
                <input type="number" class="form-control" name="kuotaTerjual" id="kuotaTerjual" min="0" value="0">
                <small class="form-hint">Otomatis bertambah saat ada booking. Bisa diisi manual jika perlu penyesuaian.</small>
              </div>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btnCancelBooking">
            Batal
          </button>
          <button type="button" class="btn btn-primary ms-auto" id="btnSaveBooking">
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
