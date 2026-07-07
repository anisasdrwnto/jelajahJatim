$(document).ready(function () {
    // 1. Inisialisasi awal
    loadTableEvent();
    loadCardEvent();
    loadLokasiDropdown();

    // 2. Event Listeners
    $('#modalAddEvent').on('show.bs.modal', function () {
        $('#formAddEvent')[0].reset();
        $('#lokasiEvent').val('').trigger('change');
    });

    $('#search-event').on('input', function () {
        var keyword = $(this).val().trim();
        var status = $('#filter-status').val();
        clearTimeout(window.debounceTimer);
        window.debounceTimer = setTimeout(() => cariEvent(keyword, status), 350);
    });

    $('#filter-status').on('change', function () {
        cariEvent($('#search-event').val().trim(), $(this).val());
    });

    // Simpan Event (Tambah)
    $('#btnSaveEvent').click(function () {
        if (!validateForm($('#formAddEvent'))) return;

        var formData = new FormData($('#formAddEvent')[0]);
        formData.append('action', 'tambah');
        formData.append('lokasi', $('#lokasiEvent').val());

        $.ajax({
            url: BASE_URL + 'proses/proses_event.php',
            type: 'POST',
            dataType: 'json',
            data: formData,
            processData: false, contentType: false,
            success: function (res) {
                if (res.success) {
                    Swal.fire('Berhasil!', res.message, 'success').then(() => {
                        $('#modalAddEvent').modal('hide');
                        loadTableEvent(); loadCardEvent();
                    });
                } else {
                    Swal.fire('Gagal!', res.message, 'error');
                }
            }
        });
    });

    // Edit Event (Ambil Data)
    $(document).on('click', '.btnEditEvent', function () {
        var id = $(this).data('id');
        $.ajax({
            url: BASE_URL + 'proses/proses_event.php',
            type: 'GET',
            dataType: 'json',
            data: { action: 'baca', id: id },
            success: function (res) {
                if (res.success) {
                    var item = res.data;
                    $('#edit_id_event').val(item.mev_id_event);
                    $('#editNamaEvent').val(item.mev_nama_event);
                    $('#editKategori').val(item.mev_kategori);
                    if ($('#editKategori').val() === null) {
                        $('#editKategori').val(''); // Reset ke placeholder
                        console.warn("Kategori tidak terdaftar di select option: " + item.mev_kategori);
                    }
                    $('#editTanggalEvent').val(item.mev_tanggal_event);
                    $('#editWaktuMulai').val(item.mev_waktu_mulai);
                    $('#editWaktuSelesai').val(item.mew_waktu_selesai);
                    
                    // Set lokasi dan trigger change untuk dropdown edit
                    $('#editLokasiEvent').val(item.mev_lokasi);
                    
                    $('#editStatus').val(item.mev_status);
                    $('#editDeskripsi').val(item.mev_deskripsi);
                    $('#editFotoLama').val(item.mev_foto);
                    $('#editPreviewFoto').attr('src', getFotoUrlEvent(item.mev_foto)).show();
                    
                    $('#modalEditEvent').modal('show');
                }
            }
        });
    });

    // Update Event (Simpan Edit)
    $('#btnUpdateEvent').click(function () {
        if (!validateForm($('#formEditEvent'))) return;

        var formData = new FormData($('#formEditEvent')[0]);
        formData.append('action', 'edit');
        formData.append('lokasi', $('#editLokasiEvent').val());

        $.ajax({
            url: BASE_URL + 'proses/proses_event.php',
            type: 'POST',
            dataType: 'json',
            data: formData,
            processData: false, contentType: false,
            success: function (res) {
                if (res.success) {
                    Swal.fire('Berhasil!', res.message, 'success').then(() => {
                        $('#modalEditEvent').modal('hide');
                        loadTableEvent(); loadCardEvent();
                    });
                } else {
                    Swal.fire('Gagal!', res.message, 'error');
                }
            }
        });
    });

    // Hapus Event
    $(document).on('click', '.btnHapusEvent', function () {
        var id = $(this).data('id');
        Swal.fire({
            title: 'Hapus Event?', text: "Data akan dihapus permanen!", icon: 'warning',
            showCancelButton: true, confirmButtonColor: '#d33', confirmButtonText: 'Ya, Hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: BASE_URL + 'proses/proses_event.php',
                    type: 'POST',
                    data: { action: 'hapus', id: id },
                    success: () => { loadTableEvent(); loadCardEvent(); }
                });
            }
        });
    });
});

