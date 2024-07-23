<?php
// app/Models/MyModel.php
namespace App\Models;

use CodeIgniter\Model;

class MyModel extends Model
{
    protected $table = 'my_table'; // Ganti dengan nama tabel Anda
    protected $primaryKey = 'id'; // Ganti dengan nama primary key Anda
    protected $allowedFields = ['field1', 'field2']; // Ganti dengan field yang diizinkan untuk diisi
    public function showTables(){
        $query = $this->query('SHOW TABLES;');
        return $query->getResultArray();
    }
    public function showCreateTables($table_name){
        $query = $this->query('SHOW CREATE TABLE '.$table_name.';');
        return $query->getResultArray();
    }
    public function describeTable($table_name){
        $query = $this->query('DESCRIBE '.$table_name.';');
        return $query->getResultArray();
    }
}