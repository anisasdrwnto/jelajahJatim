$(document).ready(function () {
    // Panggil saat halaman pertama kali dibuka
    loadDaftarEvent();
    loadTableBooking();
    loadCardBooking();

    // Reset form saat modal tambah terbuka, dan refresh dropdown event terbaru
    $('#modalAddBooking').on('show.bs.modal', function () {
        var form = $('#formAddBooking');
        if (form.length > 0) {
            form[0].reset();
            $('#kuotaTerjual').val(0);
        }
        loadDaftarEvent();
    });

    var debounceTimer = null;

    $('#search-booking').on('input', function () {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(function () {
            loadTableBooking();
        }, 350);
    });

    $('#btnSaveBooking').click(function () {
        var id_event      = $('#idEvent').val();
        var nama_tiket     = $('#namaTiket').val();
        var harga          = $('#harga').val();
        var kuota          = $('#kuota').val();
        var kuota_terjual  = $('#kuotaTerjual').val();

        if (!id_event)                     { Swal.fire('Perhatian!', 'Event harus dipilih!', 'warning'); return; }
        if (harga === '' || harga < 0)      { Swal.fire('Perhatian!', 'Harga tiket harus diisi!', 'warning'); return; }
        if (kuota === '' || kuota < 0)      { Swal.fire('Perhatian!', 'Kuota harus diisi!', 'warning'); return; }
        if (kuota_terjual === '')           { kuota_terjual = 0; }
        if (parseInt(kuota_terjual) > parseInt(kuota)) {
            Swal.fire('Perhatian!', 'Kuota terjual tidak boleh lebih besar dari kuota total!', 'warning');
            return;
        }

        $.ajax({
            url      : BASE_URL + 'proses/proses_booking.php',
            type     : 'POST',
            dataType : 'json',
            data     : {
                action       : 'tambah',
                idEvent      : id_event,
                namaTiket    : nama_tiket,
                harga        : harga,
                kuota        : kuota,
                kuotaTerjual : kuota_terjual
            },
            success: function (response) {
                if (response.success == true) {
                    Swal.fire({
                        icon              : 'success',
                        title             : 'Berhasil!',
                        text              : response.message,
                        showConfirmButton : false,
                        timer             : 1500
                    }).then(() => {
                        $('#modalAddBooking').modal('hide');
                        $('#formAddBooking')[0].reset();
                        loadTableBooking();
                        loadCardBooking();
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

    $(document).on('click', '.btnEditBooking', function () {
        var id = $(this).data('id');

        $.ajax({
            url      : BASE_URL + 'proses/proses_booking.php',
            type     : 'GET',
            dataType : 'json',
            data     : { action: 'baca', id: id },
            success  : function (response) {
                if (response.success == true) {
                    var item = response.data;

                    loadDaftarEvent(item.mbt_id_event, function () {
                        $('#edit_id_tiket').val(item.mbt_id_tiket);
                        $('#editIdEvent').val(item.mbt_id_event);
                        $('#editNamaTiket').val(item.mbt_nama_tiket);
                        $('#editHarga').val(item.mbt_harga);
                        $('#editKuota').val(item.mbt_kuota);
                        $('#editKuotaTerjual').val(item.mbt_kuota_terjual);

                        $('#modalEditBooking').modal('show');
                    });

                } else {
                    Swal.fire('Gagal!', response.message, 'error');
                }
            },
            error: function (xhr) {
                Swal.fire('Error!', 'Gagal mengambil data tiket!', 'error');
                console.error('AJAX Error:', xhr.responseText);
            }
        });
    });

    $('#btnUpdateBooking').click(function () {
        var id             = $('#edit_id_tiket').val();
        var id_event       = $('#editIdEvent').val();
        var nama_tiket      = $('#editNamaTiket').val();
        var harga          = $('#editHarga').val();
        var kuota          = $('#editKuota').val();
        var kuota_terjual  = $('#editKuotaTerjual').val();

        if (!id_event)                     { Swal.fire('Perhatian!', 'Event harus dipilih!', 'warning'); return; }
        if (harga === '' || harga < 0)      { Swal.fire('Perhatian!', 'Harga tiket harus diisi!', 'warning'); return; }
        if (kuota === '' || kuota < 0)      { Swal.fire('Perhatian!', 'Kuota harus diisi!', 'warning'); return; }
        if (kuota_terjual === '')           { kuota_terjual = 0; }
        if (parseInt(kuota_terjual) > parseInt(kuota)) {
            Swal.fire('Perhatian!', 'Kuota terjual tidak boleh lebih besar dari kuota total!', 'warning');
            return;
        }

        $.ajax({
            url      : BASE_URL + 'proses/proses_booking.php',
            type     : 'POST',
            dataType : 'json',
            data     : {
                action       : 'edit',
                id           : id,
                idEvent      : id_event,
                namaTiket    : nama_tiket,
                harga        : harga,
                kuota        : kuota,
                kuotaTerjual : kuota_terjual
            },
            success: function (response) {
                if (response.success == true) {
                    Swal.fire({
                        icon              : 'success',
                        title             : 'Berhasil!',
                        text              : response.message,
                        showConfirmButton : false,
                        timer             : 1500
                    }).then(() => {
                        $('#modalEditBooking').modal('hide');
                        $('#formEditBooking')[0].reset();
                        loadTableBooking();
                        loadCardBooking();
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

    $(document).on('click', '.btnHapusBooking', function () {
        var id   = $(this).data('id');
        var nama = $(this).data('nama');

        Swal.fire({
            title              : 'Hapus Tiket?',
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
                    url      : BASE_URL + 'proses/proses_booking.php',
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
                                loadTableBooking();
                                loadCardBooking();
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


// Ambil daftar event untuk dropdown #idEvent dan #editIdEvent.
// selectedId  : jika diisi, opsi dengan id tersebut akan langsung di-set selected (dipakai saat edit).
// callback    : dijalankan setelah dropdown selesai di-render (dipakai saat edit, supaya urutan rapi).
function loadDaftarEvent(selectedId, callback) {
    $.ajax({
        url      : BASE_URL + 'proses/proses_booking.php',
        type     : 'GET',
        dataType : 'json',
        data     : { action: 'daftarEvent' },
        success  : function (response) {
            if (response.success == true) {
                var optionsHtml = '<option value="" disabled>Pilih event...</option>';

                $.each(response.data, function (index, item) {
                    var selected = (selectedId && selectedId === item.mev_id_event) ? 'selected' : '';
                    optionsHtml += `<option value="${item.mev_id_event}" ${selected}>${escHtml(item.mev_nama_event)}</option>`;
                });

                $('#idEvent, #editIdEvent').html(optionsHtml);

                if (!selectedId) {
                    $('#idEvent, #editIdEvent').val('');
                }
            }

            if (typeof callback === 'function') {
                callback();
            }
        },
        error: function (xhr) {
            console.error('Gagal memuat daftar event:', xhr.responseText);
            if (typeof callback === 'function') {
                callback();
            }
        }
    });
}

function loadTableBooking() {
    var keyword = $('#search-booking').val().trim();

    if (keyword !== '') {
        cariBooking(keyword);
        return;
    }

    $.ajax({
        url      : BASE_URL + 'proses/proses_booking.php',
        type     : 'GET',
        dataType : 'json',
        data     : { action: 'baca' },
        beforeSend: function () {
            $('#tbodyBooking').html(`
                <tr>
                    <td colspan="8" class="text-center py-4">
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
            Swal.fire('Error!', 'Gagal memuat data tiket!', 'error');
            console.error('AJAX Error:', xhr.responseText);
        }
    });
}

function cariBooking(keyword) {
    $.ajax({
        url      : BASE_URL + 'proses/proses_booking.php',
        type     : 'GET',
        dataType : 'json',
        data     : { action: 'cari', q: keyword },

        beforeSend: function () {
            $('#tbodyBooking').html(`
                <tr>
                    <td colspan="8" class="text-center py-4">
                        <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                        <span class="ms-2 text-muted">Mencari...</span>
                    </td>
                </tr>
            `);
        },

        success: function (response) {
            if (response.success == true) {
                var data = response.data;

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

function renderTbody(data, keyword) {
    var tbody = '';

    if (data.length === 0) {
        tbody = `
            <tr>
                <td colspan="8" class="text-center text-muted py-4">
                    <i class="ti ti-ticket-off fs-2 d-block mb-2"></i>
                    ${keyword ? 'Tidak ada hasil untuk "<strong>' + escHtml(keyword) + '</strong>"' : 'Belum ada data tiket'}
                </td>
            </tr>
        `;
    } else {
        $.each(data, function (index, item) {

            var namaEvent  = hlKeyword(escHtml(item.mev_nama_event ?? '-'), keyword);
            var namaTiket  = hlKeyword(escHtml(item.mbt_nama_tiket ?? '-'), keyword);

            var harga       = formatRupiah(item.mbt_harga);
            var kuota       = parseInt(item.mbt_kuota) || 0;
            var terjual     = parseInt(item.mbt_kuota_terjual) || 0;
            var sisa        = kuota - terjual;

            var kelasSisa = 'bg-success text-white';
            if (sisa <= 0)            { kelasSisa = 'bg-danger text-white'; }
            else if (sisa <= kuota * 0.2) { kelasSisa = 'bg-warning text-white'; }

            tbody += `
                <tr>
                    <td class="text-center">${item.mbt_id_tiket}</td>
                    <td><div class="fw-bold">${namaEvent}</div></td>
                    <td>${namaTiket}</td>
                    <td class="d-none d-md-table-cell">${harga}</td>
                    <td class="d-none d-md-table-cell">${kuota}</td>
                    <td class="d-none d-sm-table-cell">${terjual}</td>
                    <td class="d-none d-lg-table-cell">
                        <span class="badge ${kelasSisa}">${sisa}</span>
                    </td>
                    <td class="text-end text-nowrap">
                        <button class="btn btn-sm btn-warning me-1 btnEditBooking"
                                data-id="${item.mbt_id_tiket}"
                                title="Edit">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-edit" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                            </svg>
                        </button>
                        <button class="btn btn-sm btn-danger btnHapusBooking"
                                data-id="${item.mbt_id_tiket}"
                                data-nama="${escHtml(item.mbt_nama_tiket || item.mev_nama_event || item.mbt_id_tiket)}"
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

    $('#tbodyBooking').html(tbody);
}

function loadCardBooking() {
    $.ajax({
        url      : BASE_URL + 'proses/proses_booking.php',
        type     : 'GET',
        dataType : 'json',
        data     : { action: 'statistik' },
        success  : function (response) {
            if (response.success == true) {
                var data = response.data;
                $('#cardTotalTiket').text(data.total_jenis_tiket);
                $('#cardTotalKuota').text(data.total_kuota);
                $('#cardTotalTerjual').text(data.total_terjual);
                $('#cardTotalPendapatan').text(formatRupiah(data.total_pendapatan));
            }
        },
        error: function (xhr) {
            console.error('Gagal load statistik:', xhr.responseText);
        }
    });
}

// Helper - format angka jadi "Rp 50.000"
function formatRupiah(angka) {
    var n = parseFloat(angka) || 0;
    return 'Rp ' + n.toLocaleString('id-ID', { maximumFractionDigits: 0 });
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
