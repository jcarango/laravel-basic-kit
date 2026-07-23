<?php

namespace App\Livewire;

use App\Models\Suffragan;
use App\Models\Survey;
use App\Models\SurveyAnswer;
use App\Models\SurveyResponse;
use Livewire\Component;

class SurveyForm extends Component
{
    public Survey $survey;
    public ?string $respondent_name = '';
    public ?string $document_number = '';
    public ?string $phone = '';
    public ?string $email = '';
    public ?string $address = '';
    public ?int $city_id = null;
    public ?string $latitude = '';
    public ?string $longitude = '';
    public array $answers = [];
    public bool $submitted = false;

    public function mount(Survey $survey)
    {
        $this->survey = $survey->load('questions');
        $this->city_id = $survey->city_id;

        foreach ($this->survey->questions as $q) {
            $this->answers[$q->id] = ($q->type === 'multiple_choice') ? [] : '';
        }
    }

    public function submit()
    {
        $rules = [];
        foreach ($this->survey->questions as $q) {
            if ($q->is_required) {
                $rules["answers.{$q->id}"] = 'required';
            }
        }

        $this->validate($rules, [], [
            'answers.*' => 'campo de respuesta',
        ]);

        $suffraganId = null;
        if (!empty($this->document_number)) {
            $suffragan = Suffragan::where('documentationnumber', trim($this->document_number))->first();
            if ($suffragan) {
                $suffraganId = $suffragan->id;
            }
        }

        $response = SurveyResponse::create([
            'survey_id' => $this->survey->id,
            'suffragan_id' => $suffraganId,
            'respondent_name' => $this->respondent_name,
            'document_number' => $this->document_number,
            'phone' => $this->phone,
            'email' => $this->email,
            'address' => $this->address,
            'city_id' => $this->city_id,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'submitted_at' => now(),
        ]);


        foreach ($this->answers as $questionId => $val) {
            $finalVal = is_array($val) ? implode(', ', $val) : (string) $val;

            SurveyAnswer::create([
                'survey_response_id' => $response->id,
                'survey_question_id' => $questionId,
                'answer_value' => $finalVal,
            ]);
        }

        $this->submitted = true;
    }

    public function render()
    {
        return view('livewire.survey-form')->layout('components.layouts.app');
    }
}
