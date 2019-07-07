<?php // DNS PHP Loader
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

// This file loads the required libraries from the /src/ directory
// to mimic legacy behaviour and allow a single-file include.

require_once __DIR__ . '/src/PurplePixie/PhpDns/DNSAnswer.php';
require_once __DIR__ . '/src/PurplePixie/PhpDns/DNSQuery.php';
require_once __DIR__ . '/src/PurplePixie/PhpDns/DNSResult.php';
require_once __DIR__ . '/src/PurplePixie/PhpDns/DNSTypes.php';
?>