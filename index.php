<?php // DNS PHP API Example
/* -------------------------------------------------------------
This file is the PurplePixie PHP DNS Query Classes

The software is (C) Copyright 2008-2016 PurplePixie Systems

This is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

The software is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this software.  If not, see www.gnu.org/licenses

For more information see www.purplepixie.org/phpdns
-------------------------------------------------------------- */


// Below is the recommended way to load PHP DNS, with individual
// classes:
use PurplePixie\PhpDns\DNSAnswer;
use PurplePixie\PhpDns\DNSQuery;
use PurplePixie\PhpDns\DNSTypes;

require_once __DIR__ . '/src/PurplePixie/PhpDns/DNSAnswer.php';
require_once __DIR__ . '/src/PurplePixie/PhpDns/DNSQuery.php';
require_once __DIR__ . '/src/PurplePixie/PhpDns/DNSResult.php';
require_once __DIR__ . '/src/PurplePixie/PhpDns/DNSTypes.php';

// Here is the legacy way (single file to load classes) BUT must
// now also have the namespace line:
/*
require_once("dns.inc.php");
use PurplePixie\PhpDns\DNSQuery;
*/

// ** IGNORE THIS - It's just the web form ** //
$server = isset($_REQUEST['server']) ? $_REQUEST['server'] : "127.0.0.1";
$port = isset($_REQUEST['port']) ? $_REQUEST['port'] : 53;
$timeout = isset($_REQUEST['timeout']) ? $_REQUEST['timeout'] : 60;
$udp = isset($_REQUEST['tcp']) ? false : true;
$debug = isset($_REQUEST['debug']) ? true : false;
$binarydebug = isset($_REQUEST['binarydebug']) ? true : false;
$extendanswer = isset($_REQUEST['extendanswer']) ? true : false;
$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : "A";
$question = isset($_REQUEST['question']) ? $_REQUEST['question'] : "www.purplepixie.org";

echo "<html><title>DNS Web Example</title><body>";
echo "<form action=./ method=get>";
echo "<input type=hidden name=doquery value=1>";
echo "Query <input type=text name=question size=50 value=\"".$question."\"> ";
echo "<select name=type>";
echo "<option value=".$type.">".$type."</option>";
$types=new DNSTypes();
$types2=$types->getAllTypeNamesSorted();
foreach ($types2 as $name) {
	echo "<option value=\"$name\">$name</option>";
}
echo "<option value=SMARTA>SmartA</option>"; 
echo "</select><br>";
echo "on nameserver <input type=text name=server size=30 value=\"".$server."\"> ";
echo "port <input type=text name=port size=4 value=\"".$port."\"><br>";

if (!$udp) $s=" checked";
else $s="";
echo "<input type=checkbox name=tcp value=1".$s."> use TCP, ";

if ($debug) $s=" checked";
else $s="";
echo "<input type=checkbox name=debug value=1".$s."> show debug, ";

if ($binarydebug) $s=" checked";
else $s="";
echo "<input type=checkbox name=binarydebug value=1".$s."> show binary,";

if ($extendanswer) $s=" checked";
else $s="";
echo "<input type=checkbox name=extendanswer value=1".$s."> show detail<br>";

echo "<input type=submit value=\"Perform DNS Query\"><br>";


// ** HERE IS THE QUERY SECTION ** //

if (isset($_REQUEST['doquery'])) {
    echo "<pre>";

    $query=new DNSQuery($server,$port,$timeout,$udp,$debug,$binarydebug);

    if ($type=="SMARTA") {
        echo "Smart A Lookup for ".$question."\n\n";
        $hostname=$query->smartALookup($question);
        echo "Result: ".$hostname."\n\n";
        echo "</pre>";
        exit();
    }

    echo "Querying: ".$question." -t ".$type." @".$server."\n";

    $result=$query->query($question,$type);

    if ($query->hasError()) {
        echo "\nQuery Error: ".$query->getLasterror()."\n\n";
        exit();
    }

    echo "Returned ".count($result)." Answers\n\n";

    ShowSection($result);

    if ($extendanswer) {
        echo "\nNameserver Records: ".count($query->getLastnameservers())."\n";
        ShowSection($query->getLastnameservers());

        echo "\nAdditional Records: ".count($query->getLastadditional())."\n";
        ShowSection($query->getLastadditional());
    }

    echo "</pre>";
}

function ShowSection(DNSAnswer $result)
{
    global $extendanswer;

    foreach ($result as $index => $record) {
        echo $index.". ";

        if ($record->getString()=="") {
            echo $record->getTypeid() . "(" . $record->getType() . ") => " . $record->getData();
        } else {
            echo $record->getString();
        }

        echo "\n";

        if ($extendanswer) {
            echo " - record type = ".$record->getTypeid()." (# ".$record->getType().")\n";
            echo " - record data = ".$record->getData()."\n";
            echo " - record ttl = ".$record->getTtl()."\n";

            // additional data
            if (count($record->getExtras()) > 0) {
                foreach($record->getExtras() as $key => $val) {
                    echo " + ".$key." = ".$val."\n";
                }
            }
        }

        echo "\n";
    }
}
