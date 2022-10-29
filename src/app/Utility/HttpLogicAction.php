<?php
namespace App\Utility;

use Illuminate\Support\Facades\DB;
use App\Exceptions\ApiException;
use Symfony\Component\HttpFoundation\Response as ResponseStatusCode;
use App\Utility\HttpResponse as HttpResponseUtility;

class HttpLogicAction
{

    public function __construct(private $customResponseStatusCode = ResponseStatusCode::HTTP_OK)
    {

    }

    /**
     * Execute controller action logic inside try/catch structure with transaction
     * @param function $actionLogic
     * @return \Illuminate\Http\JsonResponse
     */
    public function executeActionWithDml($actionLogic): \Illuminate\Http\JsonResponse {
        DB::beginTransaction();

        try {
            $responseData = $actionLogic();
            DB::commit();
            return (new HttpResponseUtility($responseData, '', $this->customResponseStatusCode))->getJsonResponse();

        } catch (ApiException $apiExcep) {
            DB::rollBack();
            return (new HttpResponseUtility([], $apiExcep->getMessage(), $apiExcep->getCode()))->getJsonResponse();
        } catch (\Exception $e) {
            DB::rollBack();
            return (new HttpResponseUtility([], 'An error has ocurred'))->getJsonResponse();
        }
    }
}
