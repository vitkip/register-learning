<!-- 
    ນີ້ແມ່ນແຟ້ມ navigation.php ສໍາລັບລະບົບລົງທະບຽນນັກສຶກສາ 
    ມີລິດສະຖານທີ່ໃນເວບໄຊທ໌ ແລະ ປ່ອນລິດສະຖານທີ່ໃນລະບົບ
-->

<nav class="bg-white p-4 rounded-lg shadow-md mb-6">
    <ul class="flex space-x-4">
        <li><a href="<?= url('home') ?>" class="text-blue-600 hover:text-blue-800">ໜ້າຫຼັກ</a></li>
        <li><a href="<?= url('register') ?>" class="text-blue-600 hover:text-blue-800">ສະໝັກຮຽນ</a></li>
        <li><a href="<?= url('students') ?>" class="text-blue-600 hover:text-blue-800">ລາຍຊື່ນັກຮຽນ</a></li>
        <li><a href="<?= url('search') ?>" class="text-blue-600 hover:text-blue-800">ຄົ້ນຫາ</a></li>
        <li>
            <a href="<?= url('export-reports') ?>" class="px-3 py-2 hover:bg-gray-700 rounded mx-1 flex items-center">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m-8.9 5H18a2 2 0 002-2V5a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            ສົ່ງອອກລາຍງານ
            </a>
        </li>
    </ul>
</nav>