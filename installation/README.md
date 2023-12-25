# installation

Set in BIOS "Boot priority" = "UEFI first"
Load from "Kali Live"

Prepare disk in gparted
	Create partition table gpt
	Create fat32 partition with size 512Mb set it flags esp, boot
	Create linux-swap partition size 16384Mb
	Create ext4 partition

Enter to terminal

> sudo screen
> cd /tmp
> git clone https://github.com/ckpunmkug/tools.git
> cd tools/installation
	
> vim ./bookworm.conf
> ./start.sh ./bookworm.conf
> halt -p

Boot from hard drive

> /usr/local/sbin/installation_after_reboot
> reboot

Basic desktop installation complete

