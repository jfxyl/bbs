<?php

namespace App\Http\Requests\Api;

use Dingo\Api\Http\FormRequest;

class ImageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
		$rules = [
			'type' => 'required|string|in:avatar,topic'
		];
		if($this->type == 'avatar'){
			$rules['image'] = 'required|mimes:bmp,jpeg,gif,png|dimensions:min_width=200,min_height=200';
		}else{
			$rules['image'] = 'required|mimes:bmp,jpeg,gif,png';
		}
        return $rules;
	}
	
	public function messages()
	{
		return [
			'image.dimensions' => '头像尺寸不小于200*200'
		];
	}
}
