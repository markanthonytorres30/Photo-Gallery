<?php

/**
 * Group
 *
 * Each group has several rights. Those rights will
 * be used to determine what a user belonging to 
 * the groups is allowed to do.
 * Groups are stored in the [thumbs]/groups.xml file.
 * Their structure is :
 * - Name
 * - Rights -> Right
 */

class Group
{
	public $name;
	public $rights;
	
	/**
	 * Find group in base.
	 *
	 * @param string $name 
	 */
	public function __construct($name = NULL){
		
		/// Check if group file exists
		if(!file_exists(CurrentUser::$groups_file)){
			Group::create_group_file();
		}

		if($name == NULL){
			return;
		}

		/// Load file
		$xml		=	simplexml_load_file(CurrentUser::$groups_file);

		foreach( $xml as $group ){
			if( $name == (string)$group->name){
				$this->name		=	$group->name;
				$this->rights	=	$group->rights;
				return;
			}
		}
		throw new Exception("$name not found");
	}

	/**
	 * Create group file
	 *
	 * @return void
	 */
	public static function create_group_file(){
			$xml=new SimpleXMLElement('<groups></groups>');
			$xml->asXML(CurrentUser::$groups_file);
			Group::create("root");
			Group::create("uploaders");
			Group::create("user");
	}

	/**
	 * Create group and save into base
	 *
	 * @param string $name 
	 * @param string $rights 
	 * @return void
	 */
	public static function create($name,$rights=array()){
		if(!isset($name)||strlen($name)<1){
			return;
		}
		/// Check that groups file exists
		if(!file_exists(CurrentUser::$groups_file)){
			Group::create_group_file();
		}
		/// Check that group doesn't already exist
		if(self::exists($name))
			throw new Exception("$name already exists");

		/// Load file
		$xml		=	simplexml_load_file(CurrentUser::$groups_file);
		
		$g=$xml->addChild('group');
		$g->addChild('name',$name);
		$xml_rights=$g->addChild('rights');
		
		foreach($rights as $r)
			$xml_right->addChild($r);
		
		$xml->asXML(CurrentUser::$groups_file);
	}
	
	/**
	 * Delete a group
	 *
	 * @param string $groupname 
	 * @return void
	 */
	public static function delete($groupname){
		$xml_infos 	=	CurrentUser::$groups_file;
		$xml		=	simplexml_load_file($xml_infos);
		
		$i=-1;
		$found = false;

		foreach( $xml as $group ){
			$i++;
			if((string)$group->name == $groupname){
				$found = true;
				break;
			}
		}
		
		if($found){
			unset($xml->group[$i]);
		}

		$xml->asXML($xml_infos);
	}

	/**
	 * Save group into base
	 *
	 * @return void
	 */
	public function save(){
		/// Load file
		$xml		=	simplexml_load_file(CurrentUser::$groups_file);

		foreach( $xml as $group ){
			if( (string)$group->name == $this->name){
				unset($group->rights);
				$xml_rights=$group->addChild('rights');
				
				foreach( $this->rights as $right )
					$xml_rights->addChild('right',$right);
		
				$xml->asXML(CurrentUser::$groups_file);
				return;
			}
		}
		
		throw new Exception("$this->name not found");
	}
	
	/**
	 * Check if the group is already in the base
	 *
	 * @param string $name 
	 * @return bool
	 */
	public static function exists($name){
		try{
		$settings	=	new Settings();
		}catch(Exception $e){
			// No file, no group !
			return false;
		}
		
		/// Load file
		$xml		=	simplexml_load_file(CurrentUser::$groups_file);

		foreach( $xml as $group ){
			if( (string)$group->name == $name)
				return true;
		}
		
		return false;
	}
	
	/**
	 * Returns the rights of the group
	 *
	 * @param string $name 
	 * @return void
	 */
	public static function rights($name){
		$rights		=	array();

		/// Load file
		$xml		=	simplexml_load_file(CurrentUser::$groups_file);

		foreach( $xml as $group ){
			if( (string)$group->name == $name){
				foreach($group->rights as $right){
					$rights[]=$right;
				}
				return $rights;
			}
		}
		throw new Exception("$name not found");
	}
	
	public static function findAll(){
		
		$groups		=	array();

		/// Load file
		$xml		=	simplexml_load_file(CurrentUser::$groups_file);

		foreach( $xml as $group ){

			$newgroup = array();
			$newgroup['name'] 	= $group->name;
			$newgroup['rights']	= array();
			
			foreach($group->rights->children() as $right){
				$newgroup['rights'][] = $right;
			}
			
			$groups[] = $newgroup;
		}

		return $groups;

	}
	

	public static function edit($groups){
		$allgroups = Group::findAll();
		$allaccounts = Account::findAll();
		foreach($allaccounts as $acc){
			$accgroups = array();
			foreach($allgroups as $g){
				$gn = (string)$g["name"];
				if(isset($groups[$gn]) && in_array($acc['login'],$groups[$gn])){
					$accgroups[] = $gn;
				}
			}
			Account::edit($acc['login'],NULL,NULL,NULL,NULL,$accgroups);
		}
	}

	public function toHTML(){
		$groupaccounts = array();


		echo "<div class='header'><h1>Groups</h1></div>";

		echo "<form class='pure-form pure-form-aligned' method='post' action='?t=Adm&a=GC'>
			<h2>".Settings::_("jsaccounts","addgroup")."</h2>
			<div class='pure-control-group'>
			<label>Group name : </label><input type='text' name='group' placeholder='".Settings::_("jsaccounts","groupname")."' />
			</div>
			<div class='pure-controls'>
			<input type='submit' class='pure-button button-success' value='".Settings::_("jsaccounts","addgroup")."' />
			</div>
			</form>";

		echo "<form class='pure-form pure-form-aligned' method='post' action='?t=Adm&a=GEd'>";
		echo "<h2>".Settings::_("jsaccounts","groups")."</h2>";

		foreach(Group::findAll() as $g){
			$gn = $g['name'];
			$group = htmlentities($gn, ENT_QUOTES ,'UTF-8');

			echo "<div class='pure-g' style='border-bottom: 1px solid #ccc; padding-bottom: 15px; margin: 20px;'>";
			echo "<div class='pure-u-1-1 pure-u-md-1-3'>";
			echo "<h3><a href=\"?t=Adm&a=GDe&g=".urlencode($gn)."\" class='pure-button button-error button-small'><i class='fa fa-trash-o '></i></a> ".htmlentities($gn, ENT_QUOTES ,'UTF-8')."</h3>";
			echo "</div>";
			echo "<div class='pure-u-1-1 pure-u-md-2-3'>";


			foreach(Account::findAll() as $acc){
				$login = htmlentities($acc["login"],ENT_QUOTES ,'UTF-8');
				$checked = in_array($gn,$acc["groups"])?"checked":"";

				echo "<div class='pure-controls'>";
				echo "<label><input type='checkbox' name=\"$group"."[]\" value=\"$login\" $checked > $login</label>";
				echo "</div>";
			}
			
			echo "</div>";
			echo "</div>";


		}
			echo "<div class='pure-controls'><input type='submit' class='pure-button pure-button-primary'></div>";


			echo "</form>";
	}
}

?>