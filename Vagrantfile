# -*- mode: ruby -*-
# vi: set ft=ruby :

# All Vagrant configuration is done below. The "2" in Vagrant.configure
# configures the configuration version (we support older styles for
# backwards compatibility). Please don't change it unless you know what
# you're doing.
Vagrant.configure("2") do |config|
	config.vm.box = "ubuntu/bionic64"

	config.vm.provider "virtualbox" do |vb|
		vb.memory = "2048"
	end

	config.vm.provision "shell", inline: <<-SHELL
		apt-get update -y

		add-apt-repository -y ppa:ondrej/php

		apt-get install -y build-essential software-properties-common php7.2 php7.2-zip php7.2-mbstring php7.2-xml \
			php7.2-xdebug
		apt-get install -y composer

		if grep -Fqvx "xdebug.remote_enable" /etc/php/7.2/mods-available/xdebug.ini; then
			echo "xdebug.remote_enable = on" >> /etc/php/7.2/mods-available/xdebug.ini
			echo "xdebug.remote_connect_back = on" >> /etc/php/7.2/mods-available/xdebug.ini
			echo "xdebug.idekey = application" >> /etc/php/7.2/mods-available/xdebug.ini
			echo "xdebug.remote_autostart = off" >> /etc/php/7.2/mods-available/xdebug.ini
			echo "xdebug.remote_host = 10.0.2.2" >> /etc/php/7.2/mods-available/xdebug.ini
		fi
	SHELL

	config.vm.provision "shell", privileged: false, inline: <<-SHELL
		echo ""
		echo "Installed packages:"
		echo "  -> PHP 7.2 (with extensions: zip, mbstring, xml, xdebug)"
		echo "  -> Composer"
		echo
		echo "XDebug Configuration:"
		echo "  -> IDE Key: application"
		echo "  -> Remote Autostart: No"
		echo "  -> Remote Connectback: Yes"
	SHELL
end