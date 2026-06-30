$(document).ready(function () {
    // Panggil saat halaman pertama kali dibuka
    loadTableDestinasi();
    loadCardDestinasi();

    // Reset form saat modal terbuka
    $('#modalAddDestinasi').on('show.bs.modal', function () {
        var form = $('#formAddDestinasi');
        if (form.length > 0) {
            form[0].reset();
        }
    });

    var debounceTimer = null;

    $('#search-destinasi').on('input', function () {
        var keyword = $(this).val().trim();

        clearTimeout(debounceTimer);

        debounceTimer = setTimeout(function () {
            cariDestinasi(keyword);
        }, 350);
    });

    $('#btnSave').click(function(){
        var id_destinasi   = $('#id_destinasi').val();
        var nama_destinasi = $('#namaDestinasi').val();
        var kab_kota       = $('#kabupatenKota').val();
        var wilayah        = $('#wilayah').val();
        var kategori       = $('#kategori').val();
        var status         = $('#status').val();
        var alamatLengkap  = $('#alamat_lengkap').val();
        var deskripsi      = $('#deskripsi').val();
        var foto           = $('#foto')[0].files[0];

        if (!nama_destinasi) { Swal.fire('Perhatian!', 'Nama destinasi tidak boleh kosong!', 'warning'); return; }
        if (!kab_kota)        { Swal.fire('Perhatian!', 'Kabupaten/Kota tidak boleh kosong!', 'warning'); return; }
        if (!kategori)        { Swal.fire('Perhatian!', 'Kategori harus dipilih!', 'warning'); return; }
        if (!alamatLengkap)   { Swal.fire('Perhatian!', 'Alamat lengkap tidak boleh kosong!', 'warning'); return; }
        if (!foto)            { Swal.fire('Perhatian!', 'Foto destinasi harus diupload!', 'warning'); return; }

        var formData = new FormData();
        formData.append('action',        'tambah');
        formData.append('id_destinasi',   id_destinasi);
        formData.append('namaDestinasi',  nama_destinasi);
        formData.append('kabupatenKota',  kab_kota);
        formData.append('wilayah',        wilayah);
        formData.append('kategori',       kategori);
        formData.append('status',         status);
        formData.append('alamatLengkap',  alamatLengkap);
        formData.append('deskripsi',      deskripsi);
        formData.append('foto',           foto);

        $.ajax({
            url         : BASE_URL + 'proses/proses_destinasi.php',
            type        : 'POST',
            dataType    : 'json',
            data        : formData,
            processData : false,
            contentType : false,
            success: function(response){
                if(response.success == true){
                    Swal.fire({
                        icon             : 'success',
                        title            : 'Berhasil!',
                        text             : response.message,
                        showConfirmButton : false,
                        timer            : 1500
                    }).then(() => {
                        $('#modalAddDestinasi').modal('hide');
                        $('#formAddDestinasi')[0].reset();
                        loadTableDestinasi();
                        loadCardDestinasi();
                    });
                } else {
                    Swal.fire('Gagal!', response.message, 'error');
                }
            },
            error: function(xhr){
                Swal.fire('Error!', 'Terjadi kesalahan pada server!', 'error');
                console.error('AJAX Error:', xhr.responseText);
            }
        });
    });

    $(document).on('click', '.btnEdit', function () {
        var id = $(this).data('id');

        $.ajax({
            url      : BASE_URL + 'proses/proses_destinasi.php',
            type     : 'GET',
            dataType : 'json',
            data     : { action: 'baca', id: id },
            success  : function (response) {
                if (response.success == true) {
                    var item = response.data;

                    $('#edit_id_destinasi').val(item.mdw_id_destinasi_wisata);
                    $('#editNamaDestinasi').val(item.mdw_nama_destinasi_wisata);
                    $('#editKabupatenKota').val(item.mdw_kabupaten_kota);
                    $('#editWilayah').val(item.mdw_wilayah);
                    $('#editKategori').val(item.mdw_kategori);
                    $('#editStatus').val(item.mdw_status);
                    $('#editAlamat_lengkap').val(item.mdw_alamat_lengkap);
                    $('#editDeskripsi').val(item.mdw_deskripsi);
                    $('#editFotoLama').val(item.mdw_foto);

                    $('#editPreviewFoto').attr('src', getFotoUrl(item.mdw_foto)).show();

                    $('#modalEditDestinasi').modal('show');

                } else {
                    Swal.fire('Gagal!', response.message, 'error');
                }
            },
            error: function (xhr) {
                Swal.fire('Error!', 'Gagal mengambil data destinasi!', 'error');
                console.error('AJAX Error:', xhr.responseText);
            }
        });
    });

    $('#btnUpdate').click(function () {
        var id             = $('#edit_id_destinasi').val();
        var nama_destinasi = $('#editNamaDestinasi').val();
        var kab_kota       = $('#editKabupatenKota').val();
        var wilayah        = $('#editWilayah').val();
        var kategori       = $('#editKategori').val();
        var status         = $('#editStatus').val();
        var alamatLengkap  = $('#editAlamat_lengkap').val();
        var deskripsi      = $('#editDeskripsi').val();
        var foto           = $('#editFoto')[0].files[0];
        var fotoLama       = $('#editFotoLama').val();

        if (!nama_destinasi) { Swal.fire('Perhatian!', 'Nama destinasi tidak boleh kosong!', 'warning'); return; }
        if (!kab_kota)        { Swal.fire('Perhatian!', 'Kabupaten/Kota tidak boleh kosong!', 'warning'); return; }
        if (!kategori)        { Swal.fire('Perhatian!', 'Kategori harus dipilih!', 'warning'); return; }
        if (!alamatLengkap)   { Swal.fire('Perhatian!', 'Alamat lengkap tidak boleh kosong!', 'warning'); return; }

        var formData = new FormData();
        formData.append('action',        'edit');
        formData.append('id',             id);
        formData.append('namaDestinasi',  nama_destinasi);
        formData.append('kabupatenKota',  kab_kota);
        formData.append('wilayah',        wilayah);
        formData.append('kategori',       kategori);
        formData.append('status',         status);
        formData.append('alamatLengkap',  alamatLengkap);
        formData.append('deskripsi',      deskripsi);
        formData.append('fotoLama',       fotoLama);
        if (foto) {
            formData.append('foto', foto);
        }

        $.ajax({
            url         : BASE_URL + 'proses/proses_destinasi.php',
            type        : 'POST',
            dataType    : 'json',
            data        : formData,
            processData : false,
            contentType : false,
            success: function (response) {
                if (response.success == true) {
                    Swal.fire({
                        icon             : 'success',
                        title            : 'Berhasil!',
                        text             : response.message,
                        showConfirmButton : false,
                        timer            : 1500
                    }).then(() => {
                        $('#modalEditDestinasi').modal('hide');
                        $('#formEditDestinasi')[0].reset();
                        loadTableDestinasi();
                        loadCardDestinasi();
                    });
                } else {
                    Swal.fire('Gagal!', response.message, 'error');
                }
            },
            error: function (xhr) {
                Swal.fire('Error!', 'Terjadi kesalahan pada server!', 'error');
                console.error('AJAX Error:', xhr.responseText);
            }
        });
    });

    $(document).on('click', '.btnHapus', function () {
        var id   = $(this).data('id');
        var nama = $(this).data('nama');

        Swal.fire({
            title             : 'Hapus Destinasi?',
            text              : `"${nama}" akan dihapus permanen dan tidak bisa dikembalikan!`,
            icon              : 'warning',
            showCancelButton  : true,
            confirmButtonColor: '#d33',
            cancelButtonColor : '#6c757d',
            confirmButtonText : 'Ya, Hapus!',
            cancelButtonText  : 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url      : BASE_URL + 'proses/proses_destinasi.php',
                    type     : 'POST',
                    dataType : 'json',
                    data     : { action: 'hapus', id: id },
                    success  : function (response) {
                        if (response.success == true) {
                            Swal.fire({
                                icon             : 'success',
                                title            : 'Berhasil!',
                                text             : response.message,
                                showConfirmButton : false,
                                timer            : 1500
                            }).then(() => {
                                loadTableDestinasi();
                                loadCardDestinasi();
                            });
                        } else {
                            Swal.fire('Gagal!', response.message, 'error');
                        }
                    },
                    error: function (xhr) {
                        Swal.fire('Error!', 'Terjadi kesalahan pada server!', 'error');
                        console.error('AJAX Error:', xhr.responseText);
                    }
                });
            }
        });
    });

    // API untuk Kabupaten Kota Select option (Dipakai untuk Modal Tambah & Modal Edit)
    $.ajax({
        url      : `https://webapi.bps.go.id/v1/api/domain/type/kabbyprov/prov/3500/key/b6028c4ff88af791a4f0a24fa44457a5/`,
        type     : 'GET',
        dataType : 'json',
        success  : function(response){
            var listKabKota = response.data[1];
            
            // Buat struktur option
            var optionAdd = '<option value="" disabled selected>Pilih Kabupaten/Kota...</option>';
            var optionEdit = '<option value="" disabled>Pilih Kabupaten/Kota...</option>'; // Tanpa 'selected' bawaan agar tidak bentrok saat inject data edit
            
            $.each(listKabKota, function(index, item){
                optionAdd += `<option value="${item.domain_name}">${item.domain_name}</option>`;
                optionEdit += `<option value="${item.domain_name}">${item.domain_name}</option>`;
            });
            
            // Masukkan ke dropdown modal tambah dan modal edit
            $('#kabupatenKota').html(optionAdd);
            $('#editKabupatenKota').html(optionEdit);
        },
        error: function() {
            // Antisipasi jika API BPS gagal dimuat
            $('#editKabupatenKota').html('<option value="" disabled>Gagal memuat data Kabupaten/Kota</option>');
        }
    });

    

}); 


