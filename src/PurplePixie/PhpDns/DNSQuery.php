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
    private $port;

    /**
     * @var int
     */
    private $timeout; // default set in constructor

    /**
     * @var bool
     */
    private $udp;

    /**
     * @var bool
     */
    private $debug;

    /**
     * @var bool
     */
    private $binarydebug = false;

    /**
     * @var DNSTypes
     */
    private $types;

    /**
     * @var string
     */
    private $rawbuffer = '';

    /**
     * @var string
     */
    private $rawheader = '';

    /**
     * @var string
     */
    private $rawresponse = '';

    /**
     * @var array
     */
    private $header;

    /**
     * @var int
     */
    private $responsecounter = 0;

    /**
     * @var DNSAnswer
     */
    private $lastnameservers;

    /**
     * @var DNSAnswer
     */
    private $lastadditional;

    /**
     * @var bool
     */
    private $error = false;

    /**
     * @var string
     */
    private $lasterror = '';

    /**
     * @param string $server
     * @param int $port
     * @param int $timeout
     * @param bool $udp
     * @param bool $debug
     * @param bool $binarydebug
     */
    public function __construct($server, $port = 53, $timeout = 60, $udp = true, $debug = false, $binarydebug = false)
    {
        $this->server = $server;
        $this->port = $port;
        $this->timeout = $timeout;
        $this->udp = $udp;
        $this->debug = $debug;
        $this->binarydebug = $binarydebug;

        $this->types = new DNSTypes();

        $this->debug('DNSQuery Class Initialised');
    }

    /**
     * @param int $count
     * @param string $offset
     * @return string
     */
    private function readResponse($count = 1, $offset = '')
    {
        if ($offset == '') {
            // no offset so use and increment the ongoing counter
            $return = substr($this->rawbuffer, $this->responsecounter, $count);
            $this->responsecounter += $count;
        } else {
            $return = substr($this->rawbuffer, $offset, $count);
        }

        return $return;
    }

    /**
     * @param int $offset
     * @param int $counter
     * @return array
     */
    private function readDomainLabels($offset, &$counter = 0)
    {
        $labels = array();
        $startoffset = $offset;
        $return = false;

        while (!$return) {
            $label_len = ord($this->readResponse(1, $offset++));

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

        $counter = $offset - $startoffset;

        return $labels;
    }

    /**
     * @return string
     */
    private function readDomainLabel()
    {
        $count = 0;
        $labels = $this->readDomainLabels($this->responsecounter, $count);
        $domain = implode('.', $labels);

        $this->responsecounter += $count;

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
    function debugBinary($data)
    {
        if (!$this->binarydebug) {
            return;
        }

        for ($a = 0; $a < strlen($data); $a++) {
            $hex = bin2hex($data[$a]);
            $dec = hexdec($hex);

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
     */
    private function setError($text)
    {
        $this->error = true;
        $this->lasterror = $text;

        $this->debug('Error: ' . $text);
    }

    private function clearError()
    {
        $this->error = false;
        $this->lasterror = '';
    }

    /**
     * @return array
     */
    private function readRecord()
    {
        // First the pesky domain names - maybe not so pesky though I suppose

        $domain = $this->readDomainLabel();

        $ans_header_bin = $this->readResponse(10); // 10 byte header
        $ans_header = unpack('ntype/nclass/Nttl/nlength', $ans_header_bin);

        $this->debug(
            'Record Type ' . $ans_header['type'] . ' Class ' . $ans_header['class'] .
            ' TTL ' . $ans_header['ttl'] . ' Length ' . $ans_header['length']
        );

        $typeid = $this->types->getById($ans_header['type']);
        $extras = array();
        $data = '';
        $string = '';

        switch ($typeid) {
            case 'A':
                $ipbin = $this->readResponse(4);
                $ip = inet_ntop($ipbin);
                $data = $ip;
                $extras['ipbin'] = $ipbin;
                $string = $domain . ' has IPv4 address ' . $ip;
                break;

            case 'AAAA':
                $ipbin = $this->readResponse(16);
                $ip = inet_ntop($ipbin);
                $data = $ip;
                $extras['ipbin'] = $ipbin;
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
                $test = unpack('nflags/cprotocol/calgo', $stuff);
                $extras['flags'] = $test['flags'];
                $extras['protocol'] = $test['protocol'];
                $extras['algorithm'] = $test['algo'];

                $data = base64_encode($this->readResponse($ans_header['length'] - 4));
                $string = $domain . ' KEY ' . $data;
                break;

            case "NSEC":
                $data=$this->ReadDomainLabel();
                $string=$domain." points to ".$data;
                break;

            case 'MX':
                $prefs = $this->readResponse(2);
                $prefs = unpack('nlevel', $prefs);
                $extras['level'] = $prefs['level'];
                $data = $this->readDomainLabel();
                $string = $domain . ' mailserver ' . $data . ' (pri=' . $extras['level'] . ')';
                break;

            case 'NS':
                $nameserver = $this->readDomainLabel();
                $data = $nameserver;
                $string = $domain . ' nameserver ' . $nameserver;
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
                $extras = unpack('Nserial/Nrefresh/Nretry/Nexpiry/Nminttl', $buffer); // butfix to NNNNN from nnNNN for 1.01
                $dot = strpos($responsible, '.');
                if($dot !== false){
                    $responsible[$dot] = '@';
                }
                $extras['responsible'] = $responsible;
                $string = $domain . ' SOA ' . $data . ' Serial ' . $extras['serial'];
                break;

            case 'SRV':
                $prefs = $this->readResponse(6);
                $prefs = unpack('npriority/nweight/nport', $prefs);
                $extras['priority'] = $prefs['priority'];
                $extras['weight'] = $prefs['weight'];
                $extras['port'] = $prefs['port'];
                $data = $this->readDomainLabel();
                $string = $domain . ' SRV ' . $data . ':' . $extras['port'] . ' (pri=' . $extras['priority'] . ', weight=' . $extras['weight'] . ')';
                break;

            case 'TXT':
            case 'SPF':
                $data = '';

                for ($string_count = 0; strlen($data) + (1 + $string_count) < $ans_header['length']; $string_count++) {
                    $string_length = ord($this->readResponse(1));
                    $data .= $this->readResponse($string_length);
                }

                $string = $domain . ' TEXT "' . $data . '" (in ' . $string_count . ' strings)';
                break;

            case "NAPTR":
                $buffer = $this->ReadResponse(4);
                $extras = unpack("norder/npreference",$buffer);
                $addonitial = $this->ReadDomainLabel();
                $data = $this->ReadDomainLabel();
                $extras['service']=$addonitial;
                $string = $domain." NAPTR ".$data;
                break;
        }

        return array(
            'header' => $ans_header,
            'typeid' => $typeid,
            'data' => $data,
            'domain' => $domain,
            'string' => $string,
            'extras' => $extras
        );
    }

    /**
     * @param string $question
     * @param string $type
     * @return DNSAnswer|false
     */
    public function query($question, $type = 'A')
    {
        $this->clearError();

        $typeid = $this->types->getByName($type);

        if ($typeid === false) {
            $this->setError('Invalid Query Type ' . $type);
            return false;
        }

        if ($this->udp) {
            $host = 'udp://' . $this->server;
        } else {
            $host = $this->server;
        }

        $errno = 0;
        $errstr = '';

        if (!$socket = fsockopen($host, $this->port, $errno, $errstr, $this->timeout)) {
            $this->setError('Failed to Open Socket');
            return false;
        }

        // handles timeout on stream read set using timeout as well
        stream_set_timeout($socket, $this->timeout);

        // Split Into Labels
        if (preg_match('/[a-z|A-Z]/', $question) == 0 && $question != '.') { // IP Address
            // reverse ARPA format
            $labels = array_reverse(explode('.', $question));
            $labels[] = 'IN-ADDR';
            $labels[] = 'ARPA';
        } else {
            if ($question == '.') {
                $labels = array('');
            } else { // hostname
                $labels = explode('.', $question);
            }
        }

        $question_binary = '';

        foreach ($labels as $label) {
            if ($label != '') {
                $size = strlen($label);
                $question_binary .= pack('C', $size); // size byte first
                $question_binary .= $label; // then the label
            } else {
                //$size = 0;
                //$question_binary.=pack('C',$size);
                //$question_binary.=pack('C',$labels[$a]);
            }
        }

        $question_binary .= pack('C', 0); // end it off

        $this->debug('Question: ' . $question . ' (type=' . $type . '/' . $typeid . ')');

        $id = rand(1, 255) | (rand(0, 255) << 8);    // generate the ID

        // Set standard codes and flags
        $flags = 0x0100 & 0x0300; // recursion & queryspecmask
        $opcode = 0x0000; // opcode

        // Build the header
        $header = '';
        $header .= pack('n', $id);
        $header .= pack('n', $opcode | $flags);
        $header .= pack('nnnn', 1, 0, 0, 0);
        $header .= $question_binary;
        $header .= pack('n', $typeid);
        $header .= pack('n', 0x0001); // internet class
        $headersize = strlen($header);
        $headersizebin = pack('n', $headersize);

        $this->debug('Header Length: ' . $headersize . ' Bytes');
        $this->debugBinary($header);

        if (($this->udp) && ($headersize >= 512)) {
            $this->setError('Question too big for UDP (' . $headersize . ' bytes)');
            fclose($socket);
            return false;
        }

        if ($this->udp) { // UDP method
            if (!fwrite($socket, $header, $headersize)) {
                $this->setError('Failed to write question to socket');
                fclose($socket);
                return false;
            }

            if (!$this->rawbuffer = fread($socket, 4096)) { // read until the end with UDP
                $this->setError('Failed to read data buffer');
                fclose($socket);
                return false;
            }
        } else { // TCP
            // write the socket
            if (!fwrite($socket, $headersizebin)) {
                $this->setError('Failed to write question length to TCP socket');
                fclose($socket);
                return false;
            }

            if (!fwrite($socket, $header, $headersize)) {
                $this->setError('Failed to write question to TCP socket');
                fclose($socket);
                return false;
            }

            if (!$returnsize = fread($socket, 2)) {
                $this->setError('Failed to read size from TCP socket');
                fclose($socket);
                return false;
            }
            
            $tmplen = unpack('nlength', $returnsize);
            $datasize = $tmplen['length'];

            $this->debug('TCP Stream Length Limit ' . $datasize);

            if (!$this->rawbuffer = fread($socket, $datasize)) {
                $this->setError('Failed to read data buffer');
                fclose($socket);
                return false;
            }
        }

        fclose($socket);

        $buffersize = strlen($this->rawbuffer);

        $this->debug('Read Buffer Size ' . $buffersize);

        if ($buffersize < 12) {
            $this->setError('Return Buffer too Small');
            return false;
        }

        $this->rawheader = substr($this->rawbuffer, 0, 12); // first 12 bytes is the header
        $this->rawresponse = substr($this->rawbuffer, 12); // after that the response

        $this->responsecounter = 12; // start parsing response counter from 12 - no longer using response so can do pointers

        $this->debugBinary($this->rawbuffer);

        $this->header = unpack('nid/nspec/nqdcount/nancount/nnscount/narcount', $this->rawheader);

        $id = $this->header['id'];

        $rcode = $this->header['spec'] & 15;
        $z = ($this->header['spec'] >> 4) & 7;
        $ra = ($this->header['spec'] >> 7) & 1;
        $rd = ($this->header['spec'] >> 8) & 1;
        $tc = ($this->header['spec'] >> 9) & 1;
        $aa = ($this->header['spec'] >> 10) & 1;
        $opcode = ($this->header['spec'] >> 11) & 15;
        $type = ($this->header['spec'] >> 15) & 1;

        $this->debug("ID=$id, Type=$type, OPCODE=$opcode, AA=$aa, TC=$tc, RD=$rd, RA=$ra, RCODE=$rcode");

        if ($tc == 1 && $this->udp) { // Truncation detected
            $this->setError('Response too big for UDP, retry with TCP');
            return false;
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
                    $c = hexdec(bin2hex($this->readResponse(1)));
                }

                $this->readResponse(4);
            }
        }

        // New Functional Method
        for ($a = 0; $a < $this->header['ancount']; $a++) {
            $record = $this->readRecord();

            $dns_answer->addResult(
                new DNSResult(
                    $record['header']['type'], $record['typeid'], $record['header']['class'], $record['header']['ttl'],
                    $record['data'], $record['domain'], $record['string'], $record['extras']
                )
            );
        }

        $this->lastnameservers = new DNSAnswer();

        for ($a = 0; $a < $this->header['nscount']; $a++) {
            $record = $this->readRecord();

            $this->lastnameservers->addResult(
                new DNSResult(
                    $record['header']['type'], $record['typeid'], $record['header']['class'], $record['header']['ttl'],
                    $record['data'], $record['domain'], $record['string'], $record['extras']
                )
            );
        }

        $this->lastadditional = new DNSAnswer();

        for ($a = 0; $a < $this->header['arcount']; $a++) {
            $record = $this->readRecord();

            $this->lastadditional->addResult(
                new DNSResult(
                    $record['header']['type'], $record['typeid'], $record['header']['class'], $record['header']['ttl'],
                    $record['data'], $record['domain'], $record['string'], $record['extras']
                )
            );
        }

        return $dns_answer;
    }

    /**
     * @param string $hostname
     * @param int $depth
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
            if ($record->getTypeid() == 'A') {
                $best_answer = $record;
                break;
            }

            // alias
            if ($record->getTypeid() == 'CNAME') {
                $best_answer = $record;
                // and keep going
            }
        }

        if (!isset($best_answer)) {
            return '';
        }

        if ($best_answer->getTypeid() == 'A') {
            return $best_answer->getData();
        } // got an IP ok

        if ($best_answer->getTypeid() != 'CNAME') {
            return '';
        } // shouldn't ever happen

        $newtarget = $best_answer->getData(); // this is what we now need to resolve

        // First is it in the additional section
        foreach ($this->lastadditional as $result) {
            if (($result->getDomain() == $hostname) && ($result->getTypeid() == 'A')) {
                return $result->getData();
            }
        }

        // Not in the results

        return $this->smartALookup($newtarget, $depth + 1);
    }

    /**
     * @return DNSAnswer
     */
    public function getLastnameservers()
    {
        return $this->lastnameservers;
    }

    /**
     * @return DNSAnswer
     */
    public function getLastadditional()
    {
        return $this->lastadditional;
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
    public function getLasterror()
    {
        return $this->lasterror;
    }
}
