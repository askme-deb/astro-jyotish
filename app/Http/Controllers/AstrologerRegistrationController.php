<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Services\AstrologerApiService;

class AstrologerRegistrationController extends Controller
{
    public function showForm()
    {
        return view('astrologers.register');
    }

    /**
     * Handle astrologer registration POST request.
     */
    public function store(Request $request, AstrologerApiService $astrologerApiService)
    {
        // Accepts payload as sent by JS (see user message)
        $data = [
            'first_name'        => $request->input('first_name'),
            'last_name'         => $request->input('last_name'),
            'display_name'      => $request->input('display_name'),
            'short_intro'       => $request->input('short_intro'),
            'details_bio'       => $request->input('details_bio'),
            'address'           => $request->input('address'),
            'state_id'          => $request->input('state_id'),
            'city_id'           => $request->input('city_id'),
            'pin_code'          => $request->input('pin_code'),
            'consultation_mode' => $request->input('consultation_mode'),
            'ac_holder_name'    => $request->input('ac_holder_name'),
            'bank_name'         => $request->input('bank_name'),
            'ac_number'         => $request->input('ac_number'),
            'ifsc_code'         => $request->input('ifsc_code'),
            'branch_name'       => $request->input('branch_name'),
            'upi_id'            => $request->input('upi_id'),
            'applicant_name'    => $request->input('applicant_name'),
            'email'             => $request->input('email'),
            'mobile_no'         => $request->input('mobile_no'),
            'password'          => $request->input('password'),
            'experience'        => $request->input('experience'),
            'rate'              => $request->input('rate'),
            'duration'          => $request->input('duration'),
            'aadhar_number'     => $request->input('aadhar_number'),
            'pan_number'        => $request->input('pan_number'),
        ];

        // Arrays (languages, skills)
        $data['languages'] = $request->input('languages', []);
        $data['skills']    = $request->input('skills', []);

        // Education (array)
        $data['education'] = [];
        $i = 0;
        while ($request->has("education.$i.degree") || $request->hasFile("education.$i.document")) {
            $data['education'][$i] = [
                'degree'      => $request->input("education.$i.degree"),
                'institution' => $request->input("education.$i.institution"),
                'year'        => $request->input("education.$i.year"),
            ];
            if ($request->hasFile("education.$i.document")) {
                $data['education'][$i]['document'] = $request->file("education.$i.document");
            }
            $i++;
        }

        // Availabilities (array)
        $data['availabilities'] = [];
        $i = 0;
        while ($request->has("availabilities.$i.day")) {
            $avail = [
                'day' => $request->input("availabilities.$i.day"),
                'slots' => []
            ];
            $j = 0;
            while ($request->has("availabilities.$i.slots.$j.from") || $request->has("availabilities.$i.slots.$j.to")) {
                $avail['slots'][] = [
                    'from' => $request->input("availabilities.$i.slots.$j.from"),
                    'to'   => $request->input("availabilities.$i.slots.$j.to"),
                ];
                $j++;
            }
            $data['availabilities'][] = $avail;
            $i++;
        }

        
        // File fields (photo, aadhar, pan, signature)
        $fileMap = [
            'astrologer_photo' => 'photo',
            'astrologer_aadhar_document' => 'aadhar_document',
            'astrologer_pan_document' => 'pan_document',
            // Add signature if you have a file input for it, e.g. 'astrologer_signature' => 'signature'
        ];
        foreach ($fileMap as $formField => $apiField) {
            if ($request->hasFile($formField)) {
                $data[$apiField] = $request->file($formField);
            }
        }




        \Log::info('Astrologer registration payload', [
            'payload' => $data,
            'files' => array_map(function($f) {
                if ($f instanceof \Illuminate\Http\UploadedFile) {
                    return $f->getClientOriginalName();
                }
                return $f;
            }, array_filter($data, function($v) { return $v instanceof \Illuminate\Http\UploadedFile; }))
        ]);

        $result = $astrologerApiService->createAstrologer($data);

        if (!$result) {
            return response()->json(['error' => 'Registration failed. Please try again later.'], 500);
        }

        return response()->json(['message' => 'Registration successful!', 'data' => $result], 201);
    }
}
