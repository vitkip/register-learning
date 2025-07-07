<?php
// filepath: c:\xampp\htdocs\register-learning\templates\registration-success.php

// ตรวจสอบว่ามีการเรียกใช้ผ่าน index.php หรือไม่
if (!defined('BASE_PATH')) {
    header('Location: ../public/index.php');
    exit('Access denied. Please use proper navigation.');
}

// ตรวจสอบการเชื่อมต่อฐานข้อมูล
if (!isset($db) || !$db) {
    die('Database connection not available');
}

// ตรวจสอบว่าเรามีข้อมูลนักศึกษาที่จำเป็นหรือไม่
if (!isset($studentData) || empty($studentData)) {
    $_SESSION['message'] = "ບໍ່ພົບຂໍ້ມູນນັກສຶກສາ";
    $_SESSION['message_type'] = "error";
    header("Location: " . BASE_URL . "index.php?page=register");
    exit;
}

// นำเข้าไฟล์ helper functions
if (file_exists(BASE_PATH . '/src/helpers/functions.php')) {
    require_once BASE_PATH . '/src/helpers/functions.php';
}

// ดึงข้อมูลเพิ่มเติม (ชื่อสาขา, ปีการศึกษา)
$major_name = 'N/A';
$academic_year_name = 'N/A';

try {
    if (file_exists(BASE_PATH . '/src/classes/Major.php')) {
        require_once BASE_PATH . '/src/classes/Major.php';
        $majorObj = new Major($db);
        $major = $majorObj->readOne($studentData['major_id']);
        if ($major) {
            $major_name = $major['name'];
        }
    }

    if (file_exists(BASE_PATH . '/src/classes/AcademicYear.php')) {
        require_once BASE_PATH . '/src/classes/AcademicYear.php';
        $yearObj = new AcademicYear($db);
        $academicYear = $yearObj->readOne($studentData['academic_year_id']);
        if ($academicYear) {
            $academic_year_name = $academicYear['year'];
        }
    }
} catch (Exception $e) {
    error_log("Error loading additional data: " . $e->getMessage());
}

// สร้าง QR Code data
$qr_data = "ນັກສຶກສາ: " . $studentData['first_name'] . " " . $studentData['last_name'] . "\n";
$qr_data .= "ລະຫັດ: " . ($studentData['student_id'] ?? 'N/A') . "\n";
$qr_data .= "ສາຂາ: " . $major_name . "\n";
$qr_data .= "ປີການສຶກສາ: " . $academic_year_name . "\n";
$qr_data .= "ວັນທີລົງທະບຽນ: " . date('d/m/Y H:i:s');

// สร้าง URL สำหรับ QR Code โดยใช้ API ฟรี
$qr_code_url = "https://api.qrserver.com/v1/create-qr-code/?size=400x400&format=png&margin=10&data=" . urlencode($qr_data);
?>