function cariDestinasi(keyword) {
    $.ajax({
        url      : BASE_URL + 'proses/proses_destinasi.php',
        type     : 'GET',
        dataType : 'json',
        data     : { action: 'cari', q: keyword },

        beforeSend: function () {
            // Tampilkan spinner di tabel selama proses pencarian
            $('#tbodyDestinasi').html(`
                <tr>
                    <td colspan="10" class="text-center py-4">
                        <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                        <span class="ms-2 text-muted">Mencari...</span>
                    </td>
                </tr>
            `);
        },

        success: function (response) {
            if (response.success == true) {
                // Update label counter hasil pencarian
                if (keyword !== '') {
                    $('#labelHasilSearch')
                        .html(`Menampilkan <strong>${response.total}</strong> hasil untuk "<em>${escHtml(keyword)}</em>"`)
                        .show();
                } else {
                    $('#labelHasilSearch').hide();
                }

                renderTbody(response.data, keyword);
            } else {
                Swal.fire('Gagal!', response.message, 'error');
            }
        },

        error: function (xhr) {
            Swal.fire('Error!', 'Gagal melakukan pencarian!', 'error');
            console.error('AJAX Error:', xhr.responseText);
        }
    });
}

function loadTableDestinasi() {
    // Saat load ulang tabel (setelah tambah/edit/hapus), kosongkan search
    // agar hasil yang tampil sesuai dengan kondisi terkini
    var keyword = $('#search-destinasi').val().trim();

    if (keyword !== '') {
        // Jika ada keyword aktif, reload dengan keyword yang sama
        cariDestinasi(keyword);
        return;
    }

    $.ajax({
        url      : BASE_URL + 'proses/proses_destinasi.php',
        type     : 'GET',
        dataType : 'json',
        data     : { action: 'baca' },
        beforeSend: function () {
            $('#tbodyDestinasi').html(`
                <tr>
                    <td colspan="10" class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <div class="text-muted mt-2">Memuat data...</div>
                    </td>
                </tr>
            `);
        },
        success: function (response) {
            if (response.success == true) {
                $('#labelHasilSearch').hide();
                renderTbody(response.data, '');
            } else {
                Swal.fire('Gagal!', response.message, 'error');
            }
        },
        error: function (xhr) {
            Swal.fire('Error!', 'Gagal memuat data destinasi!', 'error');
            console.error('AJAX Error:', xhr.responseText);
        }
    });
}

