<?php

namespace OrangeHRM\ZkTeco\DownloadFormats;

use OrangeHRM\Maintenance\DownloadFormats\DownloadFormat;

class CsvDownloadFormat extends DownloadFormat
{
    /**
     * Converts an array of values into a CSV formatted string.
     *
     * @param $values
     * @return string
     */
    public function getFormattedString($values): string
    {
        if (empty($values)) {
            return '';
        }

        // Open memory stream
        $output = fopen('php://temp', 'r+');

        // Get headers from the first row keys
        fputcsv($output, array_keys(reset($values)));

        // Add rows to CSV
        foreach ($values as $row) {
            fputcsv($output, $row);
        }

        // Rewind memory stream
        rewind($output);

        // Get CSV as string
        $csvContent = stream_get_contents($output);
        fclose($output);

        return $csvContent;
    }


    public function getDownloadFileName($fileName): string
    {
        return $fileName . '.csv';
    }
}
