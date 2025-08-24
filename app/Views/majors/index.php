<div class="animate-fade-in">
    <!-- Header -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">
                    <i class="fas fa-graduation-cap text-green-500 mr-3"></i>
                    ຈັດການສາຂາວິຊາ
                </h1>
                <p class="text-gray-600">ຈັດການຂໍ້ມູນສາຂາວິຊາທັງໝົດ</p>
            </div>
            <div>
                <a href="<?= BASE_URL ?>majors/create" 
                   class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg font-medium btn-hover">
                    <i class="fas fa-plus mr-2"></i>ເພີ່ມສາຂາວິຊາໃໝ່
                </a>
            </div>
        </div>
    </div>

    <!-- Majors Table -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <?php if (empty($majors)): ?>
            <div class="p-8 text-center">
                <i class="fas fa-graduation-cap text-gray-300 text-6xl mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-500 mb-2">ບໍ່ມີສາຂາວິຊາ</h3>
                <p class="text-gray-400 mb-4">ເລີ່ມຕົ້ນໂດຍການເພີ່ມສາຂາວິຊາໃໝ່</p>
                <a href="<?= BASE_URL ?>majors/create" 
                   class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg font-medium btn-hover">
                    <i class="fas fa-plus mr-2"></i>ເພີ່ມສາຂາວິຊາໃໝ່
                </a>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                ລະຫັດສາຂາ
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                ຊື່ສາຂາ
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                ຄຳອະທິບາຍ
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                ຈຳນວນນັກຮຽນ
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                ສະຖານະ
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                ການກະທຳ
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($majors as $major): ?>
                            <tr class="hover:bg-gray-50 transition-colors duration-200">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-medium">
                                        <?= htmlspecialchars($major['code'] ?? 'N/A') ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        <?= htmlspecialchars($major['name']) ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900 max-w-xs truncate">
                                        <?= htmlspecialchars($major['description'] ?? '-') ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <?= number_format($major['student_count']) ?> ຄົນ
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        <?= $major['status'] === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                        <?= $major['status'] === 'active' ? 'ເປີດໃຊ້ງານ' : 'ປິດໃຊ້ງານ' ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="<?= BASE_URL ?>majors/edit/<?= $major['id'] ?>" 
                                           class="text-amber-600 hover:text-amber-900 btn-hover">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <?php if ($auth->isAdmin() && $major['student_count'] == 0): ?>
                                            <button onclick="deleteMajor(<?= $major['id'] ?>)" 
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
        <?php endif; ?>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<script>
function deleteMajor(id) {
    Swal.fire({
        title: 'ຢືນຢັນການລຶບ',
        text: 'ທ່ານແນ່ໃຈບໍ່ວ່າຕ້ອງການລຶບສາຂາວິຊານີ້?',
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
            form.action = '<?= BASE_URL ?>majors/delete/' + id;
            
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