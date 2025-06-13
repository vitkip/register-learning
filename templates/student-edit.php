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

// ດຶງຂໍ້ມູນສາຂາທັງໝົດ
$majorObj = new Major($db);
$majors = $majorObj->readAll();

// ດຶງຂໍ້ມູນປີການສຶກສາທັງໝົດ
$yearObj = new AcademicYear($db);
$academicYears = $yearObj->readAll();

// ລວມເອົາຂໍ້ຄວາມແຈ້ງເຕືອນ
include_once __DIR__ . '/includes/messages.php';

// ປະມວນຜົນການອັບເດດຂໍ້ມູນ
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    // ຈະປະມວນຜົນໃນ index.php ດ້ວຍ action=update ເພື່ອໃຫ້ງ່າຍຕໍ່ການຈັດການ
}
?>

<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">ແກ້ໄຂຂໍ້ມູນນັກສຶກສາ</h2>
        <a href="index.php?page=student-detail&id=<?= $student_id ?>" class="bg-gray-500 hover:bg-gray-700 text-white py-2 px-4 rounded">
            ຍົກເລີກ
        </a>
    </div>
    
    <form action="index.php?page=student-edit&action=update&id=<?= $student_id ?>" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $student_id ?>">

        <div class="grid md:grid-cols-3 gap-6">
            <!-- ຮູບນັກສຶກສາ -->
            <div class="text-center">
                <?php if (!empty($studentData['photo']) && file_exists("../public/assets/uploads/photos/" . $studentData['photo'])): ?>
                    <img src="<?= BASE_URL ?>public/assets/uploads/photos/<?= htmlspecialchars($studentData['photo']) ?>" 
                         alt="ຮູບນັກສຶກສາ" 
                         id="photo-preview"
                         class="w-48 h-48 object-cover rounded-lg mx-auto shadow-md mb-3">
                <?php else: ?>
                    <div class="w-48 h-48 bg-gray-200 flex items-center justify-center rounded-lg mx-auto mb-3" id="photo-placeholder">
                        <span class="text-gray-500">ບໍ່ມີຮູບ</span>
                    </div>
                    <img src="" alt="ຮູບທີ່ເລືອກ" id="photo-preview" class="w-48 h-48 object-cover rounded-lg mx-auto shadow-md mb-3 hidden">
                <?php endif; ?>
                
                <div class="mt-2">
                    <label for="photo" class="block text-sm font-medium text-gray-700 mb-1">ອັບໂຫຼດຮູບໃໝ່</label>
                    <input type="file" name="photo" id="photo" class="block w-full text-sm text-gray-500
                        file:mr-4 file:py-2 file:px-4
                        file:rounded-full file:border-0
                        file:text-sm file:font-semibold
                        file:bg-blue-50 file:text-blue-700
                        hover:file:bg-blue-100"
                        accept="image/*">
                    <input type="hidden" name="current_photo" value="<?= htmlspecialchars($studentData['photo'] ?? '') ?>">
                </div>

                <script>
                    // Script ເພື່ອສະແດງຮູບທີ່ເລືອກ
                    document.getElementById('photo').addEventListener('change', function(e) {
                        const file = this.files[0];
                        if (file) {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                document.getElementById('photo-preview').src = e.target.result;
                                document.getElementById('photo-preview').classList.remove('hidden');
                                const placeholder = document.getElementById('photo-placeholder');
                                if (placeholder) placeholder.classList.add('hidden');
                            }
                            reader.readAsDataURL(file);
                        }
                    });
                </script>
            </div>

            <!-- ຂໍ້ມູນນັກສຶກສາ -->
            <div class="md:col-span-2 space-y-4">
                <!-- ຂໍ້ມູນພື້ນຖານ -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-semibold mb-4">ຂໍ້ມູນພື້ນຖານ</h3>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-gray-700">ຊື່</label>
                            <input type="text" name="first_name" id="first_name" required 
                                value="<?= htmlspecialchars($studentData['first_name']) ?>" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                        </div>
                        <div>
                            <label for="last_name" class="block text-sm font-medium text-gray-700">ນາມສະກຸນ</label>
                            <input type="text" name="last_name" id="last_name" required 
                                value="<?= htmlspecialchars($studentData['last_name']) ?>" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                        </div>

                        <div>
                            <label for="gender" class="block text-sm font-medium text-gray-700">ເພດ</label>
                            <select name="gender" id="gender" required 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                                <option value="ຊາຍ" <?= $studentData['gender'] === 'ຊາຍ' ? 'selected' : '' ?>>ຊາຍ</option>
                                <option value="ຍິງ" <?= $studentData['gender'] === 'ຍິງ' ? 'selected' : '' ?>>ຍິງ</option>
                            </select>
                        </div>

                        <div>
                            <label for="dob" class="block text-sm font-medium text-gray-700">ວັນເດືອນປີເກີດ</label>
                            <input type="date" name="dob" id="dob" required 
                                value="<?= htmlspecialchars($studentData['dob']) ?>" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">ອີເມວ</label>
                            <input type="email" name="email" id="email"
                                value="<?= htmlspecialchars($studentData['email'] ?? '') ?>" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700">ເບີໂທ</label>
                            <input type="text" name="phone" id="phone"
                                value="<?= htmlspecialchars($studentData['phone'] ?? '') ?>" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                        </div>
                    </div>
                </div>

                <!-- ຂໍ້ມູນທີ່ຢູ່ -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-semibold mb-4">ຂໍ້ມູນທີ່ຢູ່</h3>
                    
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label for="village" class="block text-sm font-medium text-gray-700">ບ້ານ</label>
                            <input type="text" name="village" id="village"
                                value="<?= htmlspecialchars($studentData['village'] ?? '') ?>" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                        </div>

                        <div>
                            <label for="district" class="block text-sm font-medium text-gray-700">ເມືອງ</label>
                            <input type="text" name="district" id="district"
                                value="<?= htmlspecialchars($studentData['district'] ?? '') ?>" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                        </div>

                        <div>
                            <label for="province" class="block text-sm font-medium text-gray-700">ແຂວງ</label>
                            <input type="text" name="province" id="province"
                                value="<?= htmlspecialchars($studentData['province'] ?? '') ?>" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                        </div>
                          
                        <div>
                            <label for="accommodation_type" class="block text-sm font-medium text-gray-700">ທີ່ພັກອາໄສ</label>
                            <select name="accommodation_type" id="accommodation_type" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                                <option value="ຫາວັດໃຫ້" <?= $studentData['accommodation_type'] === 'ຫາວັດໃຫ້' ? 'selected' : '' ?>>ຫາວັດໃຫ້</option>
                                <option value="ມີວັດຢູ່ແລ້ວ" <?= $studentData['accommodation_type'] === 'ມີວັດຢູ່ແລ້ວ' ? 'selected' : '' ?>>ມີວັດຢູ່ແລ້ວ</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- ຂໍ້ມູນການສຶກສາ -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-semibold mb-4">ຂໍ້ມູນການສຶກສາ</h3>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="previous_school" class="block text-sm font-medium text-gray-700">ໂຮງຮຽນເດີມ</label>
                            <input type="text" name="previous_school" id="previous_school"
                                value="<?= htmlspecialchars($studentData['previous_school'] ?? '') ?>" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                        </div>

                        <div>
                            <label for="major_id" class="block text-sm font-medium text-gray-700">ສາຂາຮຽນ</label>
                            <select name="major_id" id="major_id" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                                <?php foreach ($majors as $major): ?>
                                    <option value="<?= $major['id'] ?>" <?= $studentData['major_id'] == $major['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($major['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label for="academic_year_id" class="block text-sm font-medium text-gray-700">ປີການສຶກສາ</label>
                            <select name="academic_year_id" id="academic_year_id" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                                <?php foreach ($academicYears as $year): ?>
                                    <option value="<?= $year['id'] ?>" <?= $studentData['academic_year_id'] == $year['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($year['year']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end mt-6">
                    <button type="submit" name="update" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">
                        ບັນທຶກການປ່ຽນແປງ
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>