function renderTbody(data, keyword) {
    var tbody = '';

    if (data.length === 0) {
        tbody = `
            <tr>
                <td colspan="10" class="text-center text-muted py-4">
                    <i class="ti ti-map-off fs-2 d-block mb-2"></i>
                    ${keyword ? 'Tidak ada hasil untuk "<strong>' + escHtml(keyword) + '</strong>"' : 'Belum ada data destinasi wisata'}
                </td>
            </tr>
        `;
    } else {
        $.each(data, function (index, item) {

            var badgeKategori = {
                'Alam'    : 'bg-success text-white',
                'Budaya'  : 'bg-warning text-white',
                'Buatan'  : 'bg-info text-white',
                'Kuliner' : 'bg-danger text-white'
            };
            var kelasKategori = badgeKategori[item.mdw_kategori] || 'bg-secondary text-white';

            var badgeStatus = {
                'Aktif'     : 'bg-success text-white',
                'Nonaktif'  : 'bg-secondary text-white',
                'Perbaikan' : 'bg-warning text-white'
            };
            var kelasStatus = badgeStatus[item.mdw_status] || 'bg-secondary text-white';

            var fotoSrc = getFotoUrl(item.mdw_foto);

            // Highlight keyword pada kolom teks jika ada
            var nama     = hlKeyword(escHtml(item.mdw_nama_destinasi_wisata), keyword);
            var kabkota  = hlKeyword(escHtml(item.mdw_kabupaten_kota  ?? '-'), keyword);
            var wilayah  = hlKeyword(escHtml(item.mdw_wilayah         ?? '-'), keyword);
            var alamat   = hlKeyword(escHtml(item.mdw_alamat_lengkap  ?? '-'), keyword);
            var kategori = hlKeyword(escHtml(item.mdw_kategori        ?? '-'), keyword);

            tbody += `
                <tr>
                    <td class="text-center">${item.mdw_id_destinasi_wisata}</td>
                    <td><div class="fw-bold">${nama}</div></td>
                    <td class="d-none d-lg-table-cell">${kabkota}</td>
                    <td class="d-none d-lg-table-cell">${wilayah}</td>
                    <td class="d-none d-lg-table-cell">${alamat}</td>
                    <td class="w-1">
                        <img src="${fotoSrc}" alt="Foto Destinasi" 
                             width="60" height="60" 
                             style="object-fit:cover; border-radius:6px;"
                             onerror="this.src='${BASE_URL}../assets/img/placeholder.jpg'">
                    </td>
                    <td class="d-none d-md-table-cell">
                        <span class="badge ${kelasKategori}">${kategori}</span>
                    </td>
                    <td class="d-none d-sm-table-cell" style="max-width: 150px;">
                        <span class="d-inline-block text-truncate" style="max-width: 140px;" 
                              title="${escHtml(item.mdw_deskripsi ?? '-')}">
                            ${escHtml(item.mdw_deskripsi ?? '-')}
                        </span>
                    </td>
                    <td>
                        <span class="badge ${kelasStatus}">${item.mdw_status ?? '-'}</span>
                    </td>
                    <td class="text-end text-nowrap">
                        <button class="btn btn-sm btn-warning me-1 btnEdit" 
                                data-id="${item.mdw_id_destinasi_wisata}" 
                                title="Edit">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-edit" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                            </svg>
                        </button>
                        <button class="btn btn-sm btn-danger btnHapus" 
                                data-id="${item.mdw_id_destinasi_wisata}" 
                                data-nama="${escHtml(item.mdw_nama_destinasi_wisata)}"
                                title="Hapus">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-trash" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <line x1="4" y1="7" x2="20" y2="7" />
                                <line x1="10" y1="11" x2="10" y2="17" />
                                <line x1="14" y1="11" x2="14" y2="17" />
                                <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                            </svg>
                        </button>
                    </td>
                </tr>
            `;
        });
    }

    $('#tbodyDestinasi').html(tbody);
}

