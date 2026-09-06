### [<< Back to the main page <<](./)

# phpdns Examples

Here are some examples of phpdns being used. Please feel free to contribute to the documentation with specific interesting usage examples.

## Recursive Resolution

In normal operation phpdns will ask a single question of a given nameserver. This is fine when the nameserver is able to provide the answer (for example you have asked a local recursive resolver or an authoritative server for a zone) but is not representative of how DNS may actually work.

A full recursive DNS resolution, for example as you see with ```dig +trace``` will start at the *root servers* and gradually work down. For example to resolve ```davecutting.uk``` A record the root servers won't know the answer but will provide the nameservers for .uk. The .uk nameservers won't know the answer but will know the next step, and so on.

You can use phpdns to perform a full recursive search but you need to handle the recursive elements, it will only make the connections to each server in turn.

Note in this example we start and continue to use *names* for the nameservers, so an internal DNS resolution is performed. We could once running add another step to use the additional section which usually provides glue records.

```php
<?php
require("vendor/autoload.php");

// Use namespaces for ease
use PurplePixie\PhpDns\DNSQuery;
use PurplePixie\PhpDns\DNSTypes;

// Recursive function to work through NS records to type
function DNSRecurse($question, $type, $server)
{
    echo "Query ".$question." ".$type." @ ".$server."\n";
    $query = new DNSQuery($server);
    $answer = $query->query($question, $type);
    // error, stop here
    if ($answer === false || $query->hasError())
    {
        echo "Error: ".$query->getLasterror()."\n";
    }
    else // successful query
    {
        if ($answer->count() > 0) // found an answer!
        {
            foreach($answer as $result)
            {
                echo $result->getString()." from ".$server."\n";
            }
        }
        else // not found one - let's check the returned nameservers
        {
            $nameservers = $query->getLastnameservers();
            if ($nameservers->count() == 0) // no route forward, exit
            {
                echo "No more nameservers returned.\n";
            }
            else // found a nameserver(s) to move on to
            {
                $ns = $nameservers->current();
                $newserver = $ns->getData(); // get the nameserver
                // and recurse
                DNSRecurse($question, $type, $newserver);
            }
        }
    }
}

// start with a list of nameservers
$rootServers = array(
    "a.root-servers.net",
    "b.root-servers.net",
    "c.root-servers.net",
    "d.root-servers.net",
    "e.root-servers.net",
    "f.root-servers.net",
    "g.root-servers.net",
    "h.root-servers.net",
    "i.root-servers.net" 
);  

// pick one at random
$startServer = $rootServers[array_rand($rootServers)];

echo "Starting from root server: ".$startServer."\n";

// question and type we're asking
$question = "www.purplepixie.org";
$type = \PurplePixie\PhpDns\DNSTypes::NAME_A;

// and kick the process off
DNSRecurse($question, $type, $startServer);
```