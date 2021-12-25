<?php

namespace App\Http\Controllers;

use App\Models\CustomEvent as ModelsEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Utils\Utils;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Log;

use Laravel\Lumen\Routing\Controller as BaseController;

class EventsController extends BaseController
{
    public function get(Request $request)
    {
        $event = ModelsEvent::findOrFail(request()->route('id'));
        if ($request->expand) {
            try {
                $event = Utils::handleExpand($event, $request->expand); 
            } catch (Exception $e) {
                return response()->json([
                    "success" => false,
                    "error" => $e
                ]);
            }
        }
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
        $event = new ModelsEvent();
        $event->name = $request->name;
        $event->tsbegin = $request->tsbegin;
        $event->tsend = $request->tsend;
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
        $event = ModelsEvent::find($id);
        if (!$event) return response()->json([
            "success" => false,
            "error" => "Event was not found"
        ]);
        if (!($user->id == $event->owner_id || $user->hasPermission("UpdateAllEvents"))) return response("Access Denied", 403);
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

    public function delete(Request $request)
    {
        $id = request()->route('id');
        $event = ModelsEvent::find($id);
        if (!$event) return response()->json([
            "success" => false,
            "error" => "Event was not found"
        ]);
        $user = $request->user();
        if ($user->id != $event->owner_id || $user->hasPermission("deleteAllEvents")) return response("Unauthorized", 401);
        $event->delete();
        return response()->json([
            "success" => true,
            "message" => "The Event was deleted"
        ]);
    }

    public function list(Request $request)
    {
        $this->validate($request, [
            "orderByName" => "in:desc,asc",
            "orderByDate" => "in:desc,asc",
            "name" => "array:eq,like",
            "tsbegin" => "array:eq,gt,gte,lt,lte",
            "owner_id" => "array:eq"
        ]);
        $events_table = DB::table(ModelsEvent::getTableName());
        if ($request->name) {
            $events_table = Utils::getFilteredItems($events_table, 'name', $request->name);
        }
        if ($request->tsbegin) {
            $events_table = Utils::getFilteredItems($events_table, 'tsbegin', $request->tsbegin);
        }
        if ($request->owner_id) {
            $events_table = Utils::getFilteredItems($events_table, 'owner_id', $request->owner_id);
        }
        if ($request->orderByName) $events = $events_table->orderBy('name', $request->orderByName);
        else if ($request->orderByDate) $events = $events_table->orderBy('tsbegin', $request->orderByDate);
        else $events = $events_table->orderBy('tsbegin', 'desc');
        return response()->json([
            "success" => true,
            "events" => $events->paginate(15)
        ]);
    }

    public function addPicture(Request $request) {
        $id = request()->route('id');
        $event = ModelsEvent::find($id);
        
        if (!$event) return response()->json([
            "success" => false,
            "error" => "Event was not found"
        ]);
        if (!($request->user()->id == $event->owner_id || $request->user()->hasPermission("UpdateAllEvents"))) return response("Access Denied", 403);
        if ($request->hasFile('picture')) {
            $path = $request->picture->storePublicly('pictures');
            $event->picture = $path;
            $event->save();
        }        
        return response()->json([
            "success" => true,
            "event" => $event
        ]);       
    }
    public function downloadPicture(Request $request) {
        $id = request()->route('id');
        $event = ModelsEvent::find($id);
        if (!$event) return response()->json([
            "success" => false,
            "error" => "Event was not found"
        ]);
        if ($event->picture) {
            return Storage::download($event->picture);
        }
    }
}
