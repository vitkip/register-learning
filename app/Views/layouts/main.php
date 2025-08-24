<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'ລະບົບລົງທະບຽນນັກສຶກສາ' ?> - ວິທະຍາໄລການສຶກສາ</title>
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="<?= Session::generateCSRF() ?>">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Google Fonts - Phetsarath -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Phetsarath&display=swap" rel="stylesheet">
    
    <style>
        html {
            font-family: 'Phetsarath', 'Noto Sans Lao', ui-sans-serif, system-ui;
        }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
        
        /* Custom animations */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .animate-fade-in {
            animation: fadeIn 0.3s ease-out;
        }
        
        /* Button hover effects */
        .btn-hover {
            transition: all 0.3s ease;
        }
        
        .btn-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
    </style>
</head>
<body class="bg-gradient-to-br from-amber-50 via-orange-50 to-yellow-50 min-h-screen">
    
    <!-- Navigation -->
    <?php if (isset($auth) && $auth->isLoggedIn()): ?>
    <nav class="bg-white shadow-xl border-b-4 border-amber-500">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="<?= BASE_URL ?>" class="flex items-center">
                        <i class="fas fa-graduation-cap text-2xl text-amber-500 mr-3"></i>
                        <span class="text-xl font-bold text-gray-800">ລະບົບລົງທະບຽນນັກສຶກສາ</span>
                    </a>
                </div>
                
                <div class="flex items-center space-x-4">
                    <a href="<?= BASE_URL ?>dashboard" 
                       class="text-gray-700 hover:text-amber-600 px-3 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                        <i class="fas fa-home mr-2"></i>ໜ້າຫຼັກ
                    </a>
                    <a href="<?= BASE_URL ?>students" 
                       class="text-gray-700 hover:text-amber-600 px-3 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                        <i class="fas fa-users mr-2"></i>ນັກຮຽນ
                    </a>
                    <a href="<?= BASE_URL ?>majors" 
                       class="text-gray-700 hover:text-amber-600 px-3 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                        <i class="fas fa-graduation-cap mr-2"></i>ສາຂາ
                    </a>
                    <a href="<?= BASE_URL ?>academic-years" 
                       class="text-gray-700 hover:text-amber-600 px-3 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                        <i class="fas fa-calendar mr-2"></i>ປີການສຶກສາ
                    </a>
                    <a href="<?= BASE_URL ?>subjects" 
                       class="text-gray-700 hover:text-amber-600 px-3 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                        <i class="fas fa-book mr-2"></i>ວິຊາ
                    </a>
                    <a href="<?= BASE_URL ?>curriculum" 
                       class="text-gray-700 hover:text-amber-600 px-3 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                        <i class="fas fa-list mr-2"></i>ຫຼັກສູດ
                    </a>
                    <a href="<?= BASE_URL ?>enrollments" 
                       class="text-gray-700 hover:text-amber-600 px-3 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                        <i class="fas fa-user-plus mr-2"></i>ການລົງທະບຽນ
                    </a>
                    <a href="<?= BASE_URL ?>grades" 
                       class="text-gray-700 hover:text-amber-600 px-3 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                        <i class="fas fa-star mr-2"></i>ຄະແນນ
                    </a>
                    <a href="<?= BASE_URL ?>settings" 
                       class="text-gray-700 hover:text-amber-600 px-3 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                        <i class="fas fa-cog mr-2"></i>ການຕັ້ງຄ່າ
                    </a>
                    
                    <?php if ($auth->isAdmin()): ?>
                    <a href="<?= BASE_URL ?>users" 
                       class="text-gray-700 hover:text-amber-600 px-3 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                        <i class="fas fa-users-cog mr-2"></i>ຜູ້ໃຊ້
                    </a>
                    <a href="<?= BASE_URL ?>logs" 
                       class="text-gray-700 hover:text-amber-600 px-3 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                        <i class="fas fa-list-alt mr-2"></i>ບັນທຶກລະບົບ
                    </a>
                    <?php endif; ?>
                    
                    <!-- User Menu -->
                    <div class="relative">
                        <button class="text-gray-700 hover:text-amber-600 px-3 py-2 rounded-md text-sm font-medium transition-colors duration-200 flex items-center">
                            <i class="fas fa-user mr-2"></i><?= htmlspecialchars($auth->user()['username']) ?>
                            <i class="fas fa-chevron-down ml-2"></i>
                        </button>
                        <div class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 hidden">
                            <a href="<?= BASE_URL ?>auth/logout" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-sign-out-alt mr-2"></i>ອອກຈາກລະບົບ
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>
    <?php endif; ?>
    
    <!-- Flash Messages -->
    <?php 
    $message = Session::getMessage();
    if ($message): 
    ?>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="alert-message bg-<?= $message['type'] === 'success' ? 'green' : ($message['type'] === 'error' ? 'red' : 'blue') ?>-100 border border-<?= $message['type'] === 'success' ? 'green' : ($message['type'] === 'error' ? 'red' : 'blue') ?>-400 text-<?= $message['type'] === 'success' ? 'green' : ($message['type'] === 'error' ? 'red' : 'blue') ?>-700 px-4 py-3 rounded-xl relative animate-fade-in" role="alert">
                <div class="flex items-center">
                    <i class="fas fa-<?= $message['type'] === 'success' ? 'check-circle' : ($message['type'] === 'error' ? 'exclamation-triangle' : 'info-circle') ?> mr-2"></i>
                    <span class="font-medium"><?= htmlspecialchars($message['message']) ?></span>
                </div>
                <button type="button" class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="this.parentElement.style.display='none'">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    <?php endif; ?>
    
    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <?= $content ?>
    </main>
    
    <!-- Footer -->
    <footer class="bg-white shadow-xl border-t-4 border-amber-500 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="text-center text-gray-600">
                <p>&copy; <?= date('Y') ?> ວິທະຍາໄລການສຶກສາ. ສະຫງວນລິຂະສິດທັງໝົດ.</p>
            </div>
        </div>
    </footer>
    
    <!-- JavaScript -->
    <script>
        // Setup CSRF token for AJAX requests
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        // User menu toggle
        document.addEventListener('DOMContentLoaded', function() {
            const userButton = document.querySelector('button');
            const userMenu = document.querySelector('.absolute.right-0');
            
            if (userButton && userMenu) {
                userButton.addEventListener('click', function() {
                    userMenu.classList.toggle('hidden');
                });
                
                // Close menu when clicking outside
                document.addEventListener('click', function(e) {
                    if (!userButton.contains(e.target) && !userMenu.contains(e.target)) {
                        userMenu.classList.add('hidden');
                    }
                });
            }
        });
    </script>
</body>
</html>