<?php
// filepath: c:\xampp\htdocs\register-learning\templates\dashboard.php
// ตรวจสอบว่ามีการเรียกใช้ผ่าน index.php หรือไม่
if (!defined('BASE_PATH')) {
    header('Location: ../public/index.php');
    exit('Access denied. Please use proper navigation.');
}

// ดึงข้อมูลสถิติ
require_once BASE_PATH . '/src/classes/Student.php';
require_once BASE_PATH . '/src/classes/Major.php';
require_once BASE_PATH . '/src/classes/AcademicYear.php';

$student = new Student($db);
$majorObj = new Major($db);
$yearObj = new AcademicYear($db);

// ดึงสถิติ
$total_students = 0;
$total_majors = 0;
$total_years = 0;
$recent_students = [];
$male_count = 0;
$female_count = 0;
$monk_count = 0;

try {
    $total_students = $student->getTotalCount();
    $total_majors = $majorObj->getTotalCount();
    $total_years = $yearObj->getTotalCount();
    $recent_students = $student->getRecentStudents(5);
    
    $male_count = $student->getCountByGender('ຊາຍ');
    $female_count = $student->getCountByGender('ຍິງ');
    $monk_count = $student->getCountByGender('ພຣະ');
} catch (Exception $e) {
    error_log("Error loading dashboard data: " . $e->getMessage());
}

// ตรวจสอบข้อความแจ้งเตือน
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    $message_type = $_SESSION['message_type'] ?? 'info';
    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
    
    echo "<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            title: '" . addslashes($message) . "',
            icon: '$message_type',
            confirmButtonText: 'ຕົກລົງ',
            confirmButtonColor: '#f59e0b'
        });
    });
    </script>";
}
?>

<!-- Progress Bar -->
<div class="progress-bar">
    <div class="progress-fill" id="progress-fill"></div>
</div>

