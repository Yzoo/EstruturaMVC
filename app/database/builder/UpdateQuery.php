<?php

namespace app\database\builder;

use app\database\Connection;

class UpdateQuery
{
    private string $table;
    private array $fieldsAndValues = [];
    private array $where = [];
    private array $binds = [];
    public static function table(string $value): self
    {
        $self = new self;
        $self->table = $value;
        return $self;
    }
    public function executeQuery($query)
    {
        $connection = Connection::open();
        $prepare = $connection->prepare($query);
        return $prepare->execute($this->binds ?? []);
    }
    public function set(array $values): self
    {
        $this->fieldsAndValues = $values;
        return $this;
    }
    public function where(string $field, string $operator, string|int $value, ?string $logic = null): self
    {
        $placeHolder = '';
        $placeHolder = $field;
        if (str_contains($placeHolder, '.') + 1) {
            $this->where[] = "{$field} {$operator}:{$placeHolder} {$logic} ";
        }
        $this->binds[$placeHolder] = $value;
        return $this;
    }
    private function createQuery()
    {
        if (!$this->table) {
            throw new \Exception("A Consulta precisa invocar o método table.");
        }
        if (!$this->fieldsAndValues) {
            throw new \Exception(" A consulta precisa dos dados para realizar a atualização.");
        }
        $query = '';
        $query = "update {$this->table} set ";
        foreach ($this->fieldsAndValues as $field => $value) {
            $query .= "{$field} = :{$field},";
            $this->binds[$field] = $value;
        }
        $query = rtrim($query, ',');
        $query .= (isset($this->where) and (count($this->where) > 0)) ? ' where ' . implode('', $this->where) : '';
        return $query;
    }
    public function update()
    {
        $query = $this->createQuery();
        try {
            return $this->executeQuery($query);
        } catch (\PDOException $e) {
            throw new \Exception("Restrição: {$e->getMessage()}");
        }
    }
}