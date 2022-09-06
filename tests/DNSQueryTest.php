<?php

/**
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

use PHPUnit\Framework\TestCase;

use PurplePixie\PhpDns\DNSQuery;

class DNSQueryTest extends TestCase
{
    /**
     * @covers \PurplePixie\PhpDns\DNSQuery::query
     * @covers \PurplePixie\PhpDns\DNSTypes::getIdFromName
     */
    public function testInvalidDNSTypeName(): void
    {
        $query = new DNSQuery("8.8.8.8");
        $this->expectException(\PurplePixie\PhpDns\Exceptions\InvalidQueryTypeName::class);
        $query->query('google.com', 'invalid');
    }

    public function testALookup() : void {

        $query = new DNSQuery("8.8.8.8");
        $queryResult = $query->query('google.com', \PurplePixie\PhpDns\DNSTypes::NAME_A);
        $this->assertTrue($queryResult->count() > 0);

        foreach($queryResult as $entry) {

            // TODO: What is the expected class?
            //$this->assertEquals("1", $entry->getClass());

            $this->assertTrue(
                $entry->getData() != ''
                && strpos($entry->getData(), '.') !== false,
                'Result: ' . $entry->getData(),
            );

            $this->assertEquals('google.com', $entry->getDomain());

            $this->assertTrue(is_array($entry->getExtras()));
            // TODO: Test extra array

            $this->assertTrue( strpos($entry->getString(),'google.com has IPv4 address ') === 0, 'Result: ' . $entry->getString());
            $this->assertEquals(\PurplePixie\PhpDns\DNSTypes::NAME_A, $entry->getTypename());
            $this->assertEquals(\PurplePixie\PhpDns\DNSTypes::ID_A, $entry->getTypeid());
            $this->assertTrue(is_numeric($entry->getTtl()), $entry->getTtl());
        }
    }

    public function testASmartLookup() : void {

        $query = new DNSQuery("8.8.8.8");
        $queryResult = $query->smartALookup('google.com');
        $this->assertTrue(
            $queryResult != ""
            && strpos($queryResult, '.') !== false,
            'Result: ' . $queryResult,
        );
    }

    public function testAAAALookup() : void {

        $query = new DNSQuery("8.8.8.8");
        $queryResult = $query->query('google.com', \PurplePixie\PhpDns\DNSTypes::NAME_AAAA);
        $this->assertTrue($queryResult->count() > 0);

        foreach($queryResult as $entry) {

            // TODO: What is the expected class?
            //$this->assertEquals("1", $entry->getClass());

            $this->assertTrue(
                $entry->getData() != ''
                && strpos($entry->getData(), ':') !== false,
                'Result: ' . $entry->getData(),
            );

            $this->assertEquals('google.com', $entry->getDomain());

            $this->assertTrue(is_array($entry->getExtras()));
            // TODO: Test extra array

            $this->assertTrue( strpos($entry->getString(),'google.com has IPv6 address ') === 0, 'Result: ' . $entry->getString());
            $this->assertEquals(\PurplePixie\PhpDns\DNSTypes::NAME_AAAA, $entry->getTypename());
            $this->assertEquals(\PurplePixie\PhpDns\DNSTypes::ID_AAAA, $entry->getTypeid());
            $this->assertTrue(is_numeric($entry->getTtl()), $entry->getTtl());
        }
    }
}
