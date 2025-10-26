<?php 
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateStaffLocations extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'staff_id' => ['type'=>'INT','unsigned'=>true],
            'location_id' => ['type'=>'INT','unsigned'=>true],
        ]);
        $this->forge->addKey(['staff_id','location_id'], true);
        $this->forge->createTable('staff_locations');
    }
    public function down()
    {
        $this->forge->dropTable('staff_locations');
    }
}
