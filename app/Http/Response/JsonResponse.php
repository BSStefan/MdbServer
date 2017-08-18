<?php

namespace App\Http\Response;

use Illuminate\Contracts\Support\Arrayable;

class JsonResponse implements Arrayable
{

    protected $data;
    protected $message;
    protected $error;
    protected $code;

    public function __construct($data, $message = '', $code = 200, $error = false)
    {
        $this->data    = empty($data) ? null : $data;
        $this->message = empty($message) ? null : $message;
        $this->code    = $code;
        $this->error   = $code != 200 || $error ? true : false;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return ['data' => $this->data, 'message' => $this->message, 'error' => $this->error, 'code' => $this->code];
    }
}