<?php

namespace PurplePixie\PhpDns;

/**
 * This file is the PurplePixie PHP DNS Result Class
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
class DNSResult
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var int
     */
    private $typeid;

    /**
     * @var string
     */
    private $class;

    /**
     * @var int
     */
    private $ttl;

    /**
     * @var string
     */
    private $data;

    /**
     * @var string
     */
    private $domain;

    /**
     * @var string
     */
    private $string;

    /**
     * @var array string
     */
    private $extras = array();

    /**
     * @param string $type
     * @param int $typeid
     * @param string $class
     * @param int $ttl
     * @param string $data
     * @param string $domain
     * @param string $string
     * @param array $extras
     */
    public function __construct($type, $typeid, $class, $ttl, $data, $domain, $string, array $extras)
    {
        $this->type = $type;
        $this->typeid = $typeid;
        $this->class = $class;
        $this->ttl = $ttl;
        $this->data = $data;
        $this->domain = $domain;
        $this->string = $string;
        $this->extras = $extras;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return int
     */
    public function getTypeid()
    {
        return $this->typeid;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @return int
     */
    public function getTtl()
    {
        return $this->ttl;
    }

    /**
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @return string
     */
    public function getString()
    {
        return $this->string;
    }

    /**
     * @return array
     */
    public function getExtras()
    {
        return $this->extras;
    }
}