<?php namespace App\Controllers;
use App\Models\StaffModel;
use App\Models\LocationModel;
use CodeIgniter\RESTful\ResourceController;

class StaffController extends ResourceController
{
    protected $staff;
    protected $location;
    public function __construct(){ $this->staff = new StaffModel(); $this->location = new LocationModel(); }

    public function index() { 
        
        $data['locations'] = $this->location->findAll();
        echo view('staffs/index', $data);
    }

    public function apiList()
    {
        $staffs = $this->staff->findAll();
        $db = \Config\Database::connect();
    
        foreach($staffs as &$staff){
            $locs = $db->table('staff_locations')
                       ->select('l.id, l.name')
                       ->join('locations l', 'l.id = staff_locations.location_id')
                       ->where('staff_id', $staff['id'])
                       ->get()
                       ->getResultArray();
            $staff['location_ids'] = array_column($locs, 'id');
            $staff['locations'] = array_column($locs, 'name');
        }    
        return $this->respond(['data'=>$staffs]);
    }
    
    public function apiCreate()
    {
        $json = $this->request->getJSON();
        $post = (array)$json;
        $post['password'] = password_hash($post['password'], PASSWORD_DEFAULT);
        $id = $this->staff->insert($post);
        if(isset($post['locations']) && is_array($post['locations'])){
            $db = \Config\Database::connect();
            foreach($post['locations'] as $loc){
                $db->table('staff_locations')->insert(['staff_id'=>$id, 'location_id'=>$loc]);
            }
        }
        return $this->respondCreated(['id'=>$id]);
    }

    public function apiUpdate($id)
    {
        $json = $this->request->getJSON(true);
        if(isset($json['password']) && $json['password']){
            $json['password'] = password_hash($json['password'], PASSWORD_DEFAULT);
        } else {
            unset($json['password']);
        }
        $this->staff->update($id, $json);
        $db = \Config\Database::connect();
        $db->table('staff_locations')->where('staff_id',$id)->delete();
        if(isset($json['locations']) && is_array($json['locations'])){
            foreach($json['locations'] as $loc){
                $db->table('staff_locations')->insert(['staff_id'=>$id,'location_id'=>$loc]);
            }
        }
        return $this->respond(['status'=>'ok']);
    }

    public function apiDelete($id)
    {
        $this->staff->delete($id);
        $db = \Config\Database::connect();
        $db->table('staff_locations')->where('staff_id',$id)->delete();
        return $this->respondDeleted(['status'=>'deleted']);
    }
}
