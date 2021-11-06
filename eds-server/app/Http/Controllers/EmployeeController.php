<?php

namespace App\Http\Controllers;

use App\Enums\AppConstants;
use App\Enums\HttpResponseStatus;
use App\Enums\UserTypeValues;
use App\Exceptions\FailedToProccessException;
use App\Rules\ValidZuluDate;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Saurovh\EdsPhpSdk\Object\Employee;

class EmployeeController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function get()
    {
        $response = (new Employee())->get();

        $json = [];
        if ($response->isSuccessful()) {
            $data = $response->getData();

            $ids                    = array_column($data, 'id');
            $localUserCollectionMap = User::fetchByEmployeeIds($ids);
            foreach ($data as $item) {
                $localUser = $localUserCollectionMap->get($item['id']);
                if ($localUser) {
                    $json[] = array_merge($item, [
                        'type'   => $localUser->type,
                        'userId' => $localUser->id
                    ]);
                }
            }
        }

        return response()->json($json);
    }

    public function create(Request $request)
    {
        $this->validate($request, [
            'name'        => 'required|string|between:3,255',
            'phone'       => 'required|string|between:3,20',
            'email'       => 'required|email|unique:' . (new User())->getTable(),
            'joiningDate' => ['required', 'string', new ValidZuluDate()],
            'type'        => 'required|integer|in:' . implode(',', UserTypeValues::getInstance()->getValues()),

            // should also pass password_confirmation field with the confirmed rule
            'password'    => 'required|string|min:5|confirmed'
        ]);

        $employeeObj = new Employee(null, $this->getEdsApi());

        $response = $employeeObj->create([
            'name'        => $request->get('name'),
            'phone'       => $request->get('phone'),
            'email'       => $request->get('email'),
            'joiningDate' => $request->get('joiningDate')
        ]);

        if ($response->isSuccessful()) {
            $json                   = $response->getData();
            $localUser              = new User();
            $localUser->name        = $json['name'];
            $localUser->type        = $request->get('type');
            $localUser->email       = $json['email'];
            $localUser->employee_id = $json['id'];
            $localUser->password    = app('hash')->make($request->get('password'));
            if ($localUser->save()) {
                $json['userId'] = $localUser->getKey();
                $json['type']   = $localUser->type;
            } else {
                throw new FailedToProccessException("Failed to proccess the request!");
            }
        } else {
            if ($response->hasException()) {
                Log::critical($response->getException()->getMessage());
            }
            throw new FailedToProccessException("Failed to proccess the request!");
        }

        return response()->json($json, HttpResponseStatus::HTTP_CREATED);
    }

    public function update($id, Request $request)
    {
        // TODO
    }
}
