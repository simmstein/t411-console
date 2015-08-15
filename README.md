# t411-console

t411-console provided a console line tool for searching torrents on the tracker t411.me.

You also can configure a transmission remote access and run a download.

## Installation

PHP 5.5 is required

```bash
$ git clone https://github.com/simmstein/t411-console.git
$ cd t411-console
$ curl -sS https://getcomposer.org/installer | php
$ ./composer.phar install
$ ./console
```

## Commands

### Show a user's profile

```
Usage:
 users:profile [-i|--id="..."]

Options:
 --id (-i)             The user id
 --help (-h)           Display this help message
 --quiet (-q)          Do not output any message
 --verbose (-v|vv|vvv) Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug
 --version (-V)        Display this application version
 --ansi                Force ANSI output
 --no-ansi             Disable ANSI output
 --no-interaction (-n) Do not ask any interactive question

Help:
 users:profile
 
 Show a user's profile (default: auhentificated user). 
 
 Usage: users:profile [OPTIONS]
```

### Login on t411

```
Usage:
 auth:login

Options:
 --help (-h)           Display this help message
 --quiet (-q)          Do not output any message
 --verbose (-v|vv|vvv) Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug
 --version (-V)        Display this application version
 --ansi                Force ANSI output
 --no-ansi             Disable ANSI output
 --no-interaction (-n) Do not ask any interactive question

Help:
 auth:login
 
 Generate the config to access the API. You must have a valid login/password.
 
 The login and the password are not saved.
 
 Usage: auth:login
```


### Show categories and sub-categories

```
Usage:
 categories:tree

Options:
 --help (-h)           Display this help message
 --quiet (-q)          Do not output any message
 --verbose (-v|vv|vvv) Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug
 --version (-V)        Display this application version
 --ansi                Force ANSI output
 --no-ansi             Disable ANSI output
 --no-interaction (-n) Do not ask any interactive question

Help:
 categories:tree
 			
 List all categories and sub-categories with IDs.
 
 Usage: categories:tree
```


### Show a torrent details

```
Usage:
 torrents:details id

Arguments:
 id                    Torrent ID

Options:
 --help (-h)           Display this help message
 --quiet (-q)          Do not output any message
 --verbose (-v|vv|vvv) Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug
 --version (-V)        Display this application version
 --ansi                Force ANSI output
 --no-ansi             Disable ANSI output
 --no-interaction (-n) Do not ask any interactive question

Help:
 torrents:details
 
 Show torrent details.
 
 Usage: torrents:details TORRENT_ID
```


### Download a torrent

```
Usage:
 torrents:download id output_file

Arguments:
 id                    Torrent ID
 output_file           Output

Options:
 --help (-h)           Display this help message
 --quiet (-q)          Do not output any message
 --verbose (-v|vv|vvv) Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug
 --version (-V)        Display this application version
 --ansi                Force ANSI output
 --no-ansi             Disable ANSI output
 --no-interaction (-n) Do not ask any interactive question

Help:
 torrents:download 
 
 Download a torrent.
 
 Usage: torrents:download TORRENT_ID OUTPUT
 
 OUTPUT could be a file or STDIN by using -.
```


### Search torrents

```
Usage:
 torrents:search [-o|--offset="..."] [-l|--limit="..."] [-s|--sub-category="..."] [-c|--category="..."] [-t|--terms="..."] query

Arguments:
 query                 Query

Options:
 --offset (-o)         Page number
 --limit (-l)          Number of results per page
 --sub-category (-s)   Filter by sub-category ID
 --category (-c)       Filter by category ID
 --terms (-t)          Filter by terms IDs (separated by ",")
 --help (-h)           Display this help message
 --quiet (-q)          Do not output any message
 --verbose (-v|vv|vvv) Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug
 --version (-V)        Display this application version
 --ansi                Force ANSI output
 --no-ansi             Disable ANSI output
 --no-interaction (-n) Do not ask any interactive question

Help:
 torrents:search
 
 Search torrents.
 
 Usage: torrents:search QUERY [OPTIONS]
 
 --terms does not work (API bug)
```


### Search movies

