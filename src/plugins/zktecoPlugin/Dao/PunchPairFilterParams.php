<?php

namespace OrangeHRM\ZkTeco\Dao;

use OrangeHRM\Core\Dto\FilterParams;

class PunchPairFilterParams extends FilterParams
{
    public const PARAMETER_DATE = 'date';
    public const PARAMETER_EMPLOYEES = 'employees';
    public const PARAMETER_DEPARTMENTS = 'departments';
    public const PARAMETER_JOB_TITLES = 'jobTitles';
    public const PARAMETER_REPORT_MODE = 'reportMode';

    public const ALLOWED_SORT_FIELDS = [
        self::PARAMETER_DATE
    ];

    private ?string $date;
    private ?string $reportMode;
    private ?array $employees;
    private ?array $departments;
    private ?array $jobTitles;

    public function setDate(?string $date): PunchPairFilterParams
    {
        $this->date = $date;
        return $this;
    }

    public function setReportMode(?string $reportMode): PunchPairFilterParams
    {
        $this->reportMode = $reportMode;
        return $this;
    }

    public function setEmployees(?array $employees): PunchPairFilterParams
    {
        $this->employees = $employees;
        return $this;
    }

    public function setDepartments(?array $departments): PunchPairFilterParams
    {
        $this->departments = $departments;
        return $this;
    }

    public function setJobTitles(?array $jobTitles): PunchPairFilterParams
    {
        $this->jobTitles = $jobTitles;
        return $this;
    }

    public function getDate(): ?string
    {
        return $this->date;
    }

    public function getReportMode(): ?string
    {
        return $this->reportMode;
    }

    public function getEmployees(): ?array
    {
        return $this->employees;
    }

    public function getDepartments(): ?array
    {
        return $this->departments;
    }

    public function getJobTitles(): ?array
    {
        return $this->jobTitles;
    }

    public static function instance(): PunchPairFilterParams
    {
        return new PunchPairFilterParams();
    }
}