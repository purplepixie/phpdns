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
	}

	/**
	 * @covers \PurplePixie\PhpDns\DNSQuery::__construct
	 */
	public function testConstructorNoServer()
	{
        $this->expectException(\InvalidArgumentException::class);
		$d = new DNSQuery();
    }
    
	/**
	 * @covers \PurplePixie\PhpDns\DNSResult::__construct
	 * @covers \PurplePixie\PhpDns\DNSQuery::query
	 * @covers \PurplePixie\PhpDns\DNSAnswer::count
	 * @covers \PurplePixie\PhpDns\DNSResult::getData
	 * @covers \PurplePixie\PhpDns\DNSResult::getType
	 * @covers \PurplePixie\PhpDns\DNSResult::getTypeId
	 * @covers \PurplePixie\PhpDns\DNSResult::getString
	 * @covers \PurplePixie\PhpDns\DNSResult::getExtras
	 */
	public function testDNSQueryAndDNSAnswer()
	{
        $dns_server = "8.8.8.8"; // Our DNS Server

        $dns_query = new DNSQuery($dns_server); // create DNS Query object - there are other options we could pass here
		$this->assertInstanceOf('PurplePixie\\PhpDns\\DNSQuery', $dns_query);

        $question = "msn.com"; // the question we will ask
        $type = "A"; // the type of response(s) we want for this question

        $result = $dns_query->query($question, $type); // do the query
		$this->assertInstanceOf('PurplePixie\\PhpDns\\DNSAnswer', $result);

        //Process Results
        $count = $result->count(); // number of results returned
        $this->assertEquals(1, $count);

        foreach ($result as $result_count) {
            // only after A records
            if ($result_count->getType() === "A") {
                $this->assertEquals(1, $result_count->getTypeId());
                $this->assertEquals('13.82.28.61', $result_count->getData());
                $this->assertEquals('msn.com has IPv4 address 13.82.28.61', $result_count->getString());
                $this->assertEquals(1, count($result_count->getExtras()));
            }
        }
    }

	/**
	 * @covers \PurplePixie\PhpDns\DNSQuery::setError
	 * @covers \PurplePixie\PhpDns\DNSQuery::hasError
	 * @covers \PurplePixie\PhpDns\DNSQuery::getLastError
	 */
	public function testDNSQueryAndDNSAnswerErrorServer()
	{
        $dns_server = "127.0.0.1"; // Our DNS Server

        $dns_query = new DNSQuery($dns_server);
        $question = "msn.com";
        $type = "A";

        // Trap Errors
		try {
        	$result = $dns_query->query($question, $type); // do the query
		} catch(\Exception $e) {
			$this->assertEquals('Failed to read data buffer', $dns_query->getLastError());
			$this->assertTrue($dns_query->hasError());
		}
	}

	/**
	 * @covers \PurplePixie\PhpDns\DNSTypes::getByName
	 */
	public function testDNSQueryAndDNSAnswerErrorType()
	{
        $dns_server = "1.1.1.1"; // Our DNS Server

        $dns_query = new DNSQuery($dns_server);
        $question = "msn.com";
        $type = "BAD";

        // Trap Errors
		try {
        	$result = $dns_query->query($question, $type); // do the query
		} catch(\Exception $e) {
			$this->assertEquals('Invalid Query Type BAD', $dns_query->getLastError());
		}
	}

	public function testDNSQueryAndDNSAnswerErrorOpen()
	{
        $dns_server = "tcp:://127.1.1.1"; // Our DNS Server

        $dns_query = new DNSQuery($dns_server, 53, 5, false);
        $question = "msn.com";
        $type = "A";

        // Trap Errors
		try {
        	$result = $dns_query->query($question, $type); // do the query
		} catch(\Exception $e) {
			$this->assertEquals('Failed to Open Socket', $dns_query->getLastError());
		}
	}
}