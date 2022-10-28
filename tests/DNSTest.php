<?php
use PHPUnit\Framework\TestCase;

use PurplePixie\PhpDns\DNSQuery;

class DNSTest extends TestCase
{
	/**
	 * @covers \PurplePixie\PhpDns\DNSQuery::_construct
	 */
	public function testConstructor(): void
	{
		$d = new DNSQuery("127.0.0.1");
		$this->assertInstanceOf(DNSQuery::class, $d);
	}

	/**
	 * @covers \PurplePixie\PhpDns\DNSQuery::_construct
	 */
	public function testConstructorNoServer(): void
	{
        $this->expectException(ArgumentCountError::class);
		$d = new DNSQuery();
		$this->assertInstanceOf(DNSQuery::class, $d);
	}
}
