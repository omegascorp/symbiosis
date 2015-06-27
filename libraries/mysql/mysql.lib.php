<?
class MySQL{
	private $host;			//Host(localhost)
	private $user;			//Username
	private $pass;			//Password
	private $db;			//Data base
	private $last;			//Last query
	function __construct($host, $user, $pass, $db){
		$this->host=$host;
		$this->user=$user;
		$this->pass=$pass;
		$this->db=$db;
		@mysql_connect($this->host, $this->user, $this->pass) or die('Can not connect to DB');
		mysql_query('SET NAMES utf8');
		@mysql_select_db($this->db) or die('Can not select DB');
	}
	function __destruct(){
		//mysql_close();
	}
	//Formating result
	private function result($result, $is_limit_1=true){
		$rows=array();
		if(!$result) return "";
		while($row=mysql_fetch_assoc($result)){
			$rows[]=$row;
		}
		if(count($rows)==1&&$is_limit_1){
			if(count($rows[0])==1){
				return array_pop($rows[0]);
			}
			else{
				return $rows[0];
			}
		}
		elseif(!count($rows)){
			return "";
		}
		else{
			return $rows;
		}
	}
	//Convert 'where' in string
	private function where($a){
		if(is_array($a)){
			$ret="";
			foreach($a as $key => $val){
				//$ret.=($ret?" AND `".$key."`":"`".$key."`");
				if(is_array($val)){
					//$ret.=" ".$val[1]." '".$val[0]."'";
					$current='';
					foreach($val as $v){
						if($current) $current.=',';
						$current.="'".$v."'";
					}
					$current='`'.$key.'` IN('.$current.')';
				}
				else{
					//$ret.=" = '".$val."'";
					$current="`".$key."` = '".$val."'";
				}
				if($ret) $ret.=' AND ';
				$ret.=$current;
			}
			return $ret;
		}
		return $a;
	}
	//Conwert 'order' to string
	private function order($a){
		if(is_array($a)){
			$r="";
			foreach($a as $key){
				$r.=$r?", `".$key."`":"`".$key."`";
			}
			return $r;
		}
		return $a;
	}
	//Convert 'limit' to string
	private function limit($a){
		if(is_array($a)){
			$r="";
			foreach($a as $key){
				$r.=$r?", ".$key:$key;
			}
			return $r;
		}
		return $a;
	}
	//Implement mysql query and return array
	function query($query, $isLimit1=false, $isReturnArray=true){
		$this->last=$query;
		$r=mysql_query($query);
		if($isReturnArray) return $this->result($r, $isLimit1);
		return $r;
	}
	//Return the num of rows
	function num_rows($table, $where, $limit){
		$where=$this->where($where);
		$limit=$this->limit($limit);
		if($where) $where="WHERE ".$where;
		if($limit) $limit="LIMIT ".$limit;
		$q="SELECT 1 FROM `".$table."` ".$where." ".$limit;
		$r=mysql_query($q);
		$this->last=$q;
		return mysql_num_rows($r);
	}
	//Insert into data base
	function insert($table, $values, $returnId=true){
		$vars="";
		$vals="";
		foreach($values as $key => $val){
			$vars.=$vars?", `".$key."`":"`".$key."`";
			$vals.=$vals?", '".$val."'":"'".$val."'";
		}
		$q="INSERT INTO `".$table."` (".$vars.") VALUES (".$vals.")";
		$this->last=$q;
		$r=mysql_query($q);
		return $r?($returnId?mysql_insert_id():$r):0;
	}
	//Update data base
	function update($table, $values, $where="", $limit=""){
		$where=$this->where($where);
		$limit=$this->limit($limit);
		if(is_array($values)){
			$vals="";
			foreach($values as $key => $val){
				$vals.=$vals?", `".$key."` = '".$val."'":"`".$key."` = '".$val."'";
			}
		}
		else{
			$vals=$values;
		}
		$vals="SET ".$vals;
		if($where) $where="WHERE ".$where;
		if($limit) $limit="LIMIT ".$limit;
		$q="UPDATE `".$table."` ".$vals." ".$where." ".$limit."";
		$this->last=$q;
		$r=mysql_query($q);
		return $r;
	}
	//Delete from data base
	function delete($table, $where="", $limit=""){
		$where=$this->where($where);
		$limit=$this->limit($limit);
		if($where) $where="WHERE ".$where;
		if($limit) $limit="LIMIT ".$limit;
		$q="DELETE FROM `".$table."` ".$where." ".$limit;
		$this->last=$q;
		$r=mysql_query($q);
		return $r;
	}
	//Select form data base and return the array
	function select($table, $values="*", $where="", $order="", $limit=""){
		if($limit==""&&is_int($order)) $limit=$order;
		if(is_array($limit)) $is_limit_1=$limit[1]==1?true:false;
		elseif($limit==1) $is_limit_1=true;
		else $is_limit_1=false;
		
		$where=$this->where($where);
		$order=$this->order($order);
		$limit=$this->limit($limit);
		if($where) $where="WHERE ".$where;
		if($order) $order="ORDER BY ".$order;
		if($limit) $limit="LIMIT ".$limit;
		if(is_array($values)){
			$vals="";
			foreach($values as $val){
				$vals.=$vals?", `".$val."`":"`".$val."`";
			}
		}
		elseif($values=="*"){
			$vals=$values;
		}
		else{
			$vals="`".$values."`";
		}
		$q="SELECT ".$vals." FROM `".$table."` ".$where." ".$order." ".$limit;
		$this->last=$q;
		$m=mysql_query($q);
		return $this->result($m, $is_limit_1);
	}
	public function count($table, $where="", $limit=""){
		$where=$this->where($where);
		$limit=$this->limit($limit);
		if($where) $where="WHERE ".$where;
		if($limit) $limit="LIMIT ".$limit;
		$q="SELECT COUNT(1) as `count` FROM `".$table."` ".$where." ".$limit;
		$this->last=$q;
		$m=mysql_query($q);
		return $this->result($m);
	}
	//Return last query
	function __toString(){
		return $this->last;
	}
};
?>