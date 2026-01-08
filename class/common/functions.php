<?php
/**
 * Helper functions for date conversion and formatting
 */

function PhpDateToMySqlDate($date): string
{
    if (empty($date)) {
        return '';
    }
    // Handle DD/MM/YYYY format
    if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $date, $matches)) {
        return $matches[3] . '-' . $matches[2] . '-' . $matches[1];
    }
    // Already in YYYY-MM-DD format
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
        return $date;
    }
    return '';
}

function MySqlDateToPhpDate($date): string
{
    if (empty($date) || $date === '0000-00-00') {
        return '';
    }
    // Convert YYYY-MM-DD to DD/MM/YYYY
    if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $date, $matches)) {
        return $matches[3] . '/' . $matches[2] . '/' . $matches[1];
    }
    return $date;
}

function formatCurrency($amount): string
{
    return number_format($amount, 2, '.', ',');
}

function formatDate($date): string
{
    return MySqlDateToPhpDate($date);
}
