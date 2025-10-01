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
            'source' => $this->crawler['title'] ?? 'نامشخص',
            'url' => $this->final_url,
            'exists' => $this->checkExists(),
            'title' => $this->content['title'],
            'main_price' => $this->getPrice(),
            'discount_price' => $this->getPrice(false),
            'isbn' => $this->content['isbn'],
            'category' => $this->content['category'],
            'publisher' => $this->content['publisher'],
            'group' => $this->content['group'] ?? [],
            'field' => $this->content['field'] ?? $this->content['good_for'],
            'lesson' => $this->content['lesson'] ?? $this->content['subject'],
            'page_count' => $this->content['page_count'],
            'grade' => $this->content['grade'],
            'weight' => $this->content['weight'],
            'creators' => $this->content['creators'],
            'publish_year' => $this->content['publish_year'] ?? [],
            'description' => $this->content['description'],
            'image' => $this->getImageUrl(),
            'format' => $this->content['format'],
            'language' => $this->content['language'] ?? []
        ];
    }

    public function checkExists()
    {
        if (
            ($this->content['main_price']   ?? []) == [] &&
            ($this->content['discount_price'] ?? []) == [] &&
            ($this->content['solo_price'] ?? []) == []
        ) {
            return false;
        } else {
            return true;
        }
    }

    private function getPrice($main = true)
    {

        if ($this->checkExists() == false) {
            return "ندارد";
        }

        $crawler_id = $this->resource->crawler['id'];

        if ($crawler_id == '68cea2cef4df94b20a090967') {
            if ($main) {
                return (int)$this->content['solo_price'][0] * 10 ?? null;
            } else {
                return (int)$this->content['solo_price'][1] * 10 ?? null;
            }
        } else {
            if ($main) {
                return (int)$this->content['main_price'][0] * 10;
            } else {
                return (int)$this->content['discount_price'][0] * 10;
            }
        }
    }

    private function getImageUrl()
    {
        $image = null;

        if (isset($this->content['image']) and count($this->content['image'])) {
            $image = $this->content['image'][0];
        } else if (isset($this->content['picture']) and count($this->content['picture'])) {
            $image = $this->content['picture'][0];
        }

        if ($image != null) {
            preg_match('/src="([^"]+)"/i', $image, $matches);
            $src = $matches[1] ?? null;
            return $src;
        } else {
            return [];
        }
    }
}