<!-- Main Container -->
<div class="min-h-screen bg-gradient-to-br from-amber-50 via-orange-50 to-yellow-50">
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container mx-auto px-4 py-16">
            <div class="text-center fade-in">
                <div class="hero-content">
                    <div class="hero-icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <h1 class="hero-title">
                        ລະບົບລົງທະບຽນນັກສຶກສາ
                    </h1>
                    <p class="hero-subtitle">ຍິນດີຕ້ອນຮັບເຂົ້າສູ່ລະບົບການຈັດການນັກສຶກສາ</p>
                    
                    <!-- Current Time Display -->
                    <div class="time-display">
                        <div class="time-item">
                            <i class="fas fa-calendar-alt"></i>
                            <span>ວັນທີ: <?= date('d/m/Y') ?></span>
                        </div>
                        <div class="time-divider"></div>
                        <div class="time-item">
                            <i class="fas fa-clock"></i>
                            <span id="current-time"><?= date('H:i:s') ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Statistics Section -->
    <section class="stats-section">
        <div class="container mx-auto px-4 pb-8">
            <div class="stats-grid fade-in">
                <!-- Total Students Card -->
                <div class="stats-card stats-card-primary">
                    <div class="stats-card-inner">
                        <div class="stats-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stats-content">
                            <div class="stats-number"><?= number_format($total_students) ?></div>
                            <div class="stats-label">ນັກສຶກສາທັງໝົດ</div>
                        </div>
                        <div class="stats-trend">
                            <i class="fas fa-arrow-up"></i>
                            <span>+<?= $male_count + $female_count ?></span>
                        </div>
                    </div>
                </div>

                <!-- Male Students Card -->
                <div class="stats-card stats-card-blue">
                    <div class="stats-card-inner">
                        <div class="stats-icon">
                            <i class="fas fa-male"></i>
                        </div>
                        <div class="stats-content">
                            <div class="stats-number"><?= number_format($male_count) ?></div>
                            <div class="stats-label">ນັກສຶກສາຊາຍ</div>
                        </div>
                        <div class="stats-percentage">
                            <?= $total_students > 0 ? round(($male_count / $total_students) * 100, 1) : 0 ?>%
                        </div>
                    </div>
                </div>

                <!-- Female Students Card -->
                <div class="stats-card stats-card-pink">
                    <div class="stats-card-inner">
                        <div class="stats-icon">
                            <i class="fas fa-female"></i>
                        </div>
                        <div class="stats-content">
                            <div class="stats-number"><?= number_format($female_count) ?></div>
                            <div class="stats-label">ນັກສຶກສາຍິງ</div>
                        </div>
                        <div class="stats-percentage">
                            <?= $total_students > 0 ? round(($female_count / $total_students) * 100, 1) : 0 ?>%
                        </div>
                    </div>
                </div>

                <!-- Majors Card -->
                <div class="stats-card stats-card-green">
                    <div class="stats-card-inner">
                        <div class="stats-icon">
                            <i class="fas fa-book"></i>
                        </div>
                        <div class="stats-content">
                            <div class="stats-number"><?= number_format($total_majors) ?></div>
                            <div class="stats-label">ສາຂາວິຊາ</div>
                        </div>
                        <div class="stats-badge">
                            <span>ເປີດໃຫ້ບໍລິການ</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content Section -->
    <section class="main-content">
        <div class="container mx-auto px-4 pb-16">
            <div class="grid lg:grid-cols-3 gap-8">
                
                <!-- Recent Students Section -->
                <div class="lg:col-span-2">
                    <div class="content-card fade-in">
                        <div class="card-header">
                            <div class="header-title">
                                <i class="fas fa-user-plus"></i>
                                <h3>ນັກສຶກສາລົງທະບຽນລ່າສຸດ</h3>
                            </div>
                            <a href="<?= BASE_URL ?>index.php?page=students" class="header-action">
                                <span>ເບິ່ງທັງໝົດ</span>
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>

                        <div class="card-content">
                            <?php if (!empty($recent_students)): ?>
                                <div class="students-grid">
                                    <?php foreach ($recent_students as $index => $student_data): ?>
                                        <div class="student-card" style="animation-delay: <?= $index * 0.1 ?>s">
                                            <div class="student-avatar">
                                                <?php if (!empty($student_data['photo']) && file_exists(BASE_PATH . "/public/uploads/photos/" . $student_data['photo'])): ?>
                                                    <img src="<?= BASE_URL ?>uploads/photos/<?= htmlspecialchars($student_data['photo']) ?>" 
                                                         alt="ຮູບນັກສຶກສາ" 
                                                         class="avatar-image">
                                                <?php else: ?>
                                                    <div class="avatar-placeholder">
                                                        <i class="fas fa-user"></i>
                                                    </div>
                                                <?php endif; ?>
                                                <div class="status-indicator"></div>
                                            </div>
                                            
                                            <div class="student-info">
                                                <h4 class="student-name">
                                                    <?= htmlspecialchars($student_data['first_name'] . ' ' . $student_data['last_name']) ?>
                                                </h4>
                                                <p class="student-id">
                                                    <i class="fas fa-id-card"></i>
                                                    <?= htmlspecialchars($student_data['student_id'] ?? $student_data['id']) ?>
                                                </p>
                                                <p class="student-date">
                                                    <i class="fas fa-calendar"></i>
                                                    <?= date('d/m/Y', strtotime($student_data['registered_at'] ?? 'now')) ?>
                                                </p>
                                            </div>
                                            
                                            <div class="student-actions">
                                                <a href="<?= BASE_URL ?>index.php?page=student-detail&id=<?= $student_data['id'] ?>" 
                                                   class="action-btn action-btn-primary" title="ເບິ່ງລາຍລະອຽດ">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="<?= BASE_URL ?>index.php?page=student-edit&id=<?= $student_data['id'] ?>" 
                                                   class="action-btn action-btn-secondary" title="ແກ້ໄຂ">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="empty-state">
                                    <div class="empty-icon">
                                        <i class="fas fa-user-graduate"></i>
                                    </div>
                                    <h4>ຍັງບໍ່ມີນັກສຶກສາລົງທະບຽນ</h4>
                                    <p>ເລີ່ມຕົ້ນໂດຍການເພີ່ມນັກສຶກສາຄົນທຳອິດ</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions Section -->
                <div class="lg:col-span-1">
                    <div class="content-card fade-in">
                        <div class="card-header">
                            <div class="header-title">
                                <i class="fas fa-bolt"></i>
                                <h3>ດຳເນີນການດ່ວນ</h3>
                            </div>
                        </div>

                        <div class="card-content">
                            <div class="actions-grid">
                                <!-- Register New Student -->
                                <a href="<?= BASE_URL ?>index.php?page=register" class="quick-action-card action-primary">
                                    <div class="action-icon">
                                        <i class="fas fa-user-plus"></i>
                                    </div>
                                    <div class="action-content">
                                        <h4>ລົງທະບຽນນັກສຶກສາ</h4>
                                        <p>ເພີ່ມນັກສຶກສາໃໝ່</p>
                                    </div>
                                    <div class="action-arrow">
                                        <i class="fas fa-chevron-right"></i>
                                    </div>
                                </a>

                                <!-- View All Students -->
                                <a href="<?= BASE_URL ?>index.php?page=students" class="quick-action-card action-blue">
                                    <div class="action-icon">
                                        <i class="fas fa-users"></i>
                                    </div>
                                    <div class="action-content">
                                        <h4>ລາຍຊື່ນັກສຶກສາ</h4>
                                        <p>ຈັດການນັກສຶກສາ</p>
                                    </div>
                                    <div class="action-arrow">
                                        <i class="fas fa-chevron-right"></i>
                                    </div>
                                </a>

                                <!-- Search Students -->
                                <a href="<?= BASE_URL ?>index.php?page=search" class="quick-action-card action-green">
                                    <div class="action-icon">
                                        <i class="fas fa-search"></i>
                                    </div>
                                    <div class="action-content">
                                        <h4>ຄົ້ນຫານັກສຶກສາ</h4>
                                        <p>ຄົ້ນຫາຂໍ້ມູນ</p>
                                    </div>
                                    <div class="action-arrow">
                                        <i class="fas fa-chevron-right"></i>
                                    </div>
                                </a>

                                <!-- Export Reports -->
                                <a href="<?= BASE_URL ?>index.php?page=export-reports" class="quick-action-card action-purple">
                                    <div class="action-icon">
                                        <i class="fas fa-download"></i>
                                    </div>
                                    <div class="action-content">
                                        <h4>ລາຍງານ</h4>
                                        <p>ສົ່ງອອກຂໍ້ມູນ</p>
                                    </div>
                                    <div class="action-arrow">
                                        <i class="fas fa-chevron-right"></i>
                                    </div>
                                </a>

                                <!-- QR Code Scanner -->
                                <a href="<?= BASE_URL ?>index.php?page=verify-qrcode" class="quick-action-card action-orange">
                                    <div class="action-icon">
                                        <i class="fas fa-qrcode"></i>
                                    </div>
                                    <div class="action-content">
                                        <h4>ສະແກນ QR Code</h4>
                                        <p>ຢັ້ງຢືນນັກສຶກສາ</p>
                                    </div>
                                    <div class="action-arrow">
                                        <i class="fas fa-chevron-right"></i>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- System Info Card -->
                    <div class="content-card fade-in mt-8">
                        <div class="card-header">
                            <div class="header-title">
                                <i class="fas fa-info-circle"></i>
                                <h3>ຂໍ້ມູນລະບົບ</h3>
                            </div>
                        </div>

                        <div class="card-content">
                            <div class="system-info">
                                <div class="info-item">
                                    <div class="info-icon">
                                        <i class="fas fa-server"></i>
                                    </div>
                                    <div class="info-content">
                                        <span class="info-label">ເວີຊັນ PHP</span>
                                        <span class="info-value"><?= PHP_VERSION ?></span>
                                    </div>
                                </div>

                                <div class="info-item">
                                    <div class="info-icon">
                                        <i class="fas fa-database"></i>
                                    </div>
                                    <div class="info-content">
                                        <span class="info-label">ຖານຂໍ້ມູນ</span>
                                        <span class="info-value">MySQL</span>
                                    </div>
                                </div>

                                <div class="info-item">
                                    <div class="info-icon">
                                        <i class="fas fa-calendar-check"></i>
                                    </div>
                                    <div class="info-content">
                                        <span class="info-label">ອັບເດດລ່າສຸດ</span>
                                        <span class="info-value"><?= date('d/m/Y') ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Enhanced Styles -->
