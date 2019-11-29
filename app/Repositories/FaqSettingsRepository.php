<?php

namespace App\Repositories;

use App\Models\RewardSetting;
use App\Models\FaqSetting;
use App\Models\FaqSettingsQuestion;
use App\Models\MerchantReward;
use App\Repositories\Contracts\DisplaySettingsRepository;
use App\Repositories\RewardSettingsRepository;


class FaqSettingsRepository
{
    private $baseQuery;

    public function __construct()
    {
        $this->baseQuery = FaqSetting::query();
        $this->questionsQuery = FaqSettingsQuestion::query();
        $this->merchant = new MerchantRepository();
        $this->rewardSettingsRepo = new RewardSettingsRepository();
    }


    /**
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection|static[]
     */
    public function getCurrent($merchantObj = null)
    {
        if(!isset($merchantObj)) {
            $merchantObj = $this->merchant->getCurrent();
        }
        try{
            return $merchantObj->reward_settings->faq;
        }catch (\ErrorException $e){
            return NULL;
        }catch (\Exception $e){
            return $e->message;
        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection|static[]|NULL
     */
    public function getByRewardSettingsId($rewardId){
        if(empty($rewardId)){
            return NULL;
        }
        return $this->baseQuery->where('reward_settings_id', '=', $rewardId)->get();
    }

    /**
     * create new or update FaqSetting
     * @return App\Models\FaqSetting|False
     */
    public function create($data, $RewardSetting = null, $merchant = null){
        if(!isset($RewardSetting)){
            $currentRewardSetting = $this->rewardSettingsRepo->getCurrent($merchant);
        } else {
            $currentRewardSetting = $RewardSetting;
        }
        $faqModel = $this->getCurrent($merchant);
        if(empty($faqModel)){
            $faqModel = new FaqSetting();
            $faqModel->reward_settings_id = $currentRewardSetting->id;
        }
        $faqModel->save();

        $this->saveQuestions($faqModel, $data['questions']);

        $design = $data['design'];
    
        $faqModel->title = $data['title'];
        $faqModel->status = intval($data['status']);
        $faqModel->title_color = $design['titleColor'];
        $faqModel->question_color = $design['questionColor'];
        $faqModel->answer_color = $design['answerColor'];
        $faqModel->title_font_size = intval($design['titleFontSize']);
        $faqModel->question_font_size = intval($design['questionFontSize']);
        $faqModel->answer_font_size = intval($design['answerFontSize']);

        try {
            $faqModel->save();
            return $faqModel;
        } catch(\Exception $e){
            dd($e);
            return False;
        }
    }

    public function saveQuestions($faqModel, array $questions){
        $selectedQuestions = [];
        foreach ($questions as $question) {
            $questionModel = FaqSettingsQuestion::find(intval($question['id']));
            if(!isset($questionModel) || $questionModel->faq_settings_id != $faqModel->id){
                $questionModel = new FaqSettingsQuestion();
            }
            $questionModel->faq_settings_id = $faqModel->id;
            $questionModel->question = $question['question'];
            $questionModel->answer = $question['answer'];
            $questionModel->save();
            $selectedQuestions[] = $questionModel->id;
        }
        FaqSettingsQuestion::query()->where('faq_settings_id', '=', $faqModel->id) // delete unselected questions, wich was created before
            ->whereNotIn('id', $selectedQuestions)->delete();
    }
}
