# DNS Query API Technical Docs

This documentation covers some (not all) of the objects, methods and
properties of the API. The code itself should be fairly easy to hack if
more complex changes are required.

*Class: DNSQuery *

The main DNS API class.

*property* *DNSQuery::server* *(string)*
------------------------------------------------------------------------
DNS server hostname or IP as a string

*property* *DNSQuery::port* *(decimal)*
------------------------------------------------------------------------
Port to use (default 53)

*property* *DNSQuery::timeout* *(decimal)*
------------------------------------------------------------------------
Timeout in seconds (default 60)

*property* *DNSQuery::udp* *(bool)*
------------------------------------------------------------------------
Will use UDP if true else use TCP (default true)

*property* *DNSQuery::debug* *(bool)*
------------------------------------------------------------------------
Output some debug information when querying (default false)

*property* *DNSQuery::debugbinary* *(bool)*
------------------------------------------------------------------------
Dump binary debug output when querying (default false)

*property* *DNSQuery::error* *(bool)*
------------------------------------------------------------------------
Set if the last query resulted in an error

*property* *DNSQuery::lasterror* *(string)*
------------------------------------------------------------------------
Description of the last error encountered (null string if no error)

*property* *DNSQuery::lastnameservers* *(Class::DNSAnswer)*
------------------------------------------------------------------------
Nameserver record results returned from the last query

*property* *DNSQuery::lastadditional* *(Class::DNSAnswer)*
------------------------------------------------------------------------
Result records from the additional section of the last query

*method* *DNSQuery::DNSQuery* *(hostname (string), [port=53 (decimal)],
[timeout=60 (decimal)], [udp=true (bool)], [debug=false (bool)])*
------------------------------------------------------------------------
The constructor function - do not call this directly, rather use
/$object=new DNSQuery(options...)/ to create the object. Configuration
can be changed after initilisation by setting the relevant property
directly.

*mixed* *method* *DNSQuery::Query* *(question (string), [type=A (string)])*
------------------------------------------------------------------------
Query the nameserver with the given question for the optionally
specified type (defaults to A). The question string would normally
consist of a hostname, domain name or IP address (in normal or
IN-ADDR.ARPA form).

The query will be executed against the configured server using the set
protocol (UDP if the udp property is true else TCP). If an error is
encountered at any point the error property is set to true, a
description placed in the lasterror property and the method returns false.

On successful completion of the query a Class::DNSAnswer is returned
containing the records from the answer portion of the response. The
class properties lastnameservers and lastadditional will be set as
Class::DNSAnswer objects containing the resource records returned in the
nameserver and additional portion of the answer respectively.

*string* *method* *DNSQuery::SmartALookup* *(hostname (string))*
------------------------------------------------------------------------
Takes a hostname and returns an IP address or a null string if the query
failed. Will recursively lookup aliases to a depth of 5.

Intended as a DNS server specific version of gethostbyname()



*Class: DNSAnswer*

*property* *DNSAnswer::count* *(decimal)*
------------------------------------------------------------------------
Number of result records contained within the answer

*property* *DNSAnswer::results* *(Class::DNSResult Array)*
------------------------------------------------------------------------
An array of Class::DNSResult objects containing each of the result records



*Class: DNSResult*

*property* *DNSResult::type* *(decimal)*
------------------------------------------------------------------------
Decimal record type

*property* *DNSResult::typeid* *(string)*
------------------------------------------------------------------------
String of the type (i.e. A, MX, SOA etc) or null string if an unknown type

*property* *DNSResult::class* *(decimal)*
------------------------------------------------------------------------
Numeric result class type

*property* *DNSResult::ttl* *(decimal)*
------------------------------------------------------------------------
Time-to-live (TTL) of resource record

*property* *DNSResult::data* *(string|binary)*
------------------------------------------------------------------------
The processed output of a known type (the IP address for an A record,
mail exchange for an MX record) or the binary data for an unknown type

*property* *DNSResult::domain* *(string)*
------------------------------------------------------------------------
The domain/domain name/hostname the record applies to i.e. the hostname
that the A record in the data type of an A lookup refers to

*property* *DNSResult::string* *(string)*
------------------------------------------------------------------------
A textual representation of the answer (if the type is known else a null
string) i.e. "www.fish.com has address 1.2.3.4"

*property* *DNSResult::extras* *(string Array)*
------------------------------------------------------------------------
An array of any extra data available for the record if the type was
known with a relevant key i.e. "level" for MX record mail exchange
priorities. See the main documentation for a full list of extra
type-dependent data.

------------------------------------------------------------------------
[phpdns](http://www.purplepixie.org/phpdns) © Copyright 2008-2014
[PurplePixie Systems](http://www.purplepixie.org), all rights reserved, licensed under the [GNU](http://www.gnu.org/) [GPL](http://www.gnu.org/licences/gpl.html). Bugs, errata and comments should be posted to the [issues](https://github.com/purplepixie/phpdns/issues) tracker.
