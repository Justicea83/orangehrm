<?php

namespace OrangeHRM\ZkTeco\Reports;

use DateTime;
use Doctrine\DBAL\Exception;
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;

class TransactionReport extends AbstractZkTecoReport
{
    use EntityManagerHelperTrait, AuthUserTrait;

    const COLUMN_MAPPINGS = [
        "emp_code"       => "Employee Code",
        "first_name"     => "First Name",
        "last_name"      => "Last Name",
        "nick_name"      => "Nickname",
        "gender"         => "Gender",
        "dept_code"      => "Department Code",
        "dept_name"      => "Department Name",
        "position_code"  => "Position Code",
        "company_code"   => "Company Code",
        "company_name"   => "Company Name",
        "position_name"  => "Position Name",
        "att_date"       => "Attendance Date",
        "weekday"        => "Day of the Week",
        "check_in"       => "Check-in Time",
        "check_out"      => "Check-out Time",
        "total_time"     => "Total Work Time",
        "hourly_rate"    => "Hourly Rate",
        "total_comp"     => "Total Compensation",
        "currency"       => "Currency",
    ];

    public function fetchTransactions(
        array  $queryParams = [],
        string $endpoint = '/att/api/transactionReport/'
    ): ZkTecoApiResponse
    {
        $defaults = [
            'page' => 1,
            'page_size' => 100,
            'start_date' => date('Y-m-d'),
            'end_date' => date('Y-m-d'),
            'departments' => -1,
            'areas' => -1,
            'groups' => -1,
            'employees' => -1,
        ];

        // Merge incoming params with defaults
        $finalParams = array_merge($defaults, $queryParams);

        // Fields that can be an array or a single value, default to -1 if empty
        $fields = ['departments', 'areas', 'groups', 'employees'];
        foreach ($fields as $field) {
            // If not set or empty, use -1
            if (empty($finalParams[$field])) {
                $finalParams[$field] = -1;
            } else {
                // If it's an array, turn it into a comma-separated string
                if (is_array($finalParams[$field])) {
                    $finalParams[$field] = implode(',', $finalParams[$field]);
                }
            }
        }

        return $this->call($endpoint, $finalParams);
    }

    public static function instance(): TransactionReport
    {
        return new TransactionReport();
    }

    public function generateDTRReport($records, $startDate): array
    {
        // Group records by employee code and add a DateTime field (for proper sorting)
        $employeeRecords = [];
        foreach ($records as $record) {
            $empCode = $record['emp_code'];
            // Create a DateTime object by combining the att_date and punch_time
            $record['datetime'] = DateTime::createFromFormat('Y-m-d H:i', $record['att_date'] . ' ' . $record['punch_time']);
            $employeeRecords[$empCode][] = $record;
        }

        // Sort each employee's records in ascending order (by the datetime)
        foreach ($employeeRecords as $empCode => &$recordsList) {
            usort($recordsList, function ($a, $b) {
                return $a['datetime'] <=> $b['datetime'];
            });
        }
        unset($recordsList);

        $report = [];

        // Process each employee's records to find a valid punch pair.
        // We only accept pairs where the Check In is on the $startDate.
        foreach ($employeeRecords as $empCode => $recordsList) {
            foreach ($recordsList as $index => $record) {
                // Only consider a Check In if its att_date equals the start date.
                if ($record['punch_state'] === 'Check In' && $record['att_date'] === $startDate) {
                    $checkIn = $record;
                    // Look for the next valid Check Out (it may fall on the next day).
                    for ($i = $index + 1; $i < count($recordsList); $i++) {
                        $nextRecord = $recordsList[$i];
                        if ($nextRecord['punch_state'] === 'Check Out' && $nextRecord['datetime'] > $checkIn['datetime']) {
                            $checkOut = $nextRecord;

                            // Compute the total time difference.
                            $interval = $checkIn['datetime']->diff($checkOut['datetime']);
                            // Calculate hours, taking days into account in case the shift crosses midnight.
                            $totalHours = $interval->h + ($interval->days * 24);
                            $minutes = $interval->i;
                            $totalTime = sprintf("%02d:%02d", $totalHours, $minutes);

                            // Determine the weekday from the Check In date.
                            $weekday = date('l', strtotime($checkIn['att_date']));

                            // Build the report entry.
                            $report[] = [
                                "emp_code" => $checkIn['emp_code'] ?? "",
                                "first_name" => $checkIn['first_name'] ?? "",
                                "last_name" => $checkIn['last_name'] ?? "",
                                "nick_name" => $checkIn['nick_name'] ?? "",
                                "gender" => $checkIn['gender'] ?? "",
                                "dept_code" => $checkIn['dept_code'] ?? "",
                                "dept_name" => $checkIn['dept_name'] ?? "",
                                "position_code" => $checkIn['position_code'] ?? "",
                                "company_code" => $checkIn['company_code'] ?? "",
                                "company_name" => $checkIn['company_name'] ?? "",
                                "position_name" => $checkIn['position_name'] ?? "",
                                "att_date" => $checkIn['att_date'] ?? "",
                                "weekday" => $weekday,
                                "check_in" => $checkIn['punch_time'] ?? "",
                                "check_out" => $checkOut['punch_time'] ?? "",
                                "total_time" => $totalTime
                            ];
                            break 2;  // Stop further processing for this employee once a valid pair is found.
                        }
                    }
                }
            }
        }

        // Sort the final report array by att_date.
        usort($report, function ($a, $b) {
            return strcmp($a['att_date'], $b['att_date']);
        });

        foreach ($report as &$record) {
            $this->computerSalary($record);
            //$record['computed_amount'] = $record['hours_worked'] * $record['hourly_rate'];
        }

        return $report;
    }

