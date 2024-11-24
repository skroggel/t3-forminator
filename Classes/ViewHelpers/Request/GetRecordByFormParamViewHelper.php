<?php
declare(strict_types=1);
namespace Madj2k\Forminator\ViewHelpers\Request;

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
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class GetRecordByFormParamViewHelper
 *
 * @author Steffen Kroggel <developer@steffenkroggel.de>
 * @copyright Steffen Kroggel <developer@steffenkroggel.de>
 * @package Madj2k\Forminator
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
final class GetRecordByFormParamViewHelper extends AbstractViewHelper
{

	/**
	 * Initialize arguments
	 *
	 * @return void
	 */
	public function initializeArguments(): void
	{
		parent::initializeArguments();
        $this->registerArgument('formIdentifier', 'string', 'The form identifier.', true);
        $this->registerArgument('param', 'string', 'The parameter name to get from the request.', true);
        $this->registerArgument('table', 'string', 'The table to fetch the data from.', true);
    }


    /**
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param \TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface $renderingContext
     * @return mixed
     * @throws \Doctrine\DBAL\Exception
     */
	public static function renderStatic(
		array $arguments,
		\Closure $renderChildrenClosure,
		RenderingContextInterface $renderingContext
	): array {

        /** @var string $param */
        $param = $arguments['param'];

        /** @var string $param */
        $formIdentifier = $arguments['formIdentifier'];

        /** @var string $param */
        $table = $arguments['table'];

        /** @var \Psr\Http\Message\ServerRequestInterface $request */
        $request = $renderingContext->getRequest();

        if ($params = $request->getParsedBody()) {
            if (!empty($params['tx_form_formframework'][$formIdentifier][$param])) {

                if ($uid = (int) $params['tx_form_formframework'][$formIdentifier][$param]) {
                    return self::findByUid($uid, $table);
                }
            }
        }

        return [];
	}


    /**
     * Returns dataset by uid
     *
     * @param int $uid
     * @param string $table
     * @return array
     * @throws \Doctrine\DBAL\Exception
     */
    protected static function findByUid(int $uid, string $table) : array
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable($table);

        $queryBuilder->getRestrictions()
            ->removeByType(PageIdListRestriction::class);

        $statement = $queryBuilder
            ->select('*')
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
