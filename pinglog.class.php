<?php

/*
 @nom: pinglog
 @auteur: Julien LOISEAU (bird@tadikwa.fr) / Morvaivor (monogravity@gmail.com) / Iya (contact@iyadev.fr)
 @description:  Ping de surveillande, Wake on Lan, et log deséquipements 
 */

//Ce fichier permet de gerer vos donnees en provenance de la base de donnees

//Il faut changer le nom de la classe ici (je sens que vous allez oublier)
class pinglog extends SQLiteEntity{

	
	protected $id,$name,$ip,$mac,$description; //Variables de la base de données ici
	protected $TABLE_NAME = 'plugin_pinglog'; 	//nom du plugin
	protected $CLASS_NAME = 'pinglog';  //nom de la classe
	protected $object_fields = 
	array( //...Puis dans l'array ici mettre nom du champ => type
		'id'=>'key',
		'name'=>'string',
		'ip'=>'string',
		'mac'=>'string',
		'description'=>'string',
		'control'=>'string',
		'log'=>'integer',
		'libvirt'=>'integer',
		'backup'=>'integer'
	);

	function __construct(){
		parent::__construct();
	}
//Methodes pour recuperer et modifier les champs (set/get)
//Recuperation et modification tous les champs en base
	function setId($id){
		$this->id = $id;
	}
	
	function getId(){
		return $this->id;
	}

	function getName(){
		return $this->name;
	}

	function setName($name){
		$this->name = $name;
	}
	
    function getIp(){
		return $this->ip;
	}

	function setIp($ip){
		$this->ip = $ip;
	}

	function getMac(){
		return $this->mac;
	}

	function setMac($mac){
		$this->mac = $mac;
	}	
	
    function getDescription(){
		return $this->description;
	}

	function setDescription($description){
		$this->description = $description;
	}	
	function getControl(){
		return $this->control;
	}

	function setControl($control){
		$this->control = $control;
	}	
	function getBackup(){
		return $this->backup;
	}

	function setBackup($backup){
		$this->backup = $backup;
	}	
	function getLibvirt(){
		return $this->libvirt;
	}

	function setLibvirt($libvirt){
		$this->libvirt = $libvirt;
	}	
	function getLog(){
		return $this->log;
	}

	function setLog($log){
		$this->log = $log;
	}	
}

?>
