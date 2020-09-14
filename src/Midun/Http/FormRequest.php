<?php

namespace Midun\Http;

use Midun\Http\Exceptions\AppException;
use Midun\Http\Validation\ValidationException;
use Midun\Http\Exceptions\UnauthorizedException;

abstract class FormRequest extends Request
{
    /**
     * Overriding parent __construct method
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Abstract function for overriding
     * verify authorize
     *
     * @return boolean
     */
    abstract public function authorize(): bool;

    /**
     * Abstract function for overriding
     * setting up rules
     *
     * @return array
     */
    abstract public function rules(): array;

    /**
     * Abstract function for overriding
     * setting up messages
     *
     * @return array
     */
    abstract public function messages(): array;

    /**
     * Execute verify request method
     *
     * @return void
     * 
     * @throws ValidationException
     */
    public function executeValidate(): void
    {
        if (!$this->authorize()) {
            throw new UnauthorizedException();
        }

        $validator = app()->make('validator');

        $validator->makeValidate(
            $this,
            $this->rules(),
            $this->messages()
        );

        if ($validator->isFailed()) {
            throw new ValidationException($validator->errors());
        }
    }
}
