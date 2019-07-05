<?php
use PHPUnit\Framework\TestCase;

use PurplePixie\PhpDns\DNSQuery;

class DNSTest extends TestCase
{
	/**
	 * @covers \PurplePixie\PhpDns\DNSQuery::__construct
	 */
	public function testConstructor()
	{
		$d = new DNSQuery("127.0.0.1");
		$this->assertInstanceOf('PurplePixie\\PhpDns\\DNSQuery', $d);
		return $d;
	}

	/**
	 * @covers \PurplePixie\PhpDns\DNSQuery::__construct
	 */
	public function testConstructorNoServer()
	{
        $this->expectException(\TypeError::class);
		$d = new DNSQuery();
		$this->assertInstanceOf('PurplePixie\\PhpDns\\DNSQuery', $d);
		return $d;
	}
}