<?php
/**
 * sysPass
 *
 * @author    nuxsmin
 * @link      http://syspass.org
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

namespace SP\Repositories\Account;

use SP\Account\AccountRequest;
use SP\Account\AccountSearchFilter;
use SP\Account\AccountUtil;
use SP\Core\Exceptions\QueryException;
use SP\Core\Exceptions\SPException;
use SP\DataModel\AccountExtData;
use SP\DataModel\AccountPassData;
use SP\DataModel\AccountSearchVData;
use SP\DataModel\AccountVData;
use SP\DataModel\Dto\AccountSearchResponse;
use SP\DataModel\ItemSearchData;
use SP\Repositories\Repository;
use SP\Repositories\RepositoryItemInterface;
use SP\Repositories\RepositoryItemTrait;
use SP\Storage\DbWrapper;
use SP\Storage\QueryData;

/**
 * Class AccountRepository
 *
 * @package Services
 */
class AccountRepository extends Repository implements RepositoryItemInterface
{
    use RepositoryItemTrait;

    /**
     * @param $id
     * @return AccountPassData
     */
    public function getPasswordForId($id)
    {
        $Data = new QueryData();
        $Data->setMapClassName(AccountPassData::class);
        $Data->setLimit(1);

        $Data->setSelect('A.id, A.name, A.login, A.pass, A.key, A.parentId');
        $Data->setFrom('Account A');

        $queryWhere = AccountUtil::getAccountFilterUser($Data, $this->session);
        $queryWhere[] = 'A.id = ?';
        $Data->addParam($id);

        $Data->setWhere($queryWhere);

        return DbWrapper::getResults($Data, $this->db);
    }

    /**
     * @param $id
     * @return AccountPassData
     */
    public function getPasswordHistoryForId($id)
    {
        $Data = new QueryData();
        $Data->setMapClassName(AccountPassData::class);
        $Data->setLimit(1);

        $Data->setSelect('AH.id, AH.name, AH.login, AH.pass, AH.key, AH.parentId');
        $Data->setFrom('AccountHistory AH');

        $queryWhere = AccountUtil::getAccountHistoryFilterUser($Data, $this->session);
        $queryWhere[] = 'AH.id = ?';
        $Data->addParam($id);

        $Data->setWhere($queryWhere);

        return DbWrapper::getResults($Data, $this->db);
    }

    /**
     * Incrementa el contador de vista de clave de una cuenta en la BBDD
     *
     * @param int $id
     * @return bool
     * @throws \SP\Core\Exceptions\QueryException
     * @throws \SP\Core\Exceptions\ConstraintException
     */
    public function incrementDecryptCounter($id)
    {
        $query = /** @lang SQL */
            'UPDATE Account SET countDecrypt = (countDecrypt + 1) WHERE id = ? LIMIT 1';

        $Data = new QueryData();
        $Data->setQuery($query);
        $Data->addParam($id);

        return DbWrapper::getQuery($Data, $this->db);
    }

    /**
     * Crea una nueva cuenta en la BBDD
     *
     * @param AccountRequest $itemData
     * @return int
     * @throws QueryException
     * @throws SPException
     * @throws \SP\Core\Exceptions\ConstraintException
     */
    public function create($itemData)
    {
        $query = /** @lang SQL */
            'INSERT INTO Account SET 
            clientId = :clientId,
            categoryId = :categoryId,
            name = :name,
            login = :login,
            url = :url,
            pass = :pass,
            `key` = :key,
            notes = :notes,
            dateAdd = NOW(),
            userId = :userId,
            userGroupId = :userGroupId,
            userEditId = :userEditId,
            otherUserEdit = :otherUserEdit,
            otherUserGroupEdit = :otherGroupEdit,
            isPrivate = :isPrivate,
            isPrivateGroup = :isPrivateGroup,
            passDate = UNIX_TIMESTAMP(),
            passDateChange = :passDateChange,
            parentId = :parentId';

        $Data = new QueryData();
        $Data->setQuery($query);
        $Data->addParam($itemData->clientId, 'clientId');
        $Data->addParam($itemData->categoryId, 'aategoryId');
        $Data->addParam($itemData->name, 'name');
        $Data->addParam($itemData->login, 'login');
        $Data->addParam($itemData->url, 'url');
        $Data->addParam($itemData->pass, 'pass');
        $Data->addParam($itemData->key, 'key');
        $Data->addParam($itemData->notes, 'notes');
        $Data->addParam($itemData->userId, 'userId');
        $Data->addParam($itemData->userGroupId ?: $this->session->getUserData()->getUserGroupId(), 'userGroupId');
        $Data->addParam($itemData->userId, 'userEditId');
        $Data->addParam($itemData->otherUserEdit, 'otherUserEdit');
        $Data->addParam($itemData->otherUserGroupEdit, 'otherGroupEdit');
        $Data->addParam($itemData->isPrivate, 'isPrivate');
        $Data->addParam($itemData->isPrivateGroup, 'isPrivateGroup');
        $Data->addParam($itemData->passDateChange, 'passDateChange');
        $Data->addParam($itemData->parentId, 'parentId');
        $Data->setOnErrorMessage(__u('Error al crear la cuenta'));

        DbWrapper::getQuery($Data, $this->db);

        return $this->db->getLastId();
    }

