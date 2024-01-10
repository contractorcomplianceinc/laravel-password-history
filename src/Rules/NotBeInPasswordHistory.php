<?php

namespace Imanghafoori\PasswordHistory\Rules;

use Illuminate\Contracts\Validation\Rule;
use Imanghafoori\PasswordHistory\Facades\PasswordHistoryManager;

class NotBeInPasswordHistory implements Rule
{
    protected $user;

    private $depth;

    public function __construct($user, $depth =  null)
    {
        $this->user = $user;
        $this->depth = $depth;
    }

    public static function ofUser($user, $depth = null)
    {
        return new static($user, $depth);
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     *
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $depth = $this->depth ?: config('password_history.check_depth');

        return ! PasswordHistoryManager::isInHistoryOfUser($value, $this->user, $depth);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('passwordHistory.password_used');
    }
}
