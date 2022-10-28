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
    private string $typename;

    private int $typeid;

    private string $class;

    private int $ttl;

    private string $data;

    private string $domain;

    private string $string;

    private array $extras;

    public function __construct(string $typename, int $typeid, string $class, int $ttl, string $data, string $domain, string $string, array $extras)
    {
        $this->typename = $typename;
        $this->typeid = $typeid;
        $this->class = $class;
        $this->ttl = $ttl;
        $this->data = $data;
        $this->domain = $domain;
        $this->string = $string;
        $this->extras = $extras;
    }

    /**
     * @deprecated
     * @return string
     */
    public function getType(): string
    {
        return $this->typename;
    }

    /**
     * @return string
     */
    public function getTypename(): string
    {
        return $this->typename;
    }

    public function getTypeid(): int
    {
        return $this->typeid;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function getTtl(): int
    {
        return $this->ttl;
    }

    public function getData(): string
    {
        return $this->data;
    }

    public function getDomain(): string
    {
        return $this->domain;
    }

    public function getString(): string
    {
        return $this->string;
    }

    public function getExtras(): array
    {
        return $this->extras;
    }
}
