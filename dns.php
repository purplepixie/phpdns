<?php // PHP DNS API CLI Example
/* -------------------------------------------------------------
This file is the PurplePixie PHP DNS Query Classes

The software is (C) Copyright 2008-2014 PurplePixie Systems

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
require("dns.inc.php");

$server="127.0.0.1";
$port=53;
$timeout=60;
$udp=true;
$debug=false;
$binarydebug=false;
$type="A";
$question="www.purplepixie.org";


if ($argc>1)
	{
	for ($i=1; $i<$argc; $i++)
		{
		$arg=$argv[$i];
		if ($arg[0]=="@") $server=substr($arg,1);
		else if (($arg=="-t")||($arg=="--t"))
			{
			$i++;
			$type=strtoupper($argv[$i]);
			}
		else if (($arg=="--tcp")||($arg=="-tcp")) $udp=false;
		else if (($arg=="--udp")||($arg=="-udp")) $udp=true;
		else if (($arg=="--debug")||($arg=="-d")) $debug=true;
		else if ($arg=="-dd") $debug=$binarydebug=true;
		else $question=$arg;
		}
	}

$query=new DNSQuery($server,$port,$timeout,$udp,$debug,$binarydebug);

echo "Querying: ".$question." -t ".$type." @".$server."\n";

$result=$query->Query($question,$type);

if ($query->error)
	{
	echo "Query Error: ".$query->lasterror."\n";
	exit();
	}
echo "Returned ".$result->count." Answers\n";
for ($i=0; $i<$result->count; $i++)
	{
	echo $i.". ".$result->results[$i]->typeid."(".$result->results[$i]->type.") => ".$result->results[$i]->data." [";
	echo $result->results[$i]->string;
	echo "]\n";
	if (count($result->results[$i]->extras)>0) // additional data
		{
		foreach($result->results[$i]->extras as $key => $val)
			{
			if ($key != 'ipbin') // We don't want to echo binary data
				{
				echo " - ".$key." = ".$val."\n";
				}
			}
		}
	}
?>
