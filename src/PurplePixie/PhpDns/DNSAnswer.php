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
class DNSAnswer implements \Countable, \Iterator
{
    /**
     * @var DNSResult[]
     */
    private array $results = array();

    public function addResult(DNSResult $result)
    {
        $this->results[] = $result;
    }

    /**
     * Countable
     */
    public function count(): int
    {
        return count($this->results);
    }

    /**
     * Iterator
     */
    public function current(): DNSResult
    {
        return current($this->results);
    }

    /**
     * Iterator
     */
    public function next(): void
    {
        next($this->results);
    }

    /**
     * Iterator
     */
    public function key(): int
    {
        return key($this->results);
    }

    /**
     * Iterator
     */
    public function valid(): bool
    {
        return key($this->results) !== null;
    }

    /**
     * Iterator
     */
    public function rewind(): void
    {
        reset($this->results);
    }
}
