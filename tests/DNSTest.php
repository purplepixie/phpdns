<?php
use PHPUnit\Framework\TestCase;

use PurplePixie\PhpDns\DNSQuery;

class DNSTest extends TestCase
{
	protected function setUp()
	{
		set_error_handler(array($this, 'errorHandler'));
	}

	public function errorHandler($errno, $errstr, $errfile, $errline)
	{
		throw new \InvalidArgumentException(
			sprintf(
				'Missing argument. %s %s %s %s',
				$errno,
				$errstr,
				$errfile,
				$errline
				)
			);
	}

	/**
	 * @covers \PurplePixie\PhpDns\DNSQuery::_construct
	 */
	public function testConstructor()
	{
		$d = new DNSQuery("127.0.0.1");
		$this->assertInstanceOf('PurplePixie\\PhpDns\\DNSQuery', $d);
		return $d;
	}

	/**
	 * @covers \PurplePixie\PhpDns\DNSQuery::_construct
	 * @expectedException \InvalidArgumentException
	 */
	public function testConstructorNoServer()
	{
		$d = new DNSQuery();
		$this->assertInstanceOf('PurplePixie\\PhpDns\\DNSQuery', $d);
		return $d;
	}
}