<?php
// filepath: c:\xampp\htdocs\register-learning\templates\students-list.php

// ດັດແກ້ລາຍຊື່ນັກສຶກສາ
require_once '../config/database.php';
require_once '../src/classes/Student.php';

// ສ້າງຄວາມສຳພັນກັບຖານຂໍ້ມູນ
$database = new Database();
$db = $database->getConnection();

// ສ້າງອັບບຣິກອິດນັກສຶກສາ
$student = new Student($db);

// ດຶງລາຍຊື່ນັກສຶກສາ (ເປັ່ງຈາກ readAll() ແທນ getAllStudents())
$students = $student->readAll();
$num = count($students);
?>

<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ລາຍຊື່ນັກສຶກສາ</title>
    <link href="../public/assets/css/style.css" rel="stylesheet">
</head>
<body>
  

    <div class="container mx-auto mt-5">
        <h1 class="text-2xl font-bold">ລາຍຊື່ນັກສຶກສາ</h1>

        <?php if($num > 0): ?>
            <table class="min-w-full border-collapse border border-gray-200">
                <thead>
                    <tr>
                        <th class="border border-gray-300 px-4 py-2">ລໍາດັບ</th>
                        <th class="border border-gray-300 px-4 py-2">ເພດ</th>
                        <th class="border border-gray-300 px-4 py-2">ຊື່</th>
                        <th class="border border-gray-300 px-4 py-2">ນາມສະກຸນ</th>                       
                        <th class="border border-gray-300 px-4 py-2">ຈົບມໍປາຍຈາກ</th>
                        <th class="border border-gray-300 px-4 py-2">ທີ່ພັກ</th>
                        <th class="border border-gray-300 px-4 py-2">ເບີໂທ</th>
                        <th class="border border-gray-300 px-4 py-2">ສະຖານທີ່</th>
                        <th class="border border-gray-300 px-4 py-2">ຮູບພາບ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $row): ?>
                        <tr>
                            <td class="border border-gray-300 px-4 py-2"><?php echo $loop = isset($loop) ? $loop + 1 : 1; ?></td>
                            <td class="border border-gray-300 px-4 py-2"><?php echo $row['gender']; ?></td>
                            <td class="border border-gray-300 px-4 py-2"><?php echo $row['first_name']; ?></td>
                            <td class="border border-gray-300 px-4 py-2"><?php echo $row['last_name']; ?></td>                          
                            <td class="border border-gray-300 px-4 py-2"><?php echo $row['previous_school']; ?></td>
                            <td class="border border-gray-300 px-4 py-2"><?php echo $row['accommodation_type']; ?></td>
                            <td class="border border-gray-300 px-4 py-2">
                                <?php if(!empty($row['phone'])): ?>
                                    <a href="https://api.whatsapp.com/send?phone=<?php echo preg_replace('/[^0-9]/', '', $row['phone']); ?>" 
                                       target="_blank" class="text-blue-600 hover:underline">
                                        <?php echo $row['phone']; ?>
                                    </a>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td class="border border-gray-300 px-4 py-2"><?php echo $row['village'] . ', ' . $row['district'] . ', ' . $row['province']; ?></td>
                            <td class="border border-gray-300 px-4 py-2">
                                <?php if($row['photo']): ?>
                                    <img src="<?= BASE_URL ?>public/assets/uploads/photos/<?php echo $row['photo']; ?>" alt="ຮູບນັກສຶກສາ" class="w-16 h-16 object-cover">
                                <?php else: ?>
                                    ບໍ່ມີຮູບ
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>ບໍ່ມີຂໍໍ່ມູນນັກສຶກສາ.</p>
        <?php endif; ?>
    </div>

    <?php include 'components/footer.php'; ?>
</body>
</html>