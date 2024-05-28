<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Models\Contact;
use App\Models\ContactReply;
use App\Http\Requests\ContactReplyRequest;

class ContactController extends Controller
{
    public function contract(Request $request)
    {
        try {
            $data    = $request->all();
            $contact = Contact::create($data);

            Mail::send('emails.contact', $data, function ($message) {
                $message->to('toby@cybridge.jp', 'Recipient Name')
                        ->subject('Welcome to Our Website');
                $message->from('toby@cybridge.jp', 'Example');
            });

            return response()->json(['message' => 'Mail sent successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function index(Request $request)
    {
        $query = $request->input('search');

        $contacts = Contact::orWhere('email', 'like', "%$query%")
            ->orWhere('phone', 'like', "%$query%")
            ->orWhere('name', 'like', "%$query%")
            ->orderBy('id', 'DESC')
            ->paginate(10);

        return response()->json(['contacts' => $contacts]);
    }

    public function reply($id)
    {
        $contact = Contact::find($id);
        $contact_reply = ContactReply::where('contact_id', $id)->get();
        return response()->json([
            'contact' => $contact,
            'contact_reply' => $contact_reply,
        ]);
    }

    public function sendReply(ContactReplyRequest $request, $id)
    {
        $data       = $request->all();
        $email_form = env("EMAIL_FOR_ADMIN", "toby@cybridge.jp");
        try {
            Contact::where('id', $id)->update([
                'status' => $data['status'],
            ]);
            
            if($data['reply']) {
                $data_reply['contact_id'] = $id;
                $data_reply['content']    = $data['reply'];
                $data_reply['mail_form']  = $email_form;

                $data_contact = Contact::find($id);
                $email_to     = $data_contact->email;
                
                Mail::send('emails.reply_contact', $data, function ($message) use ($email_form, $email_to){
                    $message->to($email_to, 'Recipient Name')
                    ->subject('Email phản Hồi - TobyBlog');
                    $message->from($email_form, 'TobyBlog');
                });
            }

            return response()->json([
                'content_reply'  => $data['reply'],
                'status_contact' => $data['status'],
                'email_form'     => $email_form,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e
            ], 500);
        }
    }
}
