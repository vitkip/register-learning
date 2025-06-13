<?php
// filepath: c:\xampp\htdocs\register-learning\templates\registration-success.php

// นำเข้าคลาสที่จำเป็น
require_once __DIR__ . '/../src/classes/Major.php';
require_once __DIR__ . '/../src/classes/AcademicYear.php';

// ກວດສອບວ່າເຮົາມີຂໍ້ມູນນັກສຶກສາທີ່ຈຳເປັນຫຼືບໍ່
if (!isset($studentData) || empty($studentData)) {
    $_SESSION['error'] = "ບໍ່ພົບຂໍ້ມູນນັກສຶກສາ";
    header("Location: index.php?page=register");
    exit;
}

// ດຶງຂໍ້ມູນເພີ່ມເຕີມ (ຊື່ສາຂາ, ປີການສຶກສາ)
$majorObj = new Major($db);
$major = $majorObj->readOne($studentData['major_id']);

$yearObj = new AcademicYear($db);
$academicYear = $yearObj->readOne($studentData['academic_year_id']);

// ສ້າງ QR Code
require_once __DIR__ . '/../src/classes/QrCodeGenerator.php';
$qrCode = QrCodeGenerator::generateStudentQrCode($studentData);
$fileName = QrCodeGenerator::saveStudentQrCode($studentData);

// ລວມເອົາຂໍ້ຄວາມແຈ້ງເຕືອນ
include_once __DIR__ . '/includes/messages.php';
?>
<?php include 'components/header.php'; ?>
<div class="max-w-3xl mx-auto bg-white p-8 rounded-lg shadow-md">
    <div class="text-center mb-8">
        <div class="inline-block p-2 rounded-full bg-green-100 mb-4">
            <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-gray-800">ການລົງທະບຽນສຳເລັດແລ້ວ</h1>
        <p class="text-gray-600 mt-2">ການລົງທະບຽນຂອງທ່ານໄດ້ຖືກບັນທຶກເຂົ້າໃນລະບົບແລ້ວ</p>
    </div>
    
    <div class="grid md:grid-cols-2 gap-8 items-center">
        <!-- ຂໍ້ມູນນັກສຶກສາ -->
        <div class="space-y-4">
            <div class="bg-blue-50 p-4 rounded-lg">
                <h2 class="text-lg font-semibold mb-3">ຂໍ້ມູນນັກສຶກສາ</h2>
                <div class="space-y-2">
                    <div class="flex">
                        <span class="font-medium w-32 text-gray-600">ລະຫັດນັກສຶກສາ:</span>
                        <span class="text-gray-800"><?= htmlspecialchars($studentData['id']) ?></span>
                    </div>
                    <div class="flex">
                        <span class="font-medium w-32 text-gray-600">ຊື່-ນາມສະກຸນ:</span>
                        <span class="text-gray-800"><?= htmlspecialchars($studentData['first_name'] . ' ' . $studentData['last_name']) ?></span>
                    </div>
                    <div class="flex">
                        <span class="font-medium w-32 text-gray-600">ເພດ:</span>
                        <span class="text-gray-800"><?= htmlspecialchars($studentData['gender']) ?></span>
                    </div>
                    <div class="flex">
                        <span class="font-medium w-32 text-gray-600">ສາຂາ:</span>
                        <span class="text-gray-800"><?= htmlspecialchars($major['name']) ?></span>
                    </div>
                    <div class="flex">
                        <span class="font-medium w-32 text-gray-600">ປີການສຶກສາ:</span>
                        <span class="text-gray-800"><?= htmlspecialchars($academicYear['year']) ?></span>
                    </div>
                </div>
            </div>
            
            <div class="bg-yellow-50 p-4 rounded-lg">
                <h2 class="text-lg font-semibold mb-2">ຄຳແນະນຳ</h2>
                <p class="text-sm text-gray-700">
                    ກະລຸນາບັນທຶກ QR Code ນີ້ເອົາໄວ້ ຫຼື ສະແດງຕໍ່ເຈົ້າໜ້າທີເພື່ອຢືນຢັນການລົງທະບຽນຂອງທ່ານ.
                    ທ່ານສາມາດດາວໂຫລດ QR Code ຫຼື ບັນທຶກໜ້ານີ້ໄວ້.
                </p>
            </div>
            
            <div class="flex justify-center md:justify-start space-x-4 pt-4">
                <a href="<?= BASE_URL ?>public/assets/uploads/qrcodes/<?= $fileName ?>" download 
                   class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded flex items-center transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    ດາວໂຫລດ QR Code
                </a>
                <a href="<?= url('register') ?>"
                   class="bg-gray-600 hover:bg-gray-700 text-white py-2 px-4 rounded flex items-center transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    ລົງທະບຽນເພີ່ມ
                </a>
            </div>
        </div>
        
        <!-- QR Code -->
              
        <div class="flex flex-col items-center">
            <div class="bg-white p-4 border-4 border-blue-200 rounded-lg shadow-md">
                <img src="<?= $qrCode['data_url'] ?>" alt="QR Code" class="w-64 h-64">
            </div>
            <p class="mt-3 text-sm text-center text-gray-500">ລະຫັດ QR Code ສຳລັບນັກສຶກສາ<br/><?= htmlspecialchars($studentData['first_name'] . ' ' . $studentData['last_name']) ?></p>
            <p class="mt-1 text-xs text-center text-blue-600">
                <a href="<?= $qrCode['url'] ?>" target="_blank">ເບິ່ງຂໍ້ມູນນັກສຶກສາ</a>
            </p>
        </div>
    </div>
    
    <div class="mt-10 border-t pt-6 text-center">
        <p class="text-sm text-gray-500">ທຸລະກຳເລກທີ: <?= uniqid('REG-') ?> | ວັນທີລົງທະບຽນ: <?= date('d/m/Y H:i:s') ?></p>
    </div>
</div>
<?php include 'components/footer.php'; ?>
<!-- ສະຄຣິບລິ້ງຂໍ້ມູນ QR Code (ຖ້າຕ້ອງການ) -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ຖ້າຕ້ອງການເພີ່ມຄວາມສາມາດພິເສດເກີ່ຍວກັບ QR code
    console.log('QR Code generated successfully!');
});
</script>