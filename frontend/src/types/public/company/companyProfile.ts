export interface PublicCompanyAddress {
    id: number;
    street: string;
    number: string;
    district: string;
    city: string;
    state: string;
    zipcode: string;
    fullAddress: string;
    latitude: number | null;
    longitude: number | null;
}

export interface PublicCompanyContact {
    id: number;
    type: string;
    value: string;
    is_primary: boolean;
}

export interface PublicCompany {
    id: number;
    name: string;
    description: string | null;
    phone: string;
    imagePath: string | null;
    isOpen: boolean;
    isFree: boolean;
    addresses: PublicCompanyAddress[];
    contacts: PublicCompanyContact[];
}

export interface PublicCompanyField {
    id: number;
    name: string;
    description: string | null;
    pricePerHour: string;
    extraHourPrice: string;
    isAllowsExtraHour: boolean;
    imagePath: string;
    fieldType: {
        name: string;
    };
    fieldSurface: {
        name: string;
    };
    fieldSize: {
        name: string;
    };
}