<!-- Success Animation CSS -->
<style>
@keyframes bounce {
    0%, 20%, 53%, 80%, 100% { transform: translate3d(0,0,0); }
    40%, 43% { transform: translate3d(0, -30px, 0); }
    70% { transform: translate3d(0, -15px, 0); }
    90% { transform: translate3d(0, -4px, 0); }
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-bounce {
    animation: bounce 1s ease-in-out;
}

.animate-fadeInUp {
    animation: fadeInUp 0.6s ease-out;
}

/* Print Styles */
@media print {
    body { background: white !important; }
    .bg-gradient-to-br { background: white !important; }
    .shadow-xl { box-shadow: none !important; }
    button, .hover\:scale-105 { display: none !important; }
    .print-hidden { display: none !important; }
}
</style>

<div class="min-h-screen bg-gradient-to-br from-amber-50 to-orange-100 py-8">
    <div class="container mx-auto px-4">
        <div class="max-w-6xl mx-auto">
            
            <!-- Header Section -->
            <div class="text-center mb-8 animate-fadeInUp">
                <div class="inline-block p-4 rounded-full bg-green-100 shadow-lg mb-6">
                    <i class="fas fa-check-circle text-6xl text-green-600 animate-bounce"></i>
                </div>
                <h1 class="text-4xl md:text-5xl font-bold text-gray-800 mb-4">
                    🎉 ການລົງທະບຽນສຳເລັດແລ້ວ!
                </h1>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    ຂໍສະແດງຄວາມຍິນດີ! ການລົງທະບຽນຂອງທ່ານໄດ້ຖືກບັນທຶກເຂົ້າໃນລະບົບສຳເລັດແລ້ວ
                </p>
                <div class="mt-4 inline-flex items-center px-4 py-2 bg-green-100 text-green-800 rounded-full text-sm font-medium">
                    <i class="fas fa-clock mr-2"></i>
                    ລົງທະບຽນເມື່ອ: <?= date('d/m/Y H:i:s') ?>
                </div>
            </div>

            <!-- Main Content -->
            <div class="grid lg:grid-cols-2 gap-8 items-start animate-fadeInUp">
                
                <!-- ຂໍ້ມູນນັກສຶກສາ -->
                <div class="space-y-6">
                    <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                        <div class="bg-gradient-to-r from-amber-500 to-orange-500 px-6 py-4">
                            <h2 class="text-2xl font-bold text-white flex items-center">
                                <i class="fas fa-user-graduate mr-3"></i>
                                ຂໍ້ມູນນັກສຶກສາ
                            </h2>
                        </div>
                        <div class="p-6 space-y-4">
                            <!-- Student ID Card Style -->
                            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-4 rounded-lg border-l-4 border-blue-500">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm text-gray-600">ລະຫັດນັກສຶກສາ</p>
                                        <p class="text-2xl font-bold text-blue-600 font-mono" id="student-id">
                                            <?= htmlspecialchars($studentData['student_id'] ?? 'N/A') ?>
                                        </p>
                                    </div>
                                    <div class="text-blue-500">
                                        <i class="fas fa-id-card text-3xl"></i>
                                    </div>
                                </div>
                            </div>

                            <!-- Personal Info Grid -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-3">
                                    <div class="flex items-start">
                                        <div class="w-8 h-8 rounded-full bg-amber-100 flex items-center justify-center mr-3 mt-0.5">
                                            <i class="fas fa-user text-amber-600 text-sm"></i>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-sm text-gray-600">ຊື່-ນາມສະກຸນ</p>
                                            <p class="font-semibold text-gray-800" id="student-name">
                                                <?= htmlspecialchars($studentData['first_name'] . ' ' . $studentData['last_name']) ?>
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex items-start">
                                        <div class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center mr-3 mt-0.5">
                                            <i class="fas fa-venus-mars text-purple-600 text-sm"></i>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-sm text-gray-600">ເພດ</p>
                                            <span class="inline-block px-3 py-1 text-sm font-medium rounded-full <?= 
                                                $studentData['gender'] === 'ຊາຍ' ? 'bg-blue-100 text-blue-800' : 
                                                ($studentData['gender'] === 'ຍິງ' ? 'bg-pink-100 text-pink-800' : 
                                                ($studentData['gender'] === 'ພຣະ' ? 'bg-orange-100 text-orange-800' : 'bg-gray-100 text-gray-800'))
                                            ?>">
                                                <?= htmlspecialchars($studentData['gender']) ?>
                                            </span>
                                        </div>
                                    </div>

                                    <?php if (!empty($studentData['dob'])): ?>
                                    <div class="flex items-start">
                                        <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center mr-3 mt-0.5">
                                            <i class="fas fa-birthday-cake text-green-600 text-sm"></i>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-sm text-gray-600">ວັນເກິດ</p>
                                            <p class="font-semibold text-gray-800">
                                                <?= htmlspecialchars(date('d/m/Y', strtotime($studentData['dob']))) ?>
                                            </p>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                </div>

                                <div class="space-y-3">
                                    <?php if (!empty($studentData['phone'])): ?>
                                    <div class="flex items-start">
                                        <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center mr-3 mt-0.5">
                                            <i class="fas fa-phone text-green-600 text-sm"></i>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-sm text-gray-600">ເບອໂທ</p>
                                            <a href="https://api.whatsapp.com/send?phone=<?= preg_replace('/[^0-9]/', '', $studentData['phone']) ?>" 
                                               target="_blank" 
                                               class="font-semibold text-green-600 hover:text-green-800 flex items-center">
                                                <i class="fab fa-whatsapp mr-1"></i>
                                                <?= htmlspecialchars($studentData['phone']) ?>
                                            </a>
                                        </div>
                                    </div>
                                    <?php endif; ?>

                                    <div class="flex items-start">
                                        <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center mr-3 mt-0.5">
                                            <i class="fas fa-graduation-cap text-blue-600 text-sm"></i>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-sm text-gray-600">ສາຂາວິຊາ</p>
                                            <p class="font-semibold text-gray-800" id="major-name"><?= htmlspecialchars($major_name) ?></p>
                                        </div>
                                    </div>

                                    <div class="flex items-start">
                                        <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center mr-3 mt-0.5">
                                            <i class="fas fa-calendar-alt text-indigo-600 text-sm"></i>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-sm text-gray-600">ປີການສຶກສາ</p>
                                            <p class="font-semibold text-gray-800" id="academic-year"><?= htmlspecialchars($academic_year_name) ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Instructions Card -->
                    <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                        <div class="bg-gradient-to-r from-blue-500 to-cyan-500 px-6 py-4">
                            <h3 class="text-xl font-bold text-white flex items-center">
                                <i class="fas fa-info-circle mr-3"></i>
                                ຄຳແນະນຳສຳຄັນ
                            </h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-3 text-gray-700">
                                <div class="flex items-start">
                                    <i class="fas fa-check-circle text-green-500 mr-3 mt-1"></i>
                                    <p>ກະລຸນາບັນທຶກ QR Code ນີ້ເອົາໄວ້ສຳລັບການຢືນຢັນຕົວຕົນ</p>
                                </div>
                                <div class="flex items-start">
                                    <i class="fas fa-check-circle text-green-500 mr-3 mt-1"></i>
                                    <p>ສະແດງ QR Code ຕໍ່ເຈົ້າໜ້າທີເພື່ອຢືນຢັນການລົງທະບຽນ</p>
                                </div>
                                <div class="flex items-start">
                                    <i class="fas fa-check-circle text-green-500 mr-3 mt-1"></i>
                                    <p>ທ່ານສາມາດດາວໂຫລດ QR Code ຫຼື ບັນທຶກໜ້ານີ້ໄວ້</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 print-hidden">
                        <a href="<?= $qr_code_url ?>" target="_blank" download="qrcode-<?= htmlspecialchars($studentData['student_id'] ?? 'student') ?>.png"
                           class="bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white py-4 px-6 rounded-xl flex items-center justify-center transition-all duration-300 transform hover:scale-105 shadow-lg">
                            <i class="fas fa-download mr-3 text-lg"></i>
                            <span class="font-medium">ດາວໂຫລດ QR Code</span>
                        </a>
                        <a href="<?= BASE_URL ?>index.php?page=register"
                           class="bg-gradient-to-r from-gray-600 to-gray-700 hover:from-gray-700 hover:to-gray-800 text-white py-4 px-6 rounded-xl flex items-center justify-center transition-all duration-300 transform hover:scale-105 shadow-lg">
                            <i class="fas fa-user-plus mr-3 text-lg"></i>
                            <span class="font-medium">ລົງທະບຽນເພີ່ມ</span>
                        </a>
                    </div>
                </div>
                
                <!-- QR Code Section -->
                <div class="flex flex-col items-center">
                    <div class="bg-white p-8 rounded-2xl shadow-xl">
                        <div class="text-center mb-6">
                            <h3 class="text-2xl font-bold text-gray-800 mb-2">QR Code ຂອງທ່ານ</h3>
                            <p class="text-gray-600">ສະແກນເພື່ອເບິ່ງຂໍ້ມູນ</p>
                        </div>
                        
                        <div class="relative">
                            <div class="bg-gradient-to-br from-amber-100 to-orange-100 p-6 rounded-2xl border-4 border-amber-200">
                                <img src="<?= $qr_code_url ?>" alt="QR Code" class="w-80 h-80 mx-auto" 
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                <div class="w-80 h-80 bg-gray-100 flex items-center justify-center text-gray-500 rounded-lg mx-auto" style="display: none;">
                                    <div class="text-center">
                                        <i class="fas fa-qrcode text-6xl mb-4"></i>
                                        <p class="text-lg font-medium">QR Code ບໍ່ສາມາດໂຫລດໄດ້</p>
                                        <p class="text-sm mt-2">ກະລຸນາລອງໃໝ່ອີກຄັ້ງ</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- QR Code Corner Decorations -->
                            <div class="absolute -top-2 -left-2 w-6 h-6 border-t-4 border-l-4 border-amber-500 rounded-tl-lg"></div>
                            <div class="absolute -top-2 -right-2 w-6 h-6 border-t-4 border-r-4 border-amber-500 rounded-tr-lg"></div>
                            <div class="absolute -bottom-2 -left-2 w-6 h-6 border-b-4 border-l-4 border-amber-500 rounded-bl-lg"></div>
                            <div class="absolute -bottom-2 -right-2 w-6 h-6 border-b-4 border-r-4 border-amber-500 rounded-br-lg"></div>
                        </div>
                        
                        <div class="text-center mt-6">
                            <p class="text-lg font-semibold text-gray-800">
                                <?= htmlspecialchars($studentData['first_name'] . ' ' . $studentData['last_name']) ?>
                            </p>
                            <p class="text-gray-600 mt-2">
                                <i class="fas fa-qrcode mr-2"></i>
                                ລະຫັດນັກສຶກສາ: <?= htmlspecialchars($studentData['student_id'] ?? 'N/A') ?>
                            </p>
                        </div>
                    </div>

                    <!-- Print Button -->
                    <button onclick="window.print()" 
                            class="mt-6 bg-gradient-to-r from-purple-500 to-indigo-500 hover:from-purple-600 hover:to-indigo-600 text-white py-3 px-8 rounded-xl flex items-center transition-all duration-300 transform hover:scale-105 shadow-lg print-hidden">
                        <i class="fas fa-print mr-3"></i>
                        <span class="font-medium">ພິມບັດນັກສຶກສາ</span>
                    </button>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="mt-12 bg-white rounded-2xl shadow-xl p-6">
                <div class="text-center border-t pt-6">
                    <div class="flex flex-col sm:flex-row items-center justify-center space-y-2 sm:space-y-0 sm:space-x-6 text-sm text-gray-500">
                        <div class="flex items-center">
                            <i class="fas fa-receipt mr-2"></i>
                            <span>ທຸລະກຳເລກທີ: <?= uniqid('REG-') ?></span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-calendar mr-2"></i>
                            <span>ວັນທີລົງທະບຽນ: <?= date('d/m/Y H:i:s') ?></span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-shield-alt mr-2"></i>
                            <span>ການລົງທະບຽນຖືກຢືນຢັນ</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-scroll to top for better UX
    window.scrollTo(0, 0);
    
    // แสดง SweetAlert2 เมื่อลงทะเบียนสำเร็จ
    <?php if (isset($showSuccessAlert) && $showSuccessAlert): ?>
    Swal.fire({
        title: '🎉 ລົງທະບຽນສຳເລັດ!',
        html: `
            <div class="text-center">
                <div class="mb-4">
                    <i class="fas fa-check-circle text-green-500 text-6xl mb-3"></i>
                </div>
                <h3 class="text-xl font-bold mb-3">ຍິນດີຕ້ອນຮັບສູ່ວິທະຍາໄລ!</h3>
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-4 rounded-lg border-l-4 border-blue-500 mb-4">
                    <p class="text-sm text-gray-600 mb-1">ລະຫັດນັກສຶກສາ</p>
                    <p class="text-2xl font-bold text-blue-600 font-mono"><?= htmlspecialchars($studentData['student_id'] ?? 'N/A') ?></p>
                </div>
                <div class="text-left space-y-2">
                    <p><i class="fas fa-user text-amber-500 mr-2"></i><strong>ຊື່:</strong> <?= htmlspecialchars($studentData['first_name'] . ' ' . $studentData['last_name']) ?></p>
                    <p><i class="fas fa-graduation-cap text-blue-500 mr-2"></i><strong>ສາຂາ:</strong> <?= htmlspecialchars($major_name) ?></p>
                    <p><i class="fas fa-calendar text-indigo-500 mr-2"></i><strong>ປີການສຶກສາ:</strong> <?= htmlspecialchars($academic_year_name) ?></p>
                </div>
                <div class="mt-4 p-3 bg-green-50 rounded-lg border border-green-200">
                    <p class="text-sm text-green-700">
                        <i class="fas fa-info-circle mr-1"></i>
                        ກະລຸນາບັນທຶກ QR Code ເພື່ອການຢືນຢັນຕົວຕົນ
                    </p>
                </div>
            </div>
        `,
        icon: 'success',
        confirmButtonText: 'ເບິ່ງ QR Code',
        confirmButtonColor: '#f59e0b',
        showCancelButton: true,
        cancelButtonText: 'ລົງທະບຽນເພີ່ມ',
        cancelButtonColor: '#6b7280',
        customClass: {
            popup: 'swal2-popup-large',
            title: 'text-2xl font-bold',
            htmlContainer: 'text-left'
        },
        showClass: {
            popup: 'animate__animated animate__fadeInDown'
        },
        hideClass: {
            popup: 'animate__animated animate__fadeOutUp'
        }
    }).then((result) => {
        if (result.isDismissed && result.dismiss === Swal.DismissReason.cancel) {
            // ไปหน้าลงทะเบียนใหม่
            window.location.href = '<?= BASE_URL ?>index.php?page=register';
        } else if (result.isConfirmed) {
            // Scroll ไปยัง QR Code
            document.querySelector('.grid.lg\\:grid-cols-2 .flex.flex-col.items-center').scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
            
            // Highlight QR Code section
            const qrSection = document.querySelector('.grid.lg\\:grid-cols-2 .flex.flex-col.items-center .bg-white.p-8');
            if (qrSection) {
                qrSection.classList.add('ring-4', 'ring-amber-300', 'ring-opacity-50');
                setTimeout(() => {
                    qrSection.classList.remove('ring-4', 'ring-amber-300', 'ring-opacity-50');
                }, 3000);
            }
        }
    });
    <?php endif; ?>
    
    console.log('Registration completed successfully!');
});

// เพิ่ม custom CSS สำหรับ SweetAlert2
const style = document.createElement('style');
style.textContent = `
    .swal2-popup-large {
        width: 600px !important;
        max-width: 90vw !important;
    }
    
    .swal2-popup {
        border-radius: 1rem !important;
    }
    
    .swal2-title {
        color: #1f2937 !important;
    }
    
    /* Animation classes */
    .animate__animated {
        animation-duration: 0.5s;
        animation-fill-mode: both;
    }
    
    .animate__fadeInDown {
        animation-name: fadeInDown;
    }
    
    .animate__fadeOutUp {
        animation-name: fadeOutUp;
    }
    
    @keyframes fadeInDown {
        from {
            opacity: 0;
            transform: translate3d(0, -100%, 0);
        }
        to {
            opacity: 1;
            transform: translate3d(0, 0, 0);
        }
    }
    
    @keyframes fadeOutUp {
        from {
            opacity: 1;
        }
        to {
            opacity: 0;
            transform: translate3d(0, -100%, 0);
        }
    }
`;
document.head.appendChild(style);
</script>