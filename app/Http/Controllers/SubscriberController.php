<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubscriberRequest;
use App\Http\Resources\SubscriberResource;
use App\Models\Subscriber;
use App\Services\MailerLiteService;
use Illuminate\Http\Request;
use MailerLite\MailerLite;

class SubscriberController extends Controller
{
    private $client;

    public function __construct(private readonly MailerLiteService $mailer_lite)
    {
        $this->client = $this->mailer_lite->getClient();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //Email filter seems not supported in API
        //API ERROR: Requested filter(s) `source` are not allowed. Allowed filter(s) are `group, automation, status, form, site, page, import, post`.

        /*$payload = [
            'filter' => $request->filter,
            'limit' => $request->limit ?? 10,
            'cursor' => $request->cursor
        ];

        $payload = $this->client->subscribers->get($payload);

        $subscribers = $payload['body']['data'];

        $meta = $payload['body']['meta'];

        return response()->json(['subscribers' => $subscribers, 'meta' => $meta]);*/

        $subscribers = Subscriber::orderByDesc('subscribed_at')->filterEmail($request->search);

        $subscribers = $subscribers->paginate($request->per_page);

        return SubscriberResource::collection($subscribers);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SubscriberRequest $request)
    {
        try {

            $id = $request->id;

            $payload = [
                'fields' => $request->only(['name', 'country'])
            ];

            if (empty($id)) {

                $payload['email'] = $request->email;
            }

            if (!empty($id)) {
                $subscriber = $this->client->subscribers->update($id, $payload);
            } else {
                $subscriber = $this->client->subscribers->create($payload);
            }

            if (!empty($subscriber['body']['data'])) {

                $payload = $subscriber['body']['data'];

                Subscriber::updateOrCreate(['id' => $payload['id']], [
                    'email' =>  $payload['email'],
                    'name' => $payload['fields']['name'],
                    'country' => $payload['fields']['country'],
                    'subscribed_at' => $payload['subscribed_at']
                ]);

                $type = $id ? "updated" : "created";

                return response()->json(['subscriber' => $subscriber['body']['data'], 'message' => "Subscriber $type successfully!"]);
            } else {

                return response()->json(['subscriber' => null, 'message' => 'Error while creating subscriber!']);
            }

        } catch (\Exception $exception) {

            if ($exception->getCode() === 404) {
                $message = "Subscriber not found";
            } else {
                report($exception);
                $message = $exception->getMessage();
            }

            return response()->json(['subscriber' => null, 'message' => $message], 422);
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $this->client->subscribers->delete($id);

            Subscriber::find($id)->delete();

            return response()->json(['message' => 'Subscriber deleted']);

        } catch (\Exception $exception) {

            if ($exception->getCode() === 404) {
                $message = "Subscriber not found";
            } else {

                report($exception);
                $message = $exception->getMessage();
            }

            return response()->json(['subscriber' => null, 'message' => $message], $exception->getCode());
        }
    }
}
