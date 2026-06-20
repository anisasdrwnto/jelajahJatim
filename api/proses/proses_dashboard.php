<?php 
header('Content-Type: application/json');

require_once '../koneksi.php';


class Destinasi {
    private $db;

    public function __construct($pdo) { 
        $this->db = $pdo;
    }

    public function ambilData(){
        $stmtTotal = $this->db->prepare("SELECT COUNT(*) as total FROM mst_destinasi_wisata");
        $stmtTotal->execute();
        $total = $stmtTotal->fetch(PDO::FETCH_ASSOC)['total'];

        $stmtTotal = $this->db->prepare("SELECT COUNT(*) as user FROM mst_users");
        $stmtTotal->execute();
        $user = $stmtTotal->fetch(PDO::FETCH_ASSOC)['user'];

        $stmtTotal = $this->db->prepare("SELECT COUNT(*) as booking FROM mst_transaksi_booking WHERE mtbk_status_bayar ='Lunas'");
        $stmtTotal->execute();
        $booking = $stmtTotal->fetch(PDO::FETCH_ASSOC)['booking'];

        $stmtTotal = $this->db->prepare("SELECT COUNT(*) as ulasan FROM mst_ulasan");
        $stmtTotal->execute();
        $ulasan = $stmtTotal->fetch(PDO::FETCH_ASSOC)['ulasan'];

        return [
            'success' => true,
            'data'    => [
                'total'   => $total,
                'user'    => $user,
                'booking' => $booking,
                'ulasan'  => $ulasan
            ]
        ];
    }

    public function trenBulanan() {
        return [
            'success' => true,
            'data'    => [
                'label' => [
                    'Jan 24','Feb 24','Mar 24','Apr 24','Mei 24','Jun 24',
                    'Jul 24','Agu 24','Sep 24','Okt 24','Nov 24','Des 24',
                    'Jan 25','Feb 25','Mar 25','Apr 25','Mei 25','Jun 25',
                    'Jul 25','Agu 25','Sep 25','Okt 25','Nov 25','Des 25',
                    'Jan 26','Feb 26',
                ],
                'nilai' => [
                    // 2024 — sumber: BRS BPS Jawa Timur (BRS Des 2024 & tabel image)
                    17196, 28026, 18543, 29043, 31222, 25015,
                    34446, 38587, 31700, 28182, 20820, 19265,
                    // 2025 — sumber: BRS BPS Jawa Timur
                    22119, 19205, 15647, 24800, 33409, 34092,
                    34421, 37406, 32664, 29534, 21629, 25019,
                    // 2026 — sumber: BRS BPS Jawa Timur (Jan-Feb 2026)
                    20548, 23775,
                ],
                'sumber'     => 'BPS Provinsi Jawa Timur — Berita Resmi Statistik',
                'satuan'     => 'Kunjungan',
                'diperbarui' => '2026-04-01',
            ],
        ];
    }
}

session_start();

$action = trim($_GET['action'] ?? $_POST['action'] ?? '');

if (empty($action)) {
    echo json_encode(['success' => false, 'message' => 'Action tidak boleh kosong!']);
    exit;
}

$destinasi = new Destinasi($pdo);
$result    = [];

switch ($action) {

    case 'statistik':
        $result = $destinasi->ambilData();
        break;

    case 'tren_bulanan':
        $result = $destinasi->trenBulanan();
        break;

    default:
        $result = ['success' => false, 'message' => 'Action tidak dikenal!'];
        break;
}

echo json_encode($result);
?>