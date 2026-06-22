<div class="modal modal-blur fade" id="modalEditBooking" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Edit Jenis Tiket</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form id="formEditBooking">
        <input type="hidden" name="action" value="edit">
        <input type="hidden" id="edit_id_tiket" name="id">

        <div class="modal-body">
          <div class="row">
            <div class="col-lg-12">
              <div class="mb-3">
                <label class="form-label required">Event</label>
                <select class="form-select" id="editIdEvent" name="idEvent" required>
                  <option value="" selected disabled>Pilih event...</option>
                </select>
              </div>
            </div>
            <div class="col-lg-6">
              <div class="mb-3">
                <label class="form-label">Nama Tiket</label>
                <input type="text" class="form-control" id="editNamaTiket" name="namaTiket" placeholder="Contoh: Tiket Reguler, Tiket VIP">
              </div>
            </div>
            <div class="col-lg-6">
              <div class="mb-3">
                <label class="form-label required">Harga (Rp)</label>
                <input type="number" class="form-control" id="editHarga" name="harga" required min="0" step="0.01">
              </div>
            </div>
            <div class="col-lg-6">
              <div class="mb-3">
                <label class="form-label required">Kuota</label>
                <input type="number" class="form-control" id="editKuota" name="kuota" required min="0">
              </div>
            </div>
            <div class="col-lg-6">
              <div class="mb-3">
                <label class="form-label">Kuota Terjual</label>
                <input type="number" class="form-control" id="editKuotaTerjual" name="kuotaTerjual" min="0">
                <small class="form-hint">Otomatis bertambah saat ada booking. Bisa diisi manual jika perlu penyesuaian.</small>
              </div>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="button" class="btn btn-primary ms-auto" id="btnUpdateBooking">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
              <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
              <path d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2" />
              <circle cx="12" cy="14" r="2" />
              <polyline points="14 4 14 8 8 8 8 4" />
            </svg>
            Update Data
          </button>
        </div>
      </form>

    </div>
  </div>
</div>
