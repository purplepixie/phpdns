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
        $this->expectException(\ArgumentCountError::class);
		$d = new DNSQuery();
    }
    
	/**
	 * @covers \PurplePixie\PhpDns\DNSQuery::query
	 * @covers \PurplePixie\PhpDns\DNSAnswer::count
	 * @covers \PurplePixie\PhpDns\DNSResult::getData
	 * @covers \PurplePixie\PhpDns\DNSResult::getTypeId
	 * @covers \PurplePixie\PhpDns\DNSResult::getString
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
        $count = $result->count(); 
        $this->assertEquals(1, $count);

        foreach ($result as $result_count) {
            // only after A records
            if ($result_count->getTypeId() == "A") {
                $this->assertEquals('13.82.28.61', $result_count->getData());
                $this->assertEquals('msn.com has IPv4 address 13.82.28.61', $result_count->getString());
            }
        }
    }

	/**
	 * @covers \PurplePixie\PhpDns\DNSQuery::query
	 */
	public function testDNSQueryAndDNSAnswerError()
	{
        $dns_server = "8.8.8.8"; // Our DNS Server

        $dns_query = new DNSQuery($dns_server);
        $question = "msn.bad";
        $type = "A";

        $result = $dns_query->query($question, $type); // do the query
		$this->assertInstanceOf('PurplePixie\\PhpDns\\DNSAnswer', $result);
        $count = $result->count(); 
        $this->assertEquals(0, $count);

        // Trap Errors
		$this->assertNotNull($dns_query->getLastError());
        $this->assertFalse($dns_query->hasError());
	}
}