// --- FUNGSI GLOBAL (DI LUAR DOCUMENT READY) ---

function validateForm(form) {
    var valid = true;
    form.find('[required]').each(function() {
        if (!$(this).val()) {
            Swal.fire('Perhatian!', 'Mohon lengkapi field wajib!', 'warning');
            valid = false;
            return false;
        }
    });
    return valid;
}

function loadLokasiDropdown() {
    $.ajax({
        url: "https://webapi.bps.go.id/v1/api/domain/type/kabbyprov/prov/3500/key/b6028c4ff88af791a4f0a24fa44457a5/",
        type: "GET",
        dataType: "json",
        success: function(response) {
            var listKabKota = response.data[1];
            var options = '<option value="" disabled selected>Pilih Kabupaten/Kota...</option>';
            $.each(listKabKota, function(index, item) {
                options += `<option value="${item.domain_name}">${item.domain_name}</option>`;
            });
            $('#lokasiEvent, #editLokasiEvent').html(options);
        }
    });
}

function loadTableEvent() {
    $.ajax({
        url: BASE_URL + 'proses/proses_event.php',
        type: 'GET',
        data: { action: 'baca' },
        success: function (res) { if (res.success) renderTbodyEvent(res.data, ''); }
    });
}

function cariEvent(keyword, status) {
    $.ajax({
        url: BASE_URL + 'proses/proses_event.php',
        type: 'GET',
        data: { action: 'cari', q: keyword, status: status },
        success: function (res) { renderTbodyEvent(res.data, keyword); }
    });
}

function renderTbodyEvent(data, keyword) {
    var tbody = '';

    $.each(data, function (index, item) {
        // 1. Mapping Kategori (sesuaikan value dengan yang ada di database Anda)
        var badgeKategori = {
            'Konser': 'bg-primary',
            'Budaya': 'bg-warning',
            'Olahraga': 'bg-info',
            'Festival': 'bg-purple' // pastikan class ini ada di CSS Anda
        };
        // Fallback ke 'bg-secondary' jika kategori tidak ditemukan
        var kelasKategori = badgeKategori[item.mev_kategori] || 'bg-secondary';

        // 2. Mapping Status
        var badgeStatus = {
            'Aktif': 'bg-success',
            'Selesai': 'bg-info',
            'Batal': 'bg-danger'
        };
        var kelasStatus = badgeStatus[item.mev_status] || 'bg-secondary';

        tbody += `
            <tr>
                <td>${item.mev_id_event}</td>
                <td><div class="fw-bold">${item.mev_nama_event}</div></td>
                <td>${item.mev_tanggal_event}</td>
                <td>${item.mev_waktu_mulai} - ${item.mew_waktu_selesai}</td>
                <td>${item.mev_lokasi}</td>
                <td><img src="${getFotoUrlEvent(item.mev_foto)}" width="40" height="40" style="object-fit:cover; border-radius:4px;"></td>
                
                <td><span class="badge ${kelasKategori} text-white">${item.mev_kategori}</span></td>
                
                <td class="text-truncate" style="max-width: 150px;">${item.mev_deskripsi ?? '-'}</td>
                
                <td><span class="badge ${kelasStatus} text-white">${item.mev_status}</span></td>
                
                <td class="text-nowrap">
                    <button class="btn btn-sm btn-warning btnEditEvent" data-id="${item.mev_id_event}"><i class="ti ti-edit"></i></button>
                    <button class="btn btn-sm btn-danger btnHapusEvent" data-id="${item.mev_id_event}"><i class="ti ti-trash"></i></button>
                </td>
            </tr>
        `;
    });
    $('#tbodyEvent').html(tbody);
}

function loadCardEvent() {
    $.ajax({
        url: BASE_URL + 'proses/proses_event.php',
        type: 'GET',
        data: { action: 'statistik' },
        success: function (res) {
            if (res.success) {
                $('#cardTotalEvent').text(res.data.total || 0);
                $('#cardAktif').text(res.data.aktif || 0);
                $('#cardSelesai').text(res.data.selesai || 0);
                $('#cardBatal').text(res.data.batal || 0);
            }
        }
    });
}

function getFotoUrlEvent(foto) {
    return foto ? (foto.startsWith('http') ? foto : BASE_URL + '../uploads/event/' + foto) : BASE_URL + '../assets/img/placeholder.jpg';
}