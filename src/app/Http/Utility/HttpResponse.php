<?php
namespace App\Http\Utility;
use Symfony\Component\HttpFoundation\Response as ResponseStatusCode;
class HttpResponse {

    private $SUCCESS = 'success';
    public $FAILED = 'failed';
    private $status;
    private $data;
    private $message;
    private $responseStatusCode;
    private $SUCCESS_STATUSES = [ResponseStatusCode::HTTP_OK, ResponseStatusCode::HTTP_CREATED, ResponseStatusCode::HTTP_CREATED, ResponseStatusCode::HTTP_NO_CONTENT];

    /**
     * Constructor of the class
     * @param array
     * @param string
     */
    public function __construct($data = [], $message = '', $responseStatusCodeAlt = '')
    {
        $this->responseStatusCode = !empty($responseStatusCodeAlt) ? $responseStatusCodeAlt : (empty($message) ? ResponseStatusCode::HTTP_OK : ResponseStatusCode::HTTP_INTERNAL_SERVER_ERROR);
        $this->status = empty($message) ? $this->SUCCESS : $this->FAILED;
        $this->data = $data;
        $this->message = $message;
    }

    /**
     * Get response in json format
     */
    public function getJsonResponse(): \Illuminate\Http\JsonResponse {
        return response()->json(
            array_merge([
                'status' => $this->status,
            ],
            in_array($this->responseStatusCode, $this->SUCCESS_STATUSES)
            ? ['data' => $this->data]
            : ['message' => $this->message]),
            $this->responseStatusCode
        );
    }
}