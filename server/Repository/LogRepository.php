<?php

namespace BiteCodes\MonologUIBundle\Repository;

use BiteCodes\DoctrineFilter\Traits\EntityFilterTrait;
use Doctrine\ORM\EntityRepository;

class LogRepository extends EntityRepository
{
    use EntityFilterTrait;
}