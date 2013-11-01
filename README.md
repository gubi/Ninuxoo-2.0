# Ninuxoo Semantic Decentralized
![Ninuxoo claim](https://raw.github.com/gubi/Ninuxoo-Semantic-Decentralized/master/common/media/img/ninuxoo_claim.png)
This is a beta version of the New Ninuxoo Search Engine.<br />
Ninuxoo is a local Search Engine and Indexer made by [Clauz](https://github.com/cl4u2) for the [Ninux.org](https://github.com/ninuxorg) Wireless Community Network.

## Difference for other versions
The primary version of Ninuxoo, explore the Comunitarian Net (WCN) from a centralized Server in search of NAS (Network Attached Storage) to be scanned.<br />
This solution is much expensive and not sustainable when the network counts a lot of nodes to pass to and NAS to be scanned.<br />
Thus this is a first decentralized approach to solve this issue...

## Working principle
Most NAS has a preinstalled Server software - at times also in light version - that can solve the main efficiency problem: the scanning of files.<br />
Therefore, why not leave them this tiring work?

In this way, local servers periodically scan its files and export a list in `./API` directory, so the Main Server need only to scan and diff a text file, not a recursion on a directory tree.<br />
Otherwise - and better - the Main Server no longer needs to scan else, because the local decentralized server can also send data to the main which listens.<br />
the owners of the NAS are encouraged to use the local version of Ninuxoo because is also "personal" search engine, first, and then with the plugins like the local Meteo map, which works also without Meteo Station sensors.

