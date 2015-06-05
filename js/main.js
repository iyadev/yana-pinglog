//fonction appelée depuis la fonction squelette_plugin_menu de la page squelette.plugin.[enabled/disabled].php 
function squelette_javascript(){
	alert('Cette fonction ne sert à rien :p');
}
function updateOne(file, pc, id)
{
	var xhr = new XMLHttpRequest();
	xhr.open('GET', './plugins/pinglog/log-distant/' + pc +'_' + file , true);
	xhr.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	var send = "get" ;
	xhr.onreadystatechange = function() {
	if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) {
		  window.list = xhr.responseText;
		  window.list = window.list.replace(/(\r\n|\n|\r)/g,"<br />");
		  window.list = window.list.replace(/\t/g, '&emsp;&emsp;');
		  window.list = window.list.replace(/    /g, '&emsp;&emsp;');
		  document.getElementById(id).innerHTML = window.list;
	}
};
	xhr.send(send);	
}
function ShowSys(){
	document.getElementById("log_tab").style = "Display:block";
	document.getElementById("table_sys").style = "Display:block";
	document.getElementById("table_virt").style = "Display:none";
        document.getElementById("table_backup").style = "Display:none";
	document.getElementById("showvirt").className = "none"; 
	document.getElementById("showsys").className = "active";
	document.getElementById("showbackup").className = "none";
}
function ShowVirt(){
	document.getElementById("log_tab").style = "Display:block";
	document.getElementById("table_sys").style = "Display:none";
	document.getElementById("table_virt").style = "Display:block";
        document.getElementById("table_backup").style = "Display:none";
	document.getElementById("showvirt").className = "active" ; 
	document.getElementById("showsys").className = "none";
	document.getElementById("showbackup").className = "none";
}
function ShowBackup(){
        document.getElementById("log_tab").style = "Display:block";
        document.getElementById("table_sys").style = "Display:none";
        document.getElementById("table_virt").style = "Display:none";
	document.getElementById("table_backup").style = "Display:block";
        document.getElementById("showvirt").className = "none" ;
        document.getElementById("showsys").className = "none";
	document.getElementById("showbackup").className = "active";
}

function updateAll(pc, virt, backup){
	document.getElementById("nomMachine").innerHTML = pc;
	updateOne("uptime.log", pc ,  pc +"_uptime");
	updateOne("uname_kernel.log", pc ,   pc +"_kernel");
	updateOne("top.log", pc ,  pc +"_top");
	updateOne("dh.log", pc ,  pc +"_disk");
	if(virt == true)
	{
		updateOne("libvirt.log", pc, pc +"_libvirt");
	}
	if(backup == true)
        {
                updateOne("backup.log", pc, pc +"_backup");
        }

}
function show(pc, virt, backup){
	updateAll(pc, virt, backup);
	for(i=0; i< document.getElementsByClassName('pc').length; i++)
	{
		document.getElementsByClassName('pc')[i].style = "Display:none";
	}
	document.getElementById(pc).style = "Display:block";
}