<style>
/* Import form.css variables and base styles */
:root {
    --primary-color: #f59e0b;
    --primary-light: #fbbf24;
    --primary-dark: #d97706;
    --secondary-color: #6366f1;
    --success-color: #10b981;
    --error-color: #ef4444;
    --warning-color: #f59e0b;
    --gray-50: #f9fafb;
    --gray-100: #f3f4f6;
    --gray-200: #e5e7eb;
    --gray-300: #d1d5db;
    --gray-400: #9ca3af;
    --gray-500: #6b7280;
    --gray-600: #4b5563;
    --gray-700: #374151;
    --gray-800: #1f2937;
    --gray-900: #111827;
    --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

/* Base animations */
@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.7;
    }
}

.fade-in {
    animation: slideInUp 0.8s ease-out forwards;
}

/* Progress Bar */
.progress-bar {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 6px;
    background: rgba(255, 255, 255, 0.9);
    z-index: 1000;
    backdrop-filter: blur(10px);
}

.progress-fill {
    height: 100%;
    background: linear-gradient(90deg, var(--primary-color), var(--primary-light), var(--success-color));
    width: 0%;
    transition: width 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.progress-fill::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    bottom: 0;
    right: 0;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    animation: progress-shine 2s infinite;
}

@keyframes progress-shine {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}

