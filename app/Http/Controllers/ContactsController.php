<?php

namespace App\Http\Controllers;

use App\Http\Resources\ContactResource;
use App\Models\Contact;
use App\Enums\LifeCycleStagesEnum;
use App\Services\HubSpotService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Enum;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Str;

class ContactsController extends Controller
{
    public function __construct(private HubSpotService $hubSpotService)
    {
        $this->hubSpotService = $hubSpotService;
    }
    public function index()
    {
        return view('contacts.list');
    }

    public function getContacts(Request $request)
    {
        try {
            $contacts = Contact::query();

            if ($request->ajax()) {

                // Using Yajra Datatable for generating data datatable
                return Datatables::of($contacts)
                    ->addColumn('actions', function ($row) {
                        $editBtn = '<button action-href="' . route('contacts.update', $row['id']) . '" data-contact-id="' . $row['id'] . '" class="btn btn-sm btn-primary m-1 contact-edit-btn" data-toggle="modal" data-target="#editContactModal"><i class="fa fa-edit"></i></button>';
                        $deletebtn = '<button action-href="' . route('contacts.destroy', $row['id']) . '" class="btn btn-sm btn-danger m-1 contact-delete-btn"><i class="fa fa-trash"></i></button>';
                        return $editBtn . $deletebtn;
                    })
                    ->editColumn('created_at', function ($row) {
                        return (new \DateTime($row['created_at']))->setTimezone(new \DateTimeZone(config('app.timezone')))->format('d-m-Y h:i:s A');
                    })->editColumn('updated_at', function ($row) {
                        return (new \DateTime($row['updated_at']))->setTimezone(new \DateTimeZone(config('app.timezone')))->format('d-m-Y h:i:s A');
                    })->addColumn('lifecyclestage_label', function ($row) {
                        if($row['lifecyclestage'] == null) return '';
                        return Str::of(LifeCycleStagesEnum::from($row['lifecyclestage'])->name)
                        ->replace('_', ' ')
                        ->title();                    
                    })
                    ->rawColumns(['actions' => 'actions'])
                    ->make(true);
            }
        } catch (Exception $e) {
            $this->handleException($e);
        }
    }

    public function destroy($id){
        try{
            $contract = Contact::find($id);
            $contract->delete();

            $this->hubSpotService->deleteContact($contract->hubspot_object_id);
            return response()->json(['message' => 'Contact deleted successfully'], 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try{

            Validator::make($request->all(), [
                'firstname' => 'required',
                'lastname' => 'nullable',
                'email' => 'required|email|unique:contacts,email',
                'phone' => 'nullable|unique:contacts,phone',
                'website' => 'nullable|string|max:255',
                'lifecyclestage' => ['nullable', new Enum(LifeCycleStagesEnum::class)],
            ])->setAttributeNames(
                [
                    'firstname' => 'First Name',
                    'lastname' => 'Last Name',
                    'email' => 'Email',
                    'phone' => 'Phone',
                    'website' => 'Website',
                    'lifecyclestage' => 'Lifecycle Stage',
                ]
            )->validate();

            $contact = Contact::create($request->all());

            // Handles creating contact in hubspot via hubSpotService via hubSpot API
            $apiResponse = $this->hubSpotService->createContact($request->all());

            // After creating contact in hubspot, update hubspot_object_id in contact for furthur use
            $contact->hubspot_object_id = $apiResponse->id;
            $contact->save();
            
            return response()->json(['message' => 'Contact created successfully'], 201);

        } catch (Exception $e) {
            return $this->handleException($e);
        }
    }


    public function update(Request $request,String $id)
    {
        try{

            Validator::make($request->all(), [
                'firstname' => 'required',
                'lastname' => 'nullable',
                'email' => 'required|email|unique:contacts,email,' . $id.',id',
                'phone' => 'nullable|unique:contacts,phone,' . $id.',id',
                'website' => 'nullable|string|max:255',
                'lifecyclestage' => ['nullable', new Enum(LifeCycleStagesEnum::class)],
            ])->setAttributeNames(
                [
                    'firstname' => 'First Name',
                    'lastname' => 'Last Name',
                    'email' => 'Email',
                    'phone' => 'Phone',
                    'website' => 'Website',
                    'lifecyclestage' => 'Lifecycle Stage',
                ]
            )->validate();

            $contact = Contact::findOrFail($id);
            $contact->update($request->all());

            // Handles updating contact in hubspot via hubSpotService via hubSpot API
            $this->hubSpotService->updateOrCreateContact($contact->hubspot_object_id, $request);
            
            return response()->json(['message' => 'Contact updated successfully'], 200);

        } catch (Exception $e) {
            return $this->handleException($e);
        }
    }


    public function sync()
    {
        try{

            $this->hubSpotService->syncContacts();
            return response()->json(['message' => 'Contacts synced successfully'], 200);

        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
