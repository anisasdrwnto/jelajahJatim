<div class="modal modal-blur fade" id="modalEditDestinasi" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Edit Destinasi Wisata</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form id="formEditDestinasi">
        <input type="hidden" name="action" value="edit">
        <input type="hidden" id="edit_id_destinasi"  name="id">
        <input type="hidden" id="editFotoLama"        name="fotoLama"> <!-- simpan nama foto lama -->

        <div class="modal-body">
          <div class="row">
            <div class="col-lg-6">
              <div class="mb-3">
                <label class="form-label required">Nama Destinasi</label>
                <input type="text" class="form-control" id="editNamaDestinasi" name="namaDestinasi" required>
              </div>
            </div>
            <div class="col-lg-6">
              <div class="mb-3">
                <label class="form-label required">Kabupaten/Kota</label>
                <select class="form-select" id="editKabupatenKota" name="kabupatenKota" required>
                  <option value="" disabled selected>Memuat data...</option>
                </select>
              </div>
            </div>
            <div class="col-lg-6">
              <div class="mb-3">
                <label class="form-label">Wilayah</label>
                <input type="text" class="form-control" id="editWilayah" name="wilayah" disabled>
              </div>
            </div>
            <div class="col-lg-6">
              <div class="mb-3">
                <label class="form-label required">Kategori</label>
                <select class="form-select" id="editKategori" name="kategori" required>
                  <option value="" disabled>Pilih Kategori...</option>
                  <option value="Alam">Wisata Alam</option>
                  <option value="Budaya">Wisata Budaya</option>
                  <option value="Buatan">Wisata Buatan</option>
                  <option value="Kuliner">Wisata Kuliner</option>
                </select>
              </div>
            </div>
            <div class="col-lg-6">
              <div class="mb-3">
                <label class="form-label required">Status</label>
                <select class="form-select" id="editStatus" name="status" required>
                  <option value="Aktif">Aktif / Buka</option>
                  <option value="Nonaktif">Nonaktif / Tutup</option>
                  <option value="Perbaikan">Dalam Perbaikan</option>
                </select>
              </div>
            </div>
            <div class="col-lg-12">
              <div class="mb-3">
                <label class="form-label required">Alamat Lengkap</label>
                <textarea class="form-control" id="editAlamat_lengkap" name="alamatLengkap" rows="2" required></textarea>
              </div>
            </div>
            <div class="col-lg-12">
              <div class="mb-3">
                <label class="form-label">Deskripsi</label>
                <textarea class="form-control" id="editDeskripsi" name="deskripsi" rows="3"></textarea>
              </div>
            </div>
            <div class="col-lg-12">
              <div class="mb-3">
                <label class="form-label">Ganti Foto <small class="text-muted">(kosongkan jika tidak ingin mengganti)</small></label>
                <!-- Preview foto lama -->
                <img id="editPreviewFoto" src="" alt="Preview Foto" 
                     style="display:none; max-height:120px; border-radius:6px; margin-bottom:8px; display:block;">
                <input type="file" class="form-control" id="editFoto" name="foto" accept="image/*">
                <small class="form-hint">Format: JPG, JPEG, PNG. Maksimal 2MB.</small>
              </div>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="button" class="btn btn-primary ms-auto" id="btnUpdate">
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