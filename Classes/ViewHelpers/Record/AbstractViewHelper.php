<?php
declare(strict_types=1);
namespace Madj2k\Forminator\ViewHelpers\Record;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\PageIdListRestriction;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class AbstractViewHelper
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel <developer@steffenkroggel.de>
 * @package Madj2k\Forminator
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
abstract class AbstractViewHelper extends \TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper
{

	/**
	 * Initialize arguments
	 *
	 * @return void
	 */
	public function initializeArguments(): void
	{
		parent::initializeArguments();
        $this->registerArgument('table', 'string', 'The table to fetch the data from.', true);
        $this->registerArgument('field', 'string', 'The field to fetch.', false, '*');
    }


    /**
     * Returns dataset by uid
     *
     * @param int $uid
     * @param string $table
     * @param string $field
     * @return array
     * @throws \Doctrine\DBAL\Exception
     */
    protected static function findByUid(int $uid, string $table, string $field = '*') : array
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable($table);

        $queryBuilder->getRestrictions()
            ->removeByType(PageIdListRestriction::class);

        $statement = $queryBuilder
            ->select($field)
            ->from($table);

        $statement->where(
            $queryBuilder->expr()->eq(
                $table. '.uid',
                $queryBuilder->createNamedParameter($uid, \PDO::PARAM_INT)
            ),
        );

        $result = $statement
            ->executeQuery()
            ->fetchAssociative();

        if (! $result) {
            $result = [];
        }

        return $result;
    }

}
