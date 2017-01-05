# ImageManager
---

This package is created as a PSR-0 namespaced library that is installable via PHP Composer.  

Very little configuration is required to use this library.

Simply configure various image directories to be monitored and automatically pushed to S3, optionally deleting files after upload.

Requirements
---

- PHP 5+
- PHP Composer
- (Optional) AWS CLI
- (Optional) GCS CLI
- (Required for SSH) libssh2-php
- php5-curl (required for guzzle)
- vagrant 1.9
- vagrant-vbguest plugin (vagrant plugin install vagrant-vbguest)


Installation
---
Include this repository within your composer.json package of your library

Run `composer install`

If you want to test using Vagrant, be sure to install Ansible dependencies:

`ansible-galaxy install -v -r ansible/requirements.yml -p ansible/roles`


Configuration
---

### SSH Connection Configuration 
```
$ssh_host = "127.0.0.1"; // or DNS name 
$ssh_port = 22; 
$ssh_user = "vagrant";
$ssh_public_key = "/path/to/public_key";
$ssh_private_key = "/path/to/private_key";

$ssh_config = new \mm0\ImageManager\Configuration\SSH (
    $ssh_host,
    $ssh_port,
    $ssh_user,
    $ssh_public_key,
    $ssh_private_key,
    '',             // ssh key passphrase
    array('hostkey' => 'ssh-rsa')
);
```

We then use this configuration object to create a connection object:

```
$connection = new \mm0\ImageManager\SSH\Connection($ssh_config);
$connection->setSudoAll(true); // set this to true if you are using a non-root user to SSH 

```

Alternatively, instead of SSH'ing into your MySQL Box, you can also run this library directly on your server using a LocalShell/Connection Object

### LocalShell Connection Configuration
```
$connection = new \mm0\ImageManager\LocalShell\Connection()
```

### Save Modules
Save Modules determine where we will store our backups.  You can specify one or more save modules in an array

To date, we have only implemented AWS S3 and GCS Save Modules.  The goal is to have additional modules implemented using the same interfaces for us to be able to archive onto more providers by simply adding the configuration to the save module array


#### AWS S3 Save Module
In order to use the local module, we must be using a LocalShell Connection as this library and composer dependency (AWS-PHP-SDK) is required.

With an SSH Connection, we are limited in that we don't have a copy of this library in th
```
// Specify the storage module for the backup to use (local or remote SSH)

$s3_save_module = new \mm0\ImageManager\AWS\Uploader(
    $connection,
    $bucket, 
    $region,
    $concurrency
);
```
 
Usage
---


#### For more examples, please see the `./Examples` directory or Tests
 
 
### Testing

Configuration for AWS, GCS(Optional): 

    You must configure your aws cli by adding api key credentials to:
      /home/vagrant/.aws/boto
      
    and configure gsutil by running 
        gsutil configure
        
     You may have to update tests to reference personal buckets as well.
        
        
Start Vagrant:

```vagrant up```


Log into VM:

`vagrant ssh`


CD to shared Directory:

`cd /var/www`


Install composer packages:

`composer install --dev`

Create an SSH Key 

`ssh-keygen` ( press enter a few times )

`cat ~/.ssh/id_rsa.pub >> ~/.ssh/authorized_keys`


Run PHPUnit

`./vendor/bin/phpunit -v --debug`


To see full test coverage, run:

    ./vendor/bin/phpunit --debug --verbose  --coverage-html html
    


 
## TODO

* Finish Testing


## License

Copyright (c) 2017, Matt Margolin

All rights reserved.

Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

1. Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.

2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.

3. Neither the name of the copyright holder nor the names of its contributors may be used to endorse or promote products derived from this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

Author Information
------------------

[Matt Margolin](mailto:matt.margolin@gmail.com)

[mm0](github.com/mm0) on github
