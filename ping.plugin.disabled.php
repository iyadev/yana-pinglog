<?php
/*
@name pinglog
@author Julien LOISEAU <bird@tadikwa.fr> / Morvaivor <monogravity@gmail.com> / Iya <contact@iyadev.fr>
@link http://tadikwa.fr
@licence CC by nc sa
@version 1.1.0_mod1
@description  Ping de surveillance, Wake on Lan et log des équipements
*/

//Si vous utiliser la base de donnees a ajouter
include('pinglog.class.php');
include('wol.util.php');

//Lien pour la page des etats
function pinglog_plugin_menu(&$menuItems){
	global $_;
	$menuItems[] = array('sort'=>10,'content'=>'<a href="index.php?module=pinglog"><i class="icon-th-large">L</i>Etats des PC</a>');
}

function pinglog_IsValid($mac){
  return (preg_match('/([a-fA-F0-9]{2}[:|\-]?){6}/', $mac) == 1);
}

function pinglog_IgetBroadcastFromIp($ip){
	$arrayIp = explode('.', $ip);
	return $arrayIp[0].".".$arrayIp[1].".".$arrayIp[2].".255";
}

//fonction qui affiche la page des etats
function pinglog_plugin_page($_){
	if(isset($_['module']) && $_['module']=='pinglog'){
		$pingManager = new pinglog();
		$pings = $pingManager->populate();
		if (!isset($_['id']) && count($pings)>0)  $_['id'] = $pings[0]->getId();
		//Add Menu
		?>
		<div class="span3 bs-docs-sidebar">
		<ul class="nav nav-tabs nav-stacked">
		<?php
		foreach ($pings as $ping) {
			echo "<li><a href=\"#device_".$ping->getName()."\" onClick=\"show('pc_".$ping->getName()."'"; 
			if($ping->getLibvirt() == 1) echo ", true"; 
			else echo ", false"; 
			if($ping->getBackup() == 1) echo ", true"; 
			else echo ", false"; 
			echo ");\"><i class=\"fa fa-angle-right\"> </i> ".$ping->getName()." </a></li>";
		}
		?>
		</ul>
		</div>
		<div class="span9">
		<?php
		//
			foreach ($pings as $ping) {
					?>
					<div id="pc_<?php echo $ping->getName(); ?>" class="pc" style="display:none;">
						<h2 id="nomMachine"><?php echo $ping->getName(); ?></h2>
						<p><?php echo $ping->getDescription() ?>
						</p>
						<ul>
						<?php if($ping->getIp() != "") { ?>Adresse IP : <span class="label label-warning"><?php echo $ping->getIp() ;?></span><?php }?>
						<?php if($ping->getMac() != "") { ?> Adresse MAC : <span class="label label-warning"><?php echo $ping->getMac() ;?></span><?php }?>
						<div class="btn-toolbar">
									<div class="btn-group">
									
									<?php
									$ip = $ping->getIp();
									$pingresult = exec("/bin/ping -c 1 $ip", $outcome, $status);
									if (0 == $status) {
										echo '<a class="btn btn-success" href="#">ONLINE <i class="icon-thumbs-up icon-white"></i></a>';
									}else{	
										echo '<a class="btn" href="#">OFFLINE <i class="icon-thumbs-down "></i></a>';
									}	
									
									//Adresse Mac
									$mac = $ping->getMac();
									$id = $ping->getId();
									if(isset($mac) && pinglog_IsValid($mac)){
										echo '<a class="btn" href="#" onclick="wol('.$id.');">Wake on lan <i class="icon-thumbs-up icon-white"></i></a>';
									}

									?>
									</div>
								</div>
								<?php
							if(isset($mac) && pinglog_IsValid($mac)){
								echo '<span class="label label-info" id="outping'.$id.'"></span>';
							}
							if($ping->getControl()!= "")
							{
								echo "<a href=\"".$ping->getControl()."\" target=\"_bank\">Hibernation</a>";
							}
						?>
						<ul class="nav nav-tabs" id="log_tab" style="display:none;">
						  <li id="showsys" class="active"><a href="#" onClick="ShowSys();">Système</a></li>
						  <li id="showvirt"><a href="#" onClick="ShowVirt();" >LibVirt</a></li>
						  <li id="showbackup"><a href="#" onClick="ShowBackup();" >Backup</a></li>
						</ul>
						<?php
						if( $ping->getLog() == 1)
						{
							 
						?>
						
						<span id="table_sys">
						<table class="table table-striped table-bordered table-hover">
						<tr><th>Uptime</th><td id="pc_<?php echo $ping->getName(); ?>_uptime">col2</td></tr>
						<tr><th>kernel</th><td id="pc_<?php echo $ping->getName(); ?>_kernel">col4</td></tr>
						<tr><th>Disk</th><td id="pc_<?php echo $ping->getName(); ?>_disk">col4</td></tr>
						<tr><th>Top</th><td id="pc_<?php echo $ping->getName(); ?>_top">col4</td></tr>
						</table>
						</span>
						<?php
						}
						if( $ping->getLibvirt() == 1)
						{
						?>
						<span id="table_virt">
						<table class="table table-striped table-bordered table-hover">
						<tr><th>LibVirt</th><td id="pc_<?php echo $ping->getName(); ?>_libvirt">col4</td></tr>
						</table>
						</span>
						<?php
						}
						if( $ping->getBackup() == 1)
						{
						?>
						<span id="table_backup">
						 <table class="table table-striped table-bordered table-hover">
							<tr><th>Backup</th><td id="pc_<?php echo $ping->getName(); ?>_backup">col4</td></tr>
							</table>
						</span>
						<?php
						}
						?>
					</div>
<?php
			}
			?> </div><?php
	}
}

