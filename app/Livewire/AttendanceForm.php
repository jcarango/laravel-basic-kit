<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Event;
use App\Models\Suffragan;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Models\Divipol;
use Jenssegers\Agent\Agent;

class AttendanceForm extends Component
{
    public Event $event;

    // Campos del sufragante
    public $name;
    public $lastname;
    public $phone;
    public $documentationtype = 'CC';
    public $documentationnumber;
    public $email;
    public $address;
    public $profession;
    public $facebook;
    public $twitter;
    public $instagram;
    public $linkedin;
    public $voter_type = 'Opinión';
    
    // Asignación de líder
    public $leader_id;

    // Ubicación (combos dependientes)
    public $country_id;
    public $state_id;
    public $city_id;
    public $divipol_id;

    public $latitude_event;
    public $longitude_event;

    public $successMessage = false;

    protected $rules = [
        'name' => 'required|string|max:255',
        'lastname' => 'required|string|max:255',
        'phone' => 'required|string|max:20',
        'documentationtype' => 'required|string|max:50',
        'documentationnumber' => 'required|string|max:50',
        'email' => 'nullable|email|max:255',
        'address' => 'nullable|string|max:255',
        'profession' => 'nullable|string|max:255',
        'facebook' => 'nullable|string|max:255',
        'twitter' => 'nullable|string|max:255',
        'instagram' => 'nullable|string|max:255',
        'linkedin' => 'nullable|string|max:255',
        'voter_type' => 'nullable|string',
        'leader_id' => 'nullable|exists:users,id',
        'country_id' => 'nullable|integer',
        'state_id' => 'nullable|integer',
        'city_id' => 'nullable|integer',
        'divipol_id' => 'nullable|integer',
    ];

    public function mount(Event $event)
    {
        $this->event = $event;
        $this->leader_id = request()->query('leader');
    }

    public function updatedCountryId()
    {
        $this->state_id = null;
        $this->city_id = null;
    }

    public function updatedStateId()
    {
        $this->city_id = null;
    }

    public function submit()
    {
        $this->validate();

        $suffragan = Suffragan::where('documentationnumber', $this->documentationnumber)->first();

        $data = [
            'name' => $this->name,
            'lastname' => $this->lastname,
            'phone' => $this->phone,
            'documentationtype' => $this->documentationtype,
            'email' => $this->email,
            'address' => $this->address,
            'profession' => $this->profession,
            'facebook' => $this->facebook,
            'twitter' => $this->twitter,
            'instagram' => $this->instagram,
            'linkedin' => $this->linkedin,
            'voter_type' => $this->voter_type,
            'country_id' => $this->country_id ?: null,
            'state_id' => $this->state_id ?: null,
            'city_id' => $this->city_id ?: null,
            'divipol_id' => $this->divipol_id ?: null,
        ];

        // Ensure we don't overwrite if they are somehow empty
        $data = array_filter($data, function ($value) {
            return $value !== null && $value !== '';
        });

        // Fetch coordinates if address is present and geocoding not set yet
        if ($this->address && empty($suffragan->latitude)) {
            $fullAddress = "{$this->address}";
            $city = City::find($this->city_id);
            $state = State::find($this->state_id);
            $country = Country::find($this->country_id);
            if($city) $fullAddress .= ", {$city->name}";
            if($state) $fullAddress .= ", {$state->name}";
            if($country) $fullAddress .= ", {$country->name}";

            try {
                $response = \Illuminate\Support\Facades\Http::withHeaders([
                    'User-Agent' => 'DemosolElectoralSystem/1.0',
                ])->get('https://nominatim.openstreetmap.org/search', [
                    'q' => $fullAddress,
                    'format' => 'json',
                    'limit' => 1,
                ])->json();

                if (!empty($response) && isset($response[0]['lat']) && isset($response[0]['lon'])) {
                    $data['latitude'] = $response[0]['lat'];
                    $data['longitude'] = $response[0]['lon'];
                }
            } catch (\Exception $e) {}
        }

        if (!$suffragan) {
            $data['documentationnumber'] = $this->documentationnumber;
            $data['user_id'] = $this->leader_id;
            $suffragan = Suffragan::create($data);
        } else {
            if ($this->leader_id && empty($suffragan->user_id)) {
                $data['user_id'] = $this->leader_id;
            }
            $suffragan->update($data); // Data is already filtered above
        }

        $agent = new Agent();
        $suffragan->update([
            'user_agent' => request()->header('User-Agent'),
            'platform' => $agent->platform(),
            'language' => request()->server('HTTP_ACCEPT_LANGUAGE'),
            'timezone' => request()->header('Time-Zone'),
        ]);

        $this->event->suffragans()->syncWithoutDetaching([
            $suffragan->id => ['attended_at' => now()],
        ]);

        $this->successMessage = true;
        
        // Reset specific fields or just keep them? Let's clear main to allow next
        $this->reset(['name', 'lastname', 'phone', 'documentationnumber', 'email', 'address']);
    }

    public function render()
    {
        return view('livewire.attendance-form', [
            'countries' => Country::all(),
            'states' => $this->country_id ? State::where('country_id', $this->country_id)->get() : collect(),
            'cities' => $this->state_id ? City::where('state_id', $this->state_id)->get() : collect(),
            'divipols' => Divipol::orderBy('nom_puesto')->get(),
            'leaders' => \App\Models\User::role('lider')->orderBy('name')->get(),
        ]);
    }
}
