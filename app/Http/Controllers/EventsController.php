<?php

namespace App\Http\Controllers;

use App\Models\Event as ModelsEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Models\Event;

use Log;

use Laravel\Lumen\Routing\Controller as BaseController;

class EventsController extends BaseController
{
    public function get(Request $request)
    {
        $event = Event::findOrFail( request()->route('id'));
        return response()->json([
            'success' => true,
            'event' => $event
        ]);
    }

    public function create(Request $request)
    {
        $this->validate($request, [
            "name" => 'required|string',
            "tsbegin" => 'required',
        ]);
        $user = $request->user();
        $pic_path = '';
        if ($request->picture && $request->picture_name) {
            $pic_path = Storage::disk('local')->put('example.txt', 'Contents');;
        };
        $event = new Event();
        $event->name = $request->name;
        $event->tsbegin = $request->tsbegin;
        $event->tsend =$request->tsend;
        $event->picture = $pic_path;
        $event->owner_id = $user->id;
        $event->save();
        if (!$event) return response()->json([
            "success" => false,
            "error" => "Event can\'t be created"
        ]);
        return response()->json([
            "success" => true,
            "event" => $event
        ]);
    }

    public function update(Request $request)
    {
        $id = request()->route('id');
        $user = $request->user();
        $event = Event::find($id);
        if (!$event) return response()->json([
            "success" => false,
            "error" => "Event was not found"
        ]);
        if ($user->id != $event->owner_id) return response("Unauthorized", 401);
        $pic_path = '';
        if ($request->hasFile('picture')) {
            $pic_path = $request->picture->store('pictures');
        };
        $event->name = $request->name ?? $event->name;
        $event->tsbegin = $request->tsbegin ?? $event->tsbegin;
        $event->tsend = $request->tsend ?? $event->tsend;
        $event->picture = $pic_path ?? $event->picture;
        $event->save();
        return response()->json([
            "success" => true,
            "event" => $event
        ]);        
    }

    public function delete(Request $request) {
        $id = request()->route('id');
        $event = Event::find($id);
        if (!$event) return response()->json([
            "success" => false,
            "error" => "Event was not found"
        ]);
        $user = $request->user();
        if ($user->id != $event->owner_id) return response("Unauthorized", 401);
        $event->delete();
        return response()->json([
            "success" => true,
            "message" => "The Event was deleted"
        ]);
    }

    public function list(Request $request) {
        $this->validate($request, [
            "orderByName" => "in:desc,asc",
            "orderByDate" => "in:desc,asc"
        ]);
        $events = Event::orderBy('tsbegin', 'desc');
        if ($request->orderByName) $events->orderBy('name', $request->orderByName);
        if ($request->orderByDate) $events->orderBy('name', $request->orderByDate);
        return response()->json([
            "success" => true,
            "events" => $events->paginate(15)
        ]);
    }
}