    public function generateMonthlyDTRReport($records, $date): array
    {
        // Determine the start and end of the month based on the given date.
        $monthStart = date("Y-m-01", strtotime($date));
        $monthEnd = date("Y-m-t", strtotime($date)); // "t" gives the number of days in the month

        // Group records by employee (using emp_code) and add a DateTime property for proper sorting.
        $employeeRecords = [];
        foreach ($records as $record) {
            $empCode = $record['emp_code'];
            // Create a DateTime object from att_date and punch_time.
            $record['datetime'] = DateTime::createFromFormat('Y-m-d H:i', $record['att_date'] . ' ' . $record['punch_time']);
            $employeeRecords[$empCode][] = $record;
        }

        // Sort each employee's records chronologically.
        foreach ($employeeRecords as $empCode => &$recordsList) {
            usort($recordsList, function ($a, $b) {
                return $a['datetime'] <=> $b['datetime'];
            });
        }
        unset($recordsList); // break the reference

        $report = [];

        // Process each employee's records.
        foreach ($employeeRecords as $empCode => $recordsList) {
            $totalMinutes = 0;
            $firstCheckInDT = null;
            $lastCheckOutDT = null;
            $firstCheckInStr = "";
            $lastCheckOutStr = "";

            $count = count($recordsList);
            // Iterate over all records.
            for ($i = 0; $i < $count; $i++) {
                $record = $recordsList[$i];
                // Only consider a Check In if its att_date is within the month.
                if ($record['punch_state'] === "Check In"
                    && $record['att_date'] >= $monthStart
                    && $record['att_date'] <= $monthEnd) {
                    $checkIn = $record;
                    // Look for the next Check Out record (even if it falls on the following day).
                    for ($j = $i + 1; $j < $count; $j++) {
                        $nextRecord = $recordsList[$j];
                        if ($nextRecord['punch_state'] === "Check Out"
                            && $nextRecord['datetime'] > $checkIn['datetime']) {
                            $checkOut = $nextRecord;

                            // Compute the duration in minutes for this pair.
                            $durationSeconds = $checkOut['datetime']->getTimestamp() - $checkIn['datetime']->getTimestamp();
                            $durationMinutes = floor($durationSeconds / 60);
                            $totalMinutes += $durationMinutes;

                            // Record the earliest Check In (with date) among all pairs.
                            if ($firstCheckInDT === null || $checkIn['datetime'] < $firstCheckInDT) {
                                $firstCheckInDT = $checkIn['datetime'];
                                $firstCheckInStr = $checkIn['att_date'] . " " . $checkIn['punch_time'];
                            }

                            // Record the latest Check Out (with date) among all pairs.
                            if ($lastCheckOutDT === null || $checkOut['datetime'] > $lastCheckOutDT) {
                                $lastCheckOutDT = $checkOut['datetime'];
                                $lastCheckOutStr = $checkOut['att_date'] . " " . $checkOut['punch_time'];
                            }

                            // Once a valid pair is found for this Check In, break out of the inner loop.
                            break;
                        }
                    }
                }
            }

            // If at least one valid pair was found for this employee, add an entry to the report.
            if ($firstCheckInDT !== null) {
                // Convert the total minutes into a "HH:MM" formatted string.
                $hours = floor($totalMinutes / 60);
                $minutes = $totalMinutes % 60;
                $totalTimeStr = sprintf("%02d:%02d", $hours, $minutes);

                // Prepare a month label (e.g., "2025-02").
                $monthLabel = date("Y-m", strtotime($monthStart));

                // Use one of the employee's records (here the first record) to fill in employee details.
                $employeeData = $recordsList[0];
                $report[] = [
                    "emp_code" => $employeeData['emp_code'] ?? "",
                    "first_name" => $employeeData['first_name'] ?? "",
                    "last_name" => $employeeData['last_name'] ?? "",
                    "nick_name" => $employeeData['nick_name'] ?? "",
                    "gender" => $employeeData['gender'] ?? "",
                    "dept_code" => $employeeData['dept_code'] ?? "",
                    "dept_name" => $employeeData['dept_name'] ?? "",
                    "position_code" => $employeeData['position_code'] ?? "",
                    "company_code" => $employeeData['company_code'] ?? "",
                    "company_name" => $employeeData['company_name'] ?? "",
                    "position_name" => $employeeData['position_name'] ?? "",
                    "month" => $monthLabel,
                    "first_check_in" => $firstCheckInStr,
                    "last_check_out" => $lastCheckOutStr,
                    "total_time" => $totalTimeStr
                ];
            }
        }

        // Optionally, sort the report by emp_code (or any other field).
        usort($report, function ($a, $b) {
            return strcmp($a['emp_code'], $b['emp_code']);
        });

        return $report;
    }

