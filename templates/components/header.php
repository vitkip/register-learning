<?php
// PHP initialization code can go here
?>
<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'ລະບົບລົງທະບຽນນັກສຶກສາ' ?> - ວິທະຍາໄລການສຶກສາ</title>
    
    <!-- Tailwind CSS from CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css">
    <!-- Font Awesome for icons -->
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

      <!-- Custom CSS Files -->
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css?v=<?= time() ?>">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/form.css?v=<?= time() ?>">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/student-detail.css?v=<?= time() ?>">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/student-list.css?v=<?= time() ?>">
    
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Google Fonts for Lao -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Noto Sans Lao', sans-serif;
        }
        
        .border-3 {
            border-width: 3px;
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
        
        /* Table hover effects */
        .table-hover-row:hover {
            background: linear-gradient(135deg, #fef3c7 0%, #fed7aa 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        /* Button animations */
        .btn-hover {
            transition: all 0.3s ease;
        }
        
        .btn-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        /* Custom gradient backgrounds */
        .bg-gradient-amber {
            background: linear-gradient(135deg, #f59e0b 0%, #ea580c 100%);
        }
        
        .bg-gradient-blue {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        }
        
        .bg-gradient-green {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }
        
        .bg-gradient-purple {
            background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
        }
        
        .bg-gradient-red {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }
    </style>
    
    <!-- Tailwind Configuration -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'lao': ['Noto Sans Lao', 'sans-serif'],
                    },
                    colors: {
                        'amber': {
                            50: '#fffbeb',
                            100: '#fef3c7',
                            200: '#fde68a',
                            300: '#fcd34d',
                            400: '#fbbf24',
                            500: '#f59e0b',
                            600: '#d97706',
                            700: '#b45309',
                            800: '#92400e',
                            900: '#78350f',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="font-lao bg-gradient-to-br from-amber-50 via-orange-50 to-yellow-50 min-h-screen">
    
    <!-- Navigation -->
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
                    <a href="<?= BASE_URL ?>" 
                       class="text-gray-700 hover:text-amber-600 px-3 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                        <i class="fas fa-home mr-2"></i>ໜ້າຫຼັກ
                    </a>
                    <a href="<?= BASE_URL ?>?page=register" 
                       class="text-gray-700 hover:text-amber-600 px-3 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                        <i class="fas fa-user-plus mr-2"></i>ລົງທະບຽນ
                    </a>
                    <a href="<?= BASE_URL ?>?page=students" 
                       class="text-gray-700 hover:text-amber-600 px-3 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                        <i class="fas fa-users mr-2"></i>ລາຍຊື່ນັກສຶກສາ
                    </a>
                    <a href="<?= BASE_URL ?>?page=qrcode" 
                       class="text-gray-700 hover:text-amber-600 px-3 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                        <i class="fas fa-qrcode mr-2"></i>📱 QR Code
                    </a>
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Success/Error Messages -->
    <?php if (isset($_SESSION['message'])): ?>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="alert-message bg-<?= $_SESSION['message_type'] === 'success' ? 'green' : 'red' ?>-100 border border-<?= $_SESSION['message_type'] === 'success' ? 'green' : 'red' ?>-400 text-<?= $_SESSION['message_type'] === 'success' ? 'green' : 'red' ?>-700 px-4 py-3 rounded-xl relative animate-fade-in" role="alert">
                <div class="flex items-center">
                    <i class="fas fa-<?= $_SESSION['message_type'] === 'success' ? 'check-circle' : 'exclamation-triangle' ?> mr-2"></i>
                    <span class="font-medium"><?= htmlspecialchars($_SESSION['message']) ?></span>
                </div>
                <button type="button" class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="this.parentElement.style.display='none'">
                    <i class="fas fa-times text-<?= $_SESSION['message_type'] === 'success' ? 'green' : 'red' ?>-500"></i>
                </button>
            </div>
        </div>
        <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
    <?php endif; ?>
    
    <!-- Main Content -->
    <main class="max-w-7xl mx-auto">