<?php

namespace BiteCodes\MonologUIBundle\Filter;

use BiteCodes\DoctrineFilter\FilterBuilder;
use BiteCodes\DoctrineFilter\FilterInterface;
use BiteCodes\DoctrineFilter\Type\EqualFilterType;
use Doctrine\DBAL\Query\QueryBuilder;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LogFilter
{
    protected static $defined = [
        'page',
        'message',
        'channels',
        'levels',
        'date_from',
        'date_to',
    ];

    protected static $defaults = [
        'page' => 1,
    ];

    /**
     * @param QueryBuilder $qb
     * @param array        $search
     *
     * @return QueryBuilder
     */
    public static function filter(QueryBuilder $qb, array $search)
    {
        $search = self::sanitize($search);

        if (!empty($search['message'])) {
            $qb->andWhere('l.message LIKE :message')
                ->setParameter('message', '%' . str_replace(' ', '%', $search['message']) . '%');
        }

        if (!empty($search['levels'])) {
            $qb->andWhere(
                $qb->expr()->in('l.level', preg_split('/,/', $search['levels']))
            );
        }

        if (!empty($search['channels'])) {
            $qb->andWhere(
                $qb->expr()->in('l.channel', array_map(function ($c) {
                    return '"' . $c . '"';
                }, preg_split('/,/', $search['channels'])))
            );
        }

        if (!empty($search['date_from'])) {
            $qb->andWhere('l.datetime >= :date_from')
                ->setParameter('date_from', $search['date_from']);
        }

        if (!empty($search['date_to'])) {
            $qb->andWhere('l.datetime <= :date_to')
                ->setParameter('date_to', $search['date_to']);
        }

        return $qb;
    }

    protected static function sanitize($params)
    {
        $sanitized = array_intersect_key($params, array_flip(self::$defined));

        return (new OptionsResolver())
            ->setDefined(self::$defined)
            ->setDefaults(self::$defaults)
            ->resolve($sanitized);
    }
}
