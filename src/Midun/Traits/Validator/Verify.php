<?php

namespace Midun\Traits\Validator;

use DB;
use Midun\Services\File;

trait Verify
{
    /**
     * Validate min type
     * 
     * @param mixed $value
     * @param array $rules
     * @param float $min
     * 
     * @return void
     */
    public function min($value, array $rules, float $min): void
    {
        switch (true) {
            case in_array('number', $rules):
                if ($min > (int) $value) {
                    $this->pushErrorMessage($this->current, $this->buildErrorMessage([$this->current, 'number'], __FUNCTION__, [
                        'min' => $min
                    ]));
                }
                break;
            case in_array('file', $rules) || in_array('video', $rules) || in_array('audio', $rules) || in_array('image', $rules):
                $sizeMb = $value->size / 1000 / 1000;
                if ($min > $sizeMb) {
                    $this->pushErrorMessage($this->current, $this->buildErrorMessage([$this->current, 'file'], __FUNCTION__, [
                        'min' => $min
                    ]));
                }
                break;
            case 'string':
            default:
                if (strlen((string) $value) < $min) {
                    $this->pushErrorMessage($this->current, $this->buildErrorMessage([$this->current, 'string'], __FUNCTION__, [
                        'min' => $min
                    ]));
                }
        }
    }

    /**
     * Validate max type
     * 
     * @param mixed $value
     * @param array $rules
     * @param float $max
     * 
     * @return void
     */
    public function max($value, array $rules, float $max): void
    {
        switch (true) {
            case in_array('number', $rules):
                if ($max < (int) $value) {
                    $this->pushErrorMessage($this->current, $this->buildErrorMessage([$this->current, 'number'], __FUNCTION__, [
                        'max' => $max
                    ]));
                }
                break;
            case in_array('file', $rules) || in_array('video', $rules) || in_array('audio', $rules) || in_array('image', $rules):
                $sizeMb = $value->size / 1000 / 1000;
                if ($max < $sizeMb) {
                    $this->pushErrorMessage($this->current, $this->buildErrorMessage([$this->current, 'file'], __FUNCTION__, [
                        'max' => $max
                    ]));
                }
                break;
            case 'string':
            default:
                if (strlen((string) $value) > $max) {
                    $this->pushErrorMessage($this->current, $this->buildErrorMessage([$this->current, 'string'], __FUNCTION__, [
                        'max' => $max
                    ]));
                }
        }
    }

    /**
     * Validate number type
     * 
     * @param mixed $value
     * 
     * @return void
     */
    public function number($value): void
    {
        if (!is_numeric($value)) {
            $this->pushErrorMessage($this->current, $this->buildErrorMessage($this->current, __FUNCTION__));
        }
    }

    /**
     * Validate string type
     * 
     * @param mixed $value
     * 
     * @return void
     */
    public function string($value): void
    {
        if (!is_string($value)) {
            $this->pushErrorMessage($this->current, $this->buildErrorMessage($this->current, __FUNCTION__));
        }
    }

    /**
     * Validate required type
     * 
     * @param mixed $value
     * 
     * @return void
     */
    public function required($value): void
    {
        if (is_null($value) || empty($value)) {
            $this->pushErrorMessage($this->current, $this->buildErrorMessage($this->current, __FUNCTION__));
        }
    }

    /**
     * Validate file type
     * 
     * @param mixed $value
     * 
     * @return void
     */
    public function file($value): void
    {
        if (!$value instanceof File) {
            $this->pushErrorMessage($this->current, $this->buildErrorMessage($this->current, __FUNCTION__));
        }
    }

    /**
     * Validate image type
     * 
     * @param mixed $value
     * 
     * @return void
     */
    public function image($value): void
    {
        if (!$value instanceof File || strpos($value->type, 'image/') === false) {
            $this->pushErrorMessage($this->current, $this->buildErrorMessage($this->current, __FUNCTION__));
        }
    }

    /**
     * Validate audio type
     * 
     * @param mixed $value
     * 
     * @return void
     */
    public function audio($value): void
    {
        if (!$value instanceof File || strpos($value->type, 'audio/') === true) {
            $this->pushErrorMessage($this->current, $this->buildErrorMessage($this->current, __FUNCTION__));
        }
    }

    /**
     * Validate video type
     * 
     * @param mixed $value
     * 
     * @return void
     */
    public function video($value): void
    {
        if (!$value instanceof File || strpos($value->type, 'video/') === true) {
            $this->pushErrorMessage($this->current, $this->buildErrorMessage($this->current, __FUNCTION__));
        }
    }

    /**
     * Validate email type
     * 
     * @param mixed $value
     * 
     * @return void
     */
    public function email($value): void
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->pushErrorMessage($this->current, $this->buildErrorMessage($this->current, __FUNCTION__));
        }
    }

    /**
     * Validate unique type
     * 
     * @param mixed $value
     * @param string $ruleValue
     * 
     * @return void
     */
    public function unique($value, $ruleValue): void
    {
        list($table, $columnValue) = explode(',', $ruleValue);
        if (strpos($columnValue, ';') !== false) {
            list($column, $keyValue) = explode(';', $columnValue);
        } else {
            $column = $columnValue;
        }

        $table = DB::table($table)->where($column, $value)->first();
        if ($table && isset($keyValue) && $table->$column != $keyValue || $table && !isset($keyValue)) {
            $this->pushErrorMessage($this->current, $this->buildErrorMessage($this->current, __FUNCTION__));
        }
    }

    /**
     * Handle custom rule
     * 
     * @param string $rule
     * 
     * @return void
     */
    public function handleCustomRule($rule): void
    {
        $handle = $this->getCustom($rule);
        if (!$handle($this->passable)) {
            $this->pushErrorMessage($this->current, $this->customMessages[$rule]);
        }
    }
}
