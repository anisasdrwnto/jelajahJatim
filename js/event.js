$(document).ready(function () {
    // Panggil saat halaman pertama kali dibuka
    loadTableEvent();
    loadCardEvent();

    // Reset form saat modal tambah terbuka
    $('#modalAddEvent').on('show.bs.modal', function () {
        var form = $('#formAddEvent');
        if (form.length > 0) {
            form[0].reset();
        }
    });

    var debounceTimer = null;

    $('#search-event').on('input', function () {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(function () {
            loadTableEvent();
        }, 350);
    });

    $('#filter-status').on('change', function () {
        loadTableEvent();
    });

    $('#btnSaveEvent').click(function () {
        var nama_event     = $('#namaEvent').val();
        // Tambahkan || '' untuk memastikan nilainya string kosong jika belum dipilih (bukan null)
        var kategori       = $('#kategori').val() || ''; 
        var tanggal_event  = $('#tanggalEvent').val();
        var waktu_mulai    = $('#waktuMulai').val();
        var waktu_selesai  = $('#waktuSelesai').val();
        var lokasi         = $('#lokasi').val();
        var status         = $('#status').val();
        var deskripsi      = $('#deskripsi').val();
        var foto           = $('#foto')[0].files[0];

        if (!nama_event)     { Swal.fire('Perhatian!', 'Nama event tidak boleh kosong!', 'warning'); return; }
        if (!kategori)       { Swal.fire('Perhatian!', 'Kategori harus dipilih!', 'warning'); return; }
        if (!tanggal_event)  { Swal.fire('Perhatian!', 'Tanggal event harus diisi!', 'warning'); return; }
        if (!waktu_mulai)    { Swal.fire('Perhatian!', 'Waktu mulai harus diisi!', 'warning'); return; }
        if (!waktu_selesai)  { Swal.fire('Perhatian!', 'Waktu selesai harus diisi!', 'warning'); return; }
        if (waktu_selesai <= waktu_mulai) { Swal.fire('Perhatian!', 'Waktu selesai harus setelah waktu mulai!', 'warning'); return; }
        if (!lokasi)         { Swal.fire('Perhatian!', 'Lokasi tidak boleh kosong!', 'warning'); return; }
        if (!foto)           { Swal.fire('Perhatian!', 'Foto event harus diupload!', 'warning'); return; }

        var formData = new FormData();
        formData.append('action',       'tambah');
        formData.append('namaEvent',    nama_event);
        formData.append('kategori',     kategori);
        formData.append('tanggalEvent', tanggal_event);
        formData.append('waktuMulai',   waktu_mulai);
        formData.append('waktuSelesai', waktu_selesai);
        formData.append('lokasi',       lokasi);
        formData.append('status',       status);
        formData.append('deskripsi',    deskripsi);
        formData.append('foto',         foto);

        $.ajax({
            url         : BASE_URL + 'proses/proses_event.php',
            type        : 'POST',
            dataType    : 'json',
            data        : formData,
            processData : false,
            contentType : false,
            success: function (response) {
                if (response.success == true) {
                    Swal.fire({
                        icon              : 'success',
                        title             : 'Berhasil!',
                        text              : response.message,
                        showConfirmButton : false,
                        timer             : 1500
                    }).then(() => {
                        $('#modalAddEvent').modal('hide');
                        $('#formAddEvent')[0].reset();
                        loadTableEvent();
                        loadCardEvent();
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

    $(document).on('click', '.btnEditEvent', function () {
        var id = $(this).data('id');

        $.ajax({
            url      : BASE_URL + 'proses/proses_event.php',
            type     : 'GET',
            dataType : 'json',
            data     : { action: 'baca', id: id },
            success  : function (response) {
                if (response.success == true) {
                    var item = response.data;

                    $('#edit_id_event').val(item.mev_id_event);
                    $('#editNamaEvent').val(item.mev_nama_event);
                    // Pastikan #editKategori di HTML Modal Edit juga pakai <select>
                    $('#editKategori').val(item.mev_kategori); 
                    $('#editTanggalEvent').val(item.mev_tanggal_event);
                    $('#editWaktuMulai').val(item.mev_waktu_mulai);
                    $('#editWaktuSelesai').val(item.mew_waktu_selesai);
                    $('#editLokasi').val(item.mev_lokasi);
                    $('#editStatus').val(item.mev_status);
                    $('#editDeskripsi').val(item.mev_deskripsi);
                    $('#editFotoLama').val(item.mev_foto);

                    var fotoSrc = item.mev_foto
                        ? item.mev_foto
                        : BASE_URL + '../assets/img/placeholder.jpg';
                    $('#editPreviewFoto').attr('src', fotoSrc).show();
                    $('#modalEditEvent').modal('show');

                } else {
                    Swal.fire('Gagal!', response.message, 'error');
                }
            },
            error: function (xhr) {
                Swal.fire('Error!', 'Gagal mengambil data event!', 'error');
                console.error('AJAX Error:', xhr.responseText);
            }
        });
    });

    $('#btnUpdateEvent').click(function () {
        var id             = $('#edit_id_event').val();
        var nama_event     = $('#editNamaEvent').val();
        // Tambahkan || '' di sini juga
        var kategori       = $('#editKategori').val() || ''; 
        var tanggal_event  = $('#editTanggalEvent').val();
        var waktu_mulai    = $('#editWaktuMulai').val();
        var waktu_selesai  = $('#editWaktuSelesai').val();
        var lokasi         = $('#editLokasi').val();
        var status         = $('#editStatus').val();
        var deskripsi      = $('#editDeskripsi').val();
        var foto           = $('#editFoto')[0].files[0];
        var fotoLama       = $('#editFotoLama').val();

        if (!nama_event)     { Swal.fire('Perhatian!', 'Nama event tidak boleh kosong!', 'warning'); return; }
        if (!kategori)       { Swal.fire('Perhatian!', 'Kategori harus dipilih!', 'warning'); return; }
        if (!tanggal_event)  { Swal.fire('Perhatian!', 'Tanggal event harus diisi!', 'warning'); return; }
        if (!waktu_mulai)    { Swal.fire('Perhatian!', 'Waktu mulai harus diisi!', 'warning'); return; }
        if (!waktu_selesai)  { Swal.fire('Perhatian!', 'Waktu selesai harus diisi!', 'warning'); return; }
        if (waktu_selesai <= waktu_mulai) { Swal.fire('Perhatian!', 'Waktu selesai harus setelah waktu mulai!', 'warning'); return; }
        if (!lokasi)         { Swal.fire('Perhatian!', 'Lokasi tidak boleh kosong!', 'warning'); return; }

        var formData = new FormData();
        formData.append('action',       'edit');
        formData.append('id',           id);
        formData.append('namaEvent',    nama_event);
        formData.append('kategori',     kategori);
        formData.append('tanggalEvent', tanggal_event);
        formData.append('waktuMulai',   waktu_mulai);
        formData.append('waktuSelesai', waktu_selesai);
        formData.append('lokasi',       lokasi);
        formData.append('status',       status);
        formData.append('deskripsi',    deskripsi);
        formData.append('fotoLama',     fotoLama);
        if (foto) {
            formData.append('foto', foto);
        }

        $.ajax({
            url         : BASE_URL + 'proses/proses_event.php',
            type        : 'POST',
            dataType    : 'json',
            data        : formData,
            processData : false,
            contentType : false,
            success: function (response) {
                if (response.success == true) {
                    Swal.fire({
                        icon              : 'success',
                        title             : 'Berhasil!',
                        text              : response.message,
                        showConfirmButton : false,
                        timer             : 1500
                    }).then(() => {
                        $('#modalEditEvent').modal('hide');
                        $('#formEditEvent')[0].reset();
                        loadTableEvent();
                        loadCardEvent();
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

    $(document).on('click', '.btnHapusEvent', function () {
        var id   = $(this).data('id');
        var nama = $(this).data('nama');

        Swal.fire({
            title              : 'Hapus Event?',
            text               : `"${nama}" akan dihapus permanen dan tidak bisa dikembalikan!`,
            icon               : 'warning',
            showCancelButton   : true,
            confirmButtonColor : '#d33',
            cancelButtonColor  : '#6c757d',
            confirmButtonText  : 'Ya, Hapus!',
            cancelButtonText   : 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url      : BASE_URL + 'proses/proses_event.php',
                    type     : 'POST',
                    dataType : 'json',
                    data     : { action: 'hapus', id: id },
                    success  : function (response) {
                        if (response.success == true) {
                            Swal.fire({
                                icon              : 'success',
                                title             : 'Berhasil!',
                                text              : response.message,
                                showConfirmButton : false,
                                timer             : 1500
                            }).then(() => {
                                loadTableEvent();
                                loadCardEvent();
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

});


function loadTableEvent() {
    var keyword = $('#search-event').val().trim();
    var status  = $('#filter-status').val();

    if (keyword !== '') {
        cariEvent(keyword, status);
        return;
    }

    $.ajax({
        url      : BASE_URL + 'proses/proses_event.php',
        type     : 'GET',
        dataType : 'json',
        data     : { action: 'baca' },
        beforeSend: function () {
            $('#tbodyEvent').html(`
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
                renderTbody(filterByStatus(response.data, status), '');
            } else {
                Swal.fire('Gagal!', response.message, 'error');
            }
        },
        error: function (xhr) {
            Swal.fire('Error!', 'Gagal memuat data event!', 'error');
            console.error('AJAX Error:', xhr.responseText);
        }
    });
}

function cariEvent(keyword, status) {
    $.ajax({
        url      : BASE_URL + 'proses/proses_event.php',
        type     : 'GET',
        dataType : 'json',
        data     : { action: 'cari', q: keyword },

        beforeSend: function () {
            $('#tbodyEvent').html(`
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
                var data = filterByStatus(response.data, status);

                if (keyword !== '') {
                    $('#labelHasilSearch')
                        .html(`Menampilkan <strong>${data.length}</strong> hasil untuk "<em>${escHtml(keyword)}</em>"`)
                        .show();
                } else {
                    $('#labelHasilSearch').hide();
                }

                renderTbody(data, keyword);
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

// Helper - filter data by status di sisi client
function filterByStatus(data, status) {
    if (!status) return data;
    return data.filter(function (item) {
        return item.mev_status === status;
    });
}

function renderTbody(data, keyword) {
    var tbody = '';

    if (data.length === 0) {
        tbody = `
            <tr>
                <td colspan="10" class="text-center text-muted py-4">
                    <i class="ti ti-calendar-off fs-2 d-block mb-2"></i>
                    ${keyword ? 'Tidak ada hasil untuk "<strong>' + escHtml(keyword) + '</strong>"' : 'Belum ada data event wisata'}
                </td>
            </tr>
        `;
    } else {
        $.each(data, function (index, item) {

            var badgeStatus = {
                'Aktif'   : 'bg-success text-white',
                'Selesai' : 'bg-info text-white',
                'Batal'   : 'bg-danger text-white'
            };
            var kelasStatus = badgeStatus[item.mev_status] || 'bg-secondary text-white';

            var fotoSrc = item.mev_foto
                ? item.mev_foto
                : BASE_URL + '../assets/img/placeholder.jpg';

            var nama     = hlKeyword(escHtml(item.mev_nama_event), keyword);
            var lokasi   = hlKeyword(escHtml(item.mev_lokasi   ?? '-'), keyword);
            var kategori = hlKeyword(escHtml(item.mev_kategori ?? '-'), keyword);

            var tanggal = item.mev_tanggal_event ? formatTanggal(item.mev_tanggal_event) : '-';
            var waktu   = (item.mev_waktu_mulai && item.mew_waktu_selesai)
                ? formatJam(item.mev_waktu_mulai) + ' - ' + formatJam(item.mew_waktu_selesai)
                : '-';

            tbody += `
                <tr>
                    <td class="text-center">${item.mev_id_event}</td>
                    <td><div class="fw-bold">${nama}</div></td>
                    <td class="d-none d-lg-table-cell">${tanggal}</td>
                    <td class="d-none d-lg-table-cell">${waktu}</td>
                    <td class="d-none d-lg-table-cell">${lokasi}</td>
                    <td class="w-1">
                        <img src="${fotoSrc}" alt="Foto Event"
                             width="60" height="60"
                             style="object-fit:cover; border-radius:6px;">
                    </td>
                    <td class="d-none d-md-table-cell">${kategori}</td>
                    <td class="d-none d-sm-table-cell" style="max-width: 150px;">
                        <span class="d-inline-block text-truncate" style="max-width: 140px;"
                              title="${escHtml(item.mev_deskripsi ?? '-')}">
                            ${escHtml(item.mev_deskripsi ?? '-')}
                        </span>
                    </td>
                    <td>
                        <span class="badge ${kelasStatus}">${item.mev_status ?? '-'}</span>
                    </td>
                    <td class="text-end text-nowrap">
                        <button class="btn btn-sm btn-warning me-1 btnEditEvent"
                                data-id="${item.mev_id_event}"
                                title="Edit">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-edit" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                            </svg>
                        </button>
                        <button class="btn btn-sm btn-danger btnHapusEvent"
                                data-id="${item.mev_id_event}"
                                data-nama="${escHtml(item.mev_nama_event)}"
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

    $('#tbodyEvent').html(tbody);
}

function loadCardEvent() {
    $.ajax({
        url      : BASE_URL + 'proses/proses_event.php',
        type     : 'GET',
        dataType : 'json',
        data     : { action: 'statistik' },
        success  : function (response) {
            if (response.success == true) {
                var data = response.data;
                $('#cardTotalEvent').text(data.total);
                $('#cardAktif').text(data.aktif);
                $('#cardSelesai').text(data.selesai);
                $('#cardBatal').text(data.batal);
            }
        },
        error: function (xhr) {
            console.error('Gagal load statistik:', xhr.responseText);
        }
    });
}

function formatTanggal(tanggal) {
    var bulan = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
    var d = new Date(tanggal);
    if (isNaN(d.getTime())) return tanggal;
    return d.getDate() + ' ' + bulan[d.getMonth()] + ' ' + d.getFullYear();
}

function formatJam(jam) {
    return jam ? jam.substring(0, 5) : '-';
}

function hlKeyword(teks, keyword) {
    if (!keyword) return teks;
    var regex = new RegExp('(' + escRegex(keyword) + ')', 'gi');
    return teks.replace(regex, '<mark class="p-0 bg-warning bg-opacity-50">$1</mark>');
}

function escHtml(str) {
    return $('<div>').text(String(str ?? '')).html();
}

function escRegex(str) {
    return str.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
}