    /**
     * Actualiza la clave de una cuenta en la BBDD.
     *
     * @param AccountRequest $accountRequest
     * @return bool
     * @throws QueryException
     * @throws SPException
     * @throws \SP\Core\Exceptions\ConstraintException
     */
    public function editPassword(AccountRequest $accountRequest)
    {
        $query = /** @lang SQL */
            'UPDATE Account SET 
            pass = :pass,
            `key` = :key,
            userEditId = :userEditId,
            dateEdit = NOW(),
            passDate = UNIX_TIMESTAMP(),
            passDateChange = :passDateChange
            WHERE id = :id';

        $Data = new QueryData();
        $Data->setQuery($query);
        $Data->addParam($accountRequest->pass, 'pass');
        $Data->addParam($accountRequest->key, 'key');
        $Data->addParam($accountRequest->userEditId, 'userEditId');
        $Data->addParam($accountRequest->passDateChange, 'passDateChange');
        $Data->addParam($accountRequest->id, 'id');
        $Data->setOnErrorMessage(__u('Error al actualizar la clave'));

        return DbWrapper::getQuery($Data, $this->db);
    }

    /**
     * Restaurar una cuenta desde el histórico.
     *
     * @param int $historyId El Id del registro en el histórico
     * @return bool
     * @throws QueryException
     * @throws \SP\Core\Exceptions\ConstraintException
     */
    public function editRestore($historyId)
    {
        $query = /** @lang SQL */
            'UPDATE Account dst, 
            (SELECT * FROM AccountHistory AH WHERE AH.id = :id) src SET 
            dst.clientId = src.clientId,
            dst.categoryId = src.categoryId,
            dst.name = src.name,
            dst.login = src.login,
            dst.url = src.url,
            dst.notes = src.notes,
            dst.userGroupId = src.userGroupId,
            dst.userEditId = :userEditId,
            dst.dateEdit = NOW(),
            dst.otherUserEdit = src.otherUserEdit + 0,
            dst.otherUserGroupEdit = src.otherUserGroupEdit + 0,
            dst.pass = src.pass,
            dst.key = src.key,
            dst.passDate = src.passDate,
            dst.passDateChange = src.passDateChange, 
            dst.parentId = src.parentId, 
            dst.isPrivate = src.isPrivate,
            dst.isPrivateGroup = src.isPrivateGroup
            WHERE dst.id = src.accountId';

        $Data = new QueryData();
        $Data->setQuery($query);
        $Data->addParam($historyId, 'id');
        $Data->addParam($this->session->getUserData()->getId(), 'userEditId');
        $Data->setOnErrorMessage(__u('Error al restaurar cuenta'));

        return DbWrapper::getQuery($Data, $this->db);
    }

    /**
     * Elimina los datos de una cuenta en la BBDD.
     *
     * @param int $id
     * @return bool Los ids de las cuentas eliminadas
     * @throws SPException
     */
    public function delete($id)
    {
        $Data = new QueryData();

        $query = /** @lang SQL */
            'DELETE FROM Account WHERE id = ? LIMIT 1';

        $Data->setQuery($query);
        $Data->addParam($id);
        $Data->setOnErrorMessage(__u('Error al eliminar la cuenta'));

        DbWrapper::getQuery($Data, $this->db);

        return $Data->getQueryNumRows();
    }

