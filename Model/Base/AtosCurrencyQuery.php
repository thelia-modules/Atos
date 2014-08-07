<?php

namespace Atos\Model\Base;

use \Exception;
use \PDO;
use Atos\Model\AtosCurrency as ChildAtosCurrency;
use Atos\Model\AtosCurrencyQuery as ChildAtosCurrencyQuery;
use Atos\Model\Map\AtosCurrencyTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'atos_currency' table.
 *
 *
 *
 * @method     ChildAtosCurrencyQuery orderByCode($order = Criteria::ASC) Order by the code column
 * @method     ChildAtosCurrencyQuery orderByAtosCode($order = Criteria::ASC) Order by the atos_code column
 * @method     ChildAtosCurrencyQuery orderByDecimals($order = Criteria::ASC) Order by the decimals column
 *
 * @method     ChildAtosCurrencyQuery groupByCode() Group by the code column
 * @method     ChildAtosCurrencyQuery groupByAtosCode() Group by the atos_code column
 * @method     ChildAtosCurrencyQuery groupByDecimals() Group by the decimals column
 *
 * @method     ChildAtosCurrencyQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildAtosCurrencyQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildAtosCurrencyQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildAtosCurrency findOne(ConnectionInterface $con = null) Return the first ChildAtosCurrency matching the query
 * @method     ChildAtosCurrency findOneOrCreate(ConnectionInterface $con = null) Return the first ChildAtosCurrency matching the query, or a new ChildAtosCurrency object populated from the query conditions when no match is found
 *
 * @method     ChildAtosCurrency findOneByCode(string $code) Return the first ChildAtosCurrency filtered by the code column
 * @method     ChildAtosCurrency findOneByAtosCode(int $atos_code) Return the first ChildAtosCurrency filtered by the atos_code column
 * @method     ChildAtosCurrency findOneByDecimals(int $decimals) Return the first ChildAtosCurrency filtered by the decimals column
 *
 * @method     array findByCode(string $code) Return ChildAtosCurrency objects filtered by the code column
 * @method     array findByAtosCode(int $atos_code) Return ChildAtosCurrency objects filtered by the atos_code column
 * @method     array findByDecimals(int $decimals) Return ChildAtosCurrency objects filtered by the decimals column
 *
 */
abstract class AtosCurrencyQuery extends ModelCriteria
{

    /**
     * Initializes internal state of \Atos\Model\Base\AtosCurrencyQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'thelia', $modelName = '\\Atos\\Model\\AtosCurrency', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildAtosCurrencyQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildAtosCurrencyQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \Atos\Model\AtosCurrencyQuery) {
            return $criteria;
        }
        $query = new \Atos\Model\AtosCurrencyQuery();
        if (null !== $modelAlias) {
            $query->setModelAlias($modelAlias);
        }
        if ($criteria instanceof Criteria) {
            $query->mergeWith($criteria);
        }

        return $query;
    }

    /**
     * Find object by primary key.
     * Propel uses the instance pool to skip the database if the object exists.
     * Go fast if the query is untouched.
     *
     * <code>
     * $obj  = $c->findPk(12, $con);
     * </code>
     *
     * @param mixed $key Primary key to use for the query
     * @param ConnectionInterface $con an optional connection object
     *
     * @return ChildAtosCurrency|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = AtosCurrencyTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(AtosCurrencyTableMap::DATABASE_NAME);
        }
        $this->basePreSelect($con);
        if ($this->formatter || $this->modelAlias || $this->with || $this->select
         || $this->selectColumns || $this->asColumns || $this->selectModifiers
         || $this->map || $this->having || $this->joins) {
            return $this->findPkComplex($key, $con);
        } else {
            return $this->findPkSimple($key, $con);
        }
    }

    /**
     * Find object by primary key using raw SQL to go fast.
     * Bypass doSelect() and the object formatter by using generated code.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     ConnectionInterface $con A connection object
     *
     * @return   ChildAtosCurrency A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT CODE, ATOS_CODE, DECIMALS FROM atos_currency WHERE CODE = :p0';
        try {
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':p0', $key, PDO::PARAM_STR);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), 0, $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(\PDO::FETCH_NUM)) {
            $obj = new ChildAtosCurrency();
            $obj->hydrate($row);
            AtosCurrencyTableMap::addInstanceToPool($obj, (string) $key);
        }
        $stmt->closeCursor();

        return $obj;
    }

    /**
     * Find object by primary key.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     ConnectionInterface $con A connection object
     *
     * @return ChildAtosCurrency|array|mixed the result, formatted by the current formatter
     */
    protected function findPkComplex($key, $con)
    {
        // As the query uses a PK condition, no limit(1) is necessary.
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $dataFetcher = $criteria
            ->filterByPrimaryKey($key)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->formatOne($dataFetcher);
    }

