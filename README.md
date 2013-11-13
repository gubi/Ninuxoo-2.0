# Semantic Decentralized Ninuxoo
![Ninuxoo claim](https://raw.github.com/gubi/Ninuxoo-Semantic-Decentralized/master/common/media/img/ninuxoo_claim.png)

This is a beta version of the new Ninuxoo Search Engine.<br />
Ninuxoo is a Search Engine and Indexer made by [Clauz](https://github.com/cl4u2) for the [Ninux Wireless Community Network](https://github.com/ninuxorg).

## Difference for other versions
The primary version of Ninuxoo, explore the Wireless Comunity Network from a centralized Server in search of NAS to be scanned.<br />
This application is much useful but lot expensive and not sustainable when the network counts a lot of nodes and NAS to be scanned.<br />
Thus this is a first decentralized approach to solve this issue...

## Working principle
Most NAS has a preinstalled Server software - sometimes also in light version - that can solve the main efficiency problem: the scanning of files.<br />
Therefore, why not leave them this tiring work?

In this way, local servers periodically scan itself and export the tree list in `https://github.com/gubi/Ninuxoo-Semantic-Decentralized/blob/master/API` directory, so the Main Server need only to load a siple text file: no more remote directory-tree recursion!.<br />
Obviously, owners of the decentralized NAS are also encouraged to use the local version of Ninuxoo because first is also a "personal" search engine, and then with the plugins like the local Meteo map, which works also without Meteo Station sensors.

## Istalling and configuring
First you need to clone this repo:
```bash
$ git clone git@github.com:gubi/Ninuxoo-Semantic-Decentralized.git
```

### Automatic - via installer
Simply launch your `localhost` from browser, the installer will provide to create all config files.

### Manual installation
Then, create and edit a [`config.ini`](./blob/master/config.ini) file, which contains all data required to run.<br />
Once you have configured samba directories, you can launch [scan.php](./blob/master/scan.php) via terminal

```bash
$ php scan.php
```

or via browser: [http://LOCALHOST/scan.php](http://LOCALHOST/scan.php)

This script check samba directories specified in the `config.ini` file, then start a scan recursion and save the listing in the API folder also specified in the `config.ini`.

### Configure Cron
Add these lines to your crontab:
```bash
$ crontab -e
```
```cron
# Ninuxoo Local scan job
00 */6 * * * root /usr/bin/php /var/www/scan.php
```

## Notes
* The script [`scan.php`](https://github.com/gubi/Ninuxoo-Semantic-Decentralized/blob/master/scan.php) can be launched manually or via cron, as described above


### Note for the owner of the Main Server
If you want that your server checks remote NAS, just point the crawler to `config.ini` file for retrieve further instructions about listing output position.<br />
The script `scan.php` generates three files type of listing:
* `listing` (simple list)
* `listing.json` (JSON array)
* `listing.list` (INI array)

Choose one to parse as you like :)


## License
This is an Open Source Project.<br />
Feel free to edit and improve as you like.<br />
For further info, please see the [License](./blob/master/LICENSE) section.
