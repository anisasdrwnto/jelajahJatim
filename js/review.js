$(document).ready(function () {
    loadTableUlasan();
    loadCardUlasan();

    var debounceTimer = null;
    $('#search-ulasan').on('input', function () {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(function () {
            loadTableUlasan();
        }, 350);
    });

    $(document).on('click', '.btnToggleStatus', function () {
        var id            = $(this).data('id');
        var statusSaatIni = $(this).data('status');
        
        // Memastikan keselarasan string dengan DB (Case Sensitive)
        var statusBaru    = (statusSaatIni === 'Tampil') ? 'Sembunyi' : 'Tampil';

        var judul = statusBaru === 'Tampil' ? 'Tampilkan Ulasan?' : 'Sembunyikan Ulasan?';
        var teks  = statusBaru === 'Tampil'
            ? 'Ulasan ini akan kembali muncul di halaman publik.'
            : 'Ulasan ini tidak akan terlihat oleh pengunjung, tapi datanya tetap tersimpan.';

        Swal.fire({
            title              : judul,
            text               : teks,
            icon               : 'question',
            showCancelButton   : true,
            confirmButtonText  : 'Ya, lanjutkan',
            cancelButtonText   : 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url      : BASE_URL + 'proses/proses_ulasan.php',
                    type     : 'POST',
                    dataType : 'json',
                    data     : { action: 'ubahStatus', id: id, status: statusBaru },
                    success  : function (response) {
                        if (response.success == true) {
                            Swal.fire({
                                icon: 'success', title: 'Berhasil!', text: response.message,
                                showConfirmButton: false, timer: 1300
                            });
                            loadTableUlasan();
                            loadCardUlasan();
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

    $(document).on('click', '.btnHapusUlasan', function () {
        var id = $(this).data('id');

        Swal.fire({
            title              : 'Hapus Ulasan?',
            text               : 'Ulasan ini akan dihapus permanen dan tidak bisa dikembalikan!',
            icon               : 'warning',
            showCancelButton   : true,
            confirmButtonColor : '#d33',
            cancelButtonColor  : '#6c757d',
            confirmButtonText  : 'Ya, Hapus!',
            cancelButtonText   : 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url      : BASE_URL + 'proses/proses_ulasan.php',
                    type     : 'POST',
                    dataType : 'json',
                    data     : { action: 'hapus', id: id },
                    success  : function (response) {
                        if (response.success == true) {
                            Swal.fire({
                                icon: 'success', title: 'Berhasil!', text: response.message,
                                showConfirmButton: false, timer: 1300
                            });
                            loadTableUlasan();
                            loadCardUlasan();
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

function loadTableUlasan() {
    var keyword = $('#search-ulasan').val().trim();

    var data = { action: keyword !== '' ? 'cari' : 'baca' };
    if (keyword !== '') data.q = keyword;

    $.ajax({
        url      : BASE_URL + 'proses/proses_ulasan.php',
        type     : 'GET',
        dataType : 'json',
        data     : data,
        beforeSend: function () {
            $('#tbodyUlasan').html(`
                <tr>
                    <td colspan="9" class="text-center py-4">
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
                if (keyword !== '') {
                    $('#labelHasilSearch')
                        .html(`Menampilkan <strong>${response.data.length}</strong> hasil untuk "<em>${escHtml(keyword)}</em>"`)
                        .show();
                } else {
                    $('#labelHasilSearch').hide();
                }
                renderTbody(response.data);
            } else {
                Swal.fire('Gagal!', response.message, 'error');
            }
        },
        error: function (xhr) {
            Swal.fire('Error!', 'Gagal memuat data ulasan!', 'error');
            console.error('AJAX Error:', xhr.responseText);
        }
    });
}

function renderTbody(data) {
    var tbody = '';

    if (!data || data.length === 0) {
        tbody = `
            <tr>
                <td colspan="9" class="text-center text-muted py-4">
                    <i class="ti ti-message-off fs-2 d-block mb-2"></i>
                    Belum ada ulasan masuk.
                </td>
            </tr>
        `;
    } else {
        $.each(data, function (index, item) {
            var foto = item.mul_foto
                ? `<img src="${item.mul_foto}" class="rounded" style="width:48px;height:48px;object-fit:cover;">`
                : `<span class="avatar avatar-sm bg-secondary-lt rounded"><i class="ti ti-photo-off"></i></span>`;

            var bintang = '';
            for (var i = 1; i <= 5; i++) {
                bintang += i <= item.mul_rating
                    ? '<i class="ti ti-star-filled text-warning"></i>'
                    : '<i class="ti ti-star text-muted"></i>';
            }

            var komentarPendek = (item.mul_komentar || '').length > 60
                ? item.mul_komentar.substring(0, 60) + '...'
                : (item.mul_komentar || '-');

            var tanggal = item.mul_createDate
                ? new Date(item.mul_createDate).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' })
                : '-';

            // Normalisasi pengecekan text status agar kebal TiDB Case Sensitivity
            var isTampil   = (item.mul_status && item.mul_status.toLowerCase() === 'tampil');
            var badgeStatus = isTampil
                ? '<span class="badge bg-success-lt text-success">Tampil</span>'
                : '<span class="badge bg-secondary-lt text-secondary">Disembunyikan</span>';

            tbody += `
                <tr>
                    <td>${foto}</td>
                    <td><div class="fw-bold">${escHtml(item.mdw_nama_destinasi_wisata || '-')}</div></td>
                    <td>${escHtml(item.nama_user || '-')}</td>
                    <td class="d-none d-md-table-cell">${escHtml(item.mul_kategori || '-')}</td>
                    <td class="text-nowrap" style="font-size:13px;">${bintang}</td>
                    <td title="${escHtml(item.mul_komentar || '')}">${escHtml(komentarPendek)}</td>
                    <td class="d-none d-sm-table-cell text-nowrap">${tanggal}</td>
                    <td>${badgeStatus}</td>
                    <td class="text-end text-nowrap">
                        <button class="btn btn-sm ${isTampil ? 'btn-outline-secondary' : 'btn-outline-success'} btnToggleStatus me-1"
                                data-id="${item.mul_id_ulasan}"
                                data-status="${isTampil ? 'Tampil' : 'Sembunyi'}"
                                title="${isTampil ? 'Sembunyi' : 'Tampilkan'}">
                            <i class="ti ${isTampil ? 'ti-eye-off' : 'ti-eye'}"></i>
                        </button>
                        <button class="btn btn-sm btn-danger btnHapusUlasan"
                                data-id="${item.mul_id_ulasan}"
                                title="Hapus">
                            <i class="ti ti-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
        });
    }

    $('#tbodyUlasan').html(tbody);
}

function loadCardUlasan() {
    $.ajax({
        url      : BASE_URL + 'proses/proses_ulasan.php',
        type     : 'GET',
        dataType : 'json',
        data     : { action: 'statistik' },
        success  : function (response) {
            if (response.success == true) {
                var data = response.data;
                $('#cardTotalUlasan').text(data.total_ulasan);
                $('#cardTampil').text(data.total_tampil);
                $('#cardSembunyi').text(data.total_disembunyikan);
                $('#cardRataRating').text(data.rata_rating);
            }
        },
        error: function (xhr) {
            console.error('Gagal load statistik ulasan:', xhr.responseText);
        }
    });
}

function escHtml(str) {
    return $('<div>').text(String(str ?? '')).html();
}