    /**
     * Updates an item
     *
     * @param AccountRequest $itemData
     * @return mixed
     * @throws SPException
     */
    public function update($itemData)
    {
        $Data = new QueryData();

        $fields = [
            'clientId = :clientId',
            'categoryId = :categoryId',
            'name = :name',
            'login = :login',
            'url = :url',
            'notes = :notes',
            'userEditId = :userEditId',
            'dateEdit = NOW()',
            'passDateChange = :passDateChange',
            'isPrivate = :isPrivate',
            'isPrivateGroup = :isPrivateGroup',
            'parentId = :parentId'
        ];

        if ($itemData->changeUserGroup) {
            $fields[] = 'userGroupId = :userGroupId';

            $Data->addParam($itemData->userGroupId, 'userGroupId');
        }

        if ($itemData->changePermissions) {
            $fields[] = 'otherUserEdit = :otherUserEdit';
            $fields[] = 'otherUserGroupEdit = :otherUserGroupEdit';

            $Data->addParam($itemData->otherUserEdit, 'otherUserEdit');
            $Data->addParam($itemData->otherUserGroupEdit, 'otherUserGroupEdit');
        }

        $query = /** @lang SQL */
            'UPDATE Account SET ' . implode(',', $fields) . ' WHERE id = :accountId';

        $Data->setQuery($query);
        $Data->addParam($itemData->clientId, 'clientId');
        $Data->addParam($itemData->categoryId, 'categoryId');
        $Data->addParam($itemData->name, 'name');
        $Data->addParam($itemData->login, 'login');
        $Data->addParam($itemData->url, 'url');
        $Data->addParam($itemData->notes, 'notes');
        $Data->addParam($itemData->userEditId, 'userEditId');
        $Data->addParam($itemData->passDateChange, 'passDateChange');
        $Data->addParam($itemData->isPrivate, 'isPrivate');
        $Data->addParam($itemData->isPrivateGroup, 'isPrivateGroup');
        $Data->addParam($itemData->parentId, 'parentId');
        $Data->addParam($itemData->id, 'id');
        $Data->setOnErrorMessage(__u('Error al modificar la cuenta'));

        return DbWrapper::getQuery($Data, $this->db);
    }

    /**
     * Returns the item for given id
     *
     * @param int $id
     * @return AccountVData
     * @throws SPException
     */
    public function getById($id)
    {
        $query = /** @lang SQL */
            'SELECT * FROM account_data_v WHERE id = ? LIMIT 1';

        $Data = new QueryData();
        $Data->setQuery($query);
        $Data->setMapClassName(AccountVData::class);
        $Data->addParam($id);

        /** @var AccountVData|array $queryRes */
        $queryRes = DbWrapper::getResults($Data);

        if ($queryRes === false) {
            throw new SPException(SPException::SP_CRITICAL, __u('No se pudieron obtener los datos de la cuenta'));
        }

        if (is_array($queryRes) && count($queryRes) === 0) {
            throw new SPException(SPException::SP_CRITICAL, __u('La cuenta no existe'));
        }

        return $queryRes;
    }

    /**
     * Returns all the items
     *
     */
    public function getAll()
    {
        throw new \RuntimeException('Not implemented');
    }

    /**
     * Returns all the items for given ids
     *
     * @param array $ids
     */
    public function getByIdBatch(array $ids)
    {
        throw new \RuntimeException('Not implemented');
    }

    /**
     * Deletes all the items for given ids
     *
     * @param array $ids
     */
    public function deleteByIdBatch(array $ids)
    {
        throw new \RuntimeException('Not implemented');
    }

    /**
     * Checks whether the item is in use or not
     *
     * @param $id int
     */
    public function checkInUse($id)
    {
        throw new \RuntimeException('Not implemented');
    }

    /**
     * Checks whether the item is duplicated on updating
     *
     * @param mixed $itemData
     */
    public function checkDuplicatedOnUpdate($itemData)
    {
        throw new \RuntimeException('Not implemented');
    }

    /**
     * Checks whether the item is duplicated on adding
     *
     * @param mixed $itemData
     */
    public function checkDuplicatedOnAdd($itemData)
    {
        throw new \RuntimeException('Not implemented');
    }

