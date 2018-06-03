<?php
/**
 * sysPass
 *
 * @author    nuxsmin
 * @link      https://syspass.org
 * @copyright 2012-2018, Rubén Domínguez nuxsmin@$syspass.org
 *
 * This file is part of sysPass.
 *
 * sysPass is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * sysPass is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 *  along with sysPass.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace SP\Tests;

use SP\Core\Exceptions\QueryException;
use SP\DataModel\ItemSearchData;
use SP\DataModel\TagData;
use SP\Repositories\DuplicatedItemException;
use SP\Repositories\Tag\TagRepository;
use SP\Storage\DatabaseConnectionData;

/**
 * Class TagRepositoryTest
 *
 * Tests de integración para comprobar las consultas a la BBDD relativas a las etiquetas
 *
 * @package SP\Tests
 */
class TagRepositoryTest extends DatabaseTestCase
{
    /**
     * @var TagRepository
     */
    private static $tagRepository;

    /**
     * @throws \DI\NotFoundException
     * @throws \SP\Core\Context\ContextException
     * @throws \DI\DependencyException
     */
    public static function setUpBeforeClass()
    {
        $dic = setupContext();

        // Datos de conexión a la BBDD
        self::$databaseConnectionData = $dic->get(DatabaseConnectionData::class);

        // Inicializar el repositorio
        self::$tagRepository = $dic->get(TagRepository::class);
    }

    /**
     * Comprobar la búsqueda mediante texto
     */
    public function testSearch()
    {
        $itemSearchData = new ItemSearchData();
        $itemSearchData->setLimitCount(10);
        $itemSearchData->setSeachString('www');

        $search = self::$tagRepository->search($itemSearchData);
        $this->assertCount(2, $search);
        $this->assertArrayHasKey('count', $search);
        $this->assertEquals(1, $search['count']);
        $this->assertEquals(1, $search[0]->id);

        $itemSearchData = new ItemSearchData();
        $itemSearchData->setLimitCount(10);
        $itemSearchData->setSeachString('prueba');

        $search = self::$tagRepository->search($itemSearchData);
        $this->assertCount(1, $search);
        $this->assertArrayHasKey('count', $search);
        $this->assertEquals(0, $search['count']);
    }

    /**
     * Comprobar los resultados de obtener las etiquetas por Id
     */
    public function testGetById()
    {
        $tag = self::$tagRepository->getById(10);

        $this->assertCount(0, $tag);

        $tag = self::$tagRepository->getById(1);

        $this->assertEquals('www', $tag->getName());

        $tag = self::$tagRepository->getById(2);

        $this->assertEquals('windows', $tag->getName());
    }

    /**
     * Comprobar la obtención de todas las etiquetas
     */
    public function testGetAll()
    {
        $count = $this->conn->getRowCount('Tag');

        $results = self::$tagRepository->getAll();

        $this->assertCount($count, $results);

        $this->assertInstanceOf(TagData::class, $results[0]);
        $this->assertEquals('Linux', $results[0]->getName());

        $this->assertInstanceOf(TagData::class, $results[1]);
        $this->assertEquals('windows', $results[1]->getName());

        $this->assertInstanceOf(TagData::class, $results[2]);
        $this->assertEquals('www', $results[2]->getName());
    }

    /**
     * Comprobar la actualización de etiquetas
     *
     * @covers \SP\Repositories\Category\CategoryRepository::checkDuplicatedOnUpdate()
     * @throws \SP\Core\Exceptions\ConstraintException
     * @throws \SP\Core\Exceptions\QueryException
     * @throws \SP\Core\Exceptions\SPException
     */
    public function testUpdate()
    {
        $tagData = new TagData();
        $tagData->id = 1;
        $tagData->name = 'Servidor';

        self::$tagRepository->update($tagData);

        $category = self::$tagRepository->getById(1);

        $this->assertEquals($category->getName(), $tagData->name);

        // Comprobar la a actualización con un nombre duplicado comprobando su hash
        $tagData = new TagData();
        $tagData->id = 1;
        $tagData->name = ' linux.';

        $this->expectException(DuplicatedItemException::class);

        self::$tagRepository->update($tagData);
    }

    /**
     * Comprobar la eliminación de etiquetas
     *
     * @throws \SP\Core\Exceptions\SPException
     */
    public function testDeleteByIdBatch()
    {
        $this->assertEquals(0, self::$tagRepository->deleteByIdBatch([4]));
        $this->assertEquals(3, self::$tagRepository->deleteByIdBatch([1, 2, 3]));

        $this->assertEquals(0, $this->conn->getRowCount('Tag'));
    }

    /**
     * Comprobar la creación de etiquetas
     *
     * @covers \SP\Repositories\Category\CategoryRepository::checkDuplicatedOnAdd()
     * @throws DuplicatedItemException
     * @throws \SP\Core\Exceptions\SPException
     */
    public function testCreate()
    {
        $countBefore = $this->conn->getRowCount('Tag');

        $tagData = new TagData();
        $tagData->name = 'Core';

        $id = self::$tagRepository->create($tagData);

        // Comprobar que el Id devuelto corresponde con la etiqueta creada
        $tag = self::$tagRepository->getById($id);

        $this->assertEquals($tagData->name, $tag->getName());

        $countAfter = $this->conn->getRowCount('Tag');

        $this->assertEquals($countBefore + 1, $countAfter);
    }

    /**
     * Comprobar la eliminación de etiquetas por Id
     *
     * @throws QueryException
     * @throws \SP\Core\Exceptions\ConstraintException
     */
    public function testDelete()
    {
        $countBefore = $this->conn->getRowCount('Tag');

        $this->assertEquals(1, self::$tagRepository->delete(3));

        $countAfter = $this->conn->getRowCount('Tag');

        $this->assertEquals($countBefore - 1, $countAfter);

        // Comprobar la eliminación de etiquetas usadas
        $this->assertEquals(1, self::$tagRepository->delete(1));
    }

    /**
     * Comprobar la obtención de etiquetas por Id en lote
     */
    public function testGetByIdBatch()
    {
        $this->assertCount(3, self::$tagRepository->getByIdBatch([1, 2, 3]));
        $this->assertCount(3, self::$tagRepository->getByIdBatch([1, 2, 3, 4, 5]));
        $this->assertCount(0, self::$tagRepository->getByIdBatch([]));
    }

    /**
     * @throws QueryException
     * @throws \SP\Core\Exceptions\ConstraintException
     */
    public function testCheckInUse()
    {
        $this->assertTrue(self::$tagRepository->checkInUse(1));
    }
}
