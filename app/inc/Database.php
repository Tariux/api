<?php 


class Database extends PDO {

    protected function connect()
    {
        $server_name = "localhost";
        $db_name = "neelaboo_db";
        $username = "root";
        $password = "";

        try {
        $conn = new PDO("mysql:host=$server_name;dbname=$db_name", $username, $password);
        // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return $conn;

        } catch(PDOException $e) {
            echo "Failed: " . $e->getMessage();
            return false;
        }
    }




    protected function check_table($table_name)
    {

        $result = $this->connect()->prepare("SHOW TABLES LIKE '".$table_name."'");

        if ($result->execute()) {
            if($result->rowCount() == 1) {
                return true;
            }
        }
        else {
            return false;
        }
    }
    public function create_table($table_name , $items_sql = false)
    {

        if (empty($table_name)) {
            return false;
        }
        if ($this->check_table($table_name)) {
            return false;
        }

        if (is_array($items_sql)) {

            $result = $this->connect()->prepare("CREATE TABLE ?
            $items_sql
           ");

        } else {

            $result = $this->connect()->prepare("CREATE TABLE $table_name( `id` INT NOT NULL AUTO_INCREMENT , PRIMARY KEY (`id`))");
            
        }


        if ($result->execute()) {
                return true;
        }
        else {
            return false;
        }
    }
    public function drop_table($table_name)
    {

        if (empty($table_name)) {
            return false;
        }
        if (!$this->check_table($table_name)) {
            return false;
        }


        $result = $this->connect()->prepare("DROP TABLE $table_name");
            
        
        if ($result->execute()) {
                return true;
        }
        else {
            return false;
        }
    }
    public function check_alter($table_name , $name)
    {

        if (empty($table_name)) {
            return false;
        }
        if (!$this->check_table($table_name)) {
            return false;
        }

        $result = $this->connect()->prepare("SHOW COLUMNS FROM $table_name LIKE '$name'");

        
        if ($result->execute()) {
            if ($result->rowCount() != 0) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }

            
        
    }

    public function alter_table($table_name , $name , $flash = false , $type = "varchar(255)" , $option = "")
    {
        if (empty($table_name)) {
            return false;
        }

        if ($this->check_alter($table_name , $name)) {
            return false;
        }

        if ($flash == false) {
            if (!$this->check_table($table_name)) {
                return false;
            }
        }

        $this->create_table($table_name);


        $result = $this->connect()->prepare("ALTER TABLE $table_name ADD $name $type $option");
            
        
        if ($result->execute()) {
                return true;
        }
        else {
            return false;
        }

    }

    public function get_table($table_name , $order_by = "id")
    {

        if (empty($table_name) && !($this->check_table($table_name))) {
            return false;
        }


        $sql = "SELECT * FROM $table_name ORDER BY `$order_by` DESC";
        $query = $this->connect()->prepare($sql);
        

        if ($query->execute()) {
            $result = $query->fetchAll();
            return $result;
        } else {
            return false;
        }

    }

    public function get_row($table_name , $row_name , $search_for , $limit = false , $row_name_2 = false, $search_for_2 = false)
    {
        if (empty($table_name) || empty($row_name) || empty($search_for)) {
            return false;
        }

        if (!($this->check_table($table_name))) {
            return false;
        }

        if ($row_name_2 && $search_for_2) {
            $condition_two = "AND $row_name_2 = '$search_for_2'";
        } else {
            $condition_two = "";
        }
        
        if ($limit) {
            $sql = "SELECT * FROM $table_name WHERE $row_name = ? $condition_two LIMIT $limit";
        } else {
            $sql = "SELECT * FROM $table_name WHERE $row_name = ? $condition_two";

        }

        
        $query = $this->connect()->prepare($sql);

        $query->bindParam(1, $search_for);

        if ($query->execute()) {

            $result = $query->fetchAll();
            if (empty($result)) {
                return false;
            }

            if (count($result) == 1) {
                return $result[0];
            }
            
            return $result;
        } else {
            return false;
        }


    }
    public function insert($table_name , $keys_and_values , $force = false)
    {
        if (empty($table_name) || empty($keys_and_values)) {
            return false;
        }
        if (!($this->check_table($table_name))) {
            if ($force) {
                $this->create_table($table_name);
            } else {
                return false;
            }
        } 

        $keys_str = "";

        $values_str = "";


        foreach ($keys_and_values as $key => $value) {
            $keys_str .=  $key . " ,";
            $values_str .= "'" .$value . "' ,";
        }

        $keys_str = substr_replace($keys_str , "", -1);
        $values_str = substr_replace($values_str , "", -1);


        $sql = "INSERT INTO $table_name ($keys_str) VALUES ($values_str)";

        $query = $this->connect()->prepare($sql);

        if ($query->execute()) {
            return true;
        } else {
            return false;
        }

    }



    public function update($table_name , $keys_and_values , $row_name , $search_for )
    {
        if (empty($table_name) || empty($keys_and_values)) {
            return false;
        }
        if (!($this->check_table($table_name))) {
            return false;
            
        } 

        $update_str = "";

        foreach ($keys_and_values as $key => $value) {
            $update_str .=  $key . " = '" . $value . "' ,";
        }

        $update_str = substr_replace($update_str , "", -1);
        $sql = "UPDATE $table_name SET $update_str WHERE `$row_name` = $search_for";

        $query = $this->connect()->prepare($sql);

        if ($query->execute()) {
            return true;
        } else {
            return false;
        }

    }


    public function drop($table , $key , $value)
    {
        if (!($this->check_table($table))) {
            return false;
        }

        $sql = "DELETE FROM $table WHERE $key = $value";

        $query = $this->connect()->prepare($sql);
        
        if ($this->get_row($table , $key , $value)) {
            if ($query->execute()) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }


    }







}