    /**
     * @throws Exception
     */
    public function computerSalary(&$record): void
    {
        if (empty($record['emp_code'])) {
            $record['hourly_rate'] = number_format(0, 2);
            $record['total_comp'] = number_format(0, 2);
            $record['currency'] = null;
        } else {
            $connection = $this->getEntityManager()->getConnection();

            $sql = "
                SELECT s.*
                FROM hs_hr_employee e
                LEFT JOIN hs_hr_emp_basicsalary s ON e.emp_number = s.emp_number
                LEFT JOIN hs_hr_payperiod p ON s.payperiod_code = p.payperiod_code
                WHERE JSON_UNQUOTE(JSON_EXTRACT(e.extra_data, '$.zk_emp_code')) = :empCode
                AND p.payperiod_name = 'Hourly'
                AND e.org_id = :orgId
            ";


            $stmt = $connection->prepare($sql);
            $stmt->bindValue('empCode', $record['emp_code']);
            $stmt->bindValue('orgId', $this->getAuthUser()->getOrgId());
            $result = $stmt->executeQuery(); // Fix: Use executeQuery() for SELECT statements

            $employees = $result->fetchAllAssociative(); // Fetch all results as an associative array

            $totalSalary = array_sum(array_column($employees, 'ebsal_basic_salary'));
            $record['hourly_rate'] = number_format($totalSalary, 2);
            $record['total_comp'] = $this->calculateCompensation($totalSalary, $record['total_time']);
            $record['currency'] = $employees[0]['currency_id'] ?? null;
        }
    }

    function calculateCompensation($hourlyRate, $timeWorked): string
    {
        $hourlyRate = (float)$hourlyRate;

        // Check if $timeWorked is a string and contains a colon (indicating "HH:MM" format)
        if (is_string($timeWorked) && str_contains($timeWorked, ':')) {
            list($hours, $minutes) = explode(':', $timeWorked);
            $decimalHours = (float)$hours + ((float)$minutes / 60);
        } else {
            // Otherwise assume $timeWorked is already a decimal number (or numeric string)
            $decimalHours = (float)$timeWorked;
        }

        $compensation = $hourlyRate * $decimalHours;

        return number_format($compensation, 2, '.', '');
    }

    public static function filterColumns(array $columns, array $data): array
    {
        return array_map(function ($row) use ($columns) {
            $filteredRow = array_intersect_key($row, array_flip($columns));

            // Rename keys to their mapped values
            return array_combine(
                array_map(fn($key) => self::COLUMN_MAPPINGS[$key] ?? $key, array_keys($filteredRow)),
                array_values($filteredRow)
            );
        }, $data);
    }

}