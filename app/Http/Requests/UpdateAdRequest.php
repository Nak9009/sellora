<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAdRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->route('ad')->user_id === auth()->id();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $ad = $this->route('ad');

        return [
            'category_id' => 'sometimes|exists:categories,id',
            'title' => "sometimes|string|max:255|unique:ads,title,$ad->id",
            'description' => 'sometimes|string|min:20|max:5000',
            'price' => 'sometimes|numeric|min:0',
            'currency' => 'sometimes|in:USD,EUR,GBP,PHP,KHR',
            'condition' => 'sometimes|in:new,like_new,good,fair,poor',
            'location' => 'sometimes|string|max:255',
            'status' => 'sometimes|in:active,paused,sold,expired',
        ];
    }
}