function loadCardDestinasi() {
    $.ajax({
        url      : BASE_URL + 'proses/proses_destinasi.php',
        type     : 'GET',
        dataType : 'json',
        data     : { action: 'statistik' },
        success  : function (response) {
            if (response.success == true) {
                var data = response.data;
                $('#cardTotalDestinasi').text(data.total);
                $('#cardAktif').text(data.aktif);
                $('#cardNonaktif').text(data.nonaktif);
                $('#cardKategori').text(data.total_kategori);
            }
        },
        error: function (xhr) {
            console.error('Gagal load statistik:', xhr.responseText);
        }
    });
}

// Helper — resolve URL foto: Cloudinary (http/https) langsung pakai,
// nama file lokal lama fallback ke path uploads, kosong pakai placeholder
function getFotoUrl(foto) {
    if (!foto) {
        return BASE_URL + '../assets/img/placeholder.jpg';
    }
    if (foto.startsWith('http://') || foto.startsWith('https://')) {
        return foto; // sudah full URL Cloudinary, langsung pakai
    }
    // data lama: nama file lokal
    return BASE_URL + '../uploads/destinasi/' + foto;
}

function hlKeyword(teks, keyword) {
    if (!keyword) return teks;
    var regex = new RegExp('(' + escRegex(keyword) + ')', 'gi');
    return teks.replace(regex, '<mark class="p-0 bg-warning bg-opacity-50">$1</mark>');
}

// Helper — escape string HTML (cegah XSS)
function escHtml(str) {
    return $('<div>').text(String(str ?? '')).html();
}

// Helper — escape karakter spesial untuk RegExp
function escRegex(str) {
    return str.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
}