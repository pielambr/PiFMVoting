[![FOSSA Status](https://app.fossa.io/api/projects/git%2Bgithub.com%2Fpielambr%2FPiFMVoting.svg?type=shield)](https://app.fossa.io/projects/git%2Bgithub.com%2Fpielambr%2FPiFMVoting?ref=badge_shield)

PiFMVoting
==========

- This is a simple script to enable users to have full control over their PiFM / PiRate radio setup. 
- The first part of this is written in python, and has an sqlite3 library with all the songs in it. Songs are scanned when the script is started and only the songs with valid ID3 tags for both title and artist are added to the database. The songs are not only added to this database, but are also sent to the PHP site and saved in the mysql database (I did this, because I run the PHP on one Pi and the Python on another one).
- The second part is written in PHP and is a voting system. The votes are saved in a mysql database, and you can search the database for the songs you want to vote for. 

The requirements are:
---------------------
- mysqlnd driver (for the query() instructions)
- mutagen, requests

Installation
------------
- Installation is pretty simple. You dump the install.sql file into your PHPMyAdmin, which creates the desired tables. 
- On the Pi that plays the audio, you copy the pifm file to /root and give it execution rights (`chmod +x /root/pifm`). 
- After that you change the pifm_cfg.config file to whatever settings you would like and also change the settings in voting.php.
- After that you should be able to run the script by executing `sudo python main.py > out.log &`

Credits
-------
- Bootstrap: http://getbootstrap.com
- jQuery: http://jquery.com/
- http://www.icrobotics.co.uk/wiki/index.php/Turning_the_Raspberry_Pi_Into_an_FM_Transmitter


## License
[![FOSSA Status](https://app.fossa.io/api/projects/git%2Bgithub.com%2Fpielambr%2FPiFMVoting.svg?type=large)](https://app.fossa.io/projects/git%2Bgithub.com%2Fpielambr%2FPiFMVoting?ref=badge_large)