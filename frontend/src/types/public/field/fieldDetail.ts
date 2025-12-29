export interface PublicFieldCompany {
    id: number;
    name: string;
    phone: string;
    address: string;
    imagePath: string | null;
}

export interface PublicFieldSelectedItem {
    name: string;
    icon: string;
}

export interface PublicFieldSchedule {
    id: number;
    dayOfWeek: number;
    startTime: string;
    endTime: string;
}

export interface PublicFieldAmenity {
    id: number;
    name: string;
    icon?: string;
}

export interface PublicFieldImage {
    imagePath: string;
    caption: string;
}

export interface PublicField {
    id: number;
    name: string;
    description?: string | null;
    pricePerHour: string;
    extraHourPrice: string;
    isAllowsExtraHour: boolean;
    imagePath: string;
    images?: string[];
    fieldImage?: PublicFieldImage[];
    fieldType: {
        name: string;
    };
    fieldSurface: {
        name: string;
    };
    fieldSize: {
        name: string;
    };
    company: PublicFieldCompany;
    schedules: PublicFieldSchedule[];
    selectedItem: PublicFieldSelectedItem[];
}

export interface PublicFieldDetailResponse {
    status: string;
    message: string;
    data: PublicField;
}
