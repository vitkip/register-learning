<?php
// filepath: c:\xampp\htdocs\register-learning\templates\students-list.php

// ตรวจสอบว่ามีการเรียกใช้ผ่าน index.php หรือไม่
if (!defined('BASE_PATH')) {
    header('Location: ../public/index.php?page=students');
    exit('Access denied. Please use proper navigation.');
}

// ตรวจสอบการเชื่อมต่อฐานข้อมูล
if (!isset($db) || !$db) {
    die('Database connection not available');
}

// ตรวจสอบว่ามีตัวแปร $students จาก index.php หรือไม่
if (!isset($students)) {
    die('Students data not available');
}

// กำหนดค่าเริ่มต้นสำหรับตัวแปรที่จำเป็น
$current_search = $current_search ?? '';
$current_major = $current_major ?? 0;
$current_year = $current_year ?? 0;
$current_page = $current_page ?? 1;
$total_students = $total_students ?? 0;
$total_pages = $total_pages ?? 1;
$students_per_page = $students_per_page ?? 10;
$majors = $majors ?? [];
$academicYears = $academicYears ?? [];

// นำเข้าไฟล์ helper functions
if (file_exists(BASE_PATH . '/src/helpers/functions.php')) {
    require_once BASE_PATH . '/src/helpers/functions.php';
}
?>

