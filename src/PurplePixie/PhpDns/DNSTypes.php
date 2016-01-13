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
    /**
     * @var array
     */
    protected $types_by_id;

    /**
     * @var array
     */
    protected $types_by_name;

    /**
     * @param int $id
     * @param string $name
     */
    public function AddType($id, $name)
    {
        $this->types_by_id[$id] = $name;
        $this->types_by_name[$name] = $id;
    }

    public function __construct()
    {
        $this->types_by_id = array();
        $this->types_by_name = array();

        $this->AddType(1, 'A'); // RFC 1035 (Address Record)
        $this->AddType(2, 'NS'); // RFC 1035 (Name Server Record)
        $this->AddType(5, 'CNAME'); // RFC 1035 (Canonical Name Record (Alias))
        $this->AddType(6, 'SOA'); // RFC 1035 (Start of Authority Record)
        $this->AddType(12, 'PTR'); // RFC 1035 (Pointer Record)
        $this->AddType(15, 'MX'); // RFC 1035 (Mail eXchanger Record)
        $this->AddType(16, 'TXT'); // RFC 1035 (Text Record)
        $this->AddType(17, 'RP'); // RFC 1183 (Responsible Person)
        $this->AddType(18, 'AFSDB'); // RFC 1183 (AFS Database Record)
        $this->AddType(24, 'SIG'); // RFC 2535
        $this->AddType(25, 'KEY'); // RFC 2535 & RFC 2930
        $this->AddType(28, 'AAAA'); // RFC 3596 (IPv6 Address)
        $this->AddType(29, 'LOC'); // RFC 1876 (Geographic Location)
        $this->AddType(33, 'SRV'); // RFC 2782 (Service Locator)
        $this->AddType(35, 'NAPTR'); // RFC 3403 (Naming Authority Pointer)
        $this->AddType(36, 'KX'); // RFC 2230 (Key eXchanger)
        $this->AddType(37, 'CERT'); // RFC 4398 (Certificate Record, PGP etc)
        $this->AddType(39, 'DNAME'); // RFC 2672 (Delegation Name Record, wildcard alias)
        $this->AddType(42, 'APL'); // RFC 3123 (Address Prefix List (Experimental)
        $this->AddType(43, 'DS'); // RFC 4034 (Delegation Signer (DNSSEC)
        $this->AddType(44, 'SSHFP'); // RFC 4255 (SSH Public Key Fingerprint)
        $this->AddType(45, 'IPSECKEY'); // RFC 4025 (IPSEC Key)
        $this->AddType(46, 'RRSIG'); // RFC 4034 (DNSSEC Signature)
        $this->AddType(47, 'NSEC'); // RFC 4034 (Next-secure Record (DNSSEC))
        $this->AddType(48, 'DNSKEY'); // RFC 4034 (DNS Key Record (DNSSEC))
        $this->AddType(49, 'DHCID'); // RFC 4701 (DHCP Identifier)
        $this->AddType(50, 'NSEC3'); // RFC 5155 (NSEC Record v3 (DNSSEC Extension))
        $this->AddType(51, 'NSEC3PARAM'); // RFC 5155 (NSEC3 Parameters (DNSSEC Extension))
        $this->AddType(55, 'HIP'); // RFC 5205 (Host Identity Protocol)
        $this->AddType(99, 'SPF'); // RFC 4408 (Sender Policy Framework)
        $this->AddType(249, 'TKEY'); // RFC 2930 (Secret Key)
        $this->AddType(250, 'TSIG'); // RFC 2845 (Transaction Signature)
        $this->AddType(251, 'IXFR'); // RFC 1995 (Incremental Zone Transfer)
        $this->AddType(252, 'AXFR'); // RFC 1035 (Authoritative Zone Transfer)
        $this->AddType(255, 'ANY'); // RFC 1035 AKA "*" (Pseudo Record)
        $this->AddType(32768, 'TA'); // (DNSSEC Trusted Authorities)
        $this->AddType(32769, 'DLV'); // RFC 4431 (DNSSEC Lookaside Validation)
    }

    /**
     * @param string $name
     * @return int
     */
    public function GetByName($name)
    {
        if (isset($this->types_by_name[$name])) {
            return $this->types_by_name[$name];
        }
        return 0;
    }

    /**
     * @param int $id
     * @return string
     */
    public function GetById($id)
    {
        if (isset($this->types_by_id[$id])) {
            return $this->types_by_id[$id];
        }
        return '';
    }

    /**
     * @return array
     */
    public function getAllTypesByName()
    {
        return $this->types_by_name;
    }
}