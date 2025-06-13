<?php
// filepath: c:\xampp\htdocs\register-learning\config\config.php

// ການຕັ້ງຄ່າພື້ນຖານ
define("BASE_URL", "http://localhost/register-learning/");
define("SITE_NAME", "ວິທະຍາໄລຄູສົງ ອົງຕື້ ລະບົບສະໝັກຮຽນ Online ");

// ຕັ້ງຄ່າ timezone
date_default_timezone_set('Asia/Vientiane');

// ຕັ້ງຄ່າການສະແດງຜິດພາດ (ປິດໃນການຜະລິດ)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ການຕັ້ງຄ່າອັບໂຫລດຟາຍ
define("UPLOAD_DIR", __DIR__ . "/../public/assets/uploads/photos/");
define("MAX_FILE_SIZE", 5 * 1024 * 1024); // 5MB
define("ALLOWED_EXTENSIONS", ["jpg", "jpeg", "png"]);
?>