//fonction pour avoir une entree ping dans les settings
function pinglog_plugin_setting_menu(){
	global $_;
	echo '<li '.(isset($_['section']) && $_['section']=='pinglog'?'class="active"':'').'><a href="setting.php?section=pinglog"><i class="icon-chevron-right">L</i> Etats des PC</a></li>';
}

//page de settings pour ping
function pinglog_plugin_setting_page(){
	global $_,$myUser;
	if(isset($_['section']) && $_['section']=='pinglog' ){

		if($myUser!=false){
			$pingManager = new pinglog();
			$pings = $pingManager->populate();
		
			//Gestion des modifications
			if (isset($_['id'])){
				$id_mod = $_['id'];
				$selected = $pingManager->getById($id_mod);
				$description = $selected->GetName();
				$button = "Modifier";
			}
			else
			{
				$description =  "Ajout d'un PC";
				$button = "Ajouter";
			} 

			?>

		<div class="span9 userBloc">


		<h1>Etats des PC</h1>
		<p>Gestion des PC à surveiller</p>  

		<form action="action.php?action=pinglog_add_ping" method="POST">
		<fieldset>
		    <legend><? echo $description ?></legend>

		    <div class="left">
			    <label for="namepingequip">Nom</label>
			    <? if(isset($selected)){echo '<input type="hidden" name="id" value="'.$id_mod.'">';} ?>
				<input type="text" value="<? if(isset($selected)){echo $selected->getName();} ?>" id="namepingequip" name="namepingequip" placeholder="PC Salon..."/>
			    <label for="descriptionpingequip">Description</label>
			    <input type="text" value="<? if(isset($selected)){echo $selected->getDescription();} ?>" name="descriptionpingequip" id="descriptionpingequip" placeholder="PC du salon…" />
			    <label for="adresseippingequip">Adresse IP</label>
			    <input type="text" value="<? if(isset($selected)){echo $selected->getIp();} ?>" name="adresseippingequip" id="adresseippingequip" placeholder="192.168.0.x..." />
				 <label for="adresseippingequip">Adresse MAC</label>
			    <input type="text" value="<? if(isset($selected)){echo $selected->getMac();} ?>" name="adressemacpingequip" id="adressemacpingequip" placeholder="00:22:32:..:..:.." />
			 	<label for="adresseippingequip">Adresse de controle</label>
			    <input type="text" value="<? if(isset($selected)){echo $selected->getControl();} ?>" name="adressecontrolpingequip" id="adressecontrolpingequip" placeholder="http://192.168.0.x:xxxxx/..." />
			    <label for="adresseippingequip">Recupération des logs </label>
			    <input type="checkbox" value="1" name="logsys"   <?php if(isset($selected)&& $selected->getLog() == 1 ){ echo "checked"; } ?> /> System
			    <input type="checkbox" value="1" name="logbackup" <?php if(isset($selected)&& $selected->getBackup() == 1 ){ echo "checked"; } ?> /> Backup 
			    <input type="checkbox" value="1" name="logvirt" <?php if(isset($selected)&& $selected->getLibVirt() == 1 ){ echo "checked"; } ?> /> LibVirt
			</div>

  			<div class="clear"></div>
		    <br/><button type="submit" class="btn"><? echo $button; ?></button>
	  	</fieldset>
		<br/>
	</form>

		<table class="table table-striped table-bordered table-hover">
	    <thead>
	    <tr>
	    	<th>Nom</th>
		    <th>Description</th>
		    <th>Adresse IP</th>
			<th>Adresse MAC</th>
			<th>Adresse de control</th>
			<th>Log</th>
			<th>Backup</th>
			<th>LibVirt</th>
		    <th></th>
	    </tr>
	    </thead>
	    
	    <?php foreach($pings as $ping){ ?>
        <tr>
			<td><?php echo $ping->getName(); ?></td>
			<td><?php echo $ping->getDescription(); ?></td>
			<td><?php echo $ping->getIp(); ?></td>
			<td><?php echo $ping->getMac(); ?></td>
			<td><?php echo $ping->getControl(); ?></td>
			<td><?php echo $ping->getLog(); ?></td>
			<td><?php echo $ping->getBackup(); ?></td>
			<td><?php echo $ping->getLibvirt(); ?></td>
			<td>
				<a class="btn" href="setting.php?section=pinglog&id=<?php echo $ping->getId(); ?>"><i class="fa fa-pencil"> </i></a> 
				<a class="btn" href="action.php?action=pinglog_delete_ping&id=<?php echo $ping->getId(); ?>"><i class="fa fa-times"> </i></a>
			</td>
			</tr>
	    	
	    <?php } ?>
	    </table>
		</div>

<?php }else{ ?>

		<div id="main" class="wrapper clearfix">
			<article>
					<h3>Vous devez être connecté</h3>
			</article>
		</div>
<?php
		}
	}

}