/* Hero Section */
.hero-section {
    position: relative;
    overflow: hidden;
    background: linear-gradient(135deg, #fef3c7, #fef7e3, #ecfccb);
}

.hero-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="20" cy="20" r="1" fill="rgba(245,158,11,0.1)"/><circle cx="80" cy="80" r="1" fill="rgba(245,158,11,0.1)"/><circle cx="40" cy="60" r="1" fill="rgba(245,158,11,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
    opacity: 0.6;
}

.hero-content {
    position: relative;
    z-index: 1;
}

.hero-icon {
    font-size: 5rem;
    color: var(--primary-color);
    margin-bottom: 2rem;
    animation: pulse 2s infinite;
}

.hero-title {
    font-size: 3.5rem;
    font-weight: 800;
    color: var(--gray-800);
    margin-bottom: 1.5rem;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    line-height: 1.2;
}

.hero-subtitle {
    font-size: 1.5rem;
    color: var(--gray-600);
    margin-bottom: 3rem;
    font-weight: 500;
}

.time-display {
    display: inline-flex;
    align-items: center;
    background: white;
    padding: 1rem 2rem;
    border-radius: 2rem;
    box-shadow: var(--shadow-xl);
    border: 1px solid var(--gray-200);
}

.time-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-weight: 600;
    color: var(--gray-700);
}

.time-item i {
    color: var(--primary-color);
    font-size: 1.25rem;
}

.time-divider {
    width: 2px;
    height: 2rem;
    background: var(--gray-300);
    margin: 0 1.5rem;
}

/* Statistics Section */
.stats-section {
    margin-top: -2rem;
    position: relative;
    z-index: 10;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 2rem;
}

.stats-card {
    position: relative;
    border-radius: 2rem;
    overflow: hidden;
    box-shadow: var(--shadow-xl);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    background: white;
    border: 1px solid var(--gray-200);
}

.stats-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 6px;
    background: linear-gradient(90deg, currentColor, transparent);
}

.stats-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
}

.stats-card-inner {
    padding: 2rem;
    position: relative;
}

.stats-card-primary {
    color: var(--primary-color);
}

.stats-card-blue {
    color: #3b82f6;
}

.stats-card-pink {
    color: #ec4899;
}

.stats-card-green {
    color: var(--success-color);
}

.stats-icon {
    font-size: 3rem;
    margin-bottom: 1.5rem;
    opacity: 0.9;
}

.stats-number {
    font-size: 3rem;
    font-weight: 800;
    color: var(--gray-800);
    line-height: 1;
    margin-bottom: 0.5rem;
}

.stats-label {
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--gray-600);
    margin-bottom: 1rem;
}

.stats-trend {
    position: absolute;
    top: 1.5rem;
    right: 1.5rem;
    background: rgba(16, 185, 129, 0.1);
    color: var(--success-color);
    padding: 0.5rem 1rem;
    border-radius: 1rem;
    font-size: 0.875rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.stats-percentage {
    position: absolute;
    top: 1.5rem;
    right: 1.5rem;
    background: rgba(0, 0, 0, 0.05);
    color: var(--gray-700);
    padding: 0.5rem 1rem;
    border-radius: 1rem;
    font-size: 0.875rem;
    font-weight: 600;
}

.stats-badge {
    position: absolute;
    top: 1.5rem;
    right: 1.5rem;
    background: rgba(16, 185, 129, 0.1);
    color: var(--success-color);
    padding: 0.5rem 1rem;
    border-radius: 1rem;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}

/* Content Cards */
.content-card {
    background: white;
    border-radius: 2rem;
    box-shadow: var(--shadow-lg);
    overflow: hidden;
    border: 1px solid var(--gray-200);
    transition: all 0.3s ease;
}

.content-card:hover {
    box-shadow: var(--shadow-xl);
    transform: translateY(-2px);
}

.card-header {
    padding: 2rem 2rem 1rem 2rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid var(--gray-100);
}

.header-title {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.header-title h3 {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--gray-800);
}

.header-title i {
    font-size: 1.5rem;
    color: var(--primary-color);
}

.header-action {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--primary-color);
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
    padding: 0.5rem 1rem;
    border-radius: 0.75rem;
}

