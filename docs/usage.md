### [<< Back to the main page <<](./)

# phpdns User Documentation

Having successfully installed phpdns (if not see the "Installing / Obtaining phpdns" section of the [main index page](./)) you are now able to use it.

**Note:** This documentation assumes you have installed phpdns and are able to include it.

The phpdns library will on request make a network connection to a remote DNS server (using UDP or TCP) to ask a question (the query) and receive a response (an answer containing one or more results). The library consists of four main classes:

- ```DNSQuery``` - the primary class with which a user interfaces, builds and makes the actual query to the remote server.
- ```DNSAnswer``` - a list/wrapper class containing the ```DNSResult```(s) from the remote server.
- ```DNSResult``` - an actual result response from the remote server.
- ```DNSType``` - a list of the types of DNS records that can be used in the query or in a result response.

## A Simple Example

Here is a simple example to find an A record and output the result:

```php
// Use this namespace for ease
use PurplePixie\PhpDns\DNSQuery;

// Remote DNS Server
$dnsServer = "8.8.8.8";
// The question we are going to ask for an answer to
$question = "www.purplepixie.org";
// The type of record/answer we are asking for
$type = \PurplePixie\PhpDns\DNSTypes::NAME_A;

try
{
    // Create the DNSQuery instance for the remote server
    $query = new DNSQuery($dnsServer);

    // Perform an actual query for question and type
    // note this will return a DNSAnswer instance
    $answer = $query->query($question, $type);

    // Check for an error
    if ($answer === false || $query->hasError())
    {
        echo "Error: ".$query->getLasterror()."\n";
    }
    else // successful query
    {
        foreach($answer as $result)
        {
            // we are only after results of the type we want
            if ($result->getType() == $type) 
            {
                echo $question." has IP address ".$result->getData()."\n";
                // or we can use the already built English string
                echo $result->getString()."\n";
            }
        }
    }
}
catch(\Exception $e)
{
    print_r($e->getMessage());
}
```

If all goes well the output should be something like:
```
www.purplepixie.org has IP address 151.80.237.60
www.purplepixie.org has IPv4 address 151.80.237.60
```

The first output is from our script writing the question and the "data" result, the second when we output the "string" property of the answer which is the specific record in human-readable form (if the type is known).

## A Note of Warning and Why Check the Answer Type Above

DNS is not an entirely straightforward protocol and things which on the surface may seem simple may not be when you delve deeper (if you already understand DNS then skip ahead).

For example an IP lookup (A record lookup) for a host on a specific DNS server may well not just return a single answer record containing the IP address. The host may be multi-honed and return multiple records any which may not be an IP address but a CNAME alias. The namesever you are querying may not do a recursive or cache lookup for you and so return no answers even though the domain and host do exist.

For this reason we must actually process the results (unless of course we just want to see what data is provided for a query and not actually do anything with that answer).

Hosts with just a CNAME alias will not be resolved to an IP address in the answer section. If we ask for the A record of www.somehost.com we may just get back a CNAME of webhost.somehost.com. To turn this into an IP address we must then either hope it was provided in the additional answer section (and check - see below for details) or perform another A record lookup on webhost.somehost.com.

If you just want an IP address for a host then either PHP's inbuilt gethostbyname() or this API's SmartALookup() (see below) are probably what you're after rather than a full blown query.

## Answer and Query Types

