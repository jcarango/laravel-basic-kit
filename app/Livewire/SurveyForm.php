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
    public array $answers = [];
    public bool $submitted = false;

    public function mount(Survey $survey)
    {
        $this->survey = $survey->load('questions');

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
