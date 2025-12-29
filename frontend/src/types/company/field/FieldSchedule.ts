export interface FieldSchedule {
    id?: number;
    dayOfWeek: number;
    startTime: string;
    endTime: string;
}

export interface FieldSchedulePayload {
    id?: number;
    day_of_week: number;
    start_time: string;
    end_time: string;
}
