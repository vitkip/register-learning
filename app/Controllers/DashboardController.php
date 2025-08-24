<?php

require_once 'app/Core/Controller.php';
require_once 'app/Models/Student.php';
require_once 'app/Models/Major.php';
require_once 'app/Models/AcademicYear.php';
require_once 'app/Models/Subject.php';
require_once 'app/Models/Enrollment.php';
require_once 'app/Models/Settings.php';

/**
 * Dashboard Controller
 */
class DashboardController extends Controller {

    /**
     * Dashboard index page
     */
    public function index() {
        $this->requireAuth();

        // Initialize models
        $studentModel = new StudentModel($this->db);
        $majorModel = new MajorModel($this->db);
        $academicYearModel = new AcademicYearModel($this->db);
        $subjectModel = new SubjectModel($this->db);
        $enrollmentModel = new EnrollmentModel($this->db);

        // Get statistics
        try {
            $stats = [
                'total_students' => $studentModel->count(),
                'total_majors' => $majorModel->count('status = ?', ['active']),
                'total_subjects' => $subjectModel->count('status = ?', ['active']),
                'total_academic_years' => $academicYearModel->count(),
                'total_enrollments' => $enrollmentModel->count(),
                'active_students' => $studentModel->count('status = ?', ['active']),
                'male_students' => $studentModel->count('gender = ?', ['ຊາຍ']),
                'female_students' => $studentModel->count('gender = ?', ['ຍິງ']),
                'monk_students' => $studentModel->count('gender = ?', ['ພຣະ'])
            ];

            // Get recent students
            $recentStudents = $studentModel->query(
                "SELECT s.*, m.name as major_name 
                 FROM students s 
                 LEFT JOIN majors m ON s.major_id = m.id 
                 ORDER BY s.created_at DESC 
                 LIMIT 5"
            );

            // Get settings for system info
            $settingsModel = new SettingsModel($this->db);
            $systemName = $settingsModel->get('system_name', 'ລະບົບລົງທະບຽນນັກສຶກສາ');
            $schoolName = $settingsModel->get('school_name', 'ວິທະຍາໄລການສຶກສາ');

        } catch (Exception $e) {
            error_log("Dashboard error: " . $e->getMessage());
            $stats = [
                'total_students' => 0,
                'total_majors' => 0,
                'total_subjects' => 0,
                'total_academic_years' => 0,
                'total_enrollments' => 0,
                'active_students' => 0,
                'male_students' => 0,
                'female_students' => 0,
                'monk_students' => 0
            ];
            $recentStudents = [];
            $systemName = 'ລະບົບລົງທະບຽນນັກສຶກສາ';
            $schoolName = 'ວິທະຍາໄລການສຶກສາ';
        }

        $this->view('dashboard/index', [
            'title' => 'ໜ້າຫຼັກ',
            'stats' => $stats,
            'recentStudents' => $recentStudents,
            'systemName' => $systemName,
            'schoolName' => $schoolName,
            'auth' => $this->auth
        ]);
    }
}