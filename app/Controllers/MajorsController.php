<?php

require_once 'app/Core/Controller.php';
require_once 'app/Models/Major.php';

/**
 * Majors Controller
 */
class MajorsController extends Controller {

    /**
     * List majors
     */
    public function index() {
        $this->requireAuth();

        $majorModel = new MajorModel($this->db);
        $majors = $majorModel->getWithStudentCount();

        $this->view('majors/index', [
            'title' => 'ຈັດການສາຂາວິຊາ',
            'majors' => $majors,
            'auth' => $this->auth
        ]);
    }

    /**
     * Show create major form
     */
    public function create() {
        $this->requireAuth();

        $this->view('majors/create', [
            'title' => 'ເພີ່ມສາຂາວິຊາໃໝ່',
            'auth' => $this->auth
        ]);
    }

    /**
     * Store new major
     */
    public function store() {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('majors/create');
        }

        $this->validateCSRF();

        $data = Validator::sanitize($_POST);

        // Validation
        $validator = new Validator($data);
        $validator->setRules([
            'name' => 'required|min:3|max:100',
            'code' => 'min:2|max:10',
            'description' => 'max:500'
        ]);

        if (!$validator->validate()) {
            Session::setMessage($validator->getFirstError(), 'error');
            $this->redirect('majors/create');
        }

        // Check code uniqueness if provided
        $majorModel = new MajorModel($this->db);
        if (!empty($data['code']) && !$majorModel->isCodeUnique($data['code'])) {
            Session::setMessage('ລະຫັດສາຂານີ້ຖືກນຳໃຊ້ແລ້ວ', 'error');
            $this->redirect('majors/create');
        }

        $createData = [
            'name' => $data['name'],
            'code' => $data['code'] ?: null,
            'description' => $data['description'] ?: null,
            'status' => 'active'
        ];

        if ($majorModel->create($createData)) {
            Session::setMessage('ເພີ່ມສາຂາວິຊາສຳເລັດແລ້ວ', 'success');
            $this->redirect('majors');
        } else {
            Session::setMessage('ເກີດຂໍ້ຜິດພາດໃນການບັນທຶກຂໍ້ມູນ', 'error');
            $this->redirect('majors/create');
        }
    }

    /**
     * Show edit major form
     */
    public function edit($id) {
        $this->requireAuth();

        $majorModel = new MajorModel($this->db);
        $major = $majorModel->find($id);

        if (!$major) {
            Session::setMessage('ບໍ່ພົບຂໍ້ມູນສາຂາວິຊາ', 'error');
            $this->redirect('majors');
        }

        $this->view('majors/edit', [
            'title' => 'ແກ້ໄຂສາຂາວິຊາ',
            'major' => $major,
            'auth' => $this->auth
        ]);
    }

    /**
     * Update major
     */
    public function update($id) {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('majors/edit/' . $id);
        }

        $this->validateCSRF();

        $majorModel = new MajorModel($this->db);
        $major = $majorModel->find($id);

        if (!$major) {
            Session::setMessage('ບໍ່ພົບຂໍ້ມູນສາຂາວິຊາ', 'error');
            $this->redirect('majors');
        }

        $data = Validator::sanitize($_POST);

        // Validation
        $validator = new Validator($data);
        $validator->setRules([
            'name' => 'required|min:3|max:100',
            'code' => 'min:2|max:10',
            'description' => 'max:500',
            'status' => 'required'
        ]);

        if (!$validator->validate()) {
            Session::setMessage($validator->getFirstError(), 'error');
            $this->redirect('majors/edit/' . $id);
        }

        // Check code uniqueness if provided
        if (!empty($data['code']) && !$majorModel->isCodeUnique($data['code'], $id)) {
            Session::setMessage('ລະຫັດສາຂານີ້ຖືກນຳໃຊ້ແລ້ວ', 'error');
            $this->redirect('majors/edit/' . $id);
        }

        $updateData = [
            'name' => $data['name'],
            'code' => $data['code'] ?: null,
            'description' => $data['description'] ?: null,
            'status' => $data['status']
        ];

        if ($majorModel->update($id, $updateData)) {
            Session::setMessage('ແກ້ໄຂສາຂາວິຊາສຳເລັດແລ້ວ', 'success');
            $this->redirect('majors');
        } else {
            Session::setMessage('ເກີດຂໍ້ຜິດພາດໃນການບັນທຶກຂໍ້ມູນ', 'error');
            $this->redirect('majors/edit/' . $id);
        }
    }

    /**
     * Delete major
     */
    public function delete($id) {
        $this->requireAdmin(); // Only admin can delete majors

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('majors');
        }

        $this->validateCSRF();

        $majorModel = new MajorModel($this->db);
        $major = $majorModel->find($id);

        if (!$major) {
            Session::setMessage('ບໍ່ພົບຂໍ້ມູນສາຂາວິຊາ', 'error');
            $this->redirect('majors');
        }

        // Check if major has students
        $studentCount = $majorModel->query(
            "SELECT COUNT(*) as count FROM students WHERE major_id = ? AND status != 'deleted'",
            [$id]
        )[0]['count'] ?? 0;

        if ($studentCount > 0) {
            Session::setMessage('ບໍ່ສາມາດລຶບສາຂາວິຊາທີ່ມີນັກຮຽນຢູ່ໄດ້', 'error');
            $this->redirect('majors');
        }

        if ($majorModel->delete($id)) {
            Session::setMessage('ລຶບສາຂາວິຊາສຳເລັດແລ້ວ', 'success');
        } else {
            Session::setMessage('ເກີດຂໍ້ຜິດພາດໃນການລຶບຂໍ້ມູນ', 'error');
        }

        $this->redirect('majors');
    }
}