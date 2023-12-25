# installation

Set in BIOS "Boot priority" = "UEFI first" <br />
Load from "Kali Live" <br />

Prepare disk in gparted <br />
	Create partition table gpt <br />
	Create fat32 partition with size 512Mb set it flags esp, boot <br />
	Create linux-swap partition size 16384Mb <br />
	Create ext4 partition <br />
 <br />
Enter to terminal <br />
 <br />
> sudo screen <br />
> cd /tmp <br />
> git clone https://github.com/ckpunmkug/tools.git <br />
> cd tools/installation <br />
 <br />
> vim ./bookworm.conf <br />
> ./start.sh ./bookworm.conf <br />
> halt -p
 <br />
Boot from hard drive <br />
 <br />
> /usr/local/sbin/installation_after_reboot <br />
> reboot <br />
 <br />
Basic desktop installation complete <br />

