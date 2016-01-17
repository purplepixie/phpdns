<?php // PHP DNS API CLI Example
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
use PurplePixie\PhpDns\DNSQuery;

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

$server="127.0.0.1";
$port=53;
$timeout=60;
$udp=true;
$debug=false;
$binarydebug=false;
$type="A";
$question="www.purplepixie.org";

if ($argc>1) {
	for ($i=1; $i<$argc; $i++) {
		$arg=$argv[$i];

        if ($arg[0] == "@") {
            $server = substr($arg, 1);
        } elseif (($arg == "-t") || ($arg == "--t")) {
            $i++;
            $type = strtoupper($argv[$i]);
        } elseif (($arg == "--tcp") || ($arg == "-tcp")) {
            $udp = false;
        } elseif (($arg == "--udp") || ($arg == "-udp")) {
            $udp = true;
        } elseif (($arg == "--debug") || ($arg == "-d")) {
            $debug = true;
        } elseif ($arg == "-dd") {
            $debug = $binarydebug = true;
        } else {
            $question = $arg;
        }
    }
}

$query=new DNSQuery($server,$port,$timeout,$udp,$debug,$binarydebug);

echo "Querying: ".$question." -t ".$type." @".$server."\n";

$result=$query->query($question,$type);

if ($query->hasError()) {
    echo "Query Error: " . $query->getLasterror() . "\n";
    exit();
}

echo "Returned ".count($result)." Answers\n";

foreach ($result as $index => $record) {
    echo $index . ". " . $record->getTypeid() . "(" . $record->getType() . ") => " . $record->getData() . " [";
    echo $record->getString();
    echo "]\n";

    // additional data
    if (count($record->getExtras()) > 0) {
        foreach ($record->getExtras() as $key => $val) {
            // We don't want to echo binary data
            if ($key != 'ipbin') {
                echo " - " . $key . " = " . $val . "\n";
            }
        }
    }
}
