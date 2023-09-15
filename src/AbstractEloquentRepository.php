<?php

namespace Rofflexor\LumenEloquentAbstractRepository;


use Rofflexor\LumenEloquentAbstractRepository\Interfaces\BaseRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;


class AbstractEloquentRepository implements BaseRepository
{


    /**
     * Illuminate\Database\Eloquent\Model
     *
     * @var Model
     */
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Возвращает экземпляр модели
     *
     * @return Model
     */
    public function getModel(): Model
    {
        return $this->model;
    }

    /**
     * Поиск элемента по ID
     *
     * @param $id
     * @return Model|null
     */
    public function findOne($id): ?Model
    {
        return $this->findOneBy(['id' => $id]);
    }

    /**
     * Поиск элемента по критериям
     *
     * @param array $criteria
     * @return Model|null
     */
    public function findOneBy(array $criteria): ?Model
    {
        return $this->model->where($criteria)->first();
    }

    /**
     * Поиск всех элементов по критериям
     *
     * @param array $searchCriteria
     * @return mixed
     */
    public function findBy(array $searchCriteria = [])
    {
        $limit = !empty($searchCriteria['per_page']) ? (int)$searchCriteria['per_page'] : config('app.per_page');
        $queryBuilder = $this->model->where(function($query) use ($searchCriteria) {
            $this->applySearchCriteriaInQueryBuilder($query, $searchCriteria);
        });

        if(isset($searchCriteria['sortBy'])) {
            if(isset($searchCriteria['orderBy']) && $searchCriteria['orderBy'] === 'desc') {
                $queryBuilder->orderByDesc($searchCriteria['sortBy']);
            }
            else {
                $queryBuilder->orderBy($searchCriteria['sortBy']);
            }
        }

        return $queryBuilder->paginate($limit);
    }

    /**
     * Поиск всех элементов по значению ключа
     *
     * @param string $key
     * @param array $values
     * @return Collection
     */
    public function findIn(string $key, array $values): Collection
    {
        return $this->model->whereIn($key, $values)->get();
    }

    /**
     * Сохранение элемента
     *
     * @param array $data
     * @return Model
     */
    public function save(array $data): Model
    {
        return $this->model->create($data);
    }

    /**
     * Обновление элемента
     *
     * @param Model $model
     * @param array $data
     * @return bool|Model
     */
    public function update(Model $model, array $data): bool|Model
    {
        $fillAbleProperties = $this->model->getFillable();

        foreach ($data as $key => $value) {
            if(in_array($key, $fillAbleProperties, true)) {
                $model->$key = $value;
            }
        }

        $model->save();

        return $this->findOne($model->id);

    }

    /**
     * Удаление элемента
     *
     * @param Model $model
     * @return mixed
     */
    public function delete(Model $model): mixed
    {
        return $model->delete();
    }

    /**
     * Применяет условия к конструктору запроса
     *
     * @param Object $queryBuilder
     * @param array $searchCriteria
     * @return mixed
     */
    protected function applySearchCriteriaInQueryBuilder(object $queryBuilder, array $searchCriteria = []): mixed
    {


        foreach ($searchCriteria as $key => $value) {

            if (in_array($key, ['page', 'per_page', 'sortBy','orderBy', 'onlySets'])) {
                continue;
            }

            $allValues = explode(',', $value);

            if (count($allValues) > 1) {
                $queryBuilder->whereIn($key, $allValues);
            } else {
                $operator = '=';

                if($value === 'null') {
                    $value = null;
                }
                if($value === '!=null') {
                    $operator = '!=';
                    $value = null;
                }
                if($value === 'true') {
                    $value = true;
                }
                if($value === 'false') {
                    $value = false;
                }

                $queryBuilder->where($key, $operator, $value);
            }
        }

        return $queryBuilder;
    }

    /**
     * Массовое обновление
     * @param array $data
     * @param array $searchCriteria
     * @return mixed
     */
    public function updateMany(array $data, array $searchCriteria): bool
    {
        return $this->model->withTrashed()->where(function($query) use ($searchCriteria) {
            $this->applySearchCriteriaInQueryBuilder($query, $searchCriteria);
        })->update($data);
    }

}