<div class="animate-fade-in">
    <!-- Header Section -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">
                    <i class="fas fa-tachometer-alt text-amber-500 mr-3"></i>
                    ໜ້າຫຼັກລະບົບ
                </h1>
                <p class="text-gray-600"><?= htmlspecialchars($systemName) ?></p>
                <p class="text-sm text-gray-500"><?= htmlspecialchars($schoolName) ?></p>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-500">ວັນທີ</p>
                <p class="text-lg font-semibold text-gray-800"><?= date('d/m/Y') ?></p>
                <p class="text-sm text-gray-500">ເວລາ: <?= date('H:i') ?></p>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Students -->
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">ນັກຮຽນທັງໝົດ</p>
                    <p class="text-3xl font-bold"><?= number_format($stats['total_students']) ?></p>
                    <p class="text-blue-100 text-xs">ນັກຮຽນປະຈຸບັນ: <?= number_format($stats['active_students']) ?></p>
                </div>
                <div class="bg-blue-400 rounded-full p-3">
                    <i class="fas fa-users text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Majors -->
        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">ສາຂາວິຊາ</p>
                    <p class="text-3xl font-bold"><?= number_format($stats['total_majors']) ?></p>
                    <p class="text-green-100 text-xs">ສາຂາທີ່ເປີດສອນ</p>
                </div>
                <div class="bg-green-400 rounded-full p-3">
                    <i class="fas fa-graduation-cap text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Subjects -->
        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium">ວິຊາເຮຍນ</p>
                    <p class="text-3xl font-bold"><?= number_format($stats['total_subjects']) ?></p>
                    <p class="text-purple-100 text-xs">ວິຊາທີ່ເປີດສອນ</p>
                </div>
                <div class="bg-purple-400 rounded-full p-3">
                    <i class="fas fa-book text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Enrollments -->
        <div class="bg-gradient-to-r from-amber-500 to-amber-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-amber-100 text-sm font-medium">ການລົງທະບຽນ</p>
                    <p class="text-3xl font-bold"><?= number_format($stats['total_enrollments']) ?></p>
                    <p class="text-amber-100 text-xs">ລາຍການທັງໝົດ</p>
                </div>
                <div class="bg-amber-400 rounded-full p-3">
                    <i class="fas fa-user-plus text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Gender Distribution and Recent Students -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Gender Distribution -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4">
                <i class="fas fa-chart-pie text-amber-500 mr-2"></i>
                ການແຈກແຍງຕາມເພດ
            </h3>
            
            <div class="space-y-4">
                <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                    <div class="flex items-center">
                        <div class="w-4 h-4 bg-blue-500 rounded-full mr-3"></div>
                        <span class="font-medium text-gray-700">ຊາຍ</span>
                    </div>
                    <span class="text-xl font-bold text-blue-600"><?= number_format($stats['male_students']) ?></span>
                </div>
                
                <div class="flex items-center justify-between p-3 bg-pink-50 rounded-lg">
                    <div class="flex items-center">
                        <div class="w-4 h-4 bg-pink-500 rounded-full mr-3"></div>
                        <span class="font-medium text-gray-700">ຍິງ</span>
                    </div>
                    <span class="text-xl font-bold text-pink-600"><?= number_format($stats['female_students']) ?></span>
                </div>
                
                <div class="flex items-center justify-between p-3 bg-orange-50 rounded-lg">
                    <div class="flex items-center">
                        <div class="w-4 h-4 bg-orange-500 rounded-full mr-3"></div>
                        <span class="font-medium text-gray-700">ພຣະ</span>
                    </div>
                    <span class="text-xl font-bold text-orange-600"><?= number_format($stats['monk_students']) ?></span>
                </div>
            </div>
        </div>

        <!-- Recent Students -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4">
                <i class="fas fa-clock text-green-500 mr-2"></i>
                ນັກຮຽນທີ່ລົງທະບຽນຫາກໍ່ນີ້
            </h3>
            
            <?php if (empty($recentStudents)): ?>
                <p class="text-gray-500 text-center py-4">ຍັງບໍ່ມີນັກຮຽນລົງທະບຽນ</p>
            <?php else: ?>
                <div class="space-y-3">
                    <?php foreach ($recentStudents as $student): ?>
                        <div class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <div class="w-10 h-10 bg-gradient-to-r from-amber-400 to-amber-500 rounded-full flex items-center justify-center text-white font-bold mr-3">
                                <?= substr($student['first_name'], 0, 1) ?>
                            </div>
                            <div class="flex-1">
                                <p class="font-medium text-gray-800">
                                    <?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?>
                                </p>
                                <p class="text-sm text-gray-500">
                                    <?= htmlspecialchars($student['major_name'] ?? 'ບໍ່ມີສາຂາ') ?>
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-gray-500">
                                    <?= date('d/m/Y', strtotime($student['created_at'])) ?>
                                </p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="mt-4 text-center">
                    <a href="<?= BASE_URL ?>students" class="text-amber-600 hover:text-amber-700 font-medium">
                        ເບິ່ງທັງໝົດ <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <h3 class="text-xl font-bold text-gray-800 mb-6">
            <i class="fas fa-bolt text-amber-500 mr-2"></i>
            ການກະທຳດ່ວນ
        </h3>
        
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
            <a href="<?= BASE_URL ?>students/create" class="flex flex-col items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors btn-hover">
                <i class="fas fa-user-plus text-3xl text-blue-500 mb-2"></i>
                <span class="text-sm font-medium text-blue-700">ເພີ່ມນັກຮຽນ</span>
            </a>
            
            <a href="<?= BASE_URL ?>majors/create" class="flex flex-col items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors btn-hover">
                <i class="fas fa-graduation-cap text-3xl text-green-500 mb-2"></i>
                <span class="text-sm font-medium text-green-700">ເພີ່ມສາຂາ</span>
            </a>
            
            <a href="<?= BASE_URL ?>subjects/create" class="flex flex-col items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors btn-hover">
                <i class="fas fa-book text-3xl text-purple-500 mb-2"></i>
                <span class="text-sm font-medium text-purple-700">ເພີ່ມວິຊາ</span>
            </a>
            
            <a href="<?= BASE_URL ?>enrollments/create" class="flex flex-col items-center p-4 bg-amber-50 rounded-lg hover:bg-amber-100 transition-colors btn-hover">
                <i class="fas fa-user-plus text-3xl text-amber-500 mb-2"></i>
                <span class="text-sm font-medium text-amber-700">ລົງທະບຽນ</span>
            </a>
            
            <a href="<?= BASE_URL ?>grades" class="flex flex-col items-center p-4 bg-red-50 rounded-lg hover:bg-red-100 transition-colors btn-hover">
                <i class="fas fa-star text-3xl text-red-500 mb-2"></i>
                <span class="text-sm font-medium text-red-700">ບັນທຶກຄະແນນ</span>
            </a>
            
            <a href="<?= BASE_URL ?>settings" class="flex flex-col items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors btn-hover">
                <i class="fas fa-cog text-3xl text-gray-500 mb-2"></i>
                <span class="text-sm font-medium text-gray-700">ການຕັ້ງຄ່າ</span>
            </a>
        </div>
    </div>
</div>