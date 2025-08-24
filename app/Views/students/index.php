<div class="animate-fade-in">
    <!-- Header -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">
                    <i class="fas fa-users text-blue-500 mr-3"></i>
                    ລາຍຊື່ນັກຮຽນ
                </h1>
                <p class="text-gray-600">ຈັດການຂໍ້ມູນນັກຮຽນທັງໝົດ</p>
            </div>
            <div>
                <a href="<?= BASE_URL ?>students/create" 
                   class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg font-medium btn-hover">
                    <i class="fas fa-plus mr-2"></i>ເພີ່ມນັກຮຽນໃໝ່
                </a>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
        <form method="GET" action="<?= BASE_URL ?>students" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4">
            <!-- Search -->
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-2">ຄົ້ນຫາ</label>
                <input type="text" id="search" name="search" 
                       value="<?= htmlspecialchars($filters['search']) ?>"
                       placeholder="ຊື່, ນາມສະກຸນ, ລະຫັດນັກຮຽນ"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Major Filter -->
            <div>
                <label for="major_id" class="block text-sm font-medium text-gray-700 mb-2">ສາຂາ</label>
                <select id="major_id" name="major_id" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <option value="">ທັງໝົດ</option>
                    <?php foreach ($majors as $major): ?>
                        <option value="<?= $major['id'] ?>" <?= $filters['major_id'] == $major['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($major['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Academic Year Filter -->
            <div>
                <label for="academic_year_id" class="block text-sm font-medium text-gray-700 mb-2">ປີການສຶກສາ</label>
                <select id="academic_year_id" name="academic_year_id" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <option value="">ທັງໝົດ</option>
                    <?php foreach ($academicYears as $year): ?>
                        <option value="<?= $year['id'] ?>" <?= $filters['academic_year_id'] == $year['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($year['year']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Gender Filter -->
            <div>
                <label for="gender" class="block text-sm font-medium text-gray-700 mb-2">ເພດ</label>
                <select id="gender" name="gender" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <option value="">ທັງໝົດ</option>
                    <option value="ຊາຍ" <?= $filters['gender'] === 'ຊາຍ' ? 'selected' : '' ?>>ຊາຍ</option>
                    <option value="ຍິງ" <?= $filters['gender'] === 'ຍິງ' ? 'selected' : '' ?>>ຍິງ</option>
                    <option value="ພຣະ" <?= $filters['gender'] === 'ພຣະ' ? 'selected' : '' ?>>ພຣະ</option>
                    <option value="ສ.ນ" <?= $filters['gender'] === 'ສ.ນ' ? 'selected' : '' ?>>ສ.ນ</option>
                </select>
            </div>

            <!-- Submit Button -->
            <div class="flex items-end">
                <button type="submit" 
                        class="w-full bg-amber-500 hover:bg-amber-600 text-white px-4 py-2 rounded-lg font-medium btn-hover">
                    <i class="fas fa-search mr-2"></i>ຄົ້ນຫາ
                </button>
            </div>
        </form>
    </div>

    <!-- Students Table -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <?php if (empty($students)): ?>
            <div class="p-8 text-center">
                <i class="fas fa-users text-gray-300 text-6xl mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-500 mb-2">ບໍ່ພົບຂໍ້ມູນນັກຮຽນ</h3>
                <p class="text-gray-400 mb-4">ກະລຸນາປ່ຽນເງື່ອນໄຂການຄົ້ນຫາ ຫຼື ເພີ່ມນັກຮຽນໃໝ່</p>
                <a href="<?= BASE_URL ?>students/create" 
                   class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg font-medium btn-hover">
                    <i class="fas fa-plus mr-2"></i>ເພີ່ມນັກຮຽນໃໝ່
                </a>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                ລະຫັດນັກຮຽນ
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                ຮູບ
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                ຊື່ - ນາມສະກຸນ
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                ເພດ
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                ສາຂາ
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                ປີການສຶກສາ
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                ການກະທຳ
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($students as $student): ?>
                            <tr class="hover:bg-gray-50 transition-colors duration-200">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    <?= htmlspecialchars($student['student_id'] ?? 'N/A') ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if ($student['photo']): ?>
                                        <img src="<?= BASE_URL ?>uploads/photos/<?= htmlspecialchars($student['photo']) ?>" 
                                             alt="ຮູບນັກຮຽນ" 
                                             class="w-10 h-10 rounded-full object-cover">
                                    <?php else: ?>
                                        <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center">
                                            <i class="fas fa-user text-gray-500"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        <?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?>
                                    </div>
                                    <?php if ($student['email']): ?>
                                        <div class="text-sm text-gray-500">
                                            <?= htmlspecialchars($student['email']) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        <?php
                                        switch ($student['gender']) {
                                            case 'ຊາຍ':
                                                echo 'bg-blue-100 text-blue-800';
                                                break;
                                            case 'ຍິງ':
                                                echo 'bg-pink-100 text-pink-800';
                                                break;
                                            case 'ພຣະ':
                                                echo 'bg-orange-100 text-orange-800';
                                                break;
                                            default:
                                                echo 'bg-gray-100 text-gray-800';
                                        }
                                        ?>">
                                        <?= htmlspecialchars($student['gender']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?= htmlspecialchars($student['major_name'] ?? 'N/A') ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?= htmlspecialchars($student['academic_year_name'] ?? 'N/A') ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="<?= BASE_URL ?>students/show/<?= $student['id'] ?>" 
                                           class="text-blue-600 hover:text-blue-900 btn-hover">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?= BASE_URL ?>students/edit/<?= $student['id'] ?>" 
                                           class="text-amber-600 hover:text-amber-900 btn-hover">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <?php if ($auth->isAdmin()): ?>
                                            <button onclick="deleteStudent(<?= $student['id'] ?>)" 
                                                    class="text-red-600 hover:text-red-900 btn-hover">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if ($pagination['totalPages'] > 1): ?>
                <div class="bg-white px-6 py-3 border-t border-gray-200 flex items-center justify-between">
                    <div class="flex-1 flex justify-between sm:hidden">
                        <?php if ($pagination['page'] > 1): ?>
                            <a href="<?= BASE_URL ?>students?page=<?= $pagination['page'] - 1 ?><?= http_build_query($filters) ? '&' . http_build_query($filters) : '' ?>" 
                               class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                ກ່ອນໜ້າ
                            </a>
                        <?php endif; ?>
                        <?php if ($pagination['page'] < $pagination['totalPages']): ?>
                            <a href="<?= BASE_URL ?>students?page=<?= $pagination['page'] + 1 ?><?= http_build_query($filters) ? '&' . http_build_query($filters) : '' ?>" 
                               class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                ຕໍ່ໄປ
                            </a>
                        <?php endif; ?>
                    </div>
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700">
                                ສະແດງ
                                <span class="font-medium"><?= ($pagination['page'] - 1) * $pagination['perPage'] + 1 ?></span>
                                ເຖິງ
                                <span class="font-medium"><?= min($pagination['page'] * $pagination['perPage'], $pagination['total']) ?></span>
                                ຈາກ
                                <span class="font-medium"><?= number_format($pagination['total']) ?></span>
                                ລາຍການ
                            </p>
                        </div>
                        <div>
                            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                <?php if ($pagination['page'] > 1): ?>
                                    <a href="<?= BASE_URL ?>students?page=<?= $pagination['page'] - 1 ?><?= http_build_query($filters) ? '&' . http_build_query($filters) : '' ?>" 
                                       class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                        <i class="fas fa-chevron-left"></i>
                                    </a>
                                <?php endif; ?>

                                <?php for ($i = max(1, $pagination['page'] - 2); $i <= min($pagination['totalPages'], $pagination['page'] + 2); $i++): ?>
                                    <a href="<?= BASE_URL ?>students?page=<?= $i ?><?= http_build_query($filters) ? '&' . http_build_query($filters) : '' ?>" 
                                       class="relative inline-flex items-center px-4 py-2 border text-sm font-medium <?= $i === $pagination['page'] ? 'z-10 bg-amber-50 border-amber-500 text-amber-600' : 'border-gray-300 bg-white text-gray-500 hover:bg-gray-50' ?>">
                                        <?= $i ?>
                                    </a>
                                <?php endfor; ?>

                                <?php if ($pagination['page'] < $pagination['totalPages']): ?>
                                    <a href="<?= BASE_URL ?>students?page=<?= $pagination['page'] + 1 ?><?= http_build_query($filters) ? '&' . http_build_query($filters) : '' ?>" 
                                       class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                <?php endif; ?>
                            </nav>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<script>
function deleteStudent(id) {
    Swal.fire({
        title: 'ຢືນຢັນການລຶບ',
        text: 'ທ່ານແນ່ໃຈບໍ່ວ່າຕ້ອງການລຶບນັກຮຽນນີ້?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'ລຶບ',
        cancelButtonText: 'ຍົກເລີກ'
    }).then((result) => {
        if (result.isConfirmed) {
            // Create form and submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '<?= BASE_URL ?>students/delete/' + id;
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = 'csrf_token';
            csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            form.appendChild(csrfToken);
            
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>