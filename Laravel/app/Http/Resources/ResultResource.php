<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;


class ResultResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'url' => $this->final_url,
            'title' => $this->content['title'],
            'main_price' => $this->content['main_price'],
            'discount_price' => $this->content['discount_price'],
            'isbn' => $this->content['isbn'],
            'category' => $this->content['category'],
            'publisher' => $this->content['publisher'],
            'group' => $this->content['group'],
            'field' => $this->content['field'],
            'lesson' => $this->content['lesson'],
            'page_count' => $this->content['page_count'],
            'grade' => $this->content['grade'],
            'weight' => $this->content['weight'],
            'creators' => $this->content['creators'],
            'publish_year' => $this->content['publish_year'],
            'description' => $this->content['description'],
            'image' => $this->getImageUrl(),
            'format' => $this->content['format'],
        ];
    }

    private function getMainPrice()
    {
        if (empty($this->content['main_price']) or $this->content['main_price'] == null) {
            if (empty($this->content['solo_price']) or $this->content['solo_price'] == null) {
                "قیمت ندارد";
            } else {
                return $this->content['solo_price'][0];
            }
        } else {
            return $this->content['main_price'][0];
        }
    }

    private function getDiscountPrice()
    {
        if (empty($this->content['discount_price']) or $this->content['discount_price'] == null) {
            if (empty($this->content['solo_price']) or $this->content['solo_price'] == null) {
                "قیمت ندارد";
            } else {
                return $this->content['solo_price'][0];
            }
        } else {
            return $this->content['discount_price'][0];
        }
    }

    private function getImageUrl()
    {
        if (isset($this->content['picture'][0])) {
            return Str::between($this->content['picture'][0], 'src="', '" class');
        } else {
            return [] ;
        }
    }
}