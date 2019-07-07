<?php

namespace PurplePixie\PhpDns;

/**
 * This file is the PurplePixie PHP DNS Query Class
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
class DNSQuery
{
    /**
     * @var string
     */
    private $server = '';

    /**
     * @var int
     */
    private $port = 0;

    /**
     * @var int
     */
    private $timeout = 0; // default set in constructor

    /**
     * @var bool
     */
    private $udp = false;

    /**
     * @var bool
     */
    private $debug = false;

    /**
     * @var bool
     */
    private $binaryDebug = false;

    /**
     * @var DNSTypes
     */
    private $types;

    /**
     * @var string
     */
    private $rawBuffer = '';

    /**
     * @var string
     */
    private $rawHeader = '';

    /**
     * @var string
     */
    private $rawResponse = '';

    /**
     * @var array
     */
    private $header = [];

    /**
     * @var int
     */
    private $responseCounter = 0;

    /**
     * @var DNSAnswer
     */
    private $lastNameservers;

    /**
     * @var DNSAnswer
     */
    private $lastAdditional;

    /**
     * @var bool
     */
    private $error = false;

    /**
     * @var string
     */
    private $lastError = '';

    /**
     * @param string $server
     * @param int    $port
     * @param int    $timeout
     * @param bool   $udp
     * @param bool   $debug
     * @param bool   $binaryDebug
     */
    public function __construct($server = null, $port = 53, $timeout = 60, $udp = true, $debug = false, $binaryDebug = false)
    {
        if (empty($server))
            throw new \InvalidArgumentException('Missing server argument');

        $this->server = $server;
        $this->port = $port;
        $this->timeout = $timeout;
        $this->udp = $udp;
        $this->debug = $debug;
        $this->binaryDebug = $binaryDebug;

        $this->types = new DNSTypes();

        $this->debug('DNSQuery Class Initialised');
    }

    /**
     * @param string $question
     * @param string $type
     *
     * @return DNSAnswer
     * @throws \Exception
     */
    public function query($question, $type = 'A'): DNSAnswer
    {
        $this->clearError();

        $typeId = $this->types->getByName($type);

        if ($typeId === false) {
            $this->setError('Invalid Query Type ' . $type);
            throw new \Exception('Invalid Query Type ' . $type);
        }

        if ($this->udp) {
            $host = 'udp://' . $this->server;
        } else {
            $host = $this->server;
        }

        $errno = 0;
        $errstr = '';

        if (!$socket = @\fsockopen($host, $this->port, $errno, $errstr, $this->timeout)) {
            $this->setError('Failed to Open Socket');
            throw new \Exception('Failed to Open Socket');
        }

        // handles timeout on stream read set using timeout as well
        \stream_set_timeout($socket, $this->timeout);

        // Split Into Labels
        if (\preg_match('/[a-z|A-Z]/', $question) == 0 && $question != '.') { // IP Address
            // reverse ARPA format
            $labels = \array_reverse(\explode('.', $question));
            $labels[] = 'IN-ADDR';
            $labels[] = 'ARPA';
        } else {
            if ($question == '.') {
                $labels = [''];
            } else { // hostname
                $labels = \explode('.', $question);
            }
        }

        $question_binary = '';

        foreach ($labels as $label) {
            if ($label != '') {
                $size = \strlen($label);
                $question_binary .= \pack('C', $size); // size byte first
                $question_binary .= $label; // then the label
            }
        }

        $question_binary .= pack('C', 0); // end it off

        $this->debug('Question: ' . $question . ' (type=' . $type . '/' . $typeId . ')');

        $id = \rand(1, 255) | (\rand(0, 255) << 8);    // generate the ID

        // Set standard codes and flags
        $flags = 0x0100 & 0x0300; // recursion & queryspecmask
        $opcode = 0x0000; // opcode

        // Build the header
        $header = '';
        $header .= \pack('n', $id);
        $header .= \pack('n', $opcode | $flags);
        $header .= \pack('nnnn', 1, 0, 0, 0);
        $header .= $question_binary;
        $header .= \pack('n', $typeId);
        $header .= \pack('n', 0x0001); // internet class
        $headersize = \strlen($header);
        $headersizebin = \pack('n', $headersize);

        $this->debug('Header Length: ' . $headersize . ' Bytes');
        $this->debugBinary($header);

        if (($this->udp) && ($headersize >= 512)) {
            $this->setError('Question too big for UDP (' . $headersize . ' bytes)');
            \fclose($socket);
            throw new \Exception('Question too big for UDP (' . $headersize . ' bytes)');
        }

        if ($this->udp) { // UDP method
            if (!\fwrite($socket, $header, $headersize)) {
                $this->setError('Failed to write question to socket');
                \fclose($socket);
                throw new \Exception('Failed to write question to socket');
            }

            if (!$this->rawBuffer = \fread($socket, 4096)) { // read until the end with UDP
                $this->setError('Failed to read data buffer');
                \fclose($socket);
                throw new \Exception('Failed to read data buffer');
            }
        } else { // TCP
            // write the socket
            if (!\fwrite($socket, $headersizebin)) {
                $this->setError('Failed to write question length to TCP socket');
                \fclose($socket);
                throw new \Exception('Failed to write question length to TCP socket');
            }

            if (!\fwrite($socket, $header, $headersize)) {
                $this->setError('Failed to write question to TCP socket');
                \fclose($socket);
                throw new \Exception('Failed to write question to TCP socket');
            }

            if (!$returnsize = \fread($socket, 2)) {
                $this->setError('Failed to read size from TCP socket');
                \fclose($socket);
                throw new \Exception('Failed to read size from TCP socket');
            }

            $tmplen = \unpack('nlength', $returnsize);
            $datasize = $tmplen['length'];

            $this->debug('TCP Stream Length Limit ' . $datasize);

            if (!$this->rawBuffer = \fread($socket, $datasize)) {
                $this->setError('Failed to read data buffer');
                \fclose($socket);
                throw new \Exception('Failed to read data buffer');
            }
        }

        \fclose($socket);

        $bufferSize = \strlen($this->rawBuffer);

        $this->debug('Read Buffer Size ' . $bufferSize);

        if ($bufferSize < 12) {
            $this->setError('Return Buffer too Small');
            throw new \Exception('Return Buffer too Small');
        }

        $this->rawHeader = \substr($this->rawBuffer, 0, 12); // first 12 bytes is the header
        $this->rawResponse = \substr($this->rawBuffer, 12); // after that the response

        $this->responseCounter = 12; // start parsing response counter from 12 - no longer using response so can do pointers

        $this->debugBinary($this->rawBuffer);

        $this->header = \unpack('nid/nspec/nqdcount/nancount/nnscount/narcount', $this->rawHeader);

        $id = $this->header['id'];

        $rcode = $this->header['spec'] & 15;
        $ra = ($this->header['spec'] >> 7) & 1;
        $rd = ($this->header['spec'] >> 8) & 1;
        $tc = ($this->header['spec'] >> 9) & 1;
        $aa = ($this->header['spec'] >> 10) & 1;
        $opcode = ($this->header['spec'] >> 11) & 15;
        $type = ($this->header['spec'] >> 15) & 1;

        $this->debug("ID=$id, Type=$type, OPCODE=$opcode, AA=$aa, TC=$tc, RD=$rd, RA=$ra, RCODE=$rcode");

        if ($tc == 1 && $this->udp) { // Truncation detected
            $this->setError('Response too big for UDP, retry with TCP');
            throw new \Exception('Response too big for UDP, retry with TCP');
        }

        $answers = $this->header['ancount'];

        $this->debug('Query Returned ' . $answers . ' Answers');

        $dns_answer = new DNSAnswer();

        // Deal with the header question data
        if ($this->header['qdcount'] > 0) {
            $this->debug('Found ' . $this->header['qdcount'] . ' Questions');

            for ($a = 0; $a < $this->header['qdcount']; $a++) {
                $c = 1;

                while ($c != 0) {
                    $c = \hexdec(\bin2hex($this->readResponse(1)));
                }

                $this->readResponse(4);
            }
        }

        // New Functional Method
        for ($a = 0; $a < $this->header['ancount']; $a++) {
            $record = $this->readRecord();

            $dns_answer->addResult(new DNSResult($record['header']['type'], $record['typeId'],
                $record['header']['class'], $record['header']['ttl'], $record['data'], $record['domain'],
                $record['string'], $record['extras']));
        }

        $this->lastNameservers = new DNSAnswer();

        for ($a = 0; $a < $this->header['nscount']; $a++) {
            $record = $this->readRecord();

            $this->lastNameservers->addResult(new DNSResult($record['header']['type'], $record['typeId'],
                $record['header']['class'], $record['header']['ttl'], $record['data'], $record['domain'],
                $record['string'], $record['extras']));
        }

        $this->lastAdditional = new DNSAnswer();

        for ($a = 0; $a < $this->header['arcount']; $a++) {
            $record = $this->readRecord();

            $this->lastAdditional->addResult(new DNSResult($record['header']['type'], $record['typeId'],
                $record['header']['class'], $record['header']['ttl'], $record['data'], $record['domain'],
                $record['string'], $record['extras']));
        }

        return $dns_answer;
    }

    /**
     * @param string $hostname
     * @param int    $depth
     *
     * @return string
     */
    public function smartALookup($hostname, $depth = 0)
    {
        $this->debug('SmartALookup for ' . $hostname . ' depth ' . $depth);

        // avoid recursive lookups
        if ($depth > 5) {
            return '';
        }

        // The SmartALookup function will resolve CNAMES using the additional properties if possible
        $answer = $this->query($hostname, 'A');

        // failed totally
        if ($answer === false) {
            return '';
        }

        // no records at all returned
        if (count($answer) === 0) {
            return '';
        }

        foreach ($answer as $record) {
            // found it
            if ($record->getTypeId() == 'A') {
                $best_answer = $record;
                break;
            }

            // alias
            if ($record->getTypeId() == 'CNAME') {
                $best_answer = $record;
                // and keep going
            }
        }

        if (!isset($best_answer)) {
            return '';
        }

        if ($best_answer->getTypeId() == 'A') {
            return $best_answer->getData();
        } // got an IP ok

        if ($best_answer->getTypeId() != 'CNAME') {
            return '';
        } // shouldn't ever happen

        $newTarget = $best_answer->getData(); // this is what we now need to resolve

        // First is it in the additional section
        foreach ($this->lastAdditional as $result) {
            if (($result->getDomain() == $hostname) && ($result->getTypeId() == 'A')) {
                return $result->getData();
            }
        }

        // Not in the results

        return $this->smartALookup($newTarget, $depth + 1);
    }

    /**
     * @return DNSAnswer
     */
    public function getLastNameservers()
    {
        return $this->lastNameservers;
    }

    /**
     * @return DNSAnswer
     */
    public function getLastAdditional()
    {
        return $this->lastAdditional;
    }

    /**
     * @return boolean
     */
    public function hasError()
    {
        return $this->error;
    }

    /**
     * @return string
     */
    public function getLastError()
    {
        return $this->lastError;
    }

    /**
     * @param int    $count
     * @param string $offset
     *
     * @return string
     */
    private function readResponse($count = 1, $offset = '')
    {
        if ($offset == '') {
            // no offset so use and increment the ongoing counter
            $return = \substr($this->rawBuffer, $this->responseCounter, $count);
            $this->responseCounter += $count;
        } else {
            $return = \substr($this->rawBuffer, $offset, $count);
        }

        return $return;
    }

    /**
     * @param int $offset
     * @param int $counter
     *
     * @return array
     */
    private function readDomainLabels($offset, &$counter = 0)
    {
        $labels = [];
        $startOffset = $offset;
        $return = false;

        while (!$return) {
            $label_len = \ord($this->readResponse(1, $offset++));

            if ($label_len <= 0) {
                $return = true;
                // end of data
            } else {
                if ($label_len < 64) { // uncompressed data
                    $labels[] = $this->readResponse($label_len, $offset);
                    $offset += $label_len;
                } else { // label_len >= 64 -- pointer
                    $nextitem = $this->readResponse(1, $offset++);
                    $pointer_offset = (($label_len & 0x3f) << 8) + ord($nextitem);

                    // Branch Back Upon Ourselves...
                    $this->debug('Label Offset: ' . $pointer_offset);

                    $pointer_labels = $this->readDomainLabels($pointer_offset);

                    foreach ($pointer_labels as $ptr_label) {
                        $labels[] = $ptr_label;
                    }

                    $return = true;
                }
            }
        }

        $counter = $offset - $startOffset;

        return $labels;
    }

    /**
     * @return string
     */
    private function readDomainLabel()
    {
        $count = 0;
        $labels = $this->readDomainLabels($this->responseCounter, $count);
        $domain = \implode('.', $labels);

        $this->responseCounter += $count;

        $this->debug('Label ' . $domain . ' len ' . $count);

        return $domain;
    }

    /**
     * @param string $text
     */
    private function debug($text)
    {
        if ($this->debug) {
            echo $text . "\n";
        }
    }

    /**
     * @param string $data
     */
    private function debugBinary($data)
    {
        if (!$this->binaryDebug) {
            return;
        }

        for ($a = 0; $a < \strlen($data); $a++) {
            $hex = \bin2hex($data[$a]);
            $dec = \hexdec($hex);

            echo $a;
            echo "\t";
            printf('%d', $data[$a]);
            echo "\t";
            echo '0x' . $hex;
            echo "\t";
            echo $dec;
            echo "\t";

            if (($dec > 30) && ($dec < 150)) {
                echo $data[$a];
            }

            echo "\n";
        }
    }

    /**
     * @param string $text
     *
     * @return $this
     */
    private function setError($text)
    {
        $this->error = true;
        $this->lastError = $text;

        $this->debug('Error: ' . $text);
        return $this;
    }

    /**
     * @return $this
     */
    private function clearError()
    {
        $this->error = false;
        $this->lastError = '';
        return $this;
    }

    /**
     * @return array
     */
    private function readRecord()
    {
        // First the pesky domain names - maybe not so pesky though I suppose

        $domain = $this->readDomainLabel();

        $ansHeaderBin = $this->readResponse(10); // 10 byte header
        $ansHeader = \unpack('ntype/nclass/Nttl/nlength', $ansHeaderBin);

        $this->debug('Record Type ' . $ansHeader['type'] . ' Class ' . $ansHeader['class'] . ' TTL ' . $ansHeader['ttl'] . ' Length ' . $ansHeader['length']);

        $typeId = $this->types->getById($ansHeader['type']);
        $extras = [];
        $data = '';
        $string = '';

        switch ($typeId) {
            case 'A':
                $ipBin = $this->readResponse(4);
                $ip = \inet_ntop($ipBin);
                $data = $ip;
                $extras['ipBin'] = $ipBin;
                $string = $domain . ' has IPv4 address ' . $ip;
                break;

            case 'AAAA':
                $ipBin = $this->readResponse(16);
                $ip = \inet_ntop($ipBin);
                $data = $ip;
                $extras['ipBin'] = $ipBin;
                $string = $domain . ' has IPv6 address ' . $ip;
                break;

            case 'CNAME':
                $data = $this->readDomainLabel();
                $string = $domain . ' alias of ' . $data;
                break;

            case 'DNAME':
                $data = $this->readDomainLabel();
                $string = $domain . ' alias of ' . $data;
                break;

            case 'DNSKEY':
            case 'KEY':
                $stuff = $this->readResponse(4);

                // key type test 21/02/2014 DC
                $test = \unpack('nflags/cprotocol/calgo', $stuff);
                $extras['flags'] = $test['flags'];
                $extras['protocol'] = $test['protocol'];
                $extras['algorithm'] = $test['algo'];

                $data = \base64_encode($this->readResponse($ansHeader['length'] - 4));
                $string = $domain . ' KEY ' . $data;
                break;

            case "NSEC":
                $data = $this->ReadDomainLabel();
                $string = $domain . " points to " . $data;
                break;

            case 'MX':
                $prefs = $this->readResponse(2);
                $prefs = \unpack('nlevel', $prefs);
                $extras['level'] = $prefs['level'];
                $data = $this->readDomainLabel();
                $string = $domain . ' mailserver ' . $data . ' (pri=' . $extras['level'] . ')';
                break;

            case 'NS':
                $nameServer = $this->readDomainLabel();
                $data = $nameServer;
                $string = $domain . ' nameServer ' . $nameServer;
                break;

            case 'PTR':
                $data = $this->readDomainLabel();
                $string = $domain . ' points to ' . $data;
                break;

            case 'SOA':
                // Label First
                $data = $this->readDomainLabel();
                $responsible = $this->readDomainLabel();

                $buffer = $this->readResponse(20);
                $extras = \unpack('Nserial/Nrefresh/Nretry/Nexpiry/Nminttl',
                    $buffer); // butfix to NNNNN from nnNNN for 1.01
                $dot = \strpos($responsible, '.');
                if ($dot !== false) {
                    $responsible[$dot] = '@';
                }
                $extras['responsible'] = $responsible;
                $string = $domain . ' SOA ' . $data . ' Serial ' . $extras['serial'];
                break;

            case 'SRV':
                $prefs = $this->readResponse(6);
                $prefs = \unpack('npriority/nweight/nport', $prefs);
                $extras['priority'] = $prefs['priority'];
                $extras['weight'] = $prefs['weight'];
                $extras['port'] = $prefs['port'];
                $data = $this->readDomainLabel();
                $string = $domain . ' SRV ' . $data . ':' . $extras['port'] . ' (pri=' . $extras['priority'] . ', weight=' . $extras['weight'] . ')';
                break;

            case 'TXT':
            case 'SPF':
                $data = '';

                for ($string_count = 0; \strlen($data) + (1 + $string_count) < $ansHeader['length']; $string_count++) {
                    $string_length = \ord($this->readResponse(1));
                    $data .= $this->readResponse($string_length);
                }

                $string = $domain . ' TEXT "' . $data . '" (in ' . $string_count . ' strings)';
                break;

            case "NAPTR":
                $buffer = $this->ReadResponse(4);
                $extras = \unpack("norder/npreference", $buffer);
                $addonitial = $this->ReadDomainLabel();
                $data = $this->ReadDomainLabel();
                $extras['service'] = $addonitial;
                $string = $domain . " NAPTR " . $data;
                break;
        }

        return [
            'header' => $ansHeader,
            'typeId' => $typeId,
            'data'   => $data,
            'domain' => $domain,
            'string' => $string,
            'extras' => $extras,
        ];
    }
}
