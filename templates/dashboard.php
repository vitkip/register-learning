<!-- filepath: /register-learning/register-learning/templates/dashboard.php -->
<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - ແລະລົງບັດທິການສຶກສາ</title>
    <link rel="stylesheet" href="../public/assets/css/style.css">
</head>
<body>

<div class="bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold mb-4">ຍິນດີຕ້ອນຮັບ</h2>
    <p class="mb-4">ຍິນດີຕ້ອນຮັບເຂົ້າສູ່ລະບົບສະໝັກຮຽນ Online. ກະລຸນາເລືອກຕົວເລືອກຈາກເມນູເພື່ອດຳເນີນການ.</p>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
        <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
            <h3 class="font-bold text-xl mb-2">ສະໝັກຮຽນ</h3>
            <p class="mb-4">ສະໝັກເຂົ້າຮຽນສຳລັບປີການສຶກສາໃໝ່</p>
            <a href="<?= url('register') ?>" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">ສະໝັກດຽວນີ້</a>
        </div>
        
        <div class="bg-green-50 p-4 rounded-lg border border-green-200">
            <h3 class="font-bold text-xl mb-2">ລາຍຊື່ນັກຮຽນ</h3>
            <p class="mb-4">ເບິ່ງລາຍຊື່ນັກຮຽນທັງໝົດທີ່ສະໝັກແລ້ວ</p>
            <a href="<?= url('students') ?>" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">ເບິ່ງລາຍຊື່</a>
        </div>
        
        <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
            <h3 class="font-bold text-xl mb-2">ຄົ້ນຫາ</h3>
            <p class="mb-4">ຄົ້ນຫານັກຮຽນຕາມຊື່, ສາຂາ, ຫຼື ສົກຮຽນ</p>
            <a href="<?= url('search') ?>" class="bg-purple-500 text-white px-4 py-2 rounded hover:bg-purple-600">ຄົ້ນຫາ</a>
        </div>
    </div>
</div>

    <?php include 'components/footer.php'; ?>
</body>
</html>