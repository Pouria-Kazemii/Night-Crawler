<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


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
            'source_title' => $this->resource->crawler,
            'content' => $this->resource['content']
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
            preg_match('/src="([^"]+)"/i', $this->content['picture'][0], $matches);
            $src = $matches[1] ?? null;
            return $src;
        } else {
            return [];
        }
    }
}
