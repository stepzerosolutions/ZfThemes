ZfThemes 
=======================

Introduction
------------
This is a simple, Theme management module for ZF2.
This will create theme folders for Views and public html


Installation
------------


Clone the repository and manually invoke `composer` using the shipped
`composer.phar`:

    cd my/project/dir
    git clone git://github.com/stepzerosolutions/ZfThemes.git
    cd ZendSkeletonApplication
    php composer.phar self-update
    php composer.phar install

(The `self-update` directive is to ensure you have an up-to-date `composer.phar`
available.)

Another alternative for downloading the project is to grab it via `curl`, and
then pass it to `tar`:

    cd my/project/dir
    curl -#L https://github.com/stepzerosolutions/ZfThemes/tarball/master | tar xz --strip-components=1

You would then invoke `composer` to install dependencies per the previous
example.

Using Git submodules
--------------------
Alternatively, you can install using native git submodules:

    git clone git://github.com/stepzerosolutions/ZfThemes.git --recursive



Requirements
--------------------
You may need to set configuration table service as "ConfigurationTable"



NOTE
--------------------
Please install in seperate installation with ZF2 application first