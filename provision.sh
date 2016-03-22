#!/bin/bash

echo "### Update repo"
# Provision virtual machine with development tools
sudo apt-get update && apt-get upgrade

# Dev packages
echo "### Install dev packages"
sudo apt-get install -y vim curl git nodejs npm python-pip

# Install docker for ubuntu 14.04
# https://docs.docker.com/engine/installation/ubuntulinux/

echo "### Install docker"
sudo apt-key adv --keyserver hkp://p80.pool.sks-keyservers.net:80 --recv-keys 58118E89F3A912897C070ADBF76221572C52609D
sudo sh -c "echo \"deb https://apt.dockerproject.org/repo ubuntu-trusty main\" > /etc/apt/sources.list.d/docker.list"
sudo apt-get update
sudo apt-get purge lxc-docker
sudo apt-get install -y linux-image-extra-$(uname -r)
sudo apt-get install -y docker-engine
sudo usermod -aG docker vagrant
sudo sh -c "echo 'DOCKER_OPTS=\"--dns 8.8.8.8\"' >> /etc/default/docker"

# Install docker compose
pip install docker-compose

# Install PHP5 and composer
sudo curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin creates=/usr/local/bin/composer
sudo mv /usr/local/bin/composer.phar /usr/local/bin/composer
sudo apt-get install -y php5-cli php5-xdebug

# Make vagrant folder
sudo mkdir -p /vagrant
sudo chown vagrant.vagrant /vagrant