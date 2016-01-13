<?php

namespace PurplePixie\PhpDns;

/**
 * This file is the PurplePixie PHP DNS Answer Class
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
class DNSAnswer
{
    /**
     * @var int
     */
    protected $count = 0;

    /**
     * @var DNSResult[]
     */
    protected $results = array();

    /**
     * @param $type
     * @param $typeid
     * @param $class
     * @param $ttl
     * @param $data
     * @param string $domain
     * @param string $string
     * @param array $extras
     * @return int
     */
    public function AddResult($type, $typeid, $class, $ttl, $data, $domain = "", $string = "", $extras = array())
    {
        $this->results[$this->count] = new DNSResult();
        $this->results[$this->count]->type = $type;
        $this->results[$this->count]->typeid = $typeid;
        $this->results[$this->count]->class = $class;
        $this->results[$this->count]->ttl = $ttl;
        $this->results[$this->count]->data = $data;
        $this->results[$this->count]->domain = $domain;
        $this->results[$this->count]->string = $string;
        $this->results[$this->count]->extras = $extras;
        $this->count++;
        return ($this->count - 1);
    }

    /**
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * @param int $index
     * @return DNSResult
     */
    public function getResult($index)
    {
        return $this->results[$index];
    }
}