<div class="container mx-auto px-4 py-8">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-8">
        <div>
            <h1 class="text-4xl font-bold text-gray-800 mb-2">
                <i class="fas fa-users text-amber-500 mr-3"></i>
                ລາຍຊື່ນັກສຶກສາ
            </h1>
            <p class="text-gray-600">
                ທັງໝົດ <span class="font-semibold text-amber-600"><?= $total_students ?></span> ລາຍການ
                <?php if (!empty($current_search) || $current_major > 0 || $current_year > 0): ?>
                    <span class="text-sm text-gray-500">
                        (ຜົນການຄົ້ນຫາ)
                    </span>
                <?php endif; ?>
            </p>
        </div>
        <div class="mt-4 md:mt-0">
            <a href="<?= BASE_URL ?>index.php?page=register" 
               class="bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white font-bold py-3 px-6 rounded-xl shadow-lg transition-all duration-300 transform hover:scale-105 inline-flex items-center">
                <i class="fas fa-user-plus mr-2"></i> 
                ລົງທະບຽນນັກສຶກສາໃໝ່
            </a>
        </div>
    </div>

    <!-- Current Filters Display -->
    <?php if (!empty($current_search) || $current_major > 0 || $current_year > 0): ?>
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6">
            <div class="flex flex-wrap items-center gap-3">
                <span class="text-blue-800 font-semibold">
                    <i class="fas fa-filter mr-2"></i>ຕົວກອງທີ່ໃຊ້:
                </span>
                
                <?php if (!empty($current_search)): ?>
                    <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm">
                        <i class="fas fa-search mr-1"></i>
                        ຄົ້ນຫາ: "<?= htmlspecialchars($current_search) ?>"
                    </span>
                <?php endif; ?>
                
                <?php if ($current_major > 0): ?>
                    <?php 
                    $major_name = '';
                    foreach ($majors as $major) {
                        if ($major['id'] == $current_major) {
                            $major_name = $major['name'];
                            break;
                        }
                    }
                    ?>
                    <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm">
                        <i class="fas fa-graduation-cap mr-1"></i>
                        ສາຂາ: <?= htmlspecialchars($major_name) ?>
                    </span>
                <?php endif; ?>
                
                <?php if ($current_year > 0): ?>
                    <?php 
                    $year_name = '';
                    foreach ($academicYears as $year) {
                        if ($year['id'] == $current_year) {
                            $year_name = $year['year'];
                            break;
                        }
                    }
                    ?>
                    <span class="bg-purple-100 text-purple-800 px-3 py-1 rounded-full text-sm">
                        <i class="fas fa-calendar-alt mr-1"></i>
                        ປີການສຶກສາ: <?= htmlspecialchars($year_name) ?>
                    </span>
                <?php endif; ?>
                
                <a href="<?= BASE_URL ?>index.php?page=students" 
                   class="text-red-600 hover:text-red-800 text-sm font-medium">
                    <i class="fas fa-times mr-1"></i>ລ້າງຕົວກອງ
                </a>
            </div>
        </div>
    <?php endif; ?>

    <!-- Search and Filter Section -->
    <div class="bg-white rounded-2xl shadow-xl p-6 mb-8">
        <form method="GET" action="<?= BASE_URL ?>index.php" class="space-y-4">
            <input type="hidden" name="page" value="students">
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Search Input -->
                <div class="md:col-span-2">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="text" 
                               name="search" 
                               value="<?= htmlspecialchars($current_search) ?>"
                               placeholder="ຄົ້ນຫາຊື່, ນາມສະກຸນ, ລະຫັດນັກສຶກສາ..."
                               class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all duration-200">
                    </div>
                </div>
                
                <!-- Major Filter -->
                <div>
                    <select name="major" 
                            class="block w-full py-3 px-4 border border-gray-300 rounded-xl leading-5 bg-white focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all duration-200">
                        <option value="0">ທຸກສາຂາ</option>
                        <?php if (!empty($majors)): ?>
                            <?php foreach ($majors as $major): ?>
                                <option value="<?= $major['id'] ?>" <?= $current_major == $major['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($major['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                
                <!-- Academic Year Filter -->
                <div>
                    <select name="year" 
                            class="block w-full py-3 px-4 border border-gray-300 rounded-xl leading-5 bg-white focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all duration-200">
                        <option value="0">ທຸກປີການສຶກສາ</option>
                        <?php if (!empty($academicYears)): ?>
                            <?php foreach ($academicYears as $year): ?>
                                <option value="<?= $year['id'] ?>" <?= $current_year == $year['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($year['year']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
            </div>
            
            <div class="flex flex-col sm:flex-row gap-3 pt-2">
                <button type="submit" 
                        class="bg-amber-500 hover:bg-amber-600 text-white font-medium py-3 px-6 rounded-xl transition-all duration-200 flex items-center justify-center">
                    <i class="fas fa-search mr-2"></i>
                    ຄົ້ນຫາ
                </button>
                <a href="<?= BASE_URL ?>index.php?page=students" 
                   class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-3 px-6 rounded-xl transition-all duration-200 flex items-center justify-center">
                    <i class="fas fa-undo mr-2"></i>
                    ລ້າງຕົວກອງ
                </a>
                <button type="button" 
                        onclick="exportToExcel()"
                        class="bg-green-500 hover:bg-green-600 text-white font-medium py-3 px-6 rounded-xl transition-all duration-200 flex items-center justify-center">
                    <i class="fas fa-file-excel mr-2"></i>
                    ສົ່ງອອກ Excel
                </button>
            </div>
        </form>
    </div>

    <?php if (!empty($students)): ?>
        <!-- Students Table -->
        <div class="bg-white shadow-2xl rounded-2xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full leading-normal">
                    <thead>
                        <tr class="bg-gradient-to-r from-amber-500 to-orange-500 text-white text-left text-sm font-bold uppercase tracking-wider">
                            <th class="px-6 py-4">ລຳດັບ</th>
                            <th class="px-6 py-4">ຮູບພາບ</th>
                            <th class="px-6 py-4">ເພດ</th>
                            <th class="px-6 py-4">ຊື່ ແລະ ນາມສະກຸນ</th>
                            <th class="px-6 py-4">ເບີໂທ</th>
                            <th class="px-6 py-4">ສາຂາວິຊາ</th>
                            <th class="px-6 py-4">ສົກຮຽນ</th>
                            <th class="px-6 py-4">ຈົບຈາກ</th>
                            <th class="px-6 py-4">ທີ່ພັກ</th>
                            <th class="px-6 py-4 text-center">ການກະທຳ</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700">
                        <?php 
                        // คำนวณลำดับที่ถูกต้องสำหรับ pagination
                        $count = ($current_page - 1) * $students_per_page + 1;
                        ?>
                        <?php foreach ($students as $student_row): ?>
                            <tr class="border-b border-gray-200 hover:bg-amber-50 transition-all duration-200">
                                <td class="px-6 py-5 text-sm font-bold text-amber-600"><?= $count++ ?></td>
                                
                                <!-- รูปภาพ -->
                                <td class="px-6 py-5">
                                    <?php 
                                        $photo_path = BASE_PATH . '/public/uploads/photos/';
                                        $photo_url = BASE_URL . 'public/uploads/photos/';
                                        
                                        if (!empty($student_row['photo']) && file_exists($photo_path . $student_row['photo'])): 
                                    ?>
                                        <img src="<?= $photo_url . htmlspecialchars($student_row['photo']) ?>" 
                                             alt="ຮູບນັກສຶກສາ" 
                                             class="w-14 h-14 rounded-full object-cover border-3 border-amber-200 shadow-md hover:scale-110 transition-transform duration-200">
                                    <?php else: ?>
                                        <div class="w-14 h-14 rounded-full bg-gradient-to-br from-amber-100 to-orange-100 flex items-center justify-center text-amber-600 border-3 border-amber-200 shadow-md">
                                            <i class="fas fa-user text-xl"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                
                                <!-- เพศ -->
                                <td class="px-6 py-5 text-sm">
                                    <span class="px-3 py-1 text-xs font-bold rounded-full shadow-sm <?= 
                                        $student_row['gender'] === 'ຊາຍ' ? 'bg-blue-100 text-blue-800 border border-blue-200' : 
                                        ($student_row['gender'] === 'ຍິງ' ? 'bg-pink-100 text-pink-800 border border-pink-200' : 
                                        ($student_row['gender'] === 'ພຣະ' ? 'bg-orange-100 text-orange-800 border border-orange-200' : 
                                        ($student_row['gender'] === 'ສາມະເນນ' ? 'bg-yellow-100 text-yellow-800 border border-yellow-200' : 'bg-gray-100 text-gray-800 border border-gray-200')))
                                    ?>">
                                        <?= htmlspecialchars($student_row['gender'] ?? 'N/A') ?>
                                    </span>
                                </td>
                                
                                <!-- ชื่อและนามสกุล -->
                                <td class="px-6 py-5">
                                    <div class="text-sm font-bold text-gray-900">
                                        <?= htmlspecialchars($student_row['first_name'] . ' ' . $student_row['last_name']) ?>
                                    </div>
                                    <?php if (!empty($student_row['student_id'])): ?>
                                        <div class="text-xs text-amber-600 font-semibold bg-amber-50 px-2 py-1 rounded-md mt-1 inline-block">
                                            <i class="fas fa-id-card mr-1"></i>
                                            <?= htmlspecialchars($student_row['student_id']) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                
                                <!-- เบอร์โทร -->
                                <td class="px-6 py-5 text-sm">
                                    <?php if (!empty($student_row['phone'])): ?>
                                        <a href="https://api.whatsapp.com/send?phone=<?= preg_replace('/[^0-9]/', '', $student_row['phone']) ?>" 
                                           target="_blank" 
                                           class="text-green-600 hover:text-green-800 hover:underline flex items-center font-semibold transition-colors duration-200">
                                            <i class="fab fa-whatsapp mr-2 text-lg"></i>
                                            <?= htmlspecialchars($student_row['phone']) ?>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-gray-400 italic">-</span>
                                    <?php endif; ?>
                                </td>
                                
                                <!-- สาขาวิชา -->
                                <td class="px-6 py-5 text-sm font-semibold text-gray-800">
                                    <?= htmlspecialchars($student_row['major_name'] ?? 'N/A') ?>
                                </td>
                                
                                <!-- ปีการศึกษา -->
                                <td class="px-6 py-5 text-sm font-semibold text-indigo-600">
                                    <?= htmlspecialchars($student_row['academic_year_name'] ?? $student_row['year'] ?? 'N/A') ?>
                                </td>
                                
                                <!-- โรงเรียนเดิม -->
                                <td class="px-6 py-5 text-sm text-gray-600">
                                    <?= htmlspecialchars($student_row['previous_school'] ?? 'N/A') ?>
                                </td>
                                
                                <!-- ที่พัก -->
                                <td class="px-6 py-5 text-sm">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full <?= 
                                        $student_row['accommodation_type'] === 'ມີວັດຢູ່ແລ້ວ' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800'
                                    ?>">
                                        <?= htmlspecialchars($student_row['accommodation_type'] ?? 'N/A') ?>
                                    </span>
                                </td>
                                
                                <!-- ปุ่มการดำเนินการ -->
                                <td class="px-6 py-5 text-sm text-center">
                                    <div class="flex items-center justify-center space-x-1">
                                        <!-- ปุ่มดู -->
                                        <a href="<?= BASE_URL ?>index.php?page=student-detail&id=<?= htmlspecialchars($student_row['id']) ?>" 
                                           class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded text-xs font-bold transition-all duration-200 transform hover:scale-105 inline-flex items-center shadow-md" 
                                           title="ເບິ່ງລາຍລະອຽດ">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        <!-- ปุ่มแก้ไข -->
                                        <a href="<?= BASE_URL ?>index.php?page=student-edit&id=<?= htmlspecialchars($student_row['id']) ?>" 
                                           class="bg-green-500 hover:bg-green-600 text-white px-2 py-1 rounded text-xs font-bold transition-all duration-200 transform hover:scale-105 inline-flex items-center shadow-md" 
                                           title="ແກ້ໄຂຂໍ້ມູນ">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        
                                        <!-- ปุ่มพิมพ์บัตร -->
                                        <a href="<?= BASE_URL ?>index.php?page=student-card&id=<?= htmlspecialchars($student_row['id']) ?>" 
                                           class="bg-purple-500 hover:bg-purple-600 text-white px-2 py-1 rounded text-xs font-bold transition-all duration-200 transform hover:scale-105 inline-flex items-center shadow-md" 
                                           title="ພິມບັດນັກສຶກສາ">
                                            <i class="fas fa-id-card"></i>
                                        </a>
                                        
                                        <!-- ปุ่มลบ -->
                                        <button onclick="confirmDelete(<?= htmlspecialchars($student_row['id']) ?>, '<?= htmlspecialchars($student_row['first_name'] . ' ' . $student_row['last_name']) ?>')" 
                                                class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded text-xs font-bold transition-all duration-200 transform hover:scale-105 inline-flex items-center shadow-md" 
                                                title="ລຶບຂໍ້ມູນ">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Page Size Selector -->
        <div class="text-center mt-6">
            <div class="inline-block bg-white rounded-xl shadow-lg p-4">
                <span class="text-sm text-gray-600 mr-3">ສະແດງຕໍ່ໜ້າ:</span>
                <?php 
                $page_sizes = [5, 10, 20, 50];
                foreach ($page_sizes as $size): 
                    // สร้าง URL สำหรับเปลี่ยน page size
                    $current_params = $_GET;
                    $current_params['students_per_page'] = $size;
                    $current_params['p'] = 1; // รีเซ็ตไปหน้าแรก
                    unset($current_params['page']); // ลบ page parameter ออก
                    $size_url = "?" . http_build_query($current_params + ['page' => 'students']);
                ?>
                    <a href="<?= BASE_URL ?>index.php<?= $size_url ?>" 
                       class="px-3 py-1 mx-1 text-sm font-medium <?= $students_per_page == $size ? 'bg-amber-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' ?> rounded-lg transition-all duration-200">
                        <?= $size ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Enhanced Pagination -->
        <?php if ($total_pages > 1): ?>
            <div class="mt-8">
                <!-- ข้อมูลสรุป -->
                <div class="text-center mb-6">
                    <div class="bg-white rounded-xl shadow-lg p-4 inline-block">
                        <div class="flex items-center space-x-4 text-sm text-gray-600">
                            <div class="flex items-center">
                                <i class="fas fa-list-ol text-amber-500 mr-2"></i>
                                <span>ໜ້າ <span class="font-bold text-amber-600"><?= $current_page ?></span> ຈາກ <span class="font-bold"><?= $total_pages ?></span></span>
                            </div>
                            <div class="w-px h-4 bg-gray-300"></div>
                            <div class="flex items-center">
                                <i class="fas fa-users text-blue-500 mr-2"></i>
                                <span>ທັງໝົດ <span class="font-bold text-blue-600"><?= $total_students ?></span> ລາຍການ</span>
                            </div>
                            <div class="w-px h-4 bg-gray-300"></div>
                            <div class="flex items-center">
                                <i class="fas fa-eye text-green-500 mr-2"></i>
                                <span>ກຳລັງສະແດງ <span class="font-bold text-green-600"><?= count($students) ?></span> ລາຍການ</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pagination Navigation -->
                <div class="flex justify-center items-center space-x-2 flex-wrap">
                    <?php
                    // สร้าง URL parameters
                    $url_params = [];
                    if (!empty($current_search)) $url_params[] = "search=" . urlencode($current_search);
                    if ($current_major > 0) $url_params[] = "major=" . $current_major;
                    if ($current_year > 0) $url_params[] = "year=" . $current_year;
                    if ($students_per_page != 10) $url_params[] = "students_per_page=" . $students_per_page;
                    $url_suffix = !empty($url_params) ? "&" . implode("&", $url_params) : "";
                    
                    // ปุ่ม First
                    if ($current_page > 1): ?>
                        <a href="<?= BASE_URL ?>index.php?page=students&p=1<?= $url_suffix ?>"
                           class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-all duration-200 transform hover:scale-105 shadow-md mb-2">
                            <i class="fas fa-angle-double-left"></i>
                        </a>
                    <?php else: ?>
                        <span class="px-4 py-2 text-sm font-medium text-gray-400 bg-gray-100 border border-gray-300 rounded-lg cursor-not-allowed shadow-md mb-2">
                            <i class="fas fa-angle-double-left"></i>
                        </span>
                    <?php endif; ?>

                    <?php
                    // ปุ่ม Previous
                    if ($current_page > 1): ?>
                        <a href="<?= BASE_URL ?>index.php?page=students&p=<?= $current_page - 1 ?><?= $url_suffix ?>"
                           class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-all duration-200 transform hover:scale-105 shadow-md mb-2">
                            <i class="fas fa-chevron-left mr-1"></i> ກ່ອນໜ້າ
                        </a>
                    <?php else: ?>
                        <span class="px-4 py-2 text-sm font-medium text-gray-400 bg-gray-100 border border-gray-300 rounded-lg cursor-not-allowed shadow-md mb-2">
                            <i class="fas fa-chevron-left mr-1"></i> ກ່ອນໜ້າ
                        </span>
                    <?php endif; ?>

                    <?php
                    // แสดงลิงก์หมายเลขหน้า
                    $num_links_to_show = 5;
                    $start_link = max(1, $current_page - floor($num_links_to_show / 2));
                    $end_link = min($total_pages, $start_link + $num_links_to_show - 1);

                    // ปรับ start_link ใหม่ถ้า end_link น้อยกว่าจำนวนที่ต้องการแสดง
                    if ($end_link - $start_link + 1 < $num_links_to_show) {
                        $start_link = max(1, $end_link - $num_links_to_show + 1);
                    }

                    if ($start_link > 1) {
                        echo '<a href="' . BASE_URL . 'index.php?page=students&p=1' . $url_suffix . '" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-all duration-200 transform hover:scale-105 shadow-md mb-2">1</a>';
                        if ($start_link > 2) {
                            echo '<span class="px-4 py-2 text-sm font-medium text-gray-500 mb-2">...</span>';
                        }
                    }

                    for ($i = $start_link; $i <= $end_link; $i++): ?>
                        <a href="<?= BASE_URL ?>index.php?page=students&p=<?= $i ?><?= $url_suffix ?>"
                           class="px-4 py-2 text-sm font-bold <?= ($i == $current_page) ? 'text-white bg-gradient-to-r from-amber-500 to-orange-500 shadow-lg transform scale-110' : 'text-gray-700 bg-white hover:bg-gray-50' ?> border border-gray-300 rounded-lg transition-all duration-200 transform hover:scale-105 shadow-md mb-2">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>

                    <?php
                    if ($end_link < $total_pages) {
                        if ($end_link < $total_pages - 1) {
                            echo '<span class="px-4 py-2 text-sm font-medium text-gray-500 mb-2">...</span>';
                        }
                        echo '<a href="' . BASE_URL . 'index.php?page=students&p=' . $total_pages . $url_suffix . '" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-all duration-200 transform hover:scale-105 shadow-md mb-2">' . $total_pages . '</a>';
                    }
                    ?>

                    <?php
                    // ปุ่ม Next
                    if ($current_page < $total_pages): ?>
                        <a href="<?= BASE_URL ?>index.php?page=students&p=<?= $current_page + 1 ?><?= $url_suffix ?>"
                           class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-all duration-200 transform hover:scale-105 shadow-md mb-2">
                            ຕໍ່ໄປ <i class="fas fa-chevron-right ml-1"></i>
                        </a>
                    <?php else: ?>
                         <span class="px-4 py-2 text-sm font-medium text-gray-400 bg-gray-100 border border-gray-300 rounded-lg cursor-not-allowed shadow-md mb-2">
                            ຕໍ່ໄປ <i class="fas fa-chevron-right ml-1"></i>
                        </span>
                    <?php endif; ?>

                    <?php
                    // ปุ่ม Last
                    if ($current_page < $total_pages): ?>
                        <a href="<?= BASE_URL ?>index.php?page=students&p=<?= $total_pages ?><?= $url_suffix ?>"
                           class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-all duration-200 transform hover:scale-105 shadow-md mb-2">
                            <i class="fas fa-angle-double-right"></i>
                        </a>
                    <?php else: ?>
                        <span class="px-4 py-2 text-sm font-medium text-gray-400 bg-gray-100 border border-gray-300 rounded-lg cursor-not-allowed shadow-md mb-2">
                            <i class="fas fa-angle-double-right"></i>
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

    <?php else: ?>
        <!-- Empty State -->
        <div class="bg-gradient-to-br from-yellow-50 to-orange-50 border-l-4 border-yellow-400 p-8 mt-6 rounded-r-2xl shadow-xl">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-yellow-400 text-3xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-2xl font-bold text-yellow-800 mb-2">
                        <?= !empty($current_search) || $current_major > 0 || $current_year > 0 ? 'ບໍ່ພົບຜົນການຄົ້ນຫາ' : 'ບໍ່ພົບຂໍ້ມູນນັກສຶກສາ' ?>
                    </h3>
                    <p class="text-yellow-700 mb-4">
                        <?php if (!empty($current_search) || $current_major > 0 || $current_year > 0): ?>
                            ລອງປ່ຽນເງື່ອນໄຂການຄົ້ນຫາ ຫຼື ລ້າງຕົວກອງແລ້ວລອງໃໝ່
                        <?php else: ?>
                            ບໍ່ມີນັກສຶກສາໃນລະບົບ ຫຼື ຍັງບໍ່ທັນມີການລົງທະບຽນ
                        <?php endif; ?>
                    </p>
                    <div class="flex space-x-3">
                        <?php if (!empty($current_search) || $current_major > 0 || $current_year > 0): ?>
                            <a href="<?= BASE_URL ?>index.php?page=students" 
                               class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-3 px-6 rounded-xl shadow-lg transition-all duration-300 transform hover:scale-105 inline-flex items-center">
                                <i class="fas fa-undo mr-2"></i> ລ້າງການຄົ້ນຫາ
                            </a>
                        <?php endif; ?>
                        <a href="<?= BASE_URL ?>index.php?page=register" 
                           class="bg-gradient-to-r from-green-500 to-blue-500 hover:from-green-600 hover:to-blue-600 text-white font-bold py-3 px-6 rounded-xl shadow-lg transition-all duration-300 transform hover:scale-105 inline-flex items-center">
                            <i class="fas fa-user-plus mr-2"></i> ລົງທະບຽນນັກສຶກສາໃໝ່
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
let studentIdToDelete = null;

function confirmDelete(studentId, studentName) {
    studentIdToDelete = studentId;
    
    Swal.fire({
        title: 'ຢືນຢັນການລຶບ',
        html: `ທ່ານແນ່ໃຈບໍ່ວ່າຕ້ອງການລຶບຂໍ້ມູນນັກສຶກສາ<br><strong class="text-red-600">${studentName}</strong><br><small class="text-red-500"><i class="fas fa-exclamation-triangle"></i> ການກະທຳນີ້ບໍ່ສາມາດຍົກເລີກໄດ້!</small>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: '<i class="fas fa-trash-alt"></i> ລຶບ',
        cancelButtonText: '<i class="fas fa-times"></i> ຍົກເລີກ',
        reverseButtons: true,
        focusCancel: true
    }).then((result) => {
        if (result.isConfirmed) {
            // แสดง loading
            Swal.fire({
                title: 'ກຳລັງລຶບ...',
                text: 'ກະລຸນາລໍຖ້າສັກຄູ່',
                icon: 'info',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // สร้าง URL สำหรับลบพร้อม pagination parameters
            const currentParams = new URLSearchParams(window.location.search);
            currentParams.set('action', 'delete');
            currentParams.set('id', studentIdToDelete);
            
            // ส่งไปยัง controller สำหรับการลบ
            window.location.href = `<?= BASE_URL ?>index.php?${currentParams.toString()}`;
        }
    });
}

function exportToExcel() {
    Swal.fire({
        title: 'ຂອບໃຈ!',
        text: 'ຟີເຈີນີ້ຈະພັດທະນາໃນອະນາຄົດ',
        icon: 'info',
        confirmButtonText: 'ຮູ້ແລ້ວ',
        confirmButtonColor: '#f59e0b'
    });
}

// Auto-submit form when changing filters
document.addEventListener('DOMContentLoaded', function() {
    const majorSelect = document.querySelector('select[name="major"]');
    const yearSelect = document.querySelector('select[name="year"]');
    
    majorSelect?.addEventListener('change', function() {
        this.form.submit();
    });
    
    yearSelect?.addEventListener('change', function() {
        this.form.submit();
    });
});
</script>