.header-action:hover {
    background: var(--gray-50);
    transform: translateX(4px);
}

.card-content {
    padding: 2rem;
}

/* Students Grid */
.students-grid {
    display: grid;
    gap: 1.5rem;
}

.student-card {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    padding: 1.5rem;
    background: linear-gradient(135deg, var(--gray-50), white);
    border-radius: 1.5rem;
    border: 2px solid var(--gray-100);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    animation: fadeIn 0.6s ease-out forwards;
    opacity: 0;
}

.student-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-lg);
    border-color: var(--primary-color);
}

.student-avatar {
    position: relative;
    flex-shrink: 0;
}

.avatar-image {
    width: 4rem;
    height: 4rem;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid white;
    box-shadow: var(--shadow-md);
}

.avatar-placeholder {
    width: 4rem;
    height: 4rem;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--gray-300), var(--gray-400));
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
    border: 3px solid white;
    box-shadow: var(--shadow-md);
}

.status-indicator {
    position: absolute;
    bottom: 0;
    right: 0;
    width: 1rem;
    height: 1rem;
    background: var(--success-color);
    border-radius: 50%;
    border: 2px solid white;
    animation: pulse 2s infinite;
}

.student-info {
    flex-grow: 1;
}

.student-name {
    font-size: 1.125rem;
    font-weight: 700;
    color: var(--gray-800);
    margin-bottom: 0.5rem;
}

.student-id,
.student-date {
    font-size: 0.875rem;
    color: var(--gray-600);
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.25rem;
}

.student-id i,
.student-date i {
    color: var(--primary-color);
    width: 1rem;
}

.student-actions {
    display: flex;
    gap: 0.5rem;
}

.action-btn {
    width: 2.5rem;
    height: 2.5rem;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    transition: all 0.3s ease;
    font-size: 0.875rem;
}

.action-btn-primary {
    background: var(--primary-color);
    color: white;
}

.action-btn-secondary {
    background: var(--secondary-color);
    color: white;
}

.action-btn:hover {
    transform: scale(1.1);
    box-shadow: var(--shadow-lg);
}

/* Quick Actions */
.actions-grid {
    display: grid;
    gap: 1rem;
}

.quick-action-card {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.5rem;
    border-radius: 1.5rem;
    text-decoration: none;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: 2px solid transparent;
    position: relative;
    overflow: hidden;
}

.quick-action-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s;
}

.quick-action-card:hover::before {
    left: 100%;
}

.quick-action-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-lg);
}

.action-primary {
    background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
    color: white;
}

