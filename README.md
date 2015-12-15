# Email Data Disposable
This library contains data about email disposable domains.

## How To Use
*Only use contents of ```/data/```. The code in this library is just for updating that folder.*

* **Either**, include this project into yours via whatever means you want. Git-submodule, Composer, Bower, NPM, etc... are all possible.
* **Or**, copy the data you want manually.

## Formats
* PHP (returns single dimension array)
* JSON (single dimension array)
* Text (one per line)

## Contribute
### Add a Domain
* Add the domain to ```/bin/disposable.txt```
* Run ```php update.php```
* Submit a pull request

### Remove a Domain
* Add the domain to ```/bin/whitelist.txt```
* Run ```php update.php```
* Submit a pull request

### Add Another Format
* Write a new Exporter class (see ```src/Exporter/BaseInterface.php```)
* Add it to the ```export``` function in ```src/Domains.php```
* Run ```php update.php```
* Submit a pull request, like a boss

## License
The MIT License (MIT). See LICENCE file.
