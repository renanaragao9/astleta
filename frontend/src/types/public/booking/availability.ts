export interface AvailableSlot {
    startTime: string;
    endTime: string;
    durationMinutes: number;
    price: string;
}

export interface AvailabilityField {
    id: number;
    name: string;
    pricePerHour: string;
    extraHourPrice: string;
    allowsExtraHour: boolean;
}

export interface AvailabilityResponse {
    field: AvailabilityField;
    date: string;
    dayOfWeek: number;
    availableSlots: AvailableSlot[];
    totalSlots: number;
}

export interface AvailabilityPayload {
    field_id: number;
    date: string;
}
