EHAMGates
=========

Gate assignment tool for Amsterdam Airport Schiphol (EHAM). Not to be used in real aviation!

## Installation
Just clone the repository to a webserver with PHP (tested on Apache2 with PHP 5.3) and everything should work out of the box. You could symlink the public/ folder to your web root if you prefer.

If data does not get cached, please check whether data.txt exists in the root folder and whether your server has write access to it.

## Vagrant
This repository also contains a Vagrantfile. Check http://www.vagrantup.com/ for more information about Vagrant. When using this configuration, you can access the Gate Finder on your localhost, port 8080.
