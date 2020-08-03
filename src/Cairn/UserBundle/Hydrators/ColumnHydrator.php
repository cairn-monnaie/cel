<?php

namespace Cairn\UserBundle\Hydrators;

use Doctrine\ORM\Internal\Hydration\AbstractHydrator, PDO;

class ColumnHydrator extends AbstractHydrator
{
    protected function hydrateAllData()
    {
        return $this->_stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
