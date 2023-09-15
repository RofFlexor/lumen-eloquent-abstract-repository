<?php

namespace Rofflexor\LumenEloquentAbstractRepository\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface BaseRepository
{
    /**
     * Поиск элемента по ID
     *
     * @param $id
     *
     * @return Model|null
     */
    public function findOne($id): ?Model;

    /**
     * Поиск элемента по критериям
     *
     * @param array $criteria
     * @return Model|null
     */
    public function findOneBy(array $criteria): ?Model;

    /**
     * Поиск всех элементов по критериям
     *
     * @param array $searchCriteria
     * @return Collection
     */
    public function findBy(array $searchCriteria = []);

    /**
     * Поиск всех элементов по значению ключа
     *
     * @param string $key
     * @param array $values
     * @return Collection
     */
    public function findIn(string $key, array $values): Collection;

    /**
     * Сохранение элемента
     *
     * @param array $data
     * @return Model
     */
    public function save(array $data): Model;

    /**
     * Обновление элемента
     *
     * @param Model $model
     * @param array $data
     * @return Model|bool
     */
    public function update(Model $model, array $data): Model|bool;

    /**
     * Удаление элемента
     *
     * @param Model $model
     * @return mixed
     */
    public function delete(Model $model): mixed;

    /**
     * Массовое обновление
     * @param array $data
     * @param array $searchCriteria
     * @return mixed
     */
    public function updateMany(array $data, array $searchCriteria): bool;



}