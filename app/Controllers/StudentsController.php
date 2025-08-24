<?php

require_once 'app/Core/Controller.php';
require_once 'app/Models/Student.php';
require_once 'app/Models/Major.php';
require_once 'app/Models/AcademicYear.php';

/**
 * Students Controller
 */
class StudentsController extends Controller {

    /**
     * List students with pagination and filters
     */
    public function index() {
        $this->requireAuth();

        $studentModel = new StudentModel($this->db);
        $majorModel = new MajorModel($this->db);
        $academicYearModel = new AcademicYearModel($this->db);

        // Get filter parameters
        $page = (int)($_GET['page'] ?? 1);
        $perPage = (int)($_GET['per_page'] ?? 10);
        $search = trim($_GET['search'] ?? '');
        $majorId = (int)($_GET['major_id'] ?? 0);
        $academicYearId = (int)($_GET['academic_year_id'] ?? 0);
        $gender = trim($_GET['gender'] ?? '');

        $filters = array_filter([
            'search' => $search,
            'major_id' => $majorId ?: null,
            'academic_year_id' => $academicYearId ?: null,
            'gender' => $gender ?: null
        ]);

        // Get paginated students
        $result = $studentModel->getWithPagination($page, $perPage, $filters);

        // Get filter options
        $majors = $majorModel->getActive();
        $academicYears = $academicYearModel->getActive();

        $this->view('students/index', [
            'title' => 'ລາຍຊື່ນັກຮຽນ',
            'students' => $result['data'],
            'pagination' => $result,
            'majors' => $majors,
            'academicYears' => $academicYears,
            'filters' => [
                'search' => $search,
                'major_id' => $majorId,
                'academic_year_id' => $academicYearId,
                'gender' => $gender
            ],
            'auth' => $this->auth
        ]);
    }

    /**
     * Show student details
     */
    public function show($id) {
        $this->requireAuth();

        $studentModel = new StudentModel($this->db);
        $student = $studentModel->getWithRelations($id);

        if (!$student) {
            Session::setMessage('ບໍ່ພົບຂໍ້ມູນນັກຮຽນ', 'error');
            $this->redirect('students');
        }

        $this->view('students/show', [
            'title' => 'ລາຍລະອຽດນັກຮຽນ',
            'student' => $student,
            'auth' => $this->auth
        ]);
    }

    /**
     * Show create student form
     */
    public function create() {
        $this->requireAuth();

        $majorModel = new MajorModel($this->db);
        $academicYearModel = new AcademicYearModel($this->db);

        $majors = $majorModel->getActive();
        $academicYears = $academicYearModel->getActive();

        $this->view('students/create', [
            'title' => 'ເພີ່ມນັກຮຽນໃໝ່',
            'majors' => $majors,
            'academicYears' => $academicYears,
            'auth' => $this->auth
        ]);
    }

    /**
     * Store new student
     */
    public function store() {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('students/create');
        }

        $this->validateCSRF();

        $data = Validator::sanitize($_POST);

        // Validation rules
        $validator = new Validator($data);
        $validator->setRules([
            'first_name' => 'required|min:2|max:100',
            'last_name' => 'required|min:2|max:100',
            'gender' => 'required',
            'dob' => 'required|date',
            'email' => 'email',
            'phone' => 'min:8|max:20',
            'major_id' => 'required|integer',
            'academic_year_id' => 'required|integer',
            'accommodation_type' => 'required'
        ]);

        // Validate email uniqueness if provided
        if (!empty($data['email'])) {
            $studentModel = new StudentModel($this->db);
            if (!$studentModel->isEmailUnique($data['email'])) {
                Session::setMessage('ອີເມລນີ້ຖືກນຳໃຊ້ແລ້ວ', 'error');
                $this->redirect('students/create');
            }
        }

        // Validate file upload
        if (isset($_FILES['photo'])) {
            $validator->validateFile('photo', 'image|max_size:5MB');
        }

