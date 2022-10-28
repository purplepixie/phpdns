<?php

/**
 * Copyright (C) 2022, Fabian Bett / Bett Ingenieure GmbH
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

namespace PurplePixie\PhpDns\Exceptions;

class InvalidQueryTypeName extends \Exception {

    public function __construct(string $typeName) {
        parent::__construct('Invalid query type name: ' . $typeName);
    }
}