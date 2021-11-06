<?php

namespace App\Http\Controllers;

use App\Exceptions\FailedToProccessException;
use App\Rules\ValidZuluDate;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Saurovh\EdsPhpSdk\Object\EmployeeDuty;

class EmployeeDutyController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function get()
    {
        $employeeDutyObj = new EmployeeDuty();
        $response        = $employeeDutyObj->get();

        /**
         * @var User $user
         */
        $user = Auth::user();
        $json = [];
        if ($response->isSuccessful() && ($responseData = $response->getData())) {
            if (!$user->isAdmin()) {
                $json = array_filter($responseData, function ($item) use ($user) {
                    return $item['employeeId'] == $user->employee_id;
                });
                foreach ($json as $key => $item) {
                    $json[$key]['employeeName'] = $user->name;
                }
            } else {
                $employeeIds            = array_column($responseData, 'employeeId');
                $localUserCollectionMap = User::fetchByEmployeeIds($employeeIds);
                foreach ($responseData as $item) {
                    /**
                     * @var User $localUser
                     */
                    $localUser = $localUserCollectionMap->get($item['employeeId']);
                    if ($localUser) {
                        $json[] = array_merge($item, [
                            'employeeName' => $localUser->name
                        ]);
                    }
                }
            }
        }

        return response()->json($json, $response->getHttpStatusCode());
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function create(Request $request)
    {
        $this->validate($request, [
            'employeeId' => 'required|integer',
            'dutyStart'  => ['required', 'string', new ValidZuluDate()],
            'dutyEnd'    => ['required', 'string', new ValidZuluDate()]
        ]);

        $employeeDutyObj = new EmployeeDuty();
        $response        = $employeeDutyObj->create(
            Arr::only($request->all(), ['employeeId', 'dutyStart', 'dutyEnd'])
        );

        return response()->json($response->getData(), $response->getHttpStatusCode());
    }

    /**
     * @param int     $id
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws FailedToProccessException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update($id, Request $request)
    {
        $this->validate($request, [
            'employeeId' => 'required|integer',
            'dutyStart'  => ['required', 'string', new ValidZuluDate()],
            'dutyEnd'    => ['required', 'string', new ValidZuluDate()]
        ]);

        $employeeDutyObj = new EmployeeDuty();
        $response        = $employeeDutyObj->update((int)$id,
            Arr::only($request->all(), ['employeeId', 'dutyStart', 'dutyEnd'])
        );

        return response()->json($response->getData(), $response->getHttpStatusCode());
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id)
    {
        $employeeDutyObj = new EmployeeDuty();
        $response        = $employeeDutyObj->delete((int)$id);

        return response()->json($response->getData(), $response->getHttpStatusCode());
    }
}
