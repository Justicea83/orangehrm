<?php

namespace OrangeHRM\ZkTeco\Reports;

class ZkTecoApiResponse
{
    public ?string $msg = '';
    public ?string $next = null;
    public ?string $previous = null;
    public array $data = [];
    public int $count = 0;
    public int $code = -1;

    public function isSuccessful(): bool
    {
        return $this->count === 0;
    }

    public function setMsg(string $msg): ZkTecoApiResponse
    {
        $this->msg = $msg;
        return $this;
    }

    public function getMsg(): ?string
    {
        return $this->msg;
    }

    public function getNext(): ?string
    {
        return $this->next;
    }

    public function setNext(?string $next): ZkTecoApiResponse
    {
        $this->next = $next;
        return $this;
    }

    public function getPrevious(): ?string
    {
        return $this->previous;
    }

    public function setPrevious(?string $previous): ZkTecoApiResponse
    {
        $this->previous = $previous;
        return $this;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data): ZkTecoApiResponse
    {
        $this->data = $data;
        return $this;
    }

    public function getCount(): int
    {
        return $this->count;
    }

    public function setCount(int $count): ZkTecoApiResponse
    {
        $this->count = $count;
        return $this;
    }

    public function getCode(): int
    {
        return $this->code;
    }

    public function setCode(int $code): ZkTecoApiResponse
    {
        $this->code = $code;
        return $this;
    }

    public function transform(callable $callback)
    {
        return array_map($callback, $this->data);
    }

    public static function instance(): ZkTecoApiResponse
    {
        return new ZkTecoApiResponse();
    }
}