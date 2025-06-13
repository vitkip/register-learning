<?php
// ດຶງຂໍ້ມູນນັກສຶກສາຕາມ ID ທີ່ສົ່ງມາ
require_once __DIR__ . '/../src/classes/Student.php';
require_once __DIR__ . '/../src/classes/Major.php';
require_once __DIR__ . '/../src/classes/AcademicYear.php';

// ກວດສອບວ່າມີ ID ຖືກສົ່ງມາຫຼືບໍ່
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error'] = "ບໍ່ພົບຂໍ້ມູນນັກສຶກສາທີ່ຕ້ອງການ";
    header("Location: index.php?page=students");
    exit;
}

$student_id = (int)$_GET['id'];

// ສ້າງການເຊື່ອມຕໍ່ກັບຖານຂໍ້ມູນ
$database = new Database();
$db = $database->getConnection();

// ສ້າງອັອບເຈັກ Student
$student = new Student($db);

// ດຶງຂໍ້ມູນນັກສຶກສາຕາມ ID
$studentData = $student->readOne($student_id);

if (!$studentData) {
    $_SESSION['error'] = "ບໍ່ພົບຂໍ້ມູນນັກສຶກສາ ID: " . $student_id;
    header("Location: index.php?page=students");
    exit;
}

// ດຶງຂໍ້ມູນສາຂາ
$majorObj = new Major($db);
// เปลี่ยนจาก readById เป็น readOne หรือชื่อเมธอดที่ถูกต้องในคลาส Major
$major = $majorObj->readOne($studentData['major_id']);

// ດຶງຂໍ້ມູນປີການສຶກສາ
$yearObj = new AcademicYear($db);
// เปลี่ยนจาก readById เป็น readOne
$academicYear = $yearObj->readOne($studentData['academic_year_id']);

// ລວມເອົາຂໍ້ຄວາມແຈ້ງເຕືອນ
include_once __DIR__ . '/includes/messages.php';
?>

<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">ລາຍລະອຽດນັກສຶກສາ</h2>
        <div>
            <a href="<?php echo 'index.php?page=student-edit&id=' . $student_id; ?>" class="bg-blue-500 hover:bg-blue-700 text-white py-2 px-4 rounded mr-2">
                ແກ້ໄຂຂໍ້ມູນ
            </a>
            <a href="index.php?page=students" class="bg-gray-500 hover:bg-gray-700 text-white py-2 px-4 rounded">
                ກັບຄືນ
            </a>
        </div>
    </div>

    <div class="grid md:grid-cols-3 gap-6">
        <!-- ຮູບນັກສຶກສາ -->
        <div class="text-center">
            <?php if (!empty($studentData['photo']) && file_exists("../public/assets/uploads/photos/" . $studentData['photo'])): ?>
                <img src="<?= BASE_URL ?>public/assets/uploads/photos/<?php echo htmlspecialchars($studentData['photo']); ?>" 
                     alt="ຮູບນັກສຶກສາ" 
                     class="w-48 h-48 object-cover rounded-lg mx-auto shadow-md">
            <?php else: ?>
                <div class="w-48 h-48 bg-gray-200 flex items-center justify-center rounded-lg mx-auto">
                    <span class="text-gray-500">ບໍ່ມີຮູບ</span>
                </div>
            <?php endif; ?>
            <p class="mt-4 text-xl font-bold"><?php echo htmlspecialchars($studentData['first_name'] . ' ' . $studentData['last_name']); ?></p>
            <p class="text-gray-600">ລະຫັດນັກສຶກສາ: <?php echo htmlspecialchars($studentData['id']); ?></p>
        </div>

        <!-- ຂໍ້ມູນທົ່ວໄປ -->
        <div class="md:col-span-2">
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="text-lg font-semibold mb-4">ຂໍ້ມູນພື້ນຖານ</h3>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">ຊື່:</p>
                        <p class="font-medium"><?php echo htmlspecialchars($studentData['first_name']); ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">ນາມສະກຸນ:</p>
                        <p class="font-medium"><?php echo htmlspecialchars($studentData['last_name']); ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">ເພດ:</p>
                        <p class="font-medium"><?php echo htmlspecialchars($studentData['gender']); ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">ວັນເດືອນປີເກີດ:</p>
                        <p class="font-medium"><?php echo htmlspecialchars($studentData['dob']); ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">ອີເມວ:</p>
                        <p class="font-medium"><?php echo htmlspecialchars($studentData['email'] ?? '-'); ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">ເບີໂທ:</p>
                        <p class="font-medium">
                            <?php if(!empty($studentData['phone'])): ?>
                                <a href="tel:<?php echo htmlspecialchars($studentData['phone']); ?>" class="text-blue-600 hover:underline">
                                    <?php echo htmlspecialchars($studentData['phone']); ?>
                                </a>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 p-4 rounded-lg mt-4">
                <h3 class="text-lg font-semibold mb-4">ຂໍ້ມູນທີ່ຢູ່</h3>
                
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">ບ້ານ:</p>
                        <p class="font-medium"><?php echo htmlspecialchars($studentData['village'] ?? '-'); ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">ເມືອງ:</p>
                        <p class="font-medium"><?php echo htmlspecialchars($studentData['district'] ?? '-'); ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">ແຂວງ:</p>
                        <p class="font-medium"><?php echo htmlspecialchars($studentData['province'] ?? '-'); ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">ທີ່ພັກອາໃສ:</p>
                        <p class="font-medium"><?php echo htmlspecialchars($studentData['accommodation_type'] ?? '-'); ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 p-4 rounded-lg mt-4">
                <h3 class="text-lg font-semibold mb-4">ຂໍ້ມູນການສຶກສາ</h3>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">ໂຮງຮຽນເດີມ:</p>
                        <p class="font-medium"><?php echo htmlspecialchars($studentData['previous_school'] ?? '-'); ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">ສາຂາຮຽນ:</p>
                        <p class="font-medium"><?php echo htmlspecialchars($major['name'] ?? '-'); ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">ປີການສຶກສາ:</p>
                        <p class="font-medium"><?php echo htmlspecialchars($academicYear['year'] ?? '-'); ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">ວັນທີລົງທະບຽນ:</p>
                        <p class="font-medium">
                            <?php 
                            if (!empty($studentData['registered_at'])) {
                                echo date('d/m/Y H:i', strtotime($studentData['registered_at']));
                            } else {
                                echo '-';
                            }
                            ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>