    /**
     * Searches for items by a given filter
     *
     * @param ItemSearchData $SearchData
     * @return mixed
     */
    public function search(ItemSearchData $SearchData)
    {
        $Data = new QueryData();
        $Data->setSelect('A.id, A.name, C.name AS clientName');
        $Data->setFrom('Account A INNER JOIN Client C ON A.clientId = C.id');
        $Data->setOrder('A.name');

        if ($SearchData->getSeachString() !== '') {
            $Data->setWhere('A.name LIKE ? OR C.name LIKE ?');

            $search = '%' . $SearchData->getSeachString() . '%';
            $Data->addParam($search);
            $Data->addParam($search);
        }

        $Data->setLimit('?,?');
        $Data->addParam($SearchData->getLimitStart());
        $Data->addParam($SearchData->getLimitCount());

        DbWrapper::setFullRowCount();

        $queryRes = DbWrapper::getResultsArray($Data, $this->db);

        $queryRes['count'] = $Data->getQueryNumRows();

        return $queryRes;
    }

    /**
     * Incrementa el contador de visitas de una cuenta en la BBDD
     *
     * @param int $id
     * @return bool
     * @throws \SP\Core\Exceptions\QueryException
     * @throws \SP\Core\Exceptions\ConstraintException
     */
    public function incrementViewCounter($id = null)
    {
        $query = /** @lang SQL */
            'UPDATE Account SET countView = (countView + 1) WHERE id = ? LIMIT 1';

        $Data = new QueryData();
        $Data->setQuery($query);
        $Data->addParam($id);

        return DbWrapper::getQuery($Data, $this->db);
    }

    /**
     * Obtener los datos de una cuenta.
     *
     * @param $id
     * @return AccountExtData
     * @throws SPException
     */
    public function getDataForLink($id)
    {
        $query = /** @lang SQL */
            'SELECT A.name,
            A.login,
            A.pass,
            A.key,
            A.url,
            A.notes,
            C.name AS clientName,
            C2.name AS categoryName
            FROM Account A
            INNER JOIN Client C ON A.clientId = C.id
            INNER JOIN Category C2 ON A.categoryId = C2.id 
            WHERE A.id = ? LIMIT 1';

        $Data = new QueryData();
        $Data->setQuery($query);
        $Data->setMapClassName(AccountExtData::class);
        $Data->addParam($id);

        /** @var AccountExtData|array $queryRes */
        $queryRes = DbWrapper::getResults($Data, $this->db);

        if ($queryRes === false) {
            throw new SPException(SPException::SP_ERROR, __u('No se pudieron obtener los datos de la cuenta'));
        }

        if (is_array($queryRes) && count($queryRes) === 0) {
            throw new SPException(SPException::SP_ERROR, __u('La cuenta no existe'));
        }

        return $queryRes;
    }

