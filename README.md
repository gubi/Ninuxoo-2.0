# NinuXoo Local (beta)
Motore di Ricerca di files locali, estensione locale del Motore [NinuXoo](https://github.com/ninuxorg/ninuxoo).

# Features
Il sistema rileva automaticamente la configurazione Samba e scansiona e indicizza periodicamente i files locali.

Le vecchie scansioni vengono archiviate per una settimana.

----
# Installazione
1. Copiare i files nella cartella `/var/www/` (o equivalente) del vostro NAS.
2. configurare cron:

    `vi /etc/cron.d/ninuxoo_local`

e copiare il seguente script:

    # Ninuxoo Local scan job
    00 */6 * * * root /usr/bin/php /var/www/scan.php # (Modificare i percorsi assoluti se necessario)

3\. Lanciare il file index.php

Il sistema provvederà automaticamente alla sua configurazione.

# Hack
È possibile eseguire le scansioni manualmente richiamando lo script `scan.php`

----
## License
    #  License
    #  
    #	This program is free software: you can redistribute it and/or modify
    #	it under the terms of the GNU General Public License as published by
    #	the Free Software Foundation, either version 3 of the License, or
    #	(at your option) any later version.
    #
    #	This program is distributed in the hope that it will be useful,
    #	but WITHOUT ANY WARRANTY; without even the implied warranty of
    #	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    #	GNU General Public License for more details.
    #
    #	You should have received a copy of the GNU General Public License
    #	along with this program.  If not, see <http://www.gnu.org/licenses/>.
    #
    #
    #	- - -
    #	Created by Alessandro Gubitosi
    #	on Jan 2013
    #    

This application is released under the Free GNU General Public License v3.0.
For more information about GNU License, see http://www.gnu.org/licenses/gpl.html