        if (!$validator->validate()) {
            Session::setMessage($validator->getFirstError(), 'error');
            $this->redirect('students/create');
        }

        // Handle file upload
        $photoFilename = null;
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $uploadResult = $this->handleFileUpload($_FILES['photo']);
            if ($uploadResult['success']) {
                $photoFilename = $uploadResult['filename'];
            } else {
                Session::setMessage($uploadResult['error'], 'error');
                $this->redirect('students/create');
            }
        }

        // Prepare data for creation
        $createData = [
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'gender' => $data['gender'],
            'dob' => $data['dob'],
            'email' => $data['email'] ?: null,
            'phone' => $data['phone'] ?: null,
            'village' => $data['village'] ?: null,
            'district' => $data['district'] ?: null,
            'province' => $data['province'] ?: null,
            'accommodation_type' => $data['accommodation_type'],
            'photo' => $photoFilename,
            'major_id' => (int)$data['major_id'],
            'academic_year_id' => (int)$data['academic_year_id'],
            'previous_school' => $data['previous_school'] ?: null,
            'status' => 'active'
        ];

        $studentModel = new StudentModel($this->db);
        $studentId = $studentModel->createStudent($createData);

        if ($studentId) {
            Session::setMessage('ເພີ່ມຂໍ້ມູນນັກຮຽນສຳເລັດແລ້ວ', 'success');
            $this->redirect('students/show/' . $studentId);
        } else {
            Session::setMessage('ເກີດຂໍ້ຜິດພາດໃນການບັນທຶກຂໍ້ມູນ', 'error');
            $this->redirect('students/create');
        }
    }

    /**
     * Show edit student form
     */
    public function edit($id) {
        $this->requireAuth();

        $studentModel = new StudentModel($this->db);
        $student = $studentModel->find($id);

        if (!$student) {
            Session::setMessage('ບໍ່ພົບຂໍ້ມູນນັກຮຽນ', 'error');
            $this->redirect('students');
        }

        $majorModel = new MajorModel($this->db);
        $academicYearModel = new AcademicYearModel($this->db);

        $majors = $majorModel->getActive();
        $academicYears = $academicYearModel->getActive();

        $this->view('students/edit', [
            'title' => 'ແກ້ໄຂຂໍ້ມູນນັກຮຽນ',
            'student' => $student,
            'majors' => $majors,
            'academicYears' => $academicYears,
            'auth' => $this->auth
        ]);
    }

    /**
     * Update student
     */
    public function update($id) {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('students/edit/' . $id);
        }

        $this->validateCSRF();

        $studentModel = new StudentModel($this->db);
        $student = $studentModel->find($id);

        if (!$student) {
            Session::setMessage('ບໍ່ພົບຂໍ້ມູນນັກຮຽນ', 'error');
            $this->redirect('students');
        }

        $data = Validator::sanitize($_POST);

        // Validation rules
        $validator = new Validator($data);
        $validator->setRules([
            'first_name' => 'required|min:2|max:100',
            'last_name' => 'required|min:2|max:100',
            'gender' => 'required',
            'dob' => 'required|date',
            'email' => 'email',
            'phone' => 'min:8|max:20',
            'major_id' => 'required|integer',
            'academic_year_id' => 'required|integer',
            'accommodation_type' => 'required'
        ]);

        // Validate email uniqueness if provided
        if (!empty($data['email'])) {
            if (!$studentModel->isEmailUnique($data['email'], $id)) {
                Session::setMessage('ອີເມລນີ້ຖືກນຳໃຊ້ແລ້ວ', 'error');
                $this->redirect('students/edit/' . $id);
            }
        }

        // Validate file upload
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $validator->validateFile('photo', 'image|max_size:5MB');
        }

        if (!$validator->validate()) {
            Session::setMessage($validator->getFirstError(), 'error');
            $this->redirect('students/edit/' . $id);
        }

        // Handle file upload
        $photoFilename = $student['photo']; // Keep existing photo by default
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $uploadResult = $this->handleFileUpload($_FILES['photo']);
            if ($uploadResult['success']) {
                // Delete old photo if exists
                if ($student['photo']) {
                    $oldPhotoPath = BASE_PATH . '/public/uploads/photos/' . $student['photo'];
                    if (file_exists($oldPhotoPath)) {
                        unlink($oldPhotoPath);
                    }
                }
                $photoFilename = $uploadResult['filename'];
            } else {
                Session::setMessage($uploadResult['error'], 'error');
                $this->redirect('students/edit/' . $id);
            }
        }

        // Prepare data for update
        $updateData = [
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'gender' => $data['gender'],
            'dob' => $data['dob'],
            'email' => $data['email'] ?: null,
            'phone' => $data['phone'] ?: null,
            'village' => $data['village'] ?: null,
            'district' => $data['district'] ?: null,
            'province' => $data['province'] ?: null,
            'accommodation_type' => $data['accommodation_type'],
            'photo' => $photoFilename,
            'major_id' => (int)$data['major_id'],
            'academic_year_id' => (int)$data['academic_year_id'],
            'previous_school' => $data['previous_school'] ?: null
        ];

        if ($studentModel->update($id, $updateData)) {
            Session::setMessage('ແກ້ໄຂຂໍ້ມູນນັກຮຽນສຳເລັດແລ້ວ', 'success');
            $this->redirect('students/show/' . $id);
        } else {
            Session::setMessage('ເກີດຂໍ້ຜິດພາດໃນການບັນທຶກຂໍ້ມູນ', 'error');
            $this->redirect('students/edit/' . $id);
        }
    }

    /**
     * Delete student
     */
    public function delete($id) {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('students');
        }

        $this->validateCSRF();

        $studentModel = new StudentModel($this->db);
        $student = $studentModel->find($id);

        if (!$student) {
            Session::setMessage('ບໍ່ພົບຂໍ້ມູນນັກຮຽນ', 'error');
            $this->redirect('students');
        }

        // Soft delete - update status instead of hard delete
        if ($studentModel->update($id, ['status' => 'deleted'])) {
            // Delete photo file if exists
            if ($student['photo']) {
                $photoPath = BASE_PATH . '/public/uploads/photos/' . $student['photo'];
                if (file_exists($photoPath)) {
                    unlink($photoPath);
                }
            }

            Session::setMessage('ລຶບຂໍ້ມູນນັກຮຽນສຳເລັດແລ້ວ', 'success');
        } else {
            Session::setMessage('ເກີດຂໍ້ຜິດພາດໃນການລຶບຂໍ້ມູນ', 'error');
        }

        $this->redirect('students');
    }

    /**
     * Handle file upload
     */
    private function handleFileUpload($file) {
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'error' => 'ບໍ່ມີໄຟລ໌ຫຼືເກີດຂໍ້ຜິດພາດໃນການອັບໂຫຼດ'];
        }

        // Check file size (5MB)
        if ($file['size'] > 5 * 1024 * 1024) {
            return ['success' => false, 'error' => 'ຂະໜາດໄຟລ໌ໃຫຍ່ເກີນໄປ'];
        }

        // Check file type
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        if (!in_array($file['type'], $allowedTypes)) {
            return ['success' => false, 'error' => 'ປະເພດໄຟລ໌ບໍ່ຖືກຕ້ອງ'];
        }

        // Create upload directory if not exists
        $uploadDir = BASE_PATH . '/public/uploads/photos/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'student_' . time() . '_' . mt_rand(1000, 9999) . '.' . $extension;
        $filepath = $uploadDir . $filename;

        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return ['success' => true, 'filename' => $filename];
        } else {
            return ['success' => false, 'error' => 'ບໍ່ສາມາດບັນທຶກໄຟລ໌ໄດ້'];
        }
    }
}