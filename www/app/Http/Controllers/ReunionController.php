<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ReunionRequest;
use App\Http\Requests\ReunionSearchRequest;
use App\Http\Requests\ReunionSujetRequest;
use App\Http\Requests\ReunionParticipantRequest;
use App\Http\Requests\ReunionRechercheRequest;

use Carbon\Carbon;

use App\Action;
use App\Reunion;
use App\ReunionParticipant;
use App\ReunionSujet;

use App\Classes\ReunionClass;
use App\Classes\TempsClass;

class ReunionController extends Controller
{
    private $nbParPage = 3;

    public function printable($id)
    {
        return view('reunion.printable')->with([
            "reunion" => Reunion::find($id),
            "reunionParticipant" => ReunionParticipant::where('reunion_id', $id),
            "reunionParticipant2" => ReunionParticipant::where('reunion_id', $id),
            "reunionParticipant3" => ReunionParticipant::where('reunion_id', $id),
            "reunionSujet" => ReunionSujet::where('reunion_id', $id)->get(),
            "temps" => new TempsClass
        ]);
    }

    public function index(Reunion $reunion, ReunionParticipant $reunionParticipant, ReunionClass $reunionClass)
    {
        return view("reunion.index")->with([
            'reunion' => $reunion->orderBy('id', 'desc')->paginate($this->nbParPage),
            'reunionParticipant' => $reunionParticipant,
            'reunionClass' => $reunionClass,
            'carbon' => new Carbon()
        ]);
    }

    public function get($page, $nom = "", $date = "")
    {
        $page--;
        $skip = ($page * $this->nbParPage);

        /** If the name and date is filled */
        if($nom != "null" && $date != "null") {
            $date = (string) $date;

            $response = [
                "status" => "success",
                "reunions" => Reunion::where('sujet', 'like', "%$nom%")
                ->where('date', 'like', "%$date%")
                ->skip($skip)->take($this->nbParPage)->orderBy('id', 'DESC')->get()
            ];
            /** If the name is filled */
        } elseif($nom != "null" && $date == "null") {
            $response = [
                "status" => "success",
                "reunions" => Reunion::where('sujet', 'like', "%$nom%")
                ->skip($skip)->take($this->nbParPage)->orderBy('id', 'DESC')->get()
            ];
            /** If the date is filled */
        } elseif($date != "null" && $nom == "null") {
            $date = (string) $date;

            $response = [
                "status" => "success",
                "reunions" => Reunion::where('date', 'like', "%$date%")
                ->skip($skip)->take($this->nbParPage)->orderBy('id', 'DESC')->get()
            ];
        } else {
            $response = [
                "status" => "success",
                "reunions" => Reunion::skip($skip)->take($this->nbParPage)->orderBy('id', 'DESC')->get()
            ];
        }

        return json_encode($response);
    }

    public function getMaxPage($nom = "null", $date = "null")
    {
        /** If the name and date is filled */
        if($nom != "null" && $date != "null") {
            $date = (string) $date;

            $count = Reunion::where('sujet', 'like', "%$nom%")
                   ->where('date', 'like', "%$date%")
                   ->count();

            $page = ceil($count / $this->nbParPage);

            if($page >= 1) {
                $page = $page;
            } else {
                $page = 1;
            }

            return $page;

            /** If the name is filled */
        } elseif($nom != "null" && $date == "null") {
            $count = Reunion::where('sujet', 'like', "%$nom%")->count();

            $page = ceil($count / $this->nbParPage);

            if($page >= 1) {
                $page = $page;
            } else {
                $page = 1;
            }

            return $page;

            /** If the date is filled */
        } elseif($date != "null" && $nom == "null") {
            $date = (string) $date;

            $count = Reunion::where('date', 'like', "%$date%")->count();

            $page = ceil($count / $this->nbParPage);

            if($page >= 1) {
                $page = $page;
            } else {
                $page = 1;
            }

            return $page;

        } else {
            $count = Reunion::count();

            $page = ceil($count / $this->nbParPage);

            if($page >= 1) {
                $page = $page;
            } else {
                $page = 1;
            }

            return $page;
        }
    }

    public function getSubjects($id)
    {
        return Reunion::find($id)->subjects;
    }

    public function addSubject($id)
    {
        $reunionSujet = new ReunionSujet;

        $reunionSujet->reunion_id = $id;
        $reunionSujet->sujet = "Nouveau sujet";
        $reunionSujet->observation = "Observation";
        $reunionSujet->action = "Action";

        if($reunionSujet->save()) {
            $response = [
                "status" => "success"
            ];
        } else {
            $response = [];
        }

        return json_encode($response);
    }

    public function getAmount($nom = "null", $date = "null")
    {
        if($nom != "null" && $date != "null") {
            $date = (string) $date;

            return Reunion::where('sujet', 'like', "%$nom%")
                ->where('date', 'like', "%$date%")
                ->count();

            /** If the name is filled */
        } elseif($nom != "null" && $date == "null") {
            return Reunion::where('sujet', 'like', "%$nom%")->count();

            /** If the date is filled */
        } elseif($date != "null" && $nom == "null") {
            $date = (string) $date;

            return Reunion::where('date', 'like', "%$date%")->count();
        } else {
            return Reunion::count();
        }
    }

