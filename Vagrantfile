# -*- mode: ruby -*-
# vi: set ft=ruby :

VAGRANTFILE_API_VERSION = "2"

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|
    config.vm.box = "ubuntu/trusty64"
    config.vm.network "private_network", ip: "10.10.10.10"

    config.vm.synced_folder ".", "/vagrant", type: "nfs"

    config.vm.provision :shell, path: "provision.sh"

    config.vm.provider "virtualbox" do |v|
      v.memory = 6144
      v.cpus = 4
    end
end