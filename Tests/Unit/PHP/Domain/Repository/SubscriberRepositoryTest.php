<?php

namespace DMK\Mkpostman\Domain\Repository;

use DMK\Mkpostman\Domain\Model\CategoryModel;
use DMK\Mkpostman\Domain\Model\SubscriberModel;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Tx_Rnbase_Database_Connection as ConnectionInterfae;
use Tx_Rnbase_Domain_Model_DomainInterface;
use tx_rnbase_util_SearchGeneric as Searcher;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Testcase for SubscriberRepository.
 *
 * @property \Prophecy\Prophecy\ObjectProphecy $connection
 * @property \Prophecy\Prophecy\ObjectProphecy $searcher
 * @property SubscriberRepository $repository
 */
class SubscriberRepositoryTest extends TestCase
{
    protected function setUp()
    {
        // instanciate repository to test
        $this->repository = new SubscriberRepository();

        GeneralUtility::purgeInstances();
        // mock connection
        $this->connection = $this->prophesize(ConnectionInterfae::class);
        GeneralUtility::setSingletonInstance(ConnectionInterfae::class, $this->connection->reveal());

        // mock model instances
        $emptyModel = $this->prophesize(SubscriberModel::class);
        $emptyModel->getTableName()->willReturn('subscriber_table');

        GeneralUtility::addInstance(SubscriberModel::class, $emptyModel->reveal());
        GeneralUtility::addInstance(SubscriberModel::class, $emptyModel->reveal());

        $this->searcher = $this->prophesize(Searcher::class);
        GeneralUtility::addInstance(Searcher::class, $this->searcher->reveal());
    }

    protected function tearDown()
    {
        // reset all instances from the testcase
        GeneralUtility::purgeInstances();
    }

    /**
     * Test the addToCategories method.
     *
     * @group unit
     * @test
     */
    public function addToCategories()
    {
        $connection = $this->connection;

        $connection->doDelete('sys_category_record_mm', 'uid_foreign = 1')
            ->shouldBeCalled();

        $connection->doInsert('sys_category_record_mm', [
            'uid_local' => 1,
            'uid_foreign' => 1,
            'tablenames' => 'tx_mkpostman_subscribers',
            'fieldname' => 'categories',
        ])->shouldBeCalled();

        $connection->doInsert('sys_category_record_mm', [
            'uid_local' => 3,
            'uid_foreign' => 1,
            'tablenames' => 'tx_mkpostman_subscribers',
            'fieldname' => 'categories',
        ])->shouldBeCalled();

        $connection->doInsert('sys_category_record_mm', [
            'uid_local' => 2,
            'uid_foreign' => 1,
            'tablenames' => 'tx_mkpostman_subscribers',
            'fieldname' => 'categories',
        ])->shouldBeCalled();

        $connection->doInsert('sys_category_record_mm', [
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

    /**
     * Test the findByUid method.
     *
     * @group unit
     * @test
     */
    public function findByUid()
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
                'basetable' => 'subscriber_table',
                'basetablealias' => 'SUBSCRIBER',
                'wrapperclass' => SubscriberModel::class,
                'alias' => [
                    'SUBSCRIBER' => [
                        'table' => 'subscriber_table',
                    ],
                    'CATEGORYMM' => [
                        'table' => 'sys_category_record_mm',
                        'join' => 'JOIN sys_category_record_mm AS CATEGORYMM ON SUBSCRIBER.uid = CATEGORYMM.uid_foreign',
                    ],
                ],
            ],
        ])->willReturn([$model->reveal()]);

        self::assertSame($model->reveal(), $this->repository->findByUid(1));
    }

    /**
     * Test the findByEmail method.
     *
     * @group unit
     * @test
     */
    public function findByEmail()
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
                'basetable' => 'subscriber_table',
                'basetablealias' => 'SUBSCRIBER',
                'wrapperclass' => SubscriberModel::class,
                'alias' => [
                    'SUBSCRIBER' => [
                        'table' => 'subscriber_table',
                    ],
                    'CATEGORYMM' => [
                        'table' => 'sys_category_record_mm',
                        'join' => 'JOIN sys_category_record_mm AS CATEGORYMM ON SUBSCRIBER.uid = CATEGORYMM.uid_foreign',
                    ],
                ],
            ],
        ])->willReturn([$model->reveal()]);

        self::assertSame($model->reveal(), $this->repository->findByEmail('foo@bar.tld'));
    }

    /**
     * Test the findByCategory method.
     *
     * @group unit
     * @test
     */
    public function findByCategory()
    {
        $category = $this->prophesize(CategoryModel::class);
        $category->getUid()->willReturn(1);

        $model = $this->prophesize(Tx_Rnbase_Domain_Model_DomainInterface::class);
        $collection = new \Tx_Rnbase_Domain_Collection_Base([$model->reveal()]);

        $this->searcher->search([
            'CATEGORYMM.uid_local' => [
                OP_EQ_INT => 1,
            ],
            'CATEGORYMM.tablenames' => [
                OP_EQ => 'tx_mkpostman_subscribers',
            ],
        ], Argument::that(function ($arg) {
            return true === $arg['enablefieldsbe'];
        }))->willReturn($collection);

        self::assertSame(
            $collection,
            $this->repository->findByCategory($category->reveal())
        );
    }
}