    public function getPresent($id)
    {
        return ReunionParticipant::where([
            ['reunion_id', $id],
            ['type', 'present']
        ])->get();
    }

    public function getAbsent($id)
    {
        return ReunionParticipant::where([
            ['reunion_id', $id],
            ['type', 'absent']
        ])->get();
    }

    public function getSecretaire($id)
    {
        return ReunionParticipant::where([
            ['reunion_id', $id],
            ['type', 'secretaire']
        ])->get();
    }

    public function add()
    {
        $reunion = new Reunion;

        $reunion->sujet = "Nouvelle réunion";
        $reunion->date = Carbon::now();
        $reunion->date_prochain = null;

        if($reunion->save()) {
            $response = [
                "status" => "success"
            ];
        } else {
            $response = [];
        }

        return json_encode($response);
    }

    public function addSujet($id, ReunionClass $reunionClass)
    {
        return $reunionClass->addSujet($id);
    }

    public function addParticipant(ReunionParticipantRequest $request)
    {
        $id = $request->get('id');
        $nom = $request->get('nom');
        $type = $request->get('type');

        $reunion = Reunion::find($id);
        $reunionParticipant = new ReunionParticipant;

        if($reunion) {
            $reunionParticipant->reunion_id = $id;
            $reunionParticipant->nom = $nom;
            $reunionParticipant->type = $type;

            $reunionParticipant->save();

            $response = [
                "status" => "success"
            ];
        } else {
            $response = [];
        }

        return json_encode($response);
    }

    public function edit(ReunionRequest $request)
    {
        $id = $request->get('id');
        $sujet = $request->get('sujet');
        $reunion = Reunion::find($id);

        if($reunion) {
            $reunion->sujet = $sujet;
            $reunion->save();

            $response = [
                "status" => "success"
            ];
        } else {
            $response = [];
        }

        return json_encode($response);
    }

    public function editDate(ReunionRequest $request)
    {
        $id = $request->get('id');
        $date = $request->get('date');
        $reunion = Reunion::find($id);

        if($reunion) {
            $reunion->date = $date;
            $reunion->save();

            $response = [
                "status" => "success"
            ];
        } else {
            $response = [];
        }

        return json_encode($response);
    }

    public function editDateProchain(ReunionRequest $request)
    {
        $id = $request->get('id');
        $date = $request->get('date_prochain');
        $reunion = Reunion::find($id);

        if($reunion) {
            $reunion->date_prochain = $date;
            $reunion->save();

            $response = [
                "status" => "success"
            ];
        } else {
            $response = [];
        }

        return json_encode($response);
    }



    public function editSubject(ReunionSujetRequest $request)
    {
        $id = $request->get('id');
        $sujet = $request->get('sujet');
        $reunionSujet = ReunionSujet::find($id);

        if($reunionSujet) {
            $reunionSujet->sujet = $sujet;
            $reunionSujet->save();

            $response = [
                "status" => "success"
            ];
        } else {
            $response = [];
        }

        return json_encode($response);
    }

    public function editObservation(ReunionSujetRequest $request)
    {
        $id = $request->get("id");
        $observation = $request->get("observation");

        $subject = ReunionSujet::find($id);

        if($subject) {
            $subject->observation = $observation;
            $subject->save();

            $response = [
                "status" => "success"
            ];
        } else {
            $response = [];
        }

        return json_encode($response);
    }

    public function editAction(ReunionSujetRequest $request)
    {
        $id = $request->get("id");
        $action = $request->get("action");

        $subject = ReunionSujet::find($id);

        if($subject) {
            $subject->action = $action;
            $subject->save();

            $response = [
                "status" => "success"
            ];
        } else {
            $response = [];
        }

        return json_encode($response);
    }

    public function editParticipant(ReunionParticipantRequest $request, ReunionClass $reunionClass)
    {
        return $reunionClass->editParticipant($request);
    }

    public function nullifyDateProchain($id)
    {
        $reunion = Reunion::find($id);

        if($reunion) {
            $reunion->date_prochain = null;
            $reunion->save();

            $response = [
                "status" => "success"
            ];
        } else {
            $response = [];
        }

        return json_encode($response);
    }

    public function delete($id)
    {
        $reunion = Reunion::find($id);

        if($reunion->delete()) {
            $response = [
                "status" => "success"
            ];
        } else {
            $response = [];
        }

        return json_encode($response);
    }

    public function deleteSubject($id)
    {
        $subject = ReunionSujet::find($id);

        if($subject) {
            $subject->delete();

            $response = [
                "status" => "success"
            ];
        } else {
            $response = [];
        }

        return json_encode($response);
    }

    public function deleteParticipant($id)
    {
        $participant = ReunionParticipant::find($id);

        if($participant) {
            $participant->delete();

            $response = [
                "status" => "success"
            ];
        } else {
            $response = [];
        }

        return json_encode($response);
    }
}