/*function pinglog_command(&$response,$actionUrl){
	$response['commands'][] = array(
		'command'=>$conf->get('VOCAL_ENTITY_NAME')." allume l'équiment un",
		'url'=>$actionUrl.'?action=pinglog_wol','confidence'=>'0.88'
		);
}*/


function pinglog_action_ping(){
		global $_,$myUser;

		//Erreur dans les droits sinon!
		$myUser->loadRight();

		switch($_['action']){

			case 'pinglog_add_ping':
			$right_toverify = isset($_['id']) ? 'u' : 'c';
			if($myUser->can('surveillance ip',$right_toverify)){
				$ping = new pinglog();
				if ($right_toverify == "u"){$ping = $ping->load(array("id"=>$_['id']));}
					$ping->setName($_['namepingequip']);
					$ping->setDescription($_['descriptionpingequip']);
					$ping->setIp($_['adresseippingequip']);
					$ping->setMac($_['adressemacpingequip']);
					$ping->setControl($_['adressecontrolpingequip']);
					if(isset($_['logbackup'])) $ping->setBackup($_['logbackup']);
					else $ping->setBackup(0);
					if(isset($_['logsys'])) $ping->setLog($_['logsys']);
					else $ping->setLog(0);
					if(isset($_['logvirt'])) $ping->setLibvirt($_['logvirt']);
					else $ping->setLibvirt(0);
					$ping->save();
					header('location:setting.php?section=pinglog');	
				}
						else
			{
				header('location:setting.php?section=pinglog&error=Vous n\'avez pas le droit de faire ça!');
			}
			
			break;
			
			case 'pinglog_wol':
			$right_toverify = isset($_['id']) ? 'u' : 'c';
			if($myUser->can('surveillance ip',$right_toverify)){
				$ping = new pinglog();
				if ($right_toverify == "u"){$ping = $ping->load(array("id"=>$_['id']));}
				$mac = $ping->getMac();
				$ip = $ping->getIp();
				//Généralement l'adresse de broadcast se termine par 255
				$ipBroadcast = pinglog_IgetBroadcastFromIp($ip);
				if(isset($mac) && pinglog_IsValid($mac) && pinglog_WakeOnLan($ipBroadcast, $mac,"9")){
					$affirmation = "J'ai demandé à ".$ping->getName()." de démarrer, on va voir ce qu'il va faire";
				}else{
					$affirmation = "Je ne peux pas commander ".$ping->getName().", je suis désolée";
				}
				$response = array('responses'=>array(
									array('type'=>'talk','sentence'=>$affirmation)
												));
					$json = json_encode($response);
					echo ($json=='[]'?'{}':$json);
			}else{
				header('location:setting.php?section=pinglog&error=Vous n\'avez pas le droit de faire ça!');
			}
			break;

			case 'pinglog_delete_ping':
			if($myUser->can('surveillance ip','d')){
				$pingManager = new pinglog();
				$pingManager->delete(array('id'=>$_['id']));
				header('location:setting.php?section=pinglog');
			}
			else
			{
				header('location:setting.php?section=pinglog&error=Vous n\'avez pas le droit de faire ça!');
			}

			break;

		}
}

Plugin::addJs("/js/wol.js"); 
Plugin::addJs("/js/main.js"); 
Plugin::addHook("menubar_pre_home", "pinglog_plugin_menu"); 
Plugin::addHook("action_post_case", "pinglog_action_ping"); 
Plugin::addHook("home", "pinglog_plugin_page");
Plugin::addHook("setting_menu", "pinglog_plugin_setting_menu"); 
Plugin::addHook("setting_bloc", "pinglog_plugin_setting_page"); 
 
?>
