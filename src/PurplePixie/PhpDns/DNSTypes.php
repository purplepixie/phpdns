<?php

namespace PurplePixie\PhpDns;

/**
 * This file is the PurplePixie PHP DNS Types Class
 *
 * The software is (C) Copyright 2008-16 PurplePixie Systems
 * Copyright (C) 2022, Fabian Bett / Bett Ingenieure GmbH
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
    const ID_A = 1;
    const ID_NS = 2;
    const ID_CNAME = 5;
    const ID_SOA = 6;
    const ID_PTR = 12;
    const ID_MX = 15;
    const ID_TXT = 16;
    const ID_RP = 17;
    const ID_AFSDB = 18;
    const ID_SIG = 24;
    const ID_KEY = 25;
    const ID_AAAA = 28;
    const ID_LOC = 29;
    const ID_SRV = 33;
    const ID_NAPTR = 35;
    const ID_KX = 36;
    const ID_CERT = 37;
    const ID_DNAME = 39;
    const ID_APL = 42;
    const ID_DS = 43;
    const ID_SSHFP = 44;
    const ID_IPSECKEY = 45;
    const ID_RRSIG = 46;
    const ID_NSEC = 47;
    const ID_DNSKEY = 48;
    const ID_DHCID = 49;
    const ID_NSEC3 = 50;
    const ID_NSEC3PARAM = 51;
    const ID_HIP = 55;
    const ID_SPF = 99;
    const ID_TKEY = 249;
    const ID_TSIG = 250;
    const ID_IXFR = 251;
    const ID_AXFR = 252;
    const ID_ANY = 255;
    const ID_TA = 32768;
    const ID_DLV = 32769;

    const NAME_A = 'A';
    const NAME_NS = 'NS';
    const NAME_CNAME = 'CNAME';
    const NAME_SOA = 'SOA';
    const NAME_PTR = 'PTR';
    const NAME_MX = 'MX';
    const NAME_TXT = 'TXT';
    const NAME_RP = 'RP';
    const NAME_AFSDB = 'AFSDB';
    const NAME_SIG = 'SIG';
    const NAME_KEY = 'KEY';
    const NAME_AAAA = 'AAAA';
    const NAME_LOC = 'LOC';
    const NAME_SRV = 'SRV';
    const NAME_NAPTR = 'NAPTR';
    const NAME_KX = 'KX';
    const NAME_CERT = 'CERT';
    const NAME_DNAME = 'DNAME';
    const NAME_APL = 'APL';
    const NAME_DS = 'DS';
    const NAME_SSHFP = 'SSHFP';
    const NAME_IPSECKEY = 'IPSECKEY';
    const NAME_RRSIG = 'RRSIG';
    const NAME_NSEC = 'NSEC';
    const NAME_DNSKEY = 'DNSKEY';
    const NAME_DHCID = 'DHCID';
    const NAME_NSEC3 = 'NSEC3';
    const NAME_NSEC3PARAM = 'NSEC3PARAM';
    const NAME_HIP = 'HIP';
    const NAME_SPF = 'SPF';
    const NAME_TKEY = 'TKEY';
    const NAME_TSIG = 'TSIG';
    const NAME_IXFR = 'IXFR';
    const NAME_AXFR = 'AXFR';
    const NAME_ANY = 'ANY';
    const NAME_TA = 'TA';
    const NAME_DLV = 'DLV';

    private array $types = [
        self::ID_A => self::NAME_A, // RFC 1035 (Address Record)
        self::ID_NS => self::NAME_NS, // RFC 1035 (Name Server Record)
        self::ID_CNAME => self::NAME_CNAME, // RFC 1035 (Canonical Name Record (Alias))
        self::ID_SOA => self::NAME_SOA, // RFC 1035 (Start of Authority Record)
        self::ID_PTR => self::NAME_PTR, // RFC 1035 (Pointer Record)
        self::ID_MX => self::NAME_MX, // RFC 1035 (Mail eXchanger Record)
        self::ID_TXT => self::NAME_TXT, // RFC 1035 (Text Record)
        self::ID_RP => self::NAME_RP, // RFC 1183 (Responsible Person)
        self::ID_AFSDB => self::NAME_AFSDB, // RFC 1183 (AFS Database Record)
        self::ID_SIG => self::NAME_SIG, // RFC 2535
        self::ID_KEY => self::NAME_KEY, // RFC 2535 & RFC 2930
        self::ID_AAAA => self::NAME_AAAA, // RFC 3596 (IPv6 Address)
        self::ID_LOC => self::NAME_LOC, // RFC 1876 (Geographic Location)
        self::ID_SRV => self::NAME_SRV, // RFC 2782 (Service Locator)
        self::ID_NAPTR => self::NAME_NAPTR, // RFC 3403 (Naming Authority Pointer)
        self::ID_KX => self::NAME_KX, // RFC 2230 (Key eXchanger)
        self::ID_CERT => self::NAME_CERT, // RFC 4398 (Certificate Record, PGP etc)
        self::ID_DNAME => self::NAME_DNAME, // RFC 2672 (Delegation Name Record, wildcard alias)
        self::ID_APL => self::NAME_APL, // RFC 3123 (Address Prefix List (Experimental)
        self::ID_DS => self::NAME_DS, // RFC 4034 (Delegation Signer (DNSSEC)
        self::ID_SSHFP => self::NAME_SSHFP, // RFC 4255 (SSH Public Key Fingerprint)
        self::ID_IPSECKEY => self::NAME_IPSECKEY, // RFC 4025 (IPSEC Key)
        self::ID_RRSIG => self::NAME_RRSIG, // RFC 4034 (DNSSEC Signature)
        self::ID_NSEC => self::NAME_NSEC, // RFC 4034 (Next-secure Record (DNSSEC))
        self::ID_DNSKEY => self::NAME_DNSKEY, // RFC 4034 (DNS Key Record (DNSSEC))
        self::ID_DHCID => self::NAME_DHCID, // RFC 4701 (DHCP Identifier)
        self::ID_NSEC3 => self::NAME_NSEC3, // RFC 5155 (NSEC Record v3 (DNSSEC Extension))
        self::ID_NSEC3PARAM => self::NAME_NSEC3PARAM, // RFC 5155 (NSEC3 Parameters (DNSSEC Extension))
        self::ID_HIP => self::NAME_HIP, // RFC 5205 (Host Identity Protocol)
        self::ID_SPF => self::NAME_SPF, // RFC 4408 (Sender Policy Framework)
        self::ID_TKEY => self::NAME_TKEY, // RFC 2930 (Secret Key)
        self::ID_TSIG => self::NAME_TSIG, // RFC 2845 (Transaction Signature)
        self::ID_IXFR => self::NAME_IXFR, // RFC 1995 (Incremental Zone Transfer)
        self::ID_AXFR => self::NAME_AXFR, // RFC 1035 (Authoritative Zone Transfer)
        self::ID_ANY => self::NAME_ANY, // RFC 1035 AKA "*" (Pseudo Record)
        self::ID_TA =>  self::NAME_TA, // (DNSSEC Trusted Authorities)
        self::ID_DLV => self::NAME_DLV, // RFC 4431 (DNSSEC Lookaside Validation)
    ];

    /**
     * @param string $name
     * @return int
     * @throws Exceptions\InvalidQueryTypeName
     */
    public function getIdFromName(string $name): int
    {
        if ( false !== $index = array_search($name, $this->types, true)) {
            return $index;
        }

        throw new Exceptions\InvalidQueryTypeName($name);
    }

    /**
     * @param int $id
     * @return string
     * @throws Exceptions\InvalidQueryTypeId
     */
    public function getNameById(int $id): string
    {
        if (isset($this->types[$id])) {
            return $this->types[$id];
        }

        throw new Exceptions\InvalidQueryTypeId($id);
    }

    public function getAllTypeNamesSorted(): array
    {
        $types = array_values($this->types);
        sort($types);

        return $types;
    }
}
