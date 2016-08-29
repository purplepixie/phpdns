phpdns
======

DNS API / Library for PHP - www.purplepixie.org/phpdns/

Changelog
---------

```
Version 2.0.5 29th August 2016

David Cutting -- dcutting (some_sort_of_at_sign) purplepixie dot org

D Tucny - 2011-06-21 - Add lots more types with RFC references
D Tucny - 2011-06-21 - Add decoding of IPv6 addresses in AAAA RRs and sorted handling alphabetically
D Tucny - 2011-06-21 - Switch to using inet_ntop function where available to decode IP addresses
                       and include binary IP in extras
D Tucny - 2011-06-21 - Add fallback inet_ntop function (sourced from php.net) for
                       platforms missing it
D Tucny - 2011-06-21 - Correct string extraction for TXT records 
                       (a TXT record can contain one of more 255 character strings 
                       with an initial length byte that are concatenated togeter) 
                       the length bytes were being included in the result as part of the text
D Tucny - 2011-06-21 - Add decoding of SPF as an alias of TXT due to identical formatting
D Tucny - 2011-06-21 - Make binarydebug a parameter of DNSQuery in addition to plain debug
D Tucny - 2011-06-21 - Add header flag parsing
D Tucny - 2011-06-21 - Add error handling for truncation flag being set when using UDP, i.e response too large
D Tucny - 2011-06-21 - Add decoding of SRV RRs
D Tucny - 2011-06-22 - Add initial decoding of DNSKEY RRs

D Cutting - 2012-06-22 - Added special case "." query for root-servers
D Cutting - 2012-06-22 - Fixed . positioning bug in 0.04

Remi Smith - 2013-05-23 - Alternative AAAA IPV6 Patching for 0.02 (included into general build - DC)
D Cutting - 2013-08-04 - Implemented bugfix for Serial and TTL provided by Kim Akero
D Cutting - 2014-02-21 - Implemented DNSKEY type recovery from data packet
D Cutting - 2014-11-19 - Fixed fsockopen bug (BID396) thanks to semperfi on forum
D Cutting - 2014-12-30 - Added stream timeout function (thanks to Jorgen Thomsen) [1.04]
                         Also corrected some indentation, added comment and updated copyright
D Cutting - 2014-12-30 - Corrected error typo (thanks to Jorgen Thomsen) [1.05]

Bert-Jan de Lange - 2016-01-13 - Convert to PHP5, PSR-0, composer|2.0-dev

D Cutting - 2016-01-16 - Integrated NSEC Support from zatr0z (https://github.com/xatr0z)
                         https://github.com/purplepixie/phpdns/pull/1
D Cutting - 2016-01-16 - Integrated NAPTR Support from Yurji (https://github.com/Yurij)
                         https://github.com/purplepixie/phpdns/pull/4
D Cutting - 2016-01-16 - Updated copyright etc to PHP4 final release 1.06

D Cutting - 2016-01-16 - Merged in changes from bjdelange (2016-01-13) and resolved conflicts for 1.10
                         https://github.com/purplepixie/phpdns/pull/5
                         by https://github.com/bjdelange
D Cutting - 2016-01-17 - Merged updated changes by Bert-Jan de Lange without composer, added legacy files - 1.11

D Cutting - 2016-01-17 - 2.0.4 release

D Cutting - 2016-08-29 - 2.0.5 release: merged changes by Derekholio via github (pull request 7)
```
