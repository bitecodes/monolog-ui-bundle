<?php

namespace BiteCodes\MonologUIBundle\Filter;

use Doctrine\DBAL\Query\QueryBuilder;
use Pagerfanta\Adapter\DoctrineDbalAdapter;
use Pagerfanta\Pagerfanta;

class LogPagination
{
    /**
     * @param QueryBuilder $qb
     * @param integer      $page
     * @param bool         $isGrouped
     *
     * @return Pagerfanta
     */
    public static function paginate(QueryBuilder $qb, $page, $isGrouped = false)
    {
        $adapter = new DoctrineDbalAdapter($qb, function (QueryBuilder $qb) use ($isGrouped) {
            if ($isGrouped) {
                $subQuery = $qb->getSQL();
                $qb->resetQueryPart('from');
                $qb->from('(' . $subQuery . ')');
                $qb->resetQueryPart('where');
                $qb->resetQueryPart('groupBy');
                $qb->resetQueryPart('orderBy');
                $qb->select('COUNT(*)');
            } else {
                $qb->select('COUNT(l.id)');
            }
        });
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta
            ->setAllowOutOfRangePages(true)
            ->setMaxPerPage(20)
            ->setCurrentPage($page);

        return $pagerfanta;
    }
}