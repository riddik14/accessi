<?php require_once('../Connections/MyPresenze.php'); 
 
class Autocomplete
{
    public $term;
    public $conn;
 
        public function printResult()
            {
            global $database_MyPresenze, $MyPresenze;
            mysql_select_db($database_MyPresenze, $MyPresenze);
            $sql = "SELECT pre_sedi.IDsede, pre_sedi.SEDE FROM pre_sedi";
            $res = mysql_query($sql, $MyPresenze) or die(mysql_error());
		 
            $parameters = array();
            $arr = array();
 
            while($row = mysql_fetch_array($res))
            {
                $arr['id'] = $row['IDnome'];
                $arr['value'] =  $row['IDnome'] . "-" .$row['Cognome']. " " . $row['Nome'];
                array_push($parameters, $arr);
            }
 
            echo json_encode($parameters);
        }
}
 
?>