    /**
     * Find objects by primary key
     * <code>
     * $objs = $c->findPks(array(12, 56, 832), $con);
     * </code>
     * @param     array $keys Primary keys to use for the query
     * @param     ConnectionInterface $con an optional connection object
     *
     * @return ObjectCollection|array|mixed the list of results, formatted by the current formatter
     */
    public function findPks($keys, $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getReadConnection($this->getDbName());
        }
        $this->basePreSelect($con);
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $dataFetcher = $criteria
            ->filterByPrimaryKeys($keys)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->format($dataFetcher);
    }

    /**
     * Filter the query by primary key
     *
     * @param     mixed $key Primary key to use for the query
     *
     * @return ChildAtosCurrencyQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(AtosCurrencyTableMap::CODE, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildAtosCurrencyQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(AtosCurrencyTableMap::CODE, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the code column
     *
     * Example usage:
     * <code>
     * $query->filterByCode('fooValue');   // WHERE code = 'fooValue'
     * $query->filterByCode('%fooValue%'); // WHERE code LIKE '%fooValue%'
     * </code>
     *
     * @param     string $code The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildAtosCurrencyQuery The current query, for fluid interface
     */
    public function filterByCode($code = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($code)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $code)) {
                $code = str_replace('*', '%', $code);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(AtosCurrencyTableMap::CODE, $code, $comparison);
    }

    /**
     * Filter the query on the atos_code column
     *
     * Example usage:
     * <code>
     * $query->filterByAtosCode(1234); // WHERE atos_code = 1234
     * $query->filterByAtosCode(array(12, 34)); // WHERE atos_code IN (12, 34)
     * $query->filterByAtosCode(array('min' => 12)); // WHERE atos_code > 12
     * </code>
     *
     * @param     mixed $atosCode The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildAtosCurrencyQuery The current query, for fluid interface
     */
    public function filterByAtosCode($atosCode = null, $comparison = null)
    {
        if (is_array($atosCode)) {
            $useMinMax = false;
            if (isset($atosCode['min'])) {
                $this->addUsingAlias(AtosCurrencyTableMap::ATOS_CODE, $atosCode['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($atosCode['max'])) {
                $this->addUsingAlias(AtosCurrencyTableMap::ATOS_CODE, $atosCode['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(AtosCurrencyTableMap::ATOS_CODE, $atosCode, $comparison);
    }

    /**
     * Filter the query on the decimals column
     *
     * Example usage:
     * <code>
     * $query->filterByDecimals(1234); // WHERE decimals = 1234
     * $query->filterByDecimals(array(12, 34)); // WHERE decimals IN (12, 34)
     * $query->filterByDecimals(array('min' => 12)); // WHERE decimals > 12
     * </code>
     *
     * @param     mixed $decimals The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildAtosCurrencyQuery The current query, for fluid interface
     */
    public function filterByDecimals($decimals = null, $comparison = null)
    {
        if (is_array($decimals)) {
            $useMinMax = false;
            if (isset($decimals['min'])) {
                $this->addUsingAlias(AtosCurrencyTableMap::DECIMALS, $decimals['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($decimals['max'])) {
                $this->addUsingAlias(AtosCurrencyTableMap::DECIMALS, $decimals['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(AtosCurrencyTableMap::DECIMALS, $decimals, $comparison);
    }

    /**
     * Exclude object from result
     *
     * @param   ChildAtosCurrency $atosCurrency Object to remove from the list of results
     *
     * @return ChildAtosCurrencyQuery The current query, for fluid interface
     */
    public function prune($atosCurrency = null)
    {
        if ($atosCurrency) {
            $this->addUsingAlias(AtosCurrencyTableMap::CODE, $atosCurrency->getCode(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the atos_currency table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(AtosCurrencyTableMap::DATABASE_NAME);
        }
        $affectedRows = 0; // initialize var to track total num of affected rows
        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            $affectedRows += parent::doDeleteAll($con);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            AtosCurrencyTableMap::clearInstancePool();
            AtosCurrencyTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildAtosCurrency or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildAtosCurrency object or primary key or array of primary keys
     *              which is used to create the DELETE statement
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
     *                if supported by native driver or if emulated using Propel.
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     */
     public function delete(ConnectionInterface $con = null)
     {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(AtosCurrencyTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(AtosCurrencyTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();


        AtosCurrencyTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            AtosCurrencyTableMap::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

} // AtosCurrencyQuery
