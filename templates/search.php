<!-- 
ຟາຍນີ້ໃຊ້ເພື່ອຄົ້ນຫານັກສຶກສາຕາມຊື່, ສາຂາ, ແລະປີການສຶກສາ 
-->

<?php
require_once '../config/database.php';
require_once '../src/classes/Student.php';
require_once '../src/classes/Major.php';
require_once '../src/classes/AcademicYear.php';

// ສ້າງການເຊື່ອມຕໍ່ຖານຂໍ້ມູນ
$database = new Database();
$db = $database->getConnection();

// ສ້າງອັອບເຈັກ Student
$student = new Student($db);

// ດຶງຂໍ້ມູນສາຂາແລະປີການສຶກສາສຳລັບຕົວກັ່ນຕອງ
$majorObj = new Major($db);
$majors = $majorObj->readAll();

$yearObj = new AcademicYear($db);
$academicYears = $yearObj->readAll();

// ສໍາລັບການຄົ້ນຫາ
$searchParams = [];
$name = '';
$majorId = '';
$academicYearId = '';

// ຮັບພາຣາມິເຕີການຄົ້ນຫາ
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $name = $_GET['search'];
    $searchParams['name'] = $name;
}

if (isset($_GET['major_id']) && !empty($_GET['major_id'])) {
    $majorId = $_GET['major_id'];
    $searchParams['major_id'] = $majorId;
}

if (isset($_GET['academic_year_id']) && !empty($_GET['academic_year_id'])) {
    $academicYearId = $_GET['academic_year_id'];
    $searchParams['academic_year_id'] = $academicYearId;
}

// ຄົ້ນຫານັກສຶກສາ
if (!empty($searchParams)) {
    $students = $student->search($searchParams);
} else {
    $students = [];
}
?>

   

    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">ຄົ້ນຫານັກສຶກສາ</h1>
        <form method="GET" class="mb-8 space-y-4">
            <input type="hidden" name="page" value="search">
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700">ຊື່ ຫຼື ນາມສະກຸນ</label>
                    <input type="text" name="search" id="search" value="<?= htmlspecialchars($name) ?>" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"
                        placeholder="ປ້ອນຊື່ຫຼືນາມສະກຸນ">
                </div>
                
                <div>
                    <label for="major_id" class="block text-sm font-medium text-gray-700">ສາຂາ</label>
                    <select name="major_id" id="major_id" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                        <option value="">-- ເລືອກສາຂາ --</option>
                        <?php foreach ($majors as $major): ?>
                            <option value="<?= $major['id'] ?>" <?= $majorId == $major['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($major['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label for="academic_year_id" class="block text-sm font-medium text-gray-700">ປີການສຶກສາ</label>
                    <select name="academic_year_id" id="academic_year_id" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                        <option value="">-- ເລືອກປີການສຶກສາ --</option>
                        <?php foreach ($academicYears as $year): ?>
                            <option value="<?= $year['id'] ?>" <?= $academicYearId == $year['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($year['year']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div>
                <button type="submit" 
                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">
                    ຄົ້ນຫາ
                </button>
                <a href="<?= url('search') ?>" 
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-opacity-50 ml-2">
                    ລ້າງການຄົ້ນຫາ
                </a>
            </div>
        </form>
        
        <!-- ຕາຕະລາງຜົນການຄົ້ນຫາ -->
        <?php if (!empty($searchParams)): ?>
            <?php if (!empty($students)): ?>
                <h3 class="text-lg font-semibold mb-3">ຜົນການຄົ້ນຫາ (<?= count($students) ?> ລາຍການ)</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white border border-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    ລະຫັດ
                                </th>
                               
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    ຊື່ ແລະ ນາມສະກຸນ
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    ສາຂາ
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    ປີການສຶກສາ
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    ຈັດການ
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php foreach ($students as $student): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900"><?= $student['id'] ?></div>

                                    </td>
                                     
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <?php if (!empty($student['photo'])): ?>
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <img class="h-10 w-10 rounded-full object-cover" 
                                                         src="<?= BASE_URL ?>public/uploads/photos/<?= $student['photo'] ?>" 
                                                         alt="<?= $student['first_name'] ?> <?= $student['last_name'] ?>">
                                                </div>
                                            <?php endif; ?>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    <?= $student['gender'] ?> <?= $student['first_name'] ?> <?= $student['last_name'] ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                  
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900"><?= $student['major_name'] ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900"><?= $student['academic_year'] ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="<?= url('student-detail', ['id' => $student['id']]) ?>" class="text-blue-600 hover:text-blue-900 mr-3">ລາຍລະອຽດ</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded">
                    ບໍ່ພົບຂໍ້ມູນທີ່ຕ້ອງການຄົ້ນຫາ
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <?php include 'components/footer.php'; ?>
</body>
</html>