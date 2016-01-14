<?php

namespace PurplePixie\PhpDns;

/**
 * This file is the PurplePixie PHP DNS Types Class
 *
 * The software is (C) Copyright 2008-16 PurplePixie Systems
 *
 * This is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * The software is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this software.  If not, see www.gnu.org/licenses
 *
 * For more information see www.purplepixie.org/phpdns
 */
class DNSTypes
{
    private $types = array(
        1 => 'A', // RFC 1035 (Address Record)
        2 => 'NS', // RFC 1035 (Name Server Record)
        5 => 'CNAME', // RFC 1035 (Canonical Name Record (Alias))
        6 => 'SOA', // RFC 1035 (Start of Authority Record)
        12 => 'PTR', // RFC 1035 (Pointer Record)
        15 => 'MX', // RFC 1035 (Mail eXchanger Record)
        16 => 'TXT', // RFC 1035 (Text Record)
        17 => 'RP', // RFC 1183 (Responsible Person)
        18 => 'AFSDB', // RFC 1183 (AFS Database Record)
        24 => 'SIG', // RFC 2535
        25 => 'KEY', // RFC 2535 & RFC 2930
        28 => 'AAAA', // RFC 3596 (IPv6 Address)
        29 => 'LOC', // RFC 1876 (Geographic Location)
        33 => 'SRV', // RFC 2782 (Service Locator)
        35 => 'NAPTR', // RFC 3403 (Naming Authority Pointer)
        36 => 'KX', // RFC 2230 (Key eXchanger)
        37 => 'CERT', // RFC 4398 (Certificate Record, PGP etc)
        39 => 'DNAME', // RFC 2672 (Delegation Name Record, wildcard alias)
        42 => 'APL', // RFC 3123 (Address Prefix List (Experimental)
        43 => 'DS', // RFC 4034 (Delegation Signer (DNSSEC)
        44 => 'SSHFP', // RFC 4255 (SSH Public Key Fingerprint)
        45 => 'IPSECKEY', // RFC 4025 (IPSEC Key)
        46 => 'RRSIG', // RFC 4034 (DNSSEC Signature)
        47 => 'NSEC', // RFC 4034 (Next-secure Record (DNSSEC))
        48 => 'DNSKEY', // RFC 4034 (DNS Key Record (DNSSEC))
        49 => 'DHCID', // RFC 4701 (DHCP Identifier)
        50 => 'NSEC3', // RFC 5155 (NSEC Record v3 (DNSSEC Extension))
        51 => 'NSEC3PARAM', // RFC 5155 (NSEC3 Parameters (DNSSEC Extension))
        55 => 'HIP', // RFC 5205 (Host Identity Protocol)
        99 => 'SPF', // RFC 4408 (Sender Policy Framework)
        249 => 'TKEY', // RFC 2930 (Secret Key)
        250 => 'TSIG', // RFC 2845 (Transaction Signature)
        251 => 'IXFR', // RFC 1995 (Incremental Zone Transfer)
        252 => 'AXFR', // RFC 1035 (Authoritative Zone Transfer)
        255 => 'ANY', // RFC 1035 AKA "*" (Pseudo Record)
        32768 => 'TA', // (DNSSEC Trusted Authorities)
        32769 => 'DLV', // RFC 4431 (DNSSEC Lookaside Validation)
    );

    /**
     * @param string $name
     * @return int
     */
    public function getByName($name)
    {
        if (false !== $index = array_search($name, $this->types, true)) {
            return $index;
        }

        return 0;
    }

    /**
     * @param int $id
     * @return string
     */
    public function getById($id)
    {
        if (isset($this->types[$id])) {
            return $this->types[$id];
        }

        return '';
    }

    /**
     * @return array
     */
    public function getAllTypeNamesSorted()
    {
        $types = array_values($this->types);
        sort($types);

        return $types;
    }
}
