<?php

namespace OrangeHRM\ZkTeco\Model;

class ZkTecoTransaction
{
    public ?int $id;
    public ?string $emp_code;
    public ?string $first_name;
    public ?string $last_name;
    public ?string $nick_name;
    public ?string $gender;
    public ?string $dept_code;
    public ?string $dept_name;
    public ?string $company_code;
    public ?string $company_name;
    public ?string $position_code;
    public ?string $position_name;
    public ?string $work_code;
    public ?string $att_date;
    public ?string $work_code_alias;
    public ?string $punch_time;
    public ?string $punch_state;
    public ?string $verify_type;
    public ?string $source;

    public function __construct(array $data)
    {
        $this->id = $data['id'] ?? null;
        $this->emp_code = $data['emp_code'] ?? null;
        $this->first_name = $data['first_name'] ?? null;
        $this->last_name = $data['last_name'] ?? null;
        $this->nick_name = $data['nick_name'] ?? null;
        $this->gender = $data['gender'] ?? null;
        $this->dept_code = $data['dept_code'] ?? null;
        $this->dept_name = $data['dept_name'] ?? null;
        $this->company_code = $data['company_code'] ?? null;
        $this->company_name = $data['company_name'] ?? null;
        $this->position_code = $data['position_code'] ?? null;
        $this->position_name = $data['position_name'] ?? null;
        $this->work_code = $data['work_code'] ?? null;
        $this->att_date = $data['att_date'] ?? null;
        $this->work_code_alias = $data['work_code_alias'] ?? null;
        $this->punch_time = $data['punch_time'] ?? null;
        $this->punch_state = $data['punch_state'] ?? null;
        $this->verify_type = $data['verify_type'] ?? null;
        $this->source = $data['source'] ?? null;
    }
}