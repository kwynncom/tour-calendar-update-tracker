WHAT THIS DOES

This keeps track of a performance calendar, checking when tickets are on sale, which groups are performing, etc.  

This makes HTTP calls to get the info.  

A running version is at [... removed] .  This is probably one of the most unstable links on my web site, though.  It will probably be gone 
by August of 2020, although a replacement should soon appear, and perhaps I'll post that code, too.

I'm being obscure about details for what will be obvious reasons to some of you. Here is what I will say:

* See out.out getShowURL().  That gives you a URL.
* See dci.php corpsNameToAb().  That gives you a list of "groups."

Even though I'm being obscure, I still want to show off my uber-geekery.  

I might also suggest that someone could do a better job of listing the tour than the offcial org does, by adding maps and distance lookups and stuff.  If you look 
carefully you'll see that exact latitude and longitude are in the data.  Not to mention a list of "groups."  Perhaps some day I'll do that, but for now I just 
use this for the shows I care about.


DETAILED DOC

There is a cli (command line) branch and a web branch.  I go to great lengths to make sure the "target" system is not abused, so some things 
only work by cli.  I have a cron job running to update the data / make HTTP calls.  

getDo.php is the cli starting point; the cron job runs it.  index.php is the web starting point.


CLI BRANCH

getDo.php

$dao as in data access object
The suffixes (sfxs) are part of the HTTP queries / suffixes to isolate the shows I'm interested in.

get GETs the http.  Parse parses the resulting JSON.  email alerts me to various conditions in the data.

Taking that in order:

get.php / dci_get class

Note that I'm obscuring the domain name I'm getting the data from.  You can figure that out for yourself.  Use Chrome Developer Tools network monitor or the 
equivalent.  Again, the reasons I leave that out will be obvious to some.  

getI as in get internal / private

...

I think I'll just push / post this.  Maybe I'll come back and doc.  I'm not sure if anyone will ever read this.
