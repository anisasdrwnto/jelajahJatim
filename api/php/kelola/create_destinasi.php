<div class="modal modal-blur fade" id="modalAddDestinasi" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      
      <div class="modal-header">
        <h5 class="modal-title">Tambah Destinasi Wisata Baru</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form id="formAddDestinasi">
        <input type="hidden" name="action" value="create">
         <input type="hidden" id="id_destinasi" name="id_destinasi">
        <div class="modal-body">
          <div class="row">
            <div class="col-lg-6">
              <div class="mb-3">
                <label class="form-label required">Nama Destinasi</label>
                <input type="text" class="form-control" name="namaDestinasi" required placeholder="Contoh: Gunung Bromo" id="namaDestinasi">
              </div>
            </div>

            <div class="col-lg-6">
              <div class="mb-3">
                  <label class="form-label required">Kabupaten/Kota</label>
                  <select class="form-select" name="kabupatenKota" id="kabupatenKota" required>
                    <option value="" disabled selected>Pilih Kabupaten/Kota..</option>
                  </select>
              </div>
          </div>

            <div class="col-lg-6">
              <div class="mb-3">
                <label class="form-label">Wilayah</label>
                <input type="disabled" class="form-control" name="wilayah" value="Jawa Timur" id="wilayah" readonly>
              </div>
            </div>

            <div class="col-lg-6">
              <div class="mb-3">
                <label class="form-label required">Kategori</label>
                <select class="form-select" name="kategori" required id="kategori">
                  <option value="" selected disabled>Pilih Kategori...</option>
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
                <select class="form-select" name="status" required id="status">
                  <option value="Aktif" selected>Aktif / Buka</option>
                  <option value="Nonaktif">Nonaktif / Tutup</option>
                  <option value="Perbaikan">Dalam Perbaikan</option>
                </select>
              </div>
            </div>

            <div class="col-lg-12">
              <div class="mb-3">
                <label class="form-label required">Alamat Lengkap</label>
                <textarea class="form-control" name="alamatLengkap" rows="2" required placeholder="Masukkan alamat lengkap destinasi" id="alamat_lengkap"></textarea>
              </div>
            </div>

            <div class="col-lg-12">
              <div class="mb-3">
                <label class="form-label">Deskripsi</label>
                <textarea class="form-control" name="deskripsi" id="deskripsi" rows="3" placeholder="Jelaskan daya tarik destinasi ini..."></textarea>
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
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btnCancel">
            Batal
          </button>
          <button type="button" class="btn btn-primary ms-auto" id="btnSave">
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