<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\EarnSetting;

class EarningSettingsTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(EarnSetting $earn)
    {
        return [
            'id' => $earn->id,
            'title' => $earn->title,
            'design' => [
                'actionFontSize' => $earn->action_font_size, 
                'actionTextColor' => $earn->action_text_color, 
                'boxColor' => $earn->box_color, 
                'pointColor' => $earn->point_color, 
                'pointFontSize' => $earn->point_font_size,
                'ribbonColor' => $earn->ribbon_color,
                'titleColor' => $earn->title_color,
                'titleFontSize' => $earn->title_font_size,
            ],
        ];
    }
}