.action-blue {
    background: linear-gradient(135deg, #3b82f6, #60a5fa);
    color: white;
}

.action-green {
    background: linear-gradient(135deg, var(--success-color), #34d399);
    color: white;
}

.action-purple {
    background: linear-gradient(135deg, #8b5cf6, #a78bfa);
    color: white;
}

.action-orange {
    background: linear-gradient(135deg, #f97316, #fb923c);
    color: white;
}

.action-icon {
    width: 3rem;
    height: 3rem;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    flex-shrink: 0;
}

.action-content {
    flex-grow: 1;
}

.action-content h4 {
    font-size: 1rem;
    font-weight: 700;
    margin-bottom: 0.25rem;
}

.action-content p {
    font-size: 0.875rem;
    opacity: 0.9;
}

.action-arrow {
    font-size: 1rem;
    opacity: 0.7;
    transition: transform 0.3s ease;
}

.quick-action-card:hover .action-arrow {
    transform: translateX(4px);
}

/* System Info */
.system-info {
    display: grid;
    gap: 1rem;
}

.info-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: var(--gray-50);
    border-radius: 1rem;
    border: 1px solid var(--gray-200);
    transition: all 0.3s ease;
}

.info-item:hover {
    background: white;
    box-shadow: var(--shadow-md);
}

.info-icon {
    width: 2.5rem;
    height: 2.5rem;
    border-radius: 50%;
    background: var(--primary-color);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.info-content {
    flex-grow: 1;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.info-label {
    font-weight: 600;
    color: var(--gray-700);
}

.info-value {
    font-weight: 700;
    color: var(--gray-800);
    background: white;
    padding: 0.25rem 0.75rem;
    border-radius: 0.5rem;
    border: 1px solid var(--gray-200);
    font-size: 0.875rem;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    color: var(--gray-500);
}

.empty-icon {
    font-size: 4rem;
    margin-bottom: 1.5rem;
    opacity: 0.5;
}

.empty-state h4 {
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
    color: var(--gray-700);
}

.empty-state p {
    font-size: 1rem;
    opacity: 0.8;
}

/* Responsive Design */
@media (max-width: 768px) {
    .hero-title {
        font-size: 2.5rem;
    }
    
    .hero-subtitle {
        font-size: 1.25rem;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .student-card {
        flex-direction: column;
        text-align: center;
        gap: 1rem;
    }
    
    .student-actions {
        justify-content: center;
    }
    
    .time-display {
        flex-direction: column;
        gap: 1rem;
        padding: 1.5rem;
    }
    
    .time-divider {
        width: 2rem;
        height: 2px;
        margin: 0;
    }
    
    .card-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
}

/* Animation delays for staggered appearance */
.fade-in:nth-child(1) { animation-delay: 0.1s; }
.fade-in:nth-child(2) { animation-delay: 0.2s; }
.fade-in:nth-child(3) { animation-delay: 0.3s; }
.fade-in:nth-child(4) { animation-delay: 0.4s; }
.fade-in:nth-child(5) { animation-delay: 0.5s; }

.student-card:nth-child(1) { animation-delay: 0.1s; }
.student-card:nth-child(2) { animation-delay: 0.2s; }
.student-card:nth-child(3) { animation-delay: 0.3s; }
.student-card:nth-child(4) { animation-delay: 0.4s; }
.student-card:nth-child(5) { animation-delay: 0.5s; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Real-time clock update
    function updateClock() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('en-GB');
        const clockElement = document.getElementById('current-time');
        if (clockElement) {
            clockElement.textContent = timeString;
        }
    }

    // Update clock every second
    setInterval(updateClock, 1000);

    // Progress bar animation
    function animateProgressBar() {
        const progressFill = document.getElementById('progress-fill');
        if (progressFill) {
            let width = 0;
            const interval = setInterval(() => {
                if (width >= 100) {
                    clearInterval(interval);
                    setTimeout(() => {
                        progressFill.style.width = '0%';
                    }, 500);
                } else {
                    width += 2;
                    progressFill.style.width = width + '%';
                }
            }, 20);
        }
    }

    // Start progress bar animation
    setTimeout(animateProgressBar, 500);

    // Add hover effects to cards
    const cards = document.querySelectorAll('.stats-card, .content-card, .quick-action-card');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-8px)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });

    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Auto-refresh page data every 5 minutes
    setInterval(() => {
        console.log('Auto-refreshing dashboard data...');
        // You can add AJAX call here to refresh data without page reload
    }, 300000); // 5 minutes

    console.log('Dashboard loaded successfully!');
});

// Function to show loading state
function showLoading() {
    const buttons = document.querySelectorAll('.quick-action-card, .action-btn');
    buttons.forEach(btn => {
        btn.style.opacity = '0.7';
        btn.style.pointerEvents = 'none';
    });
}

// Function to hide loading state
function hideLoading() {
    const buttons = document.querySelectorAll('.quick-action-card, .action-btn');
    buttons.forEach(btn => {
        btn.style.opacity = '1';
        btn.style.pointerEvents = 'auto';
    });
}

// Add click handlers for smooth transitions
document.addEventListener('click', function(e) {
    const actionCard = e.target.closest('.quick-action-card');
    if (actionCard) {
        showLoading();
        // Add small delay for visual feedback
        setTimeout(() => {
            window.location.href = actionCard.href;
        }, 200);
        e.preventDefault();
    }
});
</script>
