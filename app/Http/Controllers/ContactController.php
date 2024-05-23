<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Models\Contact;

class ContactController extends Controller
{
    public function contract(Request $request)
    {
        $data = $request->all();
        $contact = Contact::create($data);
        return response()->json(['data' => $data]);
    
        $data = ['name' => 'John Doe'];
        Mail::send('emails.check', $data, function ($message) {
            $message->to('recipient@example.com', 'Recipient Name')
                    ->subject('Welcome to Our Website');
            $message->from('from@example.com', 'Example');
        });

        return response()->json(['message' => 'Mail sent successfully']);
    }

    public function index(Request $request)
    {
        $query = $request->input('search');

        $contacts = Contact::
        orWhere('email', 'like', "%$query%")
        ->orWhere('phone', 'like', "%$query%")
        ->orWhere('name', 'like', "%$query%")
        ->orderBy('id', 'DESC')
        ->paginate(10);

        return response()->json(['contacts' => $contacts]);
    }

    public function reply($id)
    {
        $contact = Contact::find($id);
        return response()->json(['contact' => $contact]);
    }

    public function sendReply(Request $request, $id)
    {
        $data = $request->all();
        
        return response()->json([
            'content_reply' => $data['content'],
            'status_contact' => $data['status']
        ]);
    }
}