Record (query and result) types the API supports and should return sensible data for numerous and include the major record types such as A, NS, PTR, MX, CNAME, TXT and SOA. For a full list see the [DNSTypes Class](https://github.com/purplepixie/phpdns/blob/master/src/PurplePixie/PhpDns/DNSTypes.php).

Asking for an unsupported type will cause the query to fail. Unsupported types which are returned as result records will have null "string" and "typeid" properties but will contain the binary data in "data" and the decimal record type in "type".

## Answer Results

If a query succeeds it returns an iteratable ```DNSAnswer``` object containing zero or more ```DNSResult``` objects with the specific answers. a counter property "count" indicating the number of answer records returned and an array of DNSResult objects containing each of these records in turn.

You can get a count of the results using ```DNSAnswer::count()``` and either iterate using standard PHP iterations such as ```foreach``` or accessing by numeric key.

Each ```DNSResult``` object contains a lot of data, key items of which are:

- ```DNSResult::getType()``` - returns a textual version of the record type (i.e. "A").
- ```DNSResult::getTypeid()``` - returns the numeric record type which can be mapped to ```DNSType``` if supported.
- ```DNSResult::getClass()``` - returns the numeric class type (decimal) .
- ```DNSResult::getData()``` - returns the data returned by the query (i.e. IP address or hostname).
- ```DNSResult::getDomain()``` - returns the domain name data is for.
- ```DNSResult::getString()``` - returns a string representation of the answer (i.e. www.fish.sea has address x.y.z).
- ```DNSResult::getExtras()``` - returns a specific array of extra fields (i.e. "level" for MX records) - see below.

### Type-specific Extras

Some result types have extended extra information which will be in array form in the "extras" property of a ```DNSResult``` object.

MX record types for example have the decimal mail exchange priority in ```extas['level']```

SOA record types have the responsible contact for the domain in ```extras['responsible']``` as well as the following:

- ```extras['serial']``` - domain serial
- ```extras['refresh']``` - domain refresh
- ```extras['retry']``` - domain retry
- ```extras['expiry']``` - domain expiry
- ```extras['minttl']``` - domain mimumum time-to-live (TTL)


## Smart A Lookup / Smart AAAA Lookup

Because doing an A/AAAA lookup won't always return an IP address and sometimes you're just after an IP address (not potentially a list of them and aliases etc) the ```DNSQuery``` class provides the ```DNSQuery::SmartALookup()``` and ```DNSQuery::SmartAAAALookup()``` methods.

These methods takes a hostname (and an optional recursion depth which defaults to 5 if not passed) and returns an IPv4/v6 address or a null string if lookup failed (you can then check the ```DNSQuery``` error property to see if the query actually failed or just returned no results).

If the result data contains an IP address it will be returned (first preference). If no IP addresses were provided but an alias CNAME is given then this will be looked up (recursing up to a depth of five aliases; with requests to the same DNS server).

In effect this is a nameserver-specific version of [gethostbyname()](https://www.php.net/manual/en/function.gethostbyname.php) but returns a null string rather than the unmodified IP on failure.

**Note:** Smart A/AAAA Lookup only works on a single server, if you want to do a recursive resolution to different nameservers you will need to do that yourself; see the example on the [examples page](./examples).

## Error Handling

By default the ```DNSQuery``` class will return errors by (1) return a boolean ```false``` result to a method call, (2) setting the error flag and (3) putting a message into the ```lastError``` buffer.

The easiest way to check if an error occured in the last transaction therefore is to check the result of the ```hasError()``` method.

```php
$query = new DNSQuery("some.server.com");
$answer = $query->query("a.question.here", DNSTypes::NAME_A);
if ($query->hasError())
{
    // error handling code goes here
    echo "Error: ".$query->getLasterror()."\n";
}
```

In the above example assuming that a fatal error occured the ```$answer``` would be a boolean ```false``` also (though there are times where an error can occur but an answer is still returned).

There are some errors which will throw exceptions such as an invalid or unknown type being used in a query.

Connection errors can **optionally** throw an exception with a message (in which case the ```hasError()``` flag will also be set, with the message both in the exception and in the ```lastError``` buffer).

This behaviour can be triggered by setting the ```ConnectionException``` flag using ```setConnectionException(true)```.

```php
$query = new DNSQuery("some.server.com");

$query->setConnectionException(true);

try
{
    $a = $query->query("a.question.here", DNSTypes::NAME_A);
}
catch(\Exception $e)
{
    // error handling
    print_r($e);
}
```

Sometimes (especially on UDP requests) data will be returned but the format is incorrect and thus the ``unpack()`` routine fails to find the right header.

This will output an error and optionally throw an ``InvalidReponse`` exception if the flag is set with ``setResponseException(true)``.

## RCODE in Answer

DNS servers will return in their answer a Response Code (RCODE) from [RFC 1035](https://www.ietf.org/rfc/rfc1035.txt) defined as follows:

```
RCODE           Response code - this 4 bit field is set as part of responses.  The values have the following interpretation:

0               No error condition

1               Format error - The name server was unable to interpret the query.

2               Server failure - The name server was unable to process this query due to a problem with the name server.

3               Name Error - Meaningful only for responses from an authoritative name server, this code signifies that the domain name referenced in the query does not exist.

4               Not Implemented - The name server does not support the requested kind of query.

5               Refused - The name server refuses to perform the specified operation for policy reasons.  For example, a name server may not wish to provide the information to the particular requester, or a name server may not wish to perform a particular operation (e.g., zone transfer) for particular data.

6-15            Reserved for future use.
```

Once a ```DNSAnswer``` object is returned the RCODE can be read through ```DNSAnswer::getRcode()``` to get the numeric value and a brief English textual description of the code can be fetched through ```DNSAnswer::getRcodeDescription()```. Note that the default initialised value in ```DNSAnswer``` is ```-1``` which indicates that no answer has been returned/lookup performed.
