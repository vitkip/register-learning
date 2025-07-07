<?php
// filepath: c:\xampp\htdocs\register-learning\templates\components\header.php
?>
<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= SITE_NAME ?></title>
    
    <!-- Tailwind CSS from CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css">
    <!-- Font Awesome for icons -->
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

      <!-- Custom CSS Files -->
    <link rel="stylesheet" href="<?= BASE_URL ?>public/assets/css/form.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>public/assets/css/student-detail.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>public/assets/css/style.css">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Google Fonts for Lao -->
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@400;700&display=swap" rel="stylesheet">
    
    <style>
        * {
            font-family: 'Noto Sans Lao', sans-serif;
        }
        
        /* Custom SweetAlert2 styling */
        .swal2-popup {
            font-family: 'Noto Sans Lao', sans-serif !important;
        }
        
        .swal2-title {
            font-family: 'Noto Sans Lao', sans-serif !important;
        }
        
        .swal2-content {
            font-family: 'Noto Sans Lao', sans-serif !important;
        }
    </style>
</head>
<body class="bg-gray-100 font-sans">
    <div class="min-h-screen">
        <header class="bg-amber-600 text-white shadow-lg">
            <div class="container mx-auto px-4 py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold"><?= SITE_NAME ?></h1>
                        <p class="text-amber-100 text-sm mt-1">ລະບົບລົງທະບຽນນັກສຶກສາອອນໄລນ໌</p>
                    </div>
                    <div class="relative">
                        <!-- Mobile menu button - only visible on small screens -->
                        <button id="mobile-menu-button" class="md:hidden text-white focus:outline-none">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                        
                        <!-- Desktop navigation - hidden on small screens, visible on md and up -->
                        <nav class="hidden md:flex space-x-4">
                            <a href="<?= BASE_URL ?>" class="text-white hover:text-amber-200 transition-colors">
                                <i class="fas fa-home mr-1"></i> ໜ້າຫຼັກ
                            </a>
                            <a href="<?= BASE_URL ?>index.php?page=register" class="text-white hover:text-amber-200 transition-colors">
                                <i class="fas fa-user-plus mr-1"></i> ລົງທະບຽນ
                            </a>
                            <a href="<?= BASE_URL ?>index.php?page=students" class="text-white hover:text-amber-200 transition-colors">
                                <i class="fas fa-users mr-1"></i> ລາຍຊື່ນັກສຶກສາ
                            </a>
                        </nav>
                        
                        <!-- Mobile navigation menu - hidden by default -->
                        <div id="mobile-menu" class="hidden absolute top-full right-0 mt-2 bg-amber-700 shadow-lg rounded-lg w-64 z-10">
                            <div class="py-2">
                                <a href="<?= BASE_URL ?>" class="block px-4 py-2 text-white hover:bg-amber-600 transition-colors">
                                    <i class="fas fa-home mr-1"></i> ໜ້າຫຼັກ
                                </a>
                                <a href="<?= BASE_URL ?>index.php?page=register" class="block px-4 py-2 text-white hover:bg-amber-600 transition-colors">
                                    <i class="fas fa-user-plus mr-1"></i> ລົງທະບຽນ
                                </a>
                                <a href="<?= BASE_URL ?>index.php?page=students" class="block px-4 py-2 text-white hover:bg-amber-600 transition-colors">
                                    <i class="fas fa-users mr-1"></i> ລາຍຊື່ນັກສຶກສາ
                                </a>
                            </div>
                        </div>
                        
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                document.getElementById('mobile-menu-button').addEventListener('click', function(e) {
                                    e.stopPropagation();
                                    document.getElementById('mobile-menu').classList.toggle('hidden');
                                });
                                
                                document.addEventListener('click', function(event) {
                                    const menu = document.getElementById('mobile-menu');
                                    const button = document.getElementById('mobile-menu-button');
                                    if (!menu.contains(event.target) && event.target !== button) {
                                        menu.classList.add('hidden');
                                    }
                                });
                            });
                        </script>
                    </div>
                </div>
            </div>
        </header>
        
        <main class="container mx-auto px-4 py-8">