    /**
     * Obtener las cuentas de una búsqueda.
     *
     * @param AccountSearchFilter $accountSearchFilter
     * @return AccountSearchResponse
     */
    public function getByFilter(AccountSearchFilter $accountSearchFilter)
    {
        $arrFilterCommon = [];
        $arrFilterSelect = [];
        $arrayQueryJoin = [];
        $arrQueryWhere = [];
        $queryLimit = '';

        $data = new QueryData();

        $txtSearch = $accountSearchFilter->getTxtSearch();

        if ($txtSearch !== null && $txtSearch !== '') {
            // Analizar la cadena de búsqueda por etiquetas especiales
            $stringFilter = $accountSearchFilter->getStringFilters();

            if (!empty($stringFilter)) {
                $arrFilterCommon[] = $stringFilter['query'];

                foreach ($stringFilter['values'] as $value) {
                    $data->addParam($value);
                }
            } else {
                $txtSearch = '%' . $txtSearch . '%';

                $arrFilterCommon[] = 'name LIKE ?';
                $data->addParam($txtSearch);

                $arrFilterCommon[] = 'login LIKE ?';
                $data->addParam($txtSearch);

                $arrFilterCommon[] = 'url LIKE ?';
                $data->addParam($txtSearch);

                $arrFilterCommon[] = 'notes LIKE ?';
                $data->addParam($txtSearch);
            }
        }

        if ($accountSearchFilter->getCategoryId() !== 0) {
            $arrFilterSelect[] = 'categoryId = ?';
            $data->addParam($accountSearchFilter->getCategoryId());
        }

        if ($accountSearchFilter->getClientId() !== 0) {
            $arrFilterSelect[] = 'clientId = ?';
            $data->addParam($accountSearchFilter->getClientId());
        }

        $tagsId = $accountSearchFilter->getTagsId();
        $numTags = count($tagsId);

        if ($numTags > 0) {
            $tags = str_repeat('?,', $numTags - 1) . '?';

            $arrFilterSelect[] = 'id IN (SELECT accountId FROM AccountToTag WHERE tagId IN (' . $tags . '))';

            foreach ($tagsId as $tag) {
                $data->addParam($tag);
            }
        }

        if ($accountSearchFilter->isSearchFavorites() === true) {
            $arrayQueryJoin[] = 'INNER JOIN AccountToFavorite AF ON (AF.accountId = id AND AF.userId = ?)';
            $data->addParam($this->session->getUserData()->getId());
        }

        if (count($arrFilterCommon) > 0) {
            $arrQueryWhere[] = '(' . implode(' OR ', $arrFilterCommon) . ')';
        }

        if (count($arrFilterSelect) > 0) {
            $arrQueryWhere[] = '(' . implode(' AND ', $arrFilterSelect) . ')';
        }

        $arrQueryWhere = array_merge($arrQueryWhere, AccountUtil::getAccountFilterUser($data, $this->session, $accountSearchFilter->getGlobalSearch()));

        if ($accountSearchFilter->getLimitCount() > 0) {
            $queryLimit = '?, ?';

            $data->addParam($accountSearchFilter->getLimitStart());
            $data->addParam($accountSearchFilter->getLimitCount());
        }

        $queryWhere = '';

        if (count($arrQueryWhere) === 1) {
            $queryWhere = implode($arrQueryWhere);
        } elseif (count($arrQueryWhere) > 1) {
            $queryWhere = implode(' AND ', $arrQueryWhere);
        }

        $queryJoin = implode('', $arrayQueryJoin);

        $data->setSelect('*');
        $data->setFrom('account_search_v ' . $queryJoin);
        $data->setWhere($queryWhere);
        $data->setOrder($accountSearchFilter->getOrderString());
        $data->setLimit($queryLimit);

//        Log::writeNewLog(__FUNCTION__, $Data->getQuery(), Log::DEBUG);
//        Log::writeNewLog(__FUNCTION__, print_r($Data->getParams(), true), Log::DEBUG);

        $data->setMapClassName(AccountSearchVData::class);

        return new AccountSearchResponse($this->db->getFullRowCount($data), DbWrapper::getResultsArray($data, $this->db));
    }

    /**
     * @param $accountId
     * @return array
     */
    public function getForUser($accountId)
    {
        $Data = new QueryData();

        $queryWhere = AccountUtil::getAccountFilterUser($Data, $this->session);

        if (null !== $accountId) {
            $queryWhere[] = 'A.id <> ? AND (A.parentId = 0 OR A.parentId IS NULL)';
            $Data->addParam($accountId);
        }

        $query = /** @lang SQL */
            'SELECT A.id, A.name, C.name AS clientName 
            FROM Account A
            LEFT JOIN Client C ON A.clientId = C.id 
            WHERE ' . implode(' AND ', $queryWhere) . ' ORDER BY name';

        $Data->setQuery($query);

        return DbWrapper::getResultsArray($Data);
    }


    /**
     * @param $accountId
     * @return array
     */
    public function getLinked($accountId)
    {
        $Data = new QueryData();

        $queryWhere = AccountUtil::getAccountFilterUser($Data, $this->session);

        $queryWhere[] = 'A.parentId = ?';
        $Data->addParam($accountId);

        $query = /** @lang SQL */
            'SELECT A.id, A.name, C.name AS clientName 
            FROM Account A
            INNER JOIN Client C ON A.clientId = C.id 
            WHERE ' . implode(' AND ', $queryWhere) . ' ORDER  BY name';

        $Data->setQuery($query);

        return DbWrapper::getResultsArray($Data);
    }
}