<?php
// filepath: c:\xampp\htdocs\register-learning\templates\register.php

// ດຶງຂໍ້ມູນສາຂາຮຽນທັງໝົດແລະປີການສຶກສາ
require_once __DIR__ . '/../src/classes/Major.php';
require_once __DIR__ . '/../src/classes/AcademicYear.php';

// ສ້າງການເຊື່ອມຕໍ່ກັບຖານຂໍ້ມູນ
$database = new Database();
$db = $database->getConnection();

// ດຶງຂໍ້ມູນສາຂາຮຽນ
$majorObj = new Major($db);
$majors = $majorObj->readAll();

// ດຶງຂໍ້ມູນປີການສຶກສາ
$yearObj = new AcademicYear($db);
$academicYears = $yearObj->readAll();

// ກວດສອບຂໍ້ຄວາມແຈ້ງເຕືອນ
if (isset($_SESSION['message'])) {
    echo showAlert($_SESSION['message'], $_SESSION['message_type']);
    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
}
?>

<div class="bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold mb-4">ລົງທະບຽນນັກສຶກສາໃໝ່</h2>
    
    <form action="<?= url('register', ['action' => 'process']) ?>" method="POST" enctype="multipart/form-data" class="space-y-4">
        <!-- ຂໍ້ມູນທົ່ວໄປ -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="first_name" class="block text-sm font-medium text-gray-700">ຊື່</label>
                <input type="text" name="first_name" id="first_name" required 
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
            </div>
            <div>
                <label for="last_name" class="block text-sm font-medium text-gray-700">ນາມສະກຸນ</label>
                <input type="text" name="last_name" id="last_name" required 
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="gender" class="block text-sm font-medium text-gray-700">ເພດ</label>
                <select name="gender" id="gender" required 
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                    <option value="">ເລືອກເພດ</option>
                    <option value="ພຣະ">ພຣະ</option>
                    <option value="ສາມະເນນ">ສາມະເນນ</option>
                    <option value="ຊາຍ">ຊາຍ</option>
                    <option value="ຍິງ">ຍິງ</option>
                    <option value="ອຶ່ນໆ">ອຶ່ນໆ</option>
                </select>
            </div>
            <div>
                <label for="dob" class="block text-sm font-medium text-gray-700">ວັນເກິດ</label>
                <input type="date" name="dob" id="dob" required 
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
            </div>
            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700">ເບີໂທ</label>
                <input type="text" name="phone" id="phone" 
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
            </div>
        </div>
        
        <!-- ຂໍ້ມູນທີ່ຢູ່ -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="village" class="block text-sm font-medium text-gray-700">ບ້ານ</label>
                <input type="text" name="village" id="village" 
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
            </div>
            <div>
                <label for="district" class="block text-sm font-medium text-gray-700">ເມືອງ</label>
                <input type="text" name="district" id="district" 
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
            </div>
            <div>
                <label for="province" class="block text-sm font-medium text-gray-700">ແຂວງ</label>
                <input type="text" name="province" id="province" 
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
            </div>
        </div>
        
        <!-- ຂໍ້ມູນເພີ່ມເຕີມ -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">ອີເມວ</label>
                <input type="email" name="email" id="email" 
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
            </div>
            <div>
                <label for="previous_school" class="block text-sm font-medium text-gray-700">ຈົບມໍປາຍຈາກ</label>
                <input type="text" name="previous_school" id="previous_school" 
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="accommodation_type" class="block text-sm font-medium text-gray-700">ປະເພດທີ່ພັກ</label>
                <select name="accommodation_type" id="accommodation_type" required 
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                    <option value="ມີວັດຢູ່ແລ້ວ">ມີວັດຢູ່ແລ້ວ</option>
                    <option value="ຫາວັດໃຫ້">ຫາວັດໃຫ້</option>
                </select>
            </div>
            <div>
                <label for="photo" class="block text-sm font-medium text-gray-700">ຮູບຖ່າຍ (ຂະໜາດສູງສຸດ 5MB)</label>
                <input type="file" name="photo" id="photo" accept="image/jpeg,image/png,image/gif" 
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                <p class="text-xs text-gray-500 mt-1">ສະໜັບສະໜູນ: JPG, PNG, GIF</p>
            </div>
        </div>
        
        <!-- ຂໍ້ມູນການລົງທະບຽນ -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="major_id" class="block text-sm font-medium text-gray-700">ສາຂາ</label>
                <select name="major_id" id="major_id" required 
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                    <option value="">ເລືອກສາຂາ</option>
                    <?php foreach ($majors as $major): ?>
                        <option value="<?= $major['id'] ?>"><?= $major['name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="academic_year_id" class="block text-sm font-medium text-gray-700">ປີການສຶກສາ</label>
                <select name="academic_year_id" id="academic_year_id" required 
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                    <option value="">ເລືອກປີການສຶກສາ</option>
                    <?php foreach ($academicYears as $year): ?>
                        <option value="<?= $year['id'] ?>"><?= $year['year'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        
        <div class="mt-6">
            <button type="submit" name="register" 
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-colors">
                ລົງທະບຽນ
            </button>
        </div>
    </form>
</div>