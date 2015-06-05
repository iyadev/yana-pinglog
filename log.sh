#!/bin/bash
wget -d --post-data="content=`df -h | base64`" "https://[ip_du_serveur]/yana-server/plugins/pinglog/update_log.php?from=[PC]&log=dh" --output-document=/tmp/test --no-check-certificate
wget -d --post-data="content=`top -n 1 -b  | base64`" "https://[ip_du_serveur]/yana-server/plugins/pinglog/update_log.php?from=[PC]&log=top" --output-document=/tmp/test --no-check-certificate
wget -d --post-data="content=`uname -r  | base64`" "https://[ip_du_serveur]/yana-server/plugins/pinglog/update_log.php?from=[PC]&log=uname_kernel" --output-document=/tmp/test --no-check-certificate
wget -d --post-data="content=`uptime  | base64`" "https://[ip_du_serveur]/yana-server/plugins/pinglog/update_log.php?from=[PC]&log=uptime" --output-document=/tmp/test --no-check-certificate
#wget -d --post-data="content=`cat backup.log  | base64`" "https://[ip_du_serveur]/yana-server/plugins/pinglog/update_log.php?from=[PC]&log=backup" --output-document=/tmp/test --no-check-certificate
#wget -d --post-data="content=`virsh list --all | base64`" "https://[ip_du_serveur]/yana-server/plugins/pinglog/update_log.php?from=[PC]&log=libvirt" --output-document=/tmp/test --no-check-certificate