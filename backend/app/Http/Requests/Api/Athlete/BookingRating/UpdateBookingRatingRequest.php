<?php

namespace App\Http\Requests\Api\Athlete\BookingRating;

use App\Http\Requests\Api\Global\Base\BaseFormRequest;

class UpdateBookingRatingRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'booking_participant_id' => 'nullable|integer|exists:booking_participants,id',
            'rating' => 'nullable|integer|min:1|max:10',
            'technical_rating' => 'nullable|integer|min:1|max:10',
            'tactical_rating' => 'nullable|integer|min:1|max:10',
            'physical_rating' => 'nullable|integer|min:1|max:10',
            'mental_rating' => 'nullable|integer|min:1|max:10',
            'teamwork_rating' => 'nullable|integer|min:1|max:10',
            'team_rating' => 'nullable|integer|min:1|max:10',
            'comment' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'booking_participant_id.integer' => 'ID do participante deve ser um número inteiro.',
            'booking_participant_id.exists' => 'Participante não encontrado.',
            'rating.integer' => 'Avaliação geral deve ser um número inteiro.',
            'rating.min' => 'Avaliação geral deve ser no mínimo 1.',
            'rating.max' => 'Avaliação geral deve ser no máximo 10.',
            'technical_rating.integer' => 'Avaliação técnica deve ser um número inteiro.',
            'technical_rating.min' => 'Avaliação técnica deve ser no mínimo 1.',
            'technical_rating.max' => 'Avaliação técnica deve ser no máximo 10.',
            'tactical_rating.integer' => 'Avaliação tática deve ser um número inteiro.',
            'tactical_rating.min' => 'Avaliação tática deve ser no mínimo 1.',
            'tactical_rating.max' => 'Avaliação tática deve ser no máximo 10.',
            'physical_rating.integer' => 'Avaliação física deve ser um número inteiro.',
            'physical_rating.min' => 'Avaliação física deve ser no mínimo 1.',
            'physical_rating.max' => 'Avaliação física deve ser no máximo 10.',
            'mental_rating.integer' => 'Avaliação mental deve ser um número inteiro.',
            'mental_rating.min' => 'Avaliação mental deve ser no mínimo 1.',
            'mental_rating.max' => 'Avaliação mental deve ser no máximo 10.',
            'teamwork_rating.integer' => 'Avaliação de trabalho em equipe deve ser um número inteiro.',
            'teamwork_rating.min' => 'Avaliação de trabalho em equipe deve ser no mínimo 1.',
            'teamwork_rating.max' => 'Avaliação de trabalho em equipe deve ser no máximo 10.',
            'team_rating.integer' => 'Avaliação de equipe deve ser um número inteiro.',
            'team_rating.min' => 'Avaliação de equipe deve ser no mínimo 1.',
            'team_rating.max' => 'Avaliação de equipe deve ser no máximo 10.',
            'comment.string' => 'Comentário deve ser uma string.',
            'comment.max' => 'Comentário não pode ter mais de 1000 caracteres.',
        ];
    }
}
