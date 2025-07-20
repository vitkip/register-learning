<footer class="mt-8 bg-gradient-to-r from-blue-100 to-indigo-100 rounded-lg shadow-lg">
            <div class="max-w-7xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Company Info -->
                    <div class="text-center md:text-left">
                        <img src="<?= BASE_URL ?>assets/img/college-logo.png" alt="<?= SITE_NAME ?> Logo" class="h-16 mx-auto md:mx-0 mb-3">
                        <h3 class="text-lg font-semibold text-gray-800"><?= SITE_NAME ?></h3>
                    </div>
                    
                    <!-- Quick Links -->
                    <div class="text-center">
                        <h3 class="text-lg font-semibold text-gray-800">ທາງລັດ</h3>
                        <ul class="mt-2 space-y-2">
                            <li>
                                <a href="<?= BASE_URL ?>index.php" class="text-blue-600 hover:text-blue-800 transition-colors">
                                    <i class="fas fa-home mr-1"></i> ຫນ້າຫຼັກ
                                </a>
                            </li>
                            <li>
                                <a href="<?= BASE_URL ?>index.php?page=register" class="text-blue-600 hover:text-blue-800 transition-colors">
                                    <i class="fas fa-user-plus mr-1"></i> ລົງທະບຽນ
                                </a>
                            </li>
                            <li>
                                <a href="<?= BASE_URL ?>index.php?page=students" class="text-blue-600 hover:text-blue-800 transition-colors">
                                    <i class="fas fa-users mr-1"></i> ລາຍຊື່ນັກສຶກສາ
                                </a>
                            </li>
                        </ul>
                    </div>
                    
                    <!-- Social Media -->
                    <div class="text-center md:text-right">
                        <h3 class="text-lg font-semibold text-gray-800">ຕິດຕາມ</h3>
                        <div class="mt-2 flex justify-center md:justify-end space-x-4">
                            <a href="#" class="text-blue-500 hover:text-blue-700"><i class="fab fa-facebook-f text-xl"></i></a>
                            <a href="#" class="text-green-500 hover:text-green-700"><i class="fab fa-whatsapp text-xl"></i></a>
                            <a href="#" class="text-red-600 hover:text-red-800"><i class="fab fa-youtube text-xl"></i></a>
                        </div>
                    </div>
                </div>
                <div class="my-6 border-b border-gray-300"></div>
                <div class="mt-8 pt-4 border-t border-gray-200 text-center text-sm text-gray-600">
                    <p>&copy; <?= date('Y') ?> <?= SITE_NAME ?>. ສະຫງວນລິຂະສິດ.</p>
                </div>
            </div>
        </footer>
    </div>
    

    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Custom JavaScript Files -->
    <script src="<?= BASE_URL ?>public/assets/js/form.js"></script>


    <!-- SweetAlert for Messages -->
    <?php if (isset($_SESSION['message'])): ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const messageType = '<?= $_SESSION['message_type'] ?? 'info' ?>';
        const message = <?= json_encode($_SESSION['message']) ?>;
        
        let icon = 'info';
        let title = 'ແຈ້ງເຕືອນ';
        
        switch(messageType) {
            case 'success':
                icon = 'success';
                title = 'ສຳເລັດ!';
                break;
            case 'error':
                icon = 'error';
                title = 'ຂໍ້ຜິດພາດ!';
                break;
            case 'warning':
                icon = 'warning';
                title = 'ແຈ້ງເຕືອນ!';
                break;
        }
        
        Swal.fire({
            title: title,
            text: message,
            icon: icon,
            confirmButtonText: 'ຮູ້ແລ້ວ',
            confirmButtonColor: '#f59e0b',
            timer: messageType === 'success' ? 3000 : null,
            timerProgressBar: messageType === 'success',
            showClass: {
                popup: 'animate__animated animate__fadeInDown'
            },
            hideClass: {
                popup: 'animate__animated animate__fadeOutUp'
            }
        });
    });
    </script>
    <?php 
        // ลบ session message หลังแสดงแล้ว
        unset($_SESSION['message']);
        unset($_SESSION['message_type']);
    endif; 
    ?>

</body>
</html>