<?php

namespace DMK\Mkpostman\Tests\Domain\Repository;

use DMK\Mkpostman\Domain\Model\CategoryModel;
use DMK\Mkpostman\Domain\Model\SubscriberModel;
use DMK\Mkpostman\Domain\Repository\SubscriberRepository;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Tx_Rnbase_Database_Connection as ConnectionInterfae;
use Tx_Rnbase_Domain_Model_DomainInterface;
use tx_rnbase_util_SearchGeneric as Searcher;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @property SubscriberRepository repository
 * @property \Prophecy\Prophecy\ObjectProphecy emptyModel
 */
class SubscriberRepositoryProphecyTest extends TestCase
{
    const TABLE_NAME = 'sys_category_record_mm';

    private static $connection;
    /**
     * @var \Prophecy\Prophecy\ObjectProphecy
     */
    private $searcher;

    protected function setUp()
    {
        self::$connection = $this->prophesize(ConnectionInterfae::class);

        GeneralUtility::setSingletonInstance(ConnectionInterfae::class, self::$connection->reveal());

        $this->repository = new SubscriberRepository();

        $this->emptyModel = $this->prophesize(SubscriberModel::class);
        $this->emptyModel->getTableName()->willReturn('foo');

        $this->emptyModel = $this->emptyModel->reveal();

        GeneralUtility::addInstance(SubscriberModel::class, $this->emptyModel);
        GeneralUtility::addInstance(SubscriberModel::class, $this->emptyModel);

        $this->searcher = $this->prophesize(Searcher::class);
        GeneralUtility::addInstance(Searcher::class, $this->searcher->reveal());
    }

    protected function tearDown()
    {
        unset($this->searcher);
        GeneralUtility::purgeInstances();
    }

    public function testAddToCategories()
    {
        self::$connection->doDelete('sys_category_record_mm', 'uid_foreign = 1')
            ->shouldBeCalled();

        self::$connection->doInsert(self::TABLE_NAME, [
            'uid_local' => 1,
            'uid_foreign' => 1,
            'tablenames' => 'tx_mkpostman_subscribers',
            'fieldname' => 'categories',
        ])->shouldBeCalled();

        self::$connection->doInsert(self::TABLE_NAME, [
            'uid_local' => 3,
            'uid_foreign' => 1,
            'tablenames' => 'tx_mkpostman_subscribers',
            'fieldname' => 'categories',
        ])->shouldBeCalled();

        self::$connection->doInsert(self::TABLE_NAME, [
            'uid_local' => 2,
            'uid_foreign' => 1,
            'tablenames' => 'tx_mkpostman_subscribers',
            'fieldname' => 'categories',
        ])->shouldBeCalled();

        self::$connection->doInsert(self::TABLE_NAME, [
            'uid_local' => 'foo',
            'uid_foreign' => 1,
            'tablenames' => 'tx_mkpostman_subscribers',
            'fieldname' => 'categories',
        ])->shouldBeCalled();

        $model = $this->prophesize(Tx_Rnbase_Domain_Model_DomainInterface::class);
        $model->getUid()->willReturn(1);
        $categories = ['foo', 1, 2, 3];

        $this->repository->addToCategories($model->reveal(), $categories);
    }

    public function testFindByUid()
    {
        $model = $this->prophesize(Tx_Rnbase_Domain_Model_DomainInterface::class);
        $this->searcher->search([
            'SUBSCRIBER.uid' => [
                OP_EQ_INT => 1,
            ],
        ], [
            'enablefieldsbe' => true,
            'limit' => 1,
            'collection' => 'Tx_Rnbase_Domain_Collection_Base',
            'searchdef' => [
                'usealias' => 1,
                'basetable' => 'foo',
                'basetablealias' => 'SUBSCRIBER',
                'wrapperclass' => SubscriberModel::class,
                'alias' => array(
                    'SUBSCRIBER' => array(
                        'table' => 'foo',
                    ),
                    'CATEGORYMM' => array(
                        'table' => 'sys_category_record_mm',
                        'join' => 'JOIN sys_category_record_mm AS CATEGORYMM ON SUBSCRIBER.uid = CATEGORYMM.uid_foreign',
                    ),
                ),
            ],
        ])->willReturn([$model->reveal()]);

        self::assertSame($model->reveal(), $this->repository->findByUid(1));
    }

    public function testFindByEmail()
    {
        $model = $this->prophesize(Tx_Rnbase_Domain_Model_DomainInterface::class);
        $this->searcher->search([
            'SUBSCRIBER.email' => [
                OP_EQ => 'foo@bar.tld',
            ],
        ], [
            'enablefieldsbe' => true,
            'limit' => 1,
            'collection' => 'Tx_Rnbase_Domain_Collection_Base',
            'searchdef' => [
                'usealias' => 1,
                'basetable' => 'foo',
                'basetablealias' => 'SUBSCRIBER',
                'wrapperclass' => SubscriberModel::class,
                'alias' => array(
                    'SUBSCRIBER' => array(
                        'table' => 'foo',
                    ),
                    'CATEGORYMM' => array(
                        'table' => 'sys_category_record_mm',
                        'join' => 'JOIN sys_category_record_mm AS CATEGORYMM ON SUBSCRIBER.uid = CATEGORYMM.uid_foreign',
                    ),
                ),
            ],
        ])->willReturn([$model->reveal()]);

        self::assertSame($model->reveal(), $this->repository->findByEmail('foo@bar.tld'));
    }

    public function testFindByCategory()
    {
        $category = $this->prophesize(CategoryModel::class);
        $category->getUid()->willReturn(1);

        $model = $this->prophesize(Tx_Rnbase_Domain_Model_DomainInterface::class);
        $collection = new \Tx_Rnbase_Domain_Collection_Base([$model->reveal()]);

        $this->searcher->search([
            'CATEGORYMM.uid_local' => array(
                OP_EQ_INT => 1,
            ),
            'CATEGORYMM.tablenames' => array(
                OP_EQ => 'tx_mkpostman_subscribers',
            ),
        ], Argument::that(function ($arg) {
            return true === $arg['enablefieldsbe'];
        }))->willReturn($collection);

        self::assertSame(
            $collection,
            $this->repository->findByCategory($category->reveal())
        );
    }
}
