$(document).ready(function () {
    // Panggil saat halaman pertama kali dibuka
    loadTablePemesanan();
    loadCardPemesanan();

    var debounceTimer = null;

    $('#search-pemesanan').on('input', function () {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(function () {
            loadTablePemesanan();
        }, 350);
    });

    // Verifikasi pembayaran (Pending -> Lunas)
    $(document).on('click', '.btnVerifikasiPemesanan', function () {
        var id   = $(this).data('id');
        var kode = $(this).data('kode');

        Swal.fire({
            title              : 'Verifikasi Pembayaran?',
            text               : `Booking "${kode}" akan diubah statusnya menjadi Lunas!`,
            icon               : 'question',
            showCancelButton   : true,
            confirmButtonColor : '#2fb344',
            cancelButtonColor  : '#6c757d',
            confirmButtonText  : 'Ya, Verifikasi!',
            cancelButtonText   : 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url      : BASE_URL + 'proses/proses_daftar_tiket.php',
                    type     : 'POST',
                    dataType : 'json',
                    data     : { action: 'verifikasi', id: id },
                    success  : function (response) {
                        if (response.success == true) {
                            Swal.fire({
                                icon              : 'success',
                                title             : 'Berhasil!',
                                text              : response.message,
                                showConfirmButton : false,
                                timer             : 1500
                            }).then(() => {
                                loadTablePemesanan();
                                loadCardPemesanan();
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

    // Hapus pemesanan
    $(document).on('click', '.btnHapusPemesanan', function () {
        var id   = $(this).data('id');
        var kode = $(this).data('kode');

        Swal.fire({
            title              : 'Hapus Pemesanan?',
            text               : `Booking "${kode}" akan dihapus permanen dan tidak bisa dikembalikan!`,
            icon               : 'warning',
            showCancelButton   : true,
            confirmButtonColor : '#d33',
            cancelButtonColor  : '#6c757d',
            confirmButtonText  : 'Ya, Hapus!',
            cancelButtonText   : 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url      : BASE_URL + 'proses/proses_daftar_tiket.php',
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
                                loadTablePemesanan();
                                loadCardPemesanan();
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

// --- FUNGSI PENDUKUNG ---

function loadTablePemesanan() {
    var keyword = $('#search-pemesanan').val().trim();

    if (keyword !== '') {
        cariPemesanan(keyword);
        return;
    }

    $.ajax({
        url      : BASE_URL + 'proses/proses_daftar_tiket.php',
        type     : 'GET',
        dataType : 'json',
        data     : { action: 'baca' },
        beforeSend: function () {
            $('#tbodyPemesanan').html(`
                <tr>
                    <td colspan="7" class="text-center py-4">
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
                $('#labelHasilSearchPemesanan').hide();
                renderTbodyPemesanan(response.data, '');
            } else {
                Swal.fire('Gagal!', response.message, 'error');
            }
        },
        error: function (xhr) {
            Swal.fire('Error!', 'Gagal memuat data pemesanan!', 'error');
            console.error('AJAX Error:', xhr.responseText);
        }
    });
}

function cariPemesanan(keyword) {
    $.ajax({
        url      : BASE_URL + 'proses/proses_daftar_tiket.php',
        type     : 'GET',
        dataType : 'json',
        data     : { action: 'cari', q: keyword },

        beforeSend: function () {
            $('#tbodyPemesanan').html(`
                <tr>
                    <td colspan="7" class="text-center py-4">
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
                    $('#labelHasilSearchPemesanan')
                        .html(`Menampilkan <strong>${data.length}</strong> hasil untuk "<em>${escHtmlPemesanan(keyword)}</em>"`)
                        .show();
                } else {
                    $('#labelHasilSearchPemesanan').hide();
                }

                renderTbodyPemesanan(data, keyword);
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

function renderTbodyPemesanan(data, keyword) {
    var tbody = '';

    if (data.length === 0) {
        tbody = `
            <tr>
                <td colspan="7" class="text-center text-muted py-4">
                    <i class="ti ti-file-off fs-2 d-block mb-2"></i>
                    ${keyword ? 'Tidak ada hasil untuk "<strong>' + escHtmlPemesanan(keyword) + '</strong>"' : 'Belum ada data pemesanan'}
                </td>
            </tr>
        `;
    } else {
        $.each(data, function (index, item) {

            var idBooking   = escHtmlPemesanan(item.mtbk_id_booking ?? '-');
            var kodeBooking = escHtmlPemesanan(item.mtbk_booking_code ?? item.mtbk_id_booking ?? '-');
            var idUser      = hlKeywordPemesanan(escHtmlPemesanan(item.mtbk_id_user ?? '-'), keyword);
            var namaPemesan = hlKeywordPemesanan(escHtmlPemesanan(item.mtbk_nama_pemesan ?? '-'), keyword);
            var tanggal     = formatTanggalPemesanan(item.mtbk_createDate);
            var jumlahTiket = parseInt(item.mtbk_jumlah_tiket) || 0;
            var statusBayar = item.mtbk_status_bayar ?? 'Pending';

            var badgeStatus = 'bg-warning text-white';
            if (statusBayar === 'Lunas')       { badgeStatus = 'bg-success text-white'; }
            else if (statusBayar === 'Gagal')  { badgeStatus = 'bg-danger text-white'; }
            else if (statusBayar === 'Expired'){ badgeStatus = 'bg-secondary text-white'; }

            var tombolVerifikasi = '';
            if (statusBayar === 'Pending') {
                tombolVerifikasi = `
                    <button class="btn btn-sm btn-success me-1 btnVerifikasiPemesanan"
                            data-id="${idBooking}"
                            data-kode="${kodeBooking}"
                            title="Verifikasi Pembayaran">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-check" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M5 12l5 5l10 -10" />
                        </svg>
                    </button>
                `;
            }

            tbody += `
                <tr>
                    <td class="text-center">${idBooking}</td>
                    <td>${idUser}</td>
                    <td><div class="fw-bold">${namaPemesan}</div></td>
                    <td class="d-none d-md-table-cell">${tanggal}</td>
                    <td class="d-none d-sm-table-cell">${jumlahTiket}</td>
                    <td><span class="badge ${badgeStatus}">${escHtmlPemesanan(statusBayar)}</span></td>
                    <td class="text-end text-nowrap">
                        ${tombolVerifikasi}
                        <button class="btn btn-sm btn-danger btnHapusPemesanan"
                                data-id="${idBooking}"
                                data-kode="${kodeBooking}"
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

    $('#tbodyPemesanan').html(tbody);
}

function loadCardPemesanan() {
    $.ajax({
        url      : BASE_URL + 'proses/proses_pemesanan.php',
        type     : 'GET',
        dataType : 'json',
        data     : { action: 'statistik' },
        success  : function (response) {
            if (response.success == true) {
                var data = response.data;
                $('#cardTotalPemesanan').text(data.total_pemesanan);
                $('#cardTotalTiketDipesan').text(data.total_tiket_dipesan);
                $('#cardTotalPending').text(data.total_pending);
                $('#cardTotalPendapatan').text(formatRupiahPemesanan(data.total_pendapatan));
            }
        },
        error: function (xhr) {
            console.error('Gagal load statistik pemesanan:', xhr.responseText);
        }
    });
}

function formatRupiahPemesanan(angka) {
    var n = parseFloat(angka) || 0;
    return 'Rp ' + n.toLocaleString('id-ID', { maximumFractionDigits: 0 });
}

function formatTanggalPemesanan(tanggal) {
    if (!tanggal) return '-';
    var d = new Date(tanggal.replace(' ', 'T'));
    if (isNaN(d.getTime())) return tanggal;
    return d.toLocaleString('id-ID', {
        day   : '2-digit',
        month : 'short',
        year  : 'numeric',
        hour  : '2-digit',
        minute: '2-digit'
    });
}

function hlKeywordPemesanan(teks, keyword) {
    if (!keyword) return teks;
    var regex = new RegExp('(' + escRegexPemesanan(keyword) + ')', 'gi');
    return teks.replace(regex, '<mark class="p-0 bg-warning bg-opacity-50">$1</mark>');
}

function escHtmlPemesanan(str) {
    return $('<div>').text(String(str ?? '')).html();
}

function escRegexPemesanan(str) {
    return str.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
}