```
Usage:
 torrents:search:movies [-o|--offset="..."] [-l|--limit="..."] [-t|--terms="..."] query

Arguments:
 query                 Query

Options:
 --offset (-o)         Page number
 --limit (-l)          Number of results per page
 --terms (-t)          Filter by terms IDs (separated by ",")
 --help (-h)           Display this help message
 --quiet (-q)          Do not output any message
 --verbose (-v|vv|vvv) Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug
 --version (-V)        Display this application version
 --ansi                Force ANSI output
 --no-ansi             Disable ANSI output
 --no-interaction (-n) Do not ask any interactive question

Help:
 torrents:search:movies 
 			
 Search movies.
 
 Usage: torrents:search:movies QUERY [OPTIONS]
 
 --terms does not work (API bug)
```


### Search series

```
Usage:
 torrents:search:series [-o|--offset="..."] [-l|--limit="..."] [-t|--terms="..."] query

Arguments:
 query                 Query

Options:
 --offset (-o)         Page number
 --limit (-l)          Number of results per page
 --terms (-t)          Filter by terms IDs (separated by ",")
 --help (-h)           Display this help message
 --quiet (-q)          Do not output any message
 --verbose (-v|vv|vvv) Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug
 --version (-V)        Display this application version
 --ansi                Force ANSI output
 --no-ansi             Disable ANSI output
 --no-interaction (-n) Do not ask any interactive question

Help:
 torrents:search:series
 
 Search series.
 
 Usage: torrents:search:series QUERY [OPTIONS]
 
 --terms does not work (API bug)
```


### Top torrents

```
Usage:
 torrents:top [-p|--period="..."]

Options:
 --period (-p)         Period
 --help (-h)           Display this help message
 --quiet (-q)          Do not output any message
 --verbose (-v|vv|vvv) Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug
 --version (-V)        Display this application version
 --ansi                Force ANSI output
 --no-ansi             Disable ANSI output
 --no-interaction (-n) Do not ask any interactive question

Help:
 torrents:top 
 
 Show top torrents.
 
 Usage: torrents:search:top [OPTIONS]
 
 Period values: "100", "day", "week", "month"
```


### Show types and terms

```
Usage:
 types:tree [-t|--terms] [-f|--filter[="..."]]

Options:
 --terms (-t)          Show terms
 --filter (-f)         Filter types by ID or by name
 --help (-h)           Display this help message
 --quiet (-q)          Do not output any message
 --verbose (-v|vv|vvv) Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug
 --version (-V)        Display this application version
 --ansi                Force ANSI output
 --no-ansi             Disable ANSI output
 --no-interaction (-n) Do not ask any interactive question

Help:
 types:tree
 			
 List all types of terms and terms.
 
 Usage: types:tree [OPTIONS]
```

### Configure transmission web remote access

```
Usage:
 transmission:configure

Options:
 --help (-h)           Display this help message
 --quiet (-q)          Do not output any message
 --verbose (-v|vv|vvv) Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug
 --version (-V)        Display this application version
 --ansi                Force ANSI output
 --no-ansi             Disable ANSI output
 --no-interaction (-n) Do not ask any interactive question

Help:
 transmission:configure
 
 Configure your transmission web remote access.
 
 Usage: transmission:configure
```

### Download a torrent with transmission web remote client

```
Usage:
 transmission:download id

Arguments:
 id                    Torrent ID

Options:
 --help (-h)           Display this help message
 --quiet (-q)          Do not output any message
 --verbose (-v|vv|vvv) Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug
 --version (-V)        Display this application version
 --ansi                Force ANSI output
 --no-ansi             Disable ANSI output
 --no-interaction (-n) Do not ask any interactive question

Help:
 transmission:download
 
 Download a torrent.
 
 Usage: transmission:download TORRENT_ID

```

### UserScript

![UserScript](https://upload.deblan.org/u/2015-08/55cf6a91.png "UserScript")


```
// ==UserScript==
// @name        Tranmission cli cmd
// @namespace   t411
// @include     http*://www.t411.io/torrents/*
// @version     1
// @grant       none
// ==/UserScript==

$('.btn').each(function() {
	var $btn = $(this);
	var $parent = $btn.parent();

	if ($btn.attr('href').indexOf('download') !== -1) {
		var id = $btn.attr('href').split('=')[1];
		$parent.append('<p>t411-console transmission:download ' + id + '</p>');
	}
});
```
