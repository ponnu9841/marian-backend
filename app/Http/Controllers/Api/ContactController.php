<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['getContact']]);
    }

    public function getContact()
    {
        $contact = Contact::orderBy('created_at', 'desc')->first();

        return response([
            'data' => $contact
        ]);
    }

    public function createContact(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'location' => 'required|string',
                'contactOne' => 'required|string',
                'contactTwo' => 'nullable|string',
                'emailOne' => 'required|string',
                'emailTwo' => 'nullable|string',
            ]);
            if (!$validator->passes()) {
                return response([
                    'message' => 'Invalid Request',
                    'errors' => $validator->errors()
                ], 404);
            }
            $contact = new Contact();
            $contact->location = $request->location;
            $contact->contactno_one = $request->contactOne;
            $contact->contactno_two = $request->contactTwo;
            $contact->email_one = $request->emailOne;
            $contact->email_two = $request->emailTwo;

            $contact->save();

            return response([
                'message' => 'Contact Added Successfully',
                'data' => $contact,
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            return response([
                'status' => false,
                'message' => $th
            ], 500);
        }
    }

    public function updateContact(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|string',
            'location' => 'required|string',
            'contactOne' => 'required|string',
            'contactTwo' => 'nullable|string',
            'emailOne' => 'required|string',
            'emailTwo' => 'nullable|string',
        ]);
        if (!$validator->passes()) {
            return response([
                'message' => 'Invalid Request',
                'errors' => $validator->errors(),
                'request' => $request->id
            ], 404);
        }
        try {
            $contact = Contact::find($request->id);
            $contact->location = $request->location;
            $contact->contactno_one = $request->contactOne;
            $contact->contactno_two = $request->contactTwo;
            $contact->email_one = $request->emailOne;
            $contact->email_two = $request->emailTwo;

            $contact->save();

            return response([
                'message' => 'Contact Updated Successfully',
                'data' => $contact,
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            return response([
                'status' => false,
                'message' => $th
            ], 500);
        }
    }
}
