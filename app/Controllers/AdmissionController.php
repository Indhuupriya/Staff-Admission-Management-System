<?php namespace App\Controllers;
use App\Models\AdmissionModel;
use App\Models\StaffModel;
use CodeIgniter\RESTful\ResourceController;

class AdmissionController extends ResourceController
{
    protected $admission;
    protected $staff;
    public function __construct(){ $this->admission = new AdmissionModel(); $this->staff = new StaffModel(); }

    public function index()
    {
        echo view('admissions/index');
    }

    public function apiList()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('admissions a')
            ->select('a.*, s.name as staff_name, s.branch as staff_branch')
            ->join('staffs s','s.id = a.created_by','left')
            ->orderBy('a.created_at','DESC');

        $rows = $builder->get()->getResultArray();
        return $this->respond(['data'=>$rows]);
    }

    public function apiCreate()
    {
        $json = $this->request->getJSON(true);
        $this->admission->insert($json);
        return $this->respondCreated(['status'=>'created']);
    }

    public function apiUpdate($id)
    {
        $json = $this->request->getJSON(true);
        $this->admission->update($id,$json);
        return $this->respond(['status'=>'updated']);
    }

    public function apiUpdateStatus($id)
    {
        $post = $this->request->getJSON(true);
        if(!isset($post['status'])) return $this->respond(['error'=>'No status provided'], 400);
        $this->admission->update($id, ['status'=>$post['status']]);
        return $this->respond(['status'=>'ok']);
    }
}
