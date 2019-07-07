# phpdns

[![Build Status](https://travis-ci.org/techno-express/phpdns.svg?branch=master)](https://travis-ci.org/techno-express/phpdns)

> It seems original authors [purplepixie/phpdns](https://github.com/purplepixie/phpdns) has left there **repo** unmaintained for over 3 years now.

The PHP DNS Query is a GPL set of PHP classes providing a direct domain name service API. Originally developed to be a testing module for the [FreeNATS](http://www.purplepixie.org/freenats/) network monitor I decided to package it up as a standalone API as well.

Although there are plenty of other DNS classes/clients out there I found them to either be too overblown or actually non-functional. This API is intended to be a half-way house offering direct to-server queries, the ability to process the response in detail but still with a simple interface for the programmer.

## Usage Guide

### Using the DNS Query API

You can use the DNS query API from within PHP (version 4 or later) and
it requires no special raw socket permissions so should operate
correctly in most environments.

To use the API you must create a DNS Query object (with a DNS server
hostname/IP and other optional parameters), perform a query (either a
full query where you specify the type of result or a smart A lookup
<#SmartALookup>) and deal with the answer.

The answer will either be false if an error occured (see the code below
for an example error trap) or a DNSAnswer object containing a "count"
property (the number of records returned as answers) and an array of
DNSResult objects containing each answer given to the query.

The following is an example script to perform an A record (IP address)
lookup

```php
// A simple DNS query example
require("vendor/autoload.php"); // Require API Source

use PurplePixie\PhpDns\DNSQuery;

// Your DNS Server
$dns_server = "ns.somehost.com"; 

// create DNS Query object - there are other options we could pass here
$dns_query = new DNSQuery($dns_server);

// the question we will ask
$question = "www.somehost.com";

// the type of response(s) we want for this question
$type = "A";

// do the query
$result = $dns_query->query($question, $type); 

// Trap Errors
if ($dns_query->->hasError()) {
    // error occurred
    echo $dns_query->getLastError();
    exit();
}

//Process Results
$count = $result->count(); // number of results returned
foreach ($result as $result_count) {
    // only after A records
    if ($result_count->getTypeId() == "A") {
        echo $question." has IP address ".$result_count->getData()."<br>";
        echo $result_count->getString()."<br>";
    }
}
```

Which if all goes well should output something like...

* www.somehost.com has IP address 10.2.3.4
* www.somehost.com has address 10.2.3.4

The first output is from our script writing the question and the "data"
result, the second when we output the "string" property of the answer
which is the specific record in human-readable form (if the type is known).

> **A Note of Warning and Why Check the Answer Type Above**

DNS is not an entirely straightforward protocol and things which on the
surface may seem simple may not be when you delve deeper (if you already
understand DNS then skip ahead).

For example an IP lookup (A record lookup) for a host on a specific DNS
server may well not just return a single answer record containing the IP
address. The host may be multi-honed and return multiple records any
which may not be an IP address but a CNAME alias. The nameserver you are
querying may not do a recursive or cache lookup for you and so return no
answers even though the domain and host do exist.

For this reason we must actually process the results (unless of course
we just want to see what data is provided for a query and not actually
do anything with that answer).

Hosts with just a CNAME alias will not be resolved to an IP address in
the answer section. If we ask for the A record of www.somehost.com we
may just get back a CNAME of webhost.somehost.com. To turn this into an
IP address we must then either hope it was provided in the additional
answer section (and check - see below for details) or perform another A
record lookup on webhost.somehost.com.

If you just want an IP address for a host then either PHP's inbuilt
gethostbyname() <http://www.php.net/> or this API's SmartALookup()
<#SmartALookup> are probably what you're after rather than a full blown
query.

### Answer and Query Types

Record (query and result) types the API supports will should return
sensible data for are: A, NS, PTR, MX, CNAME, TXT and SOA.

Asking for an unsupported type will cause the query to fail. Unsupported
types which are returned as result records will have null "string" and
"typeid" properties but will contain the binary data in "data" and the
decimal record type in "type".

### Answer Results

If a query succeeds it returns a DNSAnswer object containing a counter
property "count" indicating the number of answer records returned and an
array of DNSResult objects containing each of these records in turn.

The DNSAnswer object breaks down as follows:
$answer->count 	Number of answer records contained
$answer->results[x]->typeid 	Textual record type ID (A, MX, CNAME etc)
$answer->results[x]->type 	Numeric record type (decimal)
$answer->results[x]->class 	Numeric class type (decimal)
$answer->results[x]->data 	Data returned (i.e. IP address or hostname)
$answer->results[x]->domain 	Domain name data is for
$answer->results[x]->string 	String representation of the answer (i.e.
www.fish.sea has address x.y.z)
$answer->results[x]->extras 	Type-specific array of extra fields (i.e.
"level" for MX exchanges) - see below

### Type-specific Extras

Some result types have extended extra information which will be in array
form in the "extras" property of a DNSResult object.

MX record types have the decimal mail exchange priority in extas['level']

SOA record types have the responsible contact for the domain in
extras['responsible'] as well as the following:
extras['serial'] - domain serial
extras['refresh'] - domain refresh
extras['retry'] - domain retry
extras['expiry'] - domain expiry
extras['minttl'] - domain mimumum time-to-live (ttl)

### Smart A Lookup

Because doing an A lookup won't always return an IP address and
sometimes you're just after an IP address (not potentially a list of
them and aliases etc) the DNSQuery class provides the SmartALookup()
method.

This function simply takes a hostname and returns an IP address or a
null string if lookup failed (you can then check the DNSQuery lasterror
property to see if the query actually failed or just returned no results).

If the result data contains an IP address it will be returned (first
preference). If no IP addresses were provided but an alias CNAME is
given then this will be looked up (recursing up to a depth of five
aliases).

In effect this is a nameserver-specific version of gethostbyname() but returns a null string rather than the unmodified IP on failure.

## More Information

The technical documentation can be found here
<https://github.com/techno-express/phpdns/wiki/DNS-Query-API-Technical-Docs>

------------------------------------------------------------------------
[phpdns](http://www.purplepixie.org/phpdns) © Copyright 2008-2014
[PurplePixie Systems](http://www.purplepixie.org), all rights reserved, licensed under the [GNU](http://www.gnu.org/) [GPL](http://www.gnu.org/licences/gpl.html). Bugs, errata and comments should be posted to the [issues](https://github.com/purplepixie/phpdns/issues) tracker.
