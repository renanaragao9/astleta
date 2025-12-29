export interface PriceDetails {
    durationMinutes: number;
    durationHours: number;
    basePrice: number;
    extraHourPrice: number;
    totalPrice: number;
    formattedBasePrice: string;
    formattedExtraHourPrice: string;
    formattedTotalPrice: string;
}

export interface CalculatePricePayload {
    field_id: number;
    start_time: string;
    end_time: string;
    include_extra_hour: boolean;
}
