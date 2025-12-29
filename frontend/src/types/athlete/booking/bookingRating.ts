export interface BookingRating {
    id: number;
    rating: number;
    technicalRating: number;
    tacticalRating: number;
    physicalRating: number;
    mentalRating: number;
    teamworkRating: number;
    comment?: string;
    createdAt: string;
    updatedAt: string;
    user?: {
        id: number;
        name: string;
        email: string;
    };
    participant?: {
        id: number;
        name: string;
        imagePath?: string;
    };
    booking?: {
        id: number;
        bookingNumber: string;
        bookingDate: string;
        startTime: string;
        endTime: string;
    };
}

export interface BookingRatingPayload {
    user_id?: number;
    booking_participant_id?: number;
    rating: number;
    technical_rating: number;
    tactical_rating: number;
    physical_rating: number;
    mental_rating: number;
    teamwork_rating: number;